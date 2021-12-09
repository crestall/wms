<?php
/**
 * Marketplacer class.
 *
 * Interacts with the marketplacer api
 * Individual marketplacer accounts are handled in the extending classes

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class Marketplacer{

    protected $output;
    protected $return_array = array(
        'import_count'          => 0,
        'imported_orders'       => array(),
        'error_orders'          => array(),
        'import_error'          => false,
        'error'                 => false,
        'error_count'           => 0,
        'error_string'          => '',
        'import_error_string'   => ''
    );
    protected $ua;
    protected $order_items;

    public $controller;

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
    }

    public function getOrders(){}

    public function fulfillAnOrder($invoice_id, $consignment_id, $carrier){}

    protected function procOrders($collected_orders)
    {
        //return $collected_orders;
        $orders = array();
        if(count($collected_orders))
        {
            $allocations = array();
            $orders_items = array();
            foreach($collected_orders as $i => $o)
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
                    'client_order_id'       => $o['id'],
                    'errors'                => 0,
                    'tracking_email'        => $o['relationships']['customer']['data']['email_address'],
                    'ship_to'               => $o['relationships']['customer']['data']['first_name']." ".$o['relationships']['customer']['data']['surname'],
                    'company_name'          => $o['relationships']['customer']['data']['company_name'],
                    'date_ordered'          => strtotime( $o['attributes']['paid_at'] ),
                    'status_id'             => $this->controller->order->ordered_id,
                    'eparcel_express'       => 0,
                    'signature_req'         => 0,
                    'contact_phone'         => $o['relationships']['customer']['data']['phone'],
                    'items_errors'          => false,
                    'items_errors_string'   => '<ul>',
                    'is_marketplacer'       => 1,
                    'marketplacer_id'       => $o['id']
                );
                if( !filter_var( $o['relationships']['customer']['data']['email_address'], FILTER_VALIDATE_EMAIL) )
                {
                    $order['errors'] = 1;
                    $order['error_string'] = "<p>The customer email is not valid</p>";
                }
                //validate address
                $fa = trim($o['relationships']['customer']['data']['address']);
                //echo "<p>Before substitution: ".$fa."</p>";
                $re = '/\b([A_Z] ?)+\b, /';
                $replacement = '';
                $address_line = preg_replace('/\b[A-Z\w\s]+\b,\W*/', '', $fa);
                //echo "<p>After substitution: ".$address_line."</p>";die();
                $ad = array(
                    'address'   => $address_line,
                    'suburb'    => $o['relationships']['customer']['data']['city'],
                    'state'     => $o['relationships']['customer']['data']['state'],
                    'postcode'  => $o['relationships']['customer']['data']['postcode'],
                    'country'   => "AU"
                );
                                if($ad['country'] == "AU")
                {
                    if(strlen($ad['address']) > 40 ||  strlen($order['company_name'])  > 40)
                    {
                        $order['errors'] = 1;
                        $order['error_string'] .= "<p>Addresses cannot have more than 40 characters</p>";
                    }
                    $aResponse = $this->controller->Eparcel->ValidateSuburb($ad['suburb'], $ad['state'], str_pad($ad['postcode'],4,'0',STR_PAD_LEFT));

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
                    if( strlen( $ad['address'] ) > 50  )
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
                foreach($o['relationships']['line_items']['data'] as $ind => $item)
                {
                    $product = $this->controller->item->getItemBySku($item['attributes']['variant_sku']);
                    if(!$product)
                    {
                        $order['items_errors'] = true;
                        $order['items_errors_string'] .= "<li>Could not find {$item['attributes']['advert_name']} in WMS based on {$item['attributes']['variant_sku']}</li>";
                    }
                    else
                    {
                        $n_name = $product['name'];
                        $item_id = $product['id'];
                        $items[] = array(
                            'qty'                   => $item['attributes']['quantity'],
                            'id'                    => $item_id,
                            'marketplacer_line_item_id'  => $item['id'],
                            'whole_pallet'          => false
                        );
                        $qty += $item['attributes']['quantity'];
                        $weight += $product['weight'] * $item['attributes']['quantity'];
                    }

                }
                if( $qty > 1 || !empty($o['relationships']['customer']['data']['company_name'])) $order['signature_req'] = 1;////////////////////////////////////////
                if( $qty > 1 || !empty($o['relationships']['customer']['data']['company_name']) )
                    $delivery_instructions =  "";
                else
                    $delivery_instructions =  "Please leave in a safe place out of the weather";
                $order['instructions'] = $delivery_instructions;
                $order['items_errors_string'] .= "</ul>";
                $order['quantity'] = $qty;
                $order['weight'] = $weight;
                $order['items'][$o['id']] = $items;
                $orders_items[$o['id']] = $items;
                $order = array_merge($order, $ad);
                $orders[] = $order;
            }
            //echo "<pre>",print_r($orders),"</pre>";die();
            $orders['orders_items'] = $orders_items;
            return $orders;
        }
        else
        {
            $this->output .= "=========================================================================================================".PHP_EOL;
            $this->output .= "No New Orders";
            $this->output .= "=========================================================================================================".PHP_EOL;
        }
        return false;
    }

    protected function sendItemErrorEmail($args)
    {
        $defaults = array(
            'import_error'  => false,
            'import_error_string'   => '',
            'item_error'            => false,
            'item_error_string'     => '',
            'items_errors'          => false,
            'items_errors_string'   => '',
            'email_function'        => false
        );
        $args = array_merge($defaults, $args);
        //echo "<pre>",print_r($args),"</pre>";die();
        extract($args);
        if( !$email_function )
            return;
        $message = "<p>There was a problem with some items</p>";
        if($import_error)
            $message .= $import_error_string;
        if($item_error)
            $message .= $item_error_string;
        if($items_errors)
            $message .= $items_errors_string;
        $message .= "<p>Orders with these items will not be processed at the moment</p>";
        $message .= "<p>Order ID: {$od['client_order_id']}</p>";
        $message .= "<p>Customer: {$od['ship_to']}</p>";
        $message .= "<p>Address: {$od['address']}</p>";
        $message .= "<p>{$od['address_2']}</p>";
        $message .= "<p>{$od['suburb']}</p>";
        $message .= "<p>{$od['state']}</p>";
        $message .= "<p>{$od['postcode']}</p>";
        $message .= "<p>{$od['country']}</p>";

        //echo "<pre>",print_r($args),"</pre>";
        //echo "<p>$message</p>";
        //die();
        if(isset($send_no_message))
           return $message;
        Email::{$email_function}($message);
        return true;
    }

    protected function sendGetRequest($endpoint,$options)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $options['ShopUrl'].$endpoint);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'MARKETPLACER-API-KEY: '.$options['ApiKey'],
            'Authorization: Basic '. base64_encode($options['Username'].":".$options['Password']))
        );
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    protected function sendPostRequest($endpoint,$options)
    {
        $post_options = [
            CURLOPT_URL               => $options['ShopUrl'].$endpoint,
            CURLOPT_CUSTOMREQUEST    => 'POST',
            CURLOPT_HTTPHEADER        => array(
                'MARKETPLACER-API-KEY: '.$options['ApiKey'],
                'Authorization: Basic '. base64_encode($options['Username'].":".$options['Password'])
            )
        ];
        $c_options = array_merge($this->curl_options, $post_options);
        $curl = curl_init();
        curl_setopt_array($curl, $c_options);
        $response = curl_exec($curl);
        curl_close($curl);
        var_dump($response);
        die();
    }

    protected function sendPutRequest($endpoint,$options,$data)
    {
        $data_string = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $options['ShopUrl'].$endpoint);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string),
            'MARKETPLACER-API-KEY: '.$options['ApiKey'],
            'Authorization: Basic '. base64_encode($options['Username'].":".$options['Password']))
        );
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}// end class
?>