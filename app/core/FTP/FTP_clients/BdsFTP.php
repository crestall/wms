<?php
/**
 * BDS implimentation of the FTP class.
 *
 *
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class BdsFTP extends FTP
{
    private $client_id = 86;

    private $output;
    private $order_items;
    private $orders_csv = array();

    private $return_array = array(
        'orders_created'        => 0,
        'invoices_processed'    => 0,
        'import_error'          => false,
        'error'                 => false,
        'error_count'           => 0,
        'error_string'          => '',
        'import_error_string'   => '',
        'import_message'        => ''
    );

    public function init()
    {
        //Client Specific Credentials
        $this->URL = 'ftp.bahai.org.au';
        $this->USERNAME = 'bdsorders';
        $this->PASSWORD = 'mN**s735a';
    }

    public function collectOrders($file)
    {
        $tmp_handle = fopen('php://temp', 'r+');
        if (ftp_fget($this->CON_ID, $tmp_handle, $file, FTP_ASCII))
        {
            while ($row = fgetcsv($tmp_handle))
            {
                echo "<pre>",print_r($row),"</pre>";
                $this->orders_csv[] = $row;
            }
            $this->processOrders($this->orders_csv) ;
        }
        else
        {
            Logger::log("FTP Could not open file", "Could not open ". $file);
            throw new Exception("Could not open ". $file);
        }
        fclose($tmp_handle);
    }

    private function processOrders($file)
    {
        echo "<pre>",print_r($file),"</pre>"; die();
        /*
        [0] => Order_Number
        [1] => Shipment_Address_Company
        [2] => Shipment_Address_Name
        [3] => Shipment_Address1
        [4] => Shipment_Address2
        [5] => Shipment_Address_City
        [6] => Shipment_Address_State
        [7] => Shipment_AddressZIP_Code
        [8] => Shipment_Address_Country_Code
        [9] => Shipment_Address_Phone
        [10] => tracking_email
        [11] => ATL
        [12] => Delivery Instructions
        [13] => Express_Post
        [14] => Client Entry
        [15] => Item_1_sku
        [16] => Item_1_qty
        [17] => Item_1_id
        [18] => Item_2_sku
        [19] => Item_2_qty
        [20] => Item_2_id
        [21] => Item_3_sku
        [22] => Item_3_qty
        [23] => Item_3_id
        [24] => Item_4_sku
        [25] => Item_4_qty
        [26] => Item_4_id
        */
        $tmp_handle = fopen('php://temp', 'r+');
        if (ftp_fget($this->CON_ID, $tmp_handle, $file, FTP_ASCII))
        {
            rewind($tmp_handle);
            $allocations = array();
            $orders_items = array();
            $imported_order_count = 0;
            $imported_orders = array();
            $skip_first = true;
            $line = 1;
            $data_error_string = $item_error_string = "<ul>";
            $import_orders = true;
            while ($row = fgetcsv($tmp_handle)) {
                //echo "<pre>",print_r($row),"</pre>";continue;
                if($skip_first)
                {
                    $skip_first = false;
                    continue;
                }
                $items_errors = false;
                $weight = 0;
                $mm = "";
                $items = array();
                $sig = ($row[11] == 1)? 0 : 1;
                $order = array(
                    'error_string'          => '',
                    'items'                 => array(),
                    'ref2'                  => '',
                    'client_order_id'       => $row[0],
                    'errors'                => 0,
                    'tracking_email'        => $row[10],
                    'ship_to'               => $row[2],
                    'company_name'          => $row[1],
                    'date_ordered'          => time(),
                    'status_id'             => $this->controller->order->ordered_id,
                    'eparcel_express'       => 0,
                    'signature_req'         => $sig,
                    'contact_phone'         => $row[9],
                    'import_error'          => false,
                    'import_error_string'   => '',
                    'weight'                => 0,
                    'instructions'          => $row[12],
                    '3pl_comments'          => $row[14]
                );
                if(!empty($order['tracking_email']))
                {
                    if( !filter_var($order['tracking_email'], FILTER_VALIDATE_EMAIL) )
                    {
                        $order['errors'] = 1;
                        $order['error_string'] = "<p>The customer email is not valid</p>";
                    }
                }
                //the items
                $items = array();
                $item_error = false;
                $i = 15;
                do
                {
                    $sku = $row[$i];
                    ++$i;
                    $qty = $row[$i];
                    ++$i;
                    $client_item_id = $row[$i];
                    $item = $this->controller->item->getItemByClientProductId($sku);
                    if(empty($item))
                    {
                        $item_error = true;
                        $import_orders = false;
                        $data_error_string .= "<li>$sku could not be matched to any items in cell $i on row $line</li>";
                    }
                    else
                    {
                        $items[] = array(
                            'qty'               =>  $qty,
                            'id'                =>  $item['id'],
                            'client_item_id'    => $client_item_id
                        );
                    }
                    ++$i;
                }
                while(!empty($row[$i]));
                if(!$item_error)
                {
                    $order['items'] = $items;
                    $orders_items[$imported_order_count] = $items;
                    //validate address
                    $ad = array(
                        'address'   => $row[3],
                        'address_2' => $row[4],
                        'suburb'    => $row[5],
                        'state'     => $row[6],
                        'postcode'  => $row[7],
                        'country'   => $row[8]
                    );
                    if($ad['country'] == "AU")
                    {
                        if(strlen($ad['address']) > 40 || strlen($ad['address_2']) > 40 || strlen($order['company_name'])  > 40)
                        {
                            $order['errors'] = 1;
                            $order['error_string'] .= "<p>Addresses cannot have more than 40 characters</p>";
                        }
                        $aResponse = $this->controller->Eparcel->ValidateSuburb($ad['suburb'], $ad['state'], str_pad($ad['postcode'],4,'0',STR_PAD_LEFT));
                        //echo "<pre>",print_r($aResponse),"</pre>";
                        if(isset($aResponse['errors']))
                        {
                            $order['errors'] = 1;
                            foreach($aResponse['errors'] as $e)
                            {
                                $order['error_string'] .= "<p>{$e['message']}</p>";
                            }
                        }
                        elseif($aResponse['found'] === false)
                        {
                            $order['errors'] = 1;
                            $order['error_string'] .= "<p>Postcode does not match suburb or state</p>";
                        }
                    }
                    else
                    {
                        if( strlen( $ad['address'] ) > 50 || strlen( $ad['address_2'] ) > 50 )
                        {
                            $order['errors'] = 1;
                            $order['error_string'] .= "<p>International addresses cannot have more than 50 characters</p>";
                        }
                        if( strlen($order['ship_to']) > 30 || strlen($order['company_name']) > 30 )
                        {
                            $order['errors'] = 1;
                            $order['error_string'] .= "<p>International names and company names cannot have more than 30 characters</p>";
                        }
                    }
                    if(!preg_match("/(?:[A-Za-z].*?\d|\d.*?[A-Za-z])/i", $ad['address']) && (!preg_match("/(?:care of)|(c\/o)|( co )/i", $ad['address'])))
                    {
                        $order['errors'] = 1;
                        $order['error_string'] .= "<p>The address is missing either a number or a word</p>";
                    }
                    $order = array_merge($order, $ad);
                    $imported_orders[$imported_order_count] = $order;
                    ++$imported_order_count;
                }
                else
                {
                    echo $data_error_string."</ul>";
                    $import_orders = false;
                }
                ++$line;
            }
            if($import_orders)
            {
                $all_items = $this->allocations->createOrderItemsArray($orders_items);
                //echo "<pre>",print_r($orders_items),"</pre>";die();
                $item_error = false;
                $error_string = "";
                foreach($all_items as $oind => $order_items)
                {
                    foreach($order_items as $item)
                    {
                        if($item['item_error'])
                        {
                            $import_orders = false;
                            $data_error_string .= "<li>".$item['item_error_string']." for order {$imported_orders[$oind]['client_order_id']}</li>";
                        }
                    }
                }
                $data_error_string .= "</ul>";
                if($import_orders)
                {

                }
                else
                {

                }
            }
            echo "<pre>",print_r($imported_orders),"</pre>";
        }
        fclose($tmp_handle);
    }
} //end class
?>