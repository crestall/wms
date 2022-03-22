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

    private $return_array = array();

    public function init()
    {
        //Client Specific Credentials
        $this->URL = 'ftp.bahai.org.au';
        $this->USERNAME = 'bdsorders';
        $this->PASSWORD = 'mN**s735a';
        $this->resetVars();
    }

    public function collectOrders($file)
    {
        $this->resetVars();
        $tmp_handle = fopen('php://temp', 'r+');
        if (ftp_fget($this->CON_ID, $tmp_handle, $file, FTP_ASCII))
        {
            rewind($tmp_handle);
            while ($row = fgetcsv($tmp_handle))
            {
                $this->orders_csv[] = $row;
            }
            fclose($tmp_handle);
            $this->output = "=========================================================================================================".PHP_EOL;
            $this->output .= "IMPORTING BDS ORDERS ON ".date("jS M Y (D), g:i a (T)").PHP_EOL;
            $this->output .= "=========================================================================================================".PHP_EOL;
            $this->return_array['total_import'] = count($this->orders_csv) - 1;

            if($orders = $this->processOrders($this->orders_csv))
            {
                if(!$this->addOrders($orders))
                {
                    die("It's All Fucked");
                }
            }
            Logger::logOrderImports('order_imports/bds', $this->output); //die();
        }
        else
        {
            Logger::log("FTP Could not open file", "Could not open ". $file);
            throw new Exception("Could not open ". $file);
        }
        return $this->return_array;
    }

    public function uploadCSVFile(array $csvData)
    {
        $temp_file = fopen('php://temp', 'r+');
        fputcsv($temp_file, $csvData['cols'], ',', '"');
        foreach($csvData['rows'] as $row)
        {
            fputcsv($temp_file, array_values($row), ',', '"');
        }
        rewind($temp_file);
        $file_name = "BDS_dispatch_".date("Ymd")."_".time().".csv";
        if($this->uploadFile($file_name, $temp_file))
        {
            fclose($temp_file);
            return true;
        }
        else
        {
            fclose($temp_file);
            return false;
        }
    }

    private function processOrders($the_orders)
    {
        //echo "<pre>",print_r($the_orders),"</pre>"; die();
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
        [18] => pod_2_id
        [19] => Item_2_sku
        [20] => Item_2_qty
        [21] => Item_2_id
        [22] => pod_2_id
        [23] => Item_3_sku
        [24] => Item_3_qty
        [25] => Item_3_id
        [26] => pod_3_id
        etc
        */
        if(count($the_orders) == 0)
            return false;
        $orders = array();
        if(!isset($the_orders[0]))
            $collected_orders[] = $the_orders;
        else
            $collected_orders = $the_orders;

        //echo "THE ORDERS<pre>",print_r($the_orders),"</pre>";//die();
        $skip_first = true;
        if(count($collected_orders) > 0)
        {
            $allocations = array();
            $orders_items = array();
            foreach($collected_orders as $o)
            {
                //echo "<pre>",print_r($row),"</pre>";continue;
                $line = 1;
                if($skip_first)
                {
                    $skip_first = false;
                    continue;
                }
                $client_order_id =  (int)preg_replace('/\D/ui','',$o[0]);
                $items_errors = false;
                $weight = 0;
                $mm = "";
                $items = array();
                //$o = trimArray($o);
                $order = array(
                    'error_string'          => '',
                    'items'                 => array(),
                    'ref2'                  => '',
                    'client_order_id'       => $client_order_id,
                    'errors'                => 0,
                    'tracking_email'        => $o[10],
                    'ship_to'               => $o[2],
                    'company_name'          => $o[1],
                    'date_ordered'          => time(),
                    'status_id'             => $this->controller->order->ordered_id,
                    'eparcel_express'       => ( isset($o[13]) && $o['13'] == 1 )? 1 : 0,
                    'signature_req'         => ($o[11] == 1)? 0 : 1,
                    '3pl_comments'          => $o[14],
                    'contact_phone'         => $o[9],
                    'import_error'          => false,
                    'import_error_string'   => ''
                );
                if(!empty($o[10]))
                {
                    if( !filter_var($o[10], FILTER_VALIDATE_EMAIL) )
                    {
                        $order['errors'] = 1;
                        $order['error_string'] = "<p>The customer email is not valid</p>";
                    }
                }
                //validate address
                $ad = array(
                    'address'   => $o[3],
                    'address_2' => $o[4],
                    'suburb'    => $o[5],
                    'state'     => $o[6],
                    'postcode'  => $o[7],
                    'country'   => $o[8]
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
                $qty = 0;
                $i = 15;
                do
                {
                    $cpi = $o[$i];
                    ++$i;

                    $iqty = $o[$i];
                    ++$i;

                    $client_item_id = $o[$i];
                    ++$i;

                    $pod_id = (isset($o[$i]))? $o[$i] : NULL;

                    $item = $this->controller->item->getItemByClientProductId($cpi);
                    if(!$item)
                    {
                        $items_errors = true;
                        $mm .= "<li>Could not find $cpi in the Warehouse system</li>";
                    }
                    else
                    {
                        $n_name = $item['name'];
                        $item_id = $item['id'];
                        $items[] = array(
                            'qty'               =>  $iqty,
                            'id'                =>  $item_id,
                            'client_item_id'    => $client_item_id,
                            'pod_id'            => $pod_id
                        );
                        $qty += $iqty;
                        $weight += $item['weight'] * $iqty;
                    }
                    ++$i;
                }
                while(!empty($o[$i]));
                if(empty($o[12]))
                {
                    $delivery_instructions =  "Please leave in a safe place out of the weather";
                }
                else
                {
                    $delivery_instructions = $o[12];
                }
                $order['instructions'] = $delivery_instructions;
                if($items_errors)
                {
                    $message = "<p>There was a problem with some items</p>";
                    $message .= "<ul>".$mm."</ul>";
                    $message .= "<p>The order with this item that could not be processed is listed below</p>";
                    $message .= "<p>This order has <strong>NOT</strong> been imported</p>";
                    $message .= "<p>---------------------------------------------------</p>";
                    $message .= "<p>BDS Order ID: #{$order['client_order_id']}</p>";
                    $message .= "<p>Customer: {$order['ship_to']}</p>";
                    $message .= "<p>Address: {$ad['address']}</p>";
                    $message .= "<p>{$ad['address_2']}</p>";
                    $message .= "<p>{$ad['suburb']}</p>";
                    $message .= "<p>{$ad['state']}</p>";
                    $message .= "<p>{$ad['postcode']}</p>";
                    $message .= "<p>{$ad['country']}</p>";
                    $message .= "<p>=============================================================</p>";
                    $message .= "<p>=============================================================</p>";
                    $this->return_array['import_error'] = true;
                    ++$this->return_array['import_error_count'];
                    $this->return_array['import_error_string'] .= $message;

                }
                elseif(count($items))
                {
                    $order['quantity'] = $qty;
                    $order['weight'] = $weight;
                    $order['items'] = $items;
                    $orders_items[$client_order_id] = $items;
                    $order = array_merge($order, $ad);
                    $orders[] = $order;
                }
                ++$line;
            }//end foreach orders
            $this->order_items = $this->controller->allocations->createOrderItemsArray($orders_items);
            //echo "<pre>ORDER ITEMS",print_r($this->order_items),"</pre>";//die();
            //echo "<pre>ORDERS",print_r($orders),"</pre>";die();
            return $orders;
        }
        else
        {
            $this->output .= "=========================================================================================================".PHP_EOL;
            $this->output .= "No New Orders".PHP_EOL;
            $this->output .= "=========================================================================================================".PHP_EOL;
            $this->return_array['import_error'] = true;
            $this->return_array['import_error_string'] .= "<p>There are NO NEW ORDERS</p>";
        }
        return false;
    }

    private function addOrders($orders)
    {
        foreach($orders as $o)
        {
            //check for errors first
            $item_error = false;
            $item_backorder = false;
            $error_string = "";
            $backorder_string = "";
            foreach($this->order_items[$o['client_order_id']] as $item)
            {
                if($item['item_error'])
                {
                    $item_error = true;
                    $error_string .= $item['item_error_string'];
                }
                if($item['item_backorder'])
                {
                    $item_backorder = true;
                    $backorder_string .= $item['item_backorder_string'];
                }
            }
            if($item_backorder)
            {
                $message = "<p>{$o['client_order_id']} cannot be shipped at the moment</p>";
                $message .= $backorder_string;
                $message .= "<p>Order details are listed below</p>";
                $message .= "<p>---------------------------------------------------</p>";
                $message .= "<p>BDS Order ID: {$o['client_order_id']}</p>";
                $message .= "<p>Customer: {$o['ship_to']}</p>";
                $message .= "<p>Address: {$o['address']}</p>";
                $message .= "<p>{$o['address_2']}</p>";
                $message .= "<p>{$o['suburb']}</p>";
                $message .= "<p>{$o['state']}</p>";
                $message .= "<p>{$o['postcode']}</p>";
                $message .= "<p>{$o['country']}</p>";
                $message .= "<p>=============================================================</p>";
                $message .= "<p>=============================================================</p>";
                /*if (php_sapi_name() !='cli')
                Email::sendBDSImportError($message);
                $this->output .= "Email Sent From Add Orders With Message $message".PHP_EOL;
                */
                $this->return_array['backorder'] = true;
                ++$this->return_array['backorder_count'];
                $this->return_array['backorder_string'] .= $message;
            }
            if($item_error)
            {
                $message = "<p>There was a problem with some items for order {$o['client_order_id']}</p>";
                $message .= $error_string;
                $message .= "<p>This order has <strong>NOT</strong> been imported</p>";
                $message .= "<p>Order details are listed below</p>";
                $message .= "<p>---------------------------------------------------</p>";
                $message .= "<p>BDS Order ID: {$o['client_order_id']}</p>";
                $message .= "<p>Customer: {$o['ship_to']}</p>";
                $message .= "<p>Address: {$o['address']}</p>";
                $message .= "<p>{$o['address_2']}</p>";
                $message .= "<p>{$o['suburb']}</p>";
                $message .= "<p>{$o['state']}</p>";
                $message .= "<p>{$o['postcode']}</p>";
                $message .= "<p>{$o['country']}</p>";
                $message .= "<p>=============================================================</p>";
                $message .= "<p>=============================================================</p>";
                /*if (php_sapi_name() !='cli')
                Email::sendBDSImportError($message);
                $this->output .= "Email Sent From Add Orders With Message $message".PHP_EOL;
                */
                $this->return_array['inventory_error'] = true;
                ++$this->return_array['inventory_error_count'];
                $this->return_array['inventory_error_string'] .= $message;
                continue;
            }
            if($o['import_error'])
            {
                $this->return_array['import_error'] = true;
                $this->return_array['import_error_string'] = $o['import_error_string'];
                continue;
            }
            //insert the order
            $vals = array(
                'client_order_id'       => $o['client_order_id'],
                'client_id'             => 86,
                'deliver_to'            => $o['ship_to'],
                'company_name'          => $o['company_name'],
                'date_ordered'          => $o['date_ordered'],
                'tracking_email'        => $o['tracking_email'],
                'weight'                => $o['weight'],
                'delivery_instructions' => $o['instructions'],
                'errors'                => $o['errors'],
                'error_string'          => $o['error_string'],
                'address'               => $o['address'],
                'address2'              => $o['address_2'],
                'state'                 => $o['state'],
                'suburb'                => $o['suburb'],
                'postcode'              => $o['postcode'],
                'country'               => $o['country'],
                '3pl_comments'          => $o['3pl_comments'],
                'contact_phone'         => $o['contact_phone']
            );
            if($item_backorder) $vals['backorder_items'] = 1;
            if($o['signature_req'] == 1) $vals['signature_req'] = 1;
            if($o['eparcel_express'] == 1) $vals['eparcel_express'] = 1;
            $itp = array($this->order_items[$o['client_order_id']]);
            $order_number = $this->controller->order->addOrder($vals, $itp);
            ++$this->return_array['import_count'];
            $this->return_array['import_string'] .= "<p>Imported BDS order {$o['client_order_id']}. The Warehouse order number is: $order_number</p>";
            $this->output .= "Inserted Order: $order_number".PHP_EOL;
            $this->output .= print_r($vals,true).PHP_EOL;
            $this->output .= print_r($this->order_items[$o['client_order_id']], true).PHP_EOL;
        }
        return true;
    }

    private function resetVars()
    {
        $this->return_array = array(
            'import_count'          => 0,
            'total_import'          => 0,
            'import_error'          => false,
            'inventory_error'       => false,
            'error'                 => false,
            'import_error_count'    => 0,
            'inventory_error_count' => 0,
            'backorder'             => false,
            'backorder_string'      => '',
            'backorder_count'       => 0,
            'inventory_error_string'=> '',
            'import_error_string'   => '',
            'import_string'         => ''
        );

        $this->orders_csv = array();
    }
} //end class
?>