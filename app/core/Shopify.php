<?php

/**
 * Shopify class.
 *
 * Interacts with the shopify api
 * Individual shopify accounts are handled in the extending classes

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

use PHPShopify\Exception\CurlException;

class Shopify{

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
    //protected $shopify;

    public $controller;
    public $shop_name;

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
    }

    public function getOrders(){}

    public function fulfillAnOrder($order_id, $consignment_id, $tracking_url, $items){}

    public function resetConfig($config)
    {
        return PHPShopify\ShopifySDK::config($config);
    }

    protected function procOrders($collected_orders)
    {
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
                    'client_order_id'       => $o['order_number'],
                    'errors'                => 0,
                    'tracking_email'        => $o['email'],
                    'ship_to'               => $o['shipping_address']['first_name']." ".$o['shipping_address']['last_name'],
                    'company_name'          => $o['shipping_address']['company'],
                    'date_ordered'          => strtotime( $o['created_at'] ),
                    'status_id'             => $this->controller->order->ordered_id,
                    'eparcel_express'       => 0,
                    'signature_req'         => 0,
                    'contact_phone'         => $o['shipping_address']['phone'],
                    'items_errors'          => false,
                    'items_errors_string'   => '<ul>',
                    'is_shopify'            => 1,
                    'shopify_id'            => $o['id']
                );
                //if(strtolower($o['shipping_lines'][0]['code']) == "express shipping") $order['eparcel_express'] = 1;
                if(isset($o['shipping_lines'][0]) && strtolower($o['shipping_lines'][0]['code']) == "express shipping") $order['eparcel_express'] = 1;
                if( isset($o['pickup']) )
                    $order['pickup'] = 1;
                if( !filter_var($o['email'], FILTER_VALIDATE_EMAIL) )
                {
                    $order['errors'] = 1;
                    $order['error_string'] = "<p>The customer email is not valid</p>";
                }
                if(isset($o['tags']))
                    $order['shopify_tags'] = $o['tags'];
                //validate address
                $ad = array(
                    'address'   => $o['shipping_address']['address1'],
                    'address_2' => $o['shipping_address']['address2'],
                    'suburb'    => $o['shipping_address']['city'],
                    'state'     => $o['shipping_address']['province_code'],
                    'postcode'  => $o['shipping_address']['zip'],
                    'country'   => $o['shipping_address']['country_code']
                );
                if($ad['country'] == "AU")
                {
                    if(strlen($ad['address']) > 40 || strlen($ad['address_2']) > 40 || strlen($order['company_name'])  > 40)
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
                ///$order['sort_order'] = ($ad['country'] == "AU")? 2:1;
                $qty = 0;
                foreach($o['line_items'] as $item)
                {
                    if($item['fulfillable_quantity'] < 1)
                        continue;
                    $product = $this->controller->item->getItemBySku($item['sku']);
                    if(!$product)
                    {
                        $order['items_errors'] = true;
                        $order['items_errors_string'] .= "<li>Could not find {$item['name']} in WMS based on {$item['sku']}</li>";
                    }
                    else
                    {
                        $n_name = $product['name'];
                        $item_id = $product['id'];
                        $items[] = array(
                            'qty'                   => $item['quantity'],
                            'id'                    => $item_id,
                            'shopify_line_item_id'  => $item['id'],
                            'whole_pallet'          => false
                        );
                        $qty += $item['quantity'];
                        $weight += $product['weight'] * $item['quantity'];
                    }

                }
                if($qty > 1 || !empty($o['shipping']['company'])) $order['signature_req'] = 1;////////////////////////////////////////
                if(empty($o['note']))
                {
                    if( $qty > 1 || !empty($o['shipping_address']['company']) )
                        $delivery_instructions =  "";
                    else
                        $delivery_instructions =  "Please leave in a safe place out of the weather";
                }
                else
                {
                    $delivery_instructions = $o['note'];
                }
                $order['instructions'] = $delivery_instructions;
                $order['items_errors_string'] .= "</ul>";
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
                    if ($this->ua == "CRON" && SITE_LIVE )
                    {
                        Email::sendPBAImportError($message);
                        $this->return_array['error_string'] .= $message;
                        ++$this->return_array['error_count'];
                        $this->return_array['error_orders'][] = $order['client_order_id'];
                    }
                    else
                    {
                        $this->return_array['error_string'] .= $message;
                        ++$this->return_array['error_count'];
                        $this->return_array['error_orders'][] = $order['client_order_id'];
                    }
                }
                else
                {
                    $order['quantity'] = $qty;
                    $order['weight'] = $o['total_weight'];
                    //if($qty > 1 || !empty($o['shipping']['company'])) $order['signature_req'] = 1;
                    $order['items'][$o['order_number']] = $items;
                    $orders_items[$o['order_number']] = $items;
                    $order = array_merge($order, $ad);
                    $orders[] = $order;
                }
            }//endforeach order
            $orders['orders_items'] = $orders_items;
            $this->output .= "===========================   Gonna send em back  =========================".PHP_EOL;
            return $orders;
        }//end if count orders
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

    protected function filterForAlreadyCollected($collected_orders)
    {
        $filtered_orders = array();
        $this->output .= "==============================================Filtering for already sent============================================".PHP_EOL;
        foreach($collected_orders as $co)
        {
            if(strpos($co['tags'], "sent_to_fsg") === false)
                $filtered_orders[] = $co;
            else
                $this->output .= "Removing ".$co['id']." cos we already have it".PHP_EOL;
        }
        $this->output .= "=========================================================================================================".PHP_EOL;
        return $filtered_orders;
    }

    protected function addTag($order_id, $new_tag){}

}