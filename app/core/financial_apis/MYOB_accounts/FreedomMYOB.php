<?php
/**
 * Freedom implimentation of the MYOB class.
 *
 *
 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class FreedomMYOB extends MYOB
{
    private $client_id = 7;

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
        $this->URL = 'https://coventrypress.com.au/api/';
        $this->USERNAME = 'mark';
        $this->PASSWORD = 'szqwj1QdSuVZ8dThwl';
        //Client Specific Encryption Details
        $this->CYPHER = "AES-256-CBC";
        $this->KEY = Config::get('FREEDOM_MYOB_KEY');
        $this->TAG = "Coventry Press";
    }

    public function getDecryptedData($data)
    {
        return $this->decryptData($data);
    }

    public function processOrders($collected_orders)
    {
        //echo "<pre>",print_r($collected_orders),"</pre>"; //die();
        //echo count($collected_orders);die();
        $orders = array();
        if(count($collected_orders))
        {
            //echo "Count ".count($collected_orders);die();
            $allocations = array();
            $orders_items = array();
            foreach($collected_orders as $o)
            {
                $items_errors = false;
                $weight = 0;
                $mm = "";
                $items = array();
                //$o = trimArray($o);
                $order = array(
                    'error_string'          => '',
                    'items'                 => array(),
                    'ref2'                  => '',
                    'client_order_id'       => $o['Invoice_Number'],
                    'errors'                => 0,
                    'tracking_email'        => $o['Customer_Email'],
                    'ship_to'               => $o['Customer_Name'],
                    'date_ordered'          => strtotime( $o['Date'] ),
                    'status_id'             => $this->controller->order->ordered_id,
                    'eparcel_express'       => 0,
                    'signature_req'         => 0,
                    'import_error'          => false,
                    'import_error_string'   => '',
                    'invoices'              => array(),
                    'customer_id'           => $o['Customer_UID'],
                    'invoice_UIDs'          => array(),
                    'company_file_ids'      => array(),
                );
                //if(strtolower($o['shipping_lines'][0]['method_title']) == "express shipping") $order['eparcel_express'] = 1;
                if( !filter_var($o['Customer_Email'], FILTER_VALIDATE_EMAIL) )
                {
                    $order['errors'] = 1;
                    $order['error_string'] = "<p>The customer email is not valid</p>";
                }
                //validate address
                /* Old Fucked Up style
                $atc = $o['ShipToAddress']."<br />";
                try{
                   list($name, $line1, $line2, $line3) = explode("<br />", $atc);
                }
                catch(exception $e){
                   echo $e->getMessage();
                   echo "<p>Problem with $atc</p>";
                }
                $address = $line1;
                if(empty($line3))
                {
                    //echo "<p>2 line address</p>";
                    $address_2 = "";
                    try{
                        list($suburb, $state, $postcode) = explode("  ", $line2);
                    }
                    catch(exception $e){
                        echo $e->getMessage();
                        echo "<p>Problem with $line2</p>";
                    }
                }
                else
                {
                    //echo "<p>3 line address</p>";
                    $address_2 = $line2;
                    try{
                        list($suburb, $state, $postcode) = explode("  ", $line3);
                    }
                    catch(exception $e){
                        echo $e->getMessage();
                        echo "<p>Problem with $line3</p>";
                    }
                }

                $ad = array(
                    'address'   => $address,
                    'address_2' => $address_2,
                    'suburb'    => $suburb,
                    'state'     => $state,
                    'postcode'  => $postcode,
                    'country'   => "AU"
                );
                */
                //New Better Method
                $country = empty($o['Structured_Address']['Country'])? "AU": $o['Structured_Address']['Country'];
                $ad = array(
                    'address'   => str_replace("<br />",",",nl2br($o['Structured_Address']['Street'])),
                    'suburb'    => $o['Structured_Address']['City'],
                    'state'     => $o['Structured_Address']['State'],
                    'postcode'  => $o['Structured_Address']['PostCode'],
                    'country'   => $country
                );
                if($ad['country'] == "AU")
                {
                    if(strlen($ad['address']) > 40 || strlen($ad['address_2']) > 40)
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
                    if( strlen($order['ship_to']) > 30  )
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
                if(empty($o['ItemsPurchased']) || count($o['ItemsPurchased']) == 0)
                {
                    $items_errors = true;
                    $mm .= "<li>There are no items in {$o['Invoice_Number']} for {$o['Customer_Name']}</li>";
                }
                else
                {
                    foreach($o['ItemsPurchased'] as $item)
                    {
                        $product = $this->controller->item->getItemBySku($item['ProductCode']);
                        if(!$product)
                        {
                            $items_errors = true;
                            $mm .= "<li>Could not find {$item['Title']} in WMS based on {$item['ProductCode']}</li>";
                        }
                        else
                        {
                            $n_name = $product['name'];
                            $item_id = $product['id'];
                            $items[] = array(
                                'qty'           =>  $item['Qty'],
                                'id'            =>  $item_id,
                                'whole_pallet'  => false
                            );
                            $qty += $item['Qty'];
                            $weight += $product['weight'] * $item['Qty'];
                        }
                    }
                }
                $delivery_instructions =  "Please leave in a safe place out of the weather";
                $order['instructions'] = $delivery_instructions;
                //echo "<pre>",print_r($order),"</pre>";//die();
                if($items_errors)
                {
                    $message = "<p>There was a problem with invoice number {$order['client_order_id']} for {$order['ship_to']}</p>";
                    $message .= "<ul>".$mm."</ul>";
                    $message .= "<p>This invoice could not be imported into the WMS.</p>";
                    $message .= "<p>Other invoices for {$order['ship_to']} that did not throw such an error have been imported</p>";
                    //Send an email regarding the error
                    Email::sendFreedomMYOBError($message);
                    $this->return_array['error_string'] .= $message;
                    ++$this->return_array['error_count'];
                    //echo $message;
                }
                else
                {
                    //merge orders
                    if($ind = Utility::in_array_r($o['Customer_UID'], $orders))
                    {
                        $orders[$ind]['quantity'] += $qty;
                        $orders[$ind]['weight'] += $weight;
                        $orders[$ind]['items'] = array_merge($orders[$ind]['items'], $items);
                        $orders[$ind]['invoices'][] = $o['InvoicePDF'];
                        $orders[$ind]['invoice_UIDs'][] = $o['Invoice_UID'];
                        $orders[$ind]['company_file_ids'][] = $o['Company_File_Id'];
                        $orders[$ind]['client_order_id'] .= ", ".$o['Invoice_Number'];
                        //$orders_items[$o['Invoice_Number']] = $items;

                        $orders_items[$orders[$ind]['invoice_UIDs'][0]] = array_merge($orders_items[$orders[$ind]['invoice_UIDs'][0]], $items);
                    }
                    else
                    {
                        $order['quantity'] = $qty;
                        $order['weight'] = $weight;
                        $order['items'] = $items;
                        $order['invoices'][] = $o['InvoicePDF'];
                        $order['invoice_UIDs'][] = $o['Invoice_UID'];
                        $order['company_file_ids'][] = $o['Company_File_Id'];
                        $orders_items[$o['Invoice_UID']] = $items;
                        $order = array_merge($order, $ad);
                        $orders[] = $order;
                    }
                }
            }//endforeach order
            $totoitems = $this->controller->allocations->createOrderItemsArray($orders_items);
            $this->addOrders($orders, $totoitems);
            return $this->return_array;
        }//end if count orders
        else
        {
            $summary = "
                <p>No new invoices in the system</p>
                <p>No WMS orders have been created</p>
            ";
            Email::sendFreedomMYOBSummary($summary);
        }
        return false;
    }

    private function addOrders($orders, $totoitems)
    {
        /*$feedback = array(
            'error_string'          => '',
            'import_error_string'   => '',
            'import_message'        => ''
        );*/
        $processed_invoices = array();
        $wms_orders_created = 0;
        foreach($orders as $o)
        {
            //check for errors first
            $item_error = false;
            $error_string = "";
            foreach($totoitems[$o['invoice_UIDs'][0]] as $item)
            {
                if($item['item_error'])
                {
                    $item_error = true;
                    $error_string .= $item['item_error_string'];
                }
            }
            if($item_error)
            {
                /**/
                $message = "<p>There has been a problem with some items in invoice number(s) {$o['client_order_id']} for {$o['ship_to']}</p>";
                $message .= $error_string;
                $message .= "<p>This has meant all invoices for {$o['ship_to']} have not been imported into the WMS</p>";
                ++$this->return_array['error_count'];
                $this->return_array['error_string'] .= $message;
                //Send an email regarding the error
                Email::sendFreedomMYOBError($message);
                continue;
            }
            if($o['import_error'])
            {
                $this->return_array['import_error_string'] .= $o['import_error_string'];
                Email::sendFreedomMYOBError($o['import_error_string']);
                continue;
            }
            //insert the order
            $vals = array(
                'client_order_id'       => $o['client_order_id'],
                'client_id'             => 7,
                'deliver_to'            => $o['ship_to'],
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
                'country'               => $o['country']
            );

            //save the invoices
            $pdfs = array();
            foreach($o['invoices'] as $base64_pdf)
            {
                $tmp_name = "/tmp/".Utility::generateRandString(5).".pdf";
                $this_pdf = fopen ($tmp_name,'w');
                fwrite ($this_pdf,base64_decode($base64_pdf));
                fclose ($this_pdf);
                //array_push($pdfs,'../tmp/test'.$rowp['id'].'.pdf');
                $pdfs[] = array(
                	'file'		    =>	$tmp_name,
                    'orientation'	=>	'P'
                );
            }
            $upcount = 1;
            $filename = "invoice";
            $name = "invoice.pdf";
            $upload_dir = "/client_uploads/7/";
            if ( ! is_dir(DOC_ROOT.$upload_dir))
                        mkdir(DOC_ROOT.$upload_dir);
			while(file_exists(DOC_ROOT.$upload_dir.$name))
            {
                $name = $filename."_".$upcount.".pdf";
                ++$upcount;
            }
            $pdf = new Mympdf();
            $pdf->mergePDFFilesToServer($pdfs, $name, DOC_ROOT.$upload_dir);
            $uploaded_file = $name;
            $vals['uploaded_file'] = $uploaded_file;
            //create the order
            $itp = array($totoitems[$o['invoice_UIDs'][0]]);
            //echo "<pre>",print_r($itp),"</pre>";
            $order_number = $this->controller->order->addOrder($vals, $itp);
            ++$wms_orders_created;
            ++$this->return_array['orders_created'];
            $this->return_array['import_message'] .="<p>$order_number created</p>";
            //send back to MYOB
            foreach($o['invoice_UIDs'] as $key => $invoice_UID)
            {
                $this->callTask('markInvoiceSent',array('invoiceUID' => $invoice_UID, 'companyId' => $o['company_file_ids'][$key]));
                //echo "<p>will call markInvoiceSent with $invoice_UID and ".$o['company_file_ids'][$key]."</p>";
                ++$this->return_array['invoices_processed'];
            }
            $processed_invoices[] = $o['client_order_id'];
        }
        //die();
        //Send email about what happened
        $s = (count($processed_invoices) > 1)? "s have" : " has";
        $wmsos = ($wms_orders_created == 1)? " has": "s have";
        $pi_string = implode("<br/>", $processed_invoices);
        $summary = "
            <p>The following invoice{$s} been imported into the WMS and {$this->return_array['orders_created']} order{$wmsos} been created</p>
            <p>$pi_string</p>
            <p>They have all been marked as 'Sent' in MYOB</p>
        ";
        //echo "<pre>",print_r($this->return_array),"</pre>";
        Email::sendFreedomMYOBSummary($summary);
        return $this->return_array;
    }



    protected function encryptData($data)
    {
        $ivlen = openssl_cipher_iv_length($this->CYPHER);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $encrypted = openssl_encrypt($data, $this->CYPHER, $this->KEY, $options=0, $iv);
        $iv = base64_encode($iv);
        return $iv."\r\n".$encrypted;
    }

    protected function decryptData($data)
    {
        global $cipher, $key, $tag;
        list($iv, $encrypted) = explode("\r\n",$data);
        $iv = base64_decode($iv);
        $decrypted = openssl_decrypt($encrypted, $this->CYPHER, $this->KEY, $options=0, $iv);
        return $decrypted;
    }
}//end class

?>