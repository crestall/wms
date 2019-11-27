<?php

/**
 * Squarespace class.
 *
 * Interacts with the squarespace api

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

//use Automattic\WooCommerce\HttpClient\HttpClientException;

class Squarespace{

    private $output;
    //private $squarespace;
    private $naturaldistillingitems;
    private $return_array = array(
        'import_count'          => 0,
        'import_error'          => false,
        'error'                 => false,
        'error_count'           => 0,
        'error_string'          => '',
        'import_error_string'   => ''
    );

    public $controller;

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
    }

    public function getNatutralDistillingOrders()
    {
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "Natural Distilling Co ORDER IMPORTING FOR ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;

        $ch = curl_init();

        curl_setopt_array($ch,array(
            CURLOPT_URL => "https://api.squarespace.com/1.0/commerce/orders?fulfillmentStatus=PENDING",
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer 95f6b0a4-8bd7-456d-b4b3-809ce1e2aec4",
                "cache-control: no-cache",
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($ch);
        $response2 = json_decode($response, true);
        //echo "<pre>",print_r($response2['result']),"</pre>"; die();
        $err = curl_error($ch);

        curl_close($ch);

        if ($err)
        {
            if ($_SERVER['HTTP_USER_AGENT'] == '3PLPLUSAGENT')
            {
                Email::sendCronError($err, "Natural Distilling Company");
                return;
            }
            else
            {
                $this->return_array['import_error'] = true;
                $this->return_array['import_error_string'] .= print_r($err, true);
                return $this->return_array;
            }

        }
        else
        {
            $collected_orders = $response2['result'];
            //echo "<pre>",print_r($collected_orders),"</pre>";
        }
        if($orders = $this->procNaturalDistillingOrders($collected_orders))
        {
            $this->addNDCOrders($orders);
        }
        /*  */
        Logger::logOrderImports('order_imports/natural_distilling_company', $this->output); //die();
        //if (php_sapi_name() !='cli')
        if ($_SERVER['HTTP_USER_AGENT'] != '3PLPLUSAGENT')
        {
            return $this->return_array;
        }

    }

    private function addNDCOrders($orders)
    {
        foreach($orders as $o)
        {
            //check for errors first
            $item_error = false;
            $error_string = "";
            foreach($this->naturaldistillingitems[$o['client_order_id']] as $item)
            {
                if($item['item_error'])
                {
                    $item_error = true;
                    $error_string .= $item['item_error_string'];
                }
            }
            if($item_error)
            {
                $message = "<p>There was a problem with some items</p>";
                $message .= $error_string;
                $message .= "<p>Orders with these items will not be processed at the moment</p>";
                $message .= "<p>Order ID: {$o['client_order_id']}</p>";
                $message .= "<p>Customer: {$o['ship_to']}</p>";
                $message .= "<p>Address: {$o['address']}</p>";
                $message .= "<p>{$o['address_2']}</p>";
                $message .= "<p>{$o['suburb']}</p>";
                $message .= "<p>{$o['state']}</p>";
                $message .= "<p>{$o['postcode']}</p>";
                $message .= "<p>{$o['country']}</p>";
                $message .= "<p class='bold'>If you manually enter this order into the WMS, you will need to update its status in woo-commerce, so it does not get imported tomorrow</p>";
                //if (php_sapi_name() !='cli')
                if ($_SERVER['HTTP_USER_AGENT'] != '3PLPLUSAGENT')
                {
                    ++$this->return_array['error_count'];
                    $this->return_array['error_string'] .= $message;
                }
                else
                {
                    Email::sendNDCImportError($message);

                }
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
                'client_id'             => 73,
                'deliver_to'            => $o['ship_to'],
                'date_ordered'          => $o['date_ordered'],
                'tracking_email'        => $o['tracking_email'],
                'delivery_instructions' => $o['delivery_instructions'],
                'errors'                => $o['errors'],
                'error_string'          => $o['error_string'],
                'address'               => $o['address'],
                'address2'              => $o['address_2'],
                'state'                 => $o['state'],
                'suburb'                => $o['suburb'],
                'postcode'              => $o['postcode'],
                'country'               => $o['country'],
                'contact_phone'         => $o['contact_phone']
            );
            if($o['signature_req'] == 1) $vals['signature_req'] = 1;
            if($o['eparcel_express'] == 1) $vals['express_post'] = 1;
            $itp = array($this->naturaldistillingitems[$o['client_order_id']]);
            $order_number = $this->controller->order->addOrder($vals, $itp);
            $this->output .= "Inserted Order: $order_number".PHP_EOL;
            $this->output .= print_r($vals,true).PHP_EOL;
            $this->output .= print_r($this->naturaldistillingitems[$o['client_order_id']], true).PHP_EOL;
            ++$this->return_array['import_count'];
            //update the online shop
            $this->closeNDCOrder($o['squarespace_id']);
        }
    }

    private function closeNDCOrder($ndc_orderid)
    {
        //echo "closeNDCOrder<pre>".$ndc_orderid."</pre>";die();
        $ch = curl_init();

        curl_setopt_array($ch,array(
            CURLOPT_URL => "https://api.squarespace.com/1.0/commerce/orders/".$ndc_orderid."/fulfillments",
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n    \"shouldSendNotification\": false\n}",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer 95f6b0a4-8bd7-456d-b4b3-809ce1e2aec4",
                "cache-control: no-cache",
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($ch);
        $response2 = json_decode($response, true);
        //echo "<pre>The result",print_r($response2['result']),"</pre>"; die();

    }

    private function addTeamTibuktuOrders($orders)
    {
        foreach($orders as $o)
        {
            //check for errors first
            $item_error = false;
            $error_string = "";
            foreach($this->teamtimbuktuoitems[$o['client_order_id']] as $item)
            {
                if($item['item_error'])
                {
                    $item_error = true;
                    $error_string .= $item['item_error_string'];
                }
            }
            if($item_error)
            {
                $message = "<p>There was a problem with some items</p>";
                $message .= $error_string;
                $message .= "<p>Orders with these items will not be processed at the moment</p>";
                $message .= "<p>Order ID: {$o['client_order_id']}</p>";
                $message .= "<p>Customer: {$o['ship_to']}</p>";
                $message .= "<p>Address: {$o['address']}</p>";
                $message .= "<p>{$o['address_2']}</p>";
                $message .= "<p>{$o['suburb']}</p>";
                $message .= "<p>{$o['state']}</p>";
                $message .= "<p>{$o['postcode']}</p>";
                $message .= "<p>{$o['country']}</p>";
                $message .= "<p class='bold'>If you manually enter this order into the WMS, you will need to update its status in woo-commerce, so it does not get imported tomorrow</p>";
                //if (php_sapi_name() !='cli')
                if ($_SERVER['HTTP_USER_AGENT'] != '3PLPLUSAGENT')
                {
                    ++$this->return_array['error_count'];
                    $this->return_array['error_string'] .= $message;
                }
                else
                {
                    //Email::sendBBImportError($message);

                }
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
                'client_id'             => 69,
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
                'contact_phone'         => $o['contact_phone']
            );
            if($o['signature_req'] == 1) $vals['signature_req'] = 1;
            if($o['eparcel_express'] == 1) $vals['express_post'] = 1;
            $itp = array($this->teamtimbuktuoitems[$o['client_order_id']]);
            $order_number = $this->controller->order->addOrder($vals, $itp);
            $this->output .= "Inserted Order: $order_number".PHP_EOL;
            $this->output .= print_r($vals,true).PHP_EOL;
            $this->output .= print_r($this->teamtimbuktuoitems[$o['client_order_id']], true).PHP_EOL;
            ++$this->return_array['import_count'];
        }
    }


    private function procNaturalDistillingOrders($collected_orders)
    {
        //$this->output .= print_r($collected_orders,true).PHP_EOL;
        //echo "procNaturalDistillingOrders<pre>",print_r($collected_orders),"</pre>";die();
        //echo $_SERVER['HTTP_USER_AGENT'];
        $orders = array();
        if(count($collected_orders))
        {
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
                    'client_order_id'       => $o['orderNumber'],
                    'errors'                => 0,
                    'tracking_email'        => $o['customerEmail'],
                    'ship_to'               => $o['shippingAddress']['firstName']." ".$o['shippingAddress']['lastName'],
                    'date_ordered'          => strtotime( $o['createdOn'] ),
                    'status_id'             => $this->controller->order->ordered_id,
                    'eparcel_express'       => 0,
                    'signature_req'         => 0,
                    'contact_phone'         => $o['shippingAddress']['phone'],
                    'import_error'          => false,
                    'import_error_string'   => '',
                    'delivery_instructions'	=>	"Please leave in a safe place out of the weather",
                    'squarespace_id'        => $o['id']
                );
                //echo "procNaturalDistillingOrder<pre>",print_r($order),"</pre>";die();
                //if(isset($o['shipping_lines'][0]) && strtolower($o['shipping_lines'][0]['code']) == "express shipping") $order['eparcel_express'] = 1;
                if( !filter_var($o['customerEmail'], FILTER_VALIDATE_EMAIL) )
                {
                    $order['errors'] = 1;
                    $order['error_string'] = "<p>The customer email is not valid</p>";
                }
                //validate address
                $ad = array(
                    'address'   => $o['shippingAddress']['address1'],
                    'address_2' => $o['shippingAddress']['address2'],
                    'suburb'    => $o['shippingAddress']['city'],
                    'state'     => $o['shippingAddress']['state'],
                    'postcode'  => $o['shippingAddress']['postalCode'],
                    'country'   => $o['shippingAddress']['countryCode']
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
                    if( strlen($order['ship_to']) > 30 )
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
                $order['sort_order'] = ($ad['country'] == "AU")? 2:1;

                $qty = 0;
                foreach($o['lineItems'] as $item)
                {
                    $product = $this->controller->item->getItemBySku($item['sku']);
                    if(!$product)
                    {
                        $items_errors = true;
                        $mm .= "<li>Could not find {$item['productName']} in WMS based on {$item['sku']}</li>";
                    }
                    else
                    {
                        $n_name = $product['name'];
                        $item_id = $product['id'];
                        $items[] = array(
                            'qty'           =>  $item['quantity'],
                            'id'            =>  $item_id,
                            'whole_pallet'  => false
                        );
                        $qty += $item['quantity'];
                        $weight += $product['weight'] * $item['quantity'];
                    }

                }
                //if($qty > 1 || !empty($o['shipping']['company'])) $order['signature_req'] = 1;////////////////////////////////////////

                //echo "<pre>",print_r($order),"</pre>";die();
                if($items_errors)
                {
                    $message = "<p>There was a problem with some items</p>";
                    $message .= "<ul>".$mm."</ul>";
                    $message .= "<p>Orders with these items will not be processed at the moment</p>";
                    $message .= "<p>Client Order ID: {$order['client_order_id']}</p>";
                    $message .= "<p>Customer: {$order['ship_to']}</p>";
                    $message .= "<p>Address: {$ad['address']}</p>";
                    $message .= "<p>{$ad['address_2']}</p>";
                    $message .= "<p>{$ad['suburb']}</p>";
                    $message .= "<p>{$ad['state']}</p>";
                    $message .= "<p>{$ad['postcode']}</p>";
                    $message .= "<p>{$ad['country']}</p>";
                    $message .= "<p class='bold'>If you manually enter this order into the WMS, you will need to update its status in woo-commerce, so it does not get imported tomorrow</p>";
                    //if (php_sapi_name() == 'cli')
                    if ($_SERVER['HTTP_USER_AGENT'] == '3PLPLUSAGENT')
                    {
                        Email::sendNDCImportError($message);
                    }
                    else
                    {
                        $this->return_array['error_string'] .= $message;
                        ++$this->return_array['error_count'];
                    }
                    //echo $message;
                }
                else
                {
                    $order['quantity'] = $qty;
                     //$order['weight'] = $o['total_weight'];
                    $order['items'] = $items;
                    $orders_items[$o['orderNumber']] = $items;
                    $order = array_merge($order, $ad);
                    $orders[] = $order;
                }
            }//endforeach order
            //echo "All orders<pre>",print_r($orders),"</pre>";die();
            $this->naturaldistillingitems = $this->controller->allocations->createOrderItemsArray($orders_items);

            return $orders;
        }//end if count orders
        else
        {
            $this->output .= "=========================================================================================================".PHP_EOL;
            $this->output .= "No New Orders";
            $this->output .= "=========================================================================================================".PHP_EOL;
        }
        //die();
        return false;
    }

}