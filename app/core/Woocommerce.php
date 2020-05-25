<?php

/**
 * Woocommnerce class.
 *
 * Interacts with the woocommerce api

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */
use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;

class Woocommerce{

    private $output;
    private $bboitems;
    private $ttoitems;
    private $nuchevoitems;
    private $noaoitems;
    private $woocommerce;
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

    public function getTTOrders()
    {
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "TTAust ORDER IMPORTING FOR ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        $this->woocommerce = new Client(
            'https://foreversharpknives.com.au',
            Config::get('TTWOOCONSUMERRKEY'),
            Config::get('TTWOOCONSUMERSECRET'),
            [
                'wp_api' => true,
                'version' => 'wc/v1',
                'query_string_auth' => true
            ]
        );
        $collected_orders = array();
        try {
            $page = 1;
            $next_page = $this->woocommerce->get('orders', array('status' => 'processing', 'orderby' => 'date', 'per_page' => 100, 'page' => $page));
            //$next_page = $this->woocommerce->get('orders/100595');
            $collected_orders = $next_page;
            while(count($next_page))
            {
                ++$page;
                $next_page = $this->woocommerce->get('orders', array('status' => 'processing', 'orderby' => 'date', 'per_page' => 100, 'page' => $page));
                $collected_orders = array_merge($collected_orders, $next_page);
            }
            //$collected_orders = $this->woocommerce->get('orders', array('status' => 'processing', 'orderby' => 'date', 'per_page' => 100));
        } catch (HttpClientException $e) {
            $this->output .=  $e->getMessage() .PHP_EOL;
            //$output .=  $e->getRequest() .PHP_EOL;
            $this->output .=  print_r($e->getResponse(), true) .PHP_EOL;
            if ($_SERVER['HTTP_USER_AGENT'] == '3PLPLUSAGENT')
            {
                Email::sendCronError($e, "Twin Towers Australia");
                return;
            }
            else
            {
                $this->return_array['import_error'] = true;
                $this->return_array['import_error_string'] .= print_r($e->getMessage(), true);
                return $this->return_array;
            }
        }
        //echo "<pre>",print_r($collected_orders),"</pre>";die();
        /* */
        if($orders = $this->procTTOrders($collected_orders))
        {
            //echo "<pre>",print_r($this->ttoitems),"</pre>";die();
            $this->addTTOrders($orders);
        }
        Logger::logOrderImports('order_imports/tt_aust', $this->output); //die();
        //if (php_sapi_name() !='cli')
        if ($_SERVER['HTTP_USER_AGENT'] != '3PLPLUSAGENT')
        {
            return $this->return_array;
        }

    }

    public function getBBOrder($wcorder_id = false)
    {
        if(!$wcorder_id)
        {
            return false;
        }
        $return_array = array(
            'error'                 =>  false,
            'response_string'       =>  '',
            'import_error'          =>  false,
            'import_error_string'   =>  ''
        );
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "IMPORTING SINGLE BIG BOTTLE ORDER ON ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        $this->woocommerce = new Client(
            'https://www.thebigbottleco.com',
            Config::get('BBWOOCONSUMERRKEY'),
            Config::get('BBWOOCONSUMERSECRET'),
            [
                'wp_api' => true,
                'version' => 'wc/v1',
                'query_string_auth' => true
            ]
        );
        $collected_orders = array();
        try {
            $page = 1;
            //$next_page = $this->woocommerce->get('orders', array('status' => 'processing', 'orderby' => 'date', 'per_page' => 100, 'page' => $page));
            $next_page = $this->woocommerce->get('orders/'.$wcorder_id);
            $collected_orders[] = $next_page;
            /*
            while(count($next_page))
            {
                ++$page;
                //$next_page = $this->woocommerce->get('orders', array('status' => 'processing', 'orderby' => 'date', 'per_page' => 100, 'page' => $page));
                $next_page = $this->woocommerce->get('orders/'.$wcorder_id);
                $collected_orders = array_merge($collected_orders, $next_page);
            }
            */
            //$collected_orders = $this->woocommerce->get('orders', array('status' => 'processing', 'orderby' => 'date', 'per_page' => 100));
        } catch (HttpClientException $e) {
            $this->output .=  $e->getMessage() .PHP_EOL;
            //$output .=  $e->getRequest() .PHP_EOL;
            $this->output .=  print_r($e->getResponse(), true) .PHP_EOL;
            if ($_SERVER['HTTP_USER_AGENT'] == '3PLPLUSAGENT')
            {
                Email::sendCronError($e, "The Big Bottle");
                return;
            }
            else
            {
                $this->return_array['import_error'] = true;
                $this->return_array['import_error_string'] .= print_r($e->getMessage(), true);
                return $this->return_array;
            }

        }
        //echo "<pre>",print_r($collected_orders),"</pre>";die();
        $orders = $this->procBBOrders($collected_orders);
        if(count($orders))
        {
            $this->addBBOrders($orders);
        }
        elseif($orders === false)
        {
            $this->return_array['error'] = true;
        }
        return $this->return_array;
    }

    public function getNuchevOrder($wcorder_id = false)
    {
        if(!$wcorder_id)
        {
            return false;
        }
        $return_array = array(
            'error'                 =>  false,
            'response_string'       =>  '',
            'import_error'          =>  false,
            'import_error_string'   =>  ''
        );
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "IMPORTING SINGLE NUCHEV ORDER ON ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        $this->woocommerce = new Client(
            'https://www.oli6.com',
            Config::get('NUWOOCONSUMERRKEY'),
            Config::get('NUWOOCONSUMERSECRET'),
            [
                'version' => 'v3', // WooCommerce API version
                'wp_api' => false,
                'query_string_auth' => true
            ]
        );
        $collected_orders = array();
        try {
            $next_page = $this->woocommerce->get('orders/'.$wcorder_id);
            $collected_orders = $next_page;
        } catch (HttpClientException $e) {
            $this->output .=  $e->getMessage() .PHP_EOL;
            //$output .=  $e->getRequest() .PHP_EOL;
            $this->output .=  print_r($e->getResponse(), true) .PHP_EOL;
            if ($_SERVER['HTTP_USER_AGENT'] == '3PLPLUSAGENT')
            {
                Email::sendCronError($e, "Nuchev");
                return;
            }
            else
            {
                $this->return_array['import_error'] = true;
                $this->return_array['import_error_string'] .= print_r($e->getMessage(), true);
                return $this->return_array;
            }
        }
        //echo "<pre>",print_r($collected_orders),"</pre>";die();
        if($orders = $this->procNuchevOrders($collected_orders))
        {
            $this->addNuchevOrders($orders);
        }
        else
        {
            $this->return_array['error'] = true;
        }
        return $this->return_array;
    }

    public function getNoaOrder($wcorder_id = false)
    {
        if(!$wcorder_id)
        {
            return false;
        }
        $return_array = array(
            'error'                 =>  false,
            'response_string'       =>  '',
            'import_error'          =>  false,
            'import_error_string'   =>  ''
        );
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "IMPORTING SINGLE NOA SLEEP ORDER ON ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        $this->woocommerce = new Client(
            'https://www.noahome.com/au-en',
            Config::get('NSWOOCONSUMERRKEY'),
            Config::get('NSWOOCONSUMERSECRET'),
            [
                'wp_api' => true,
                'version' => 'wc/v2',
                'query_string_auth' => true
            ]
        );
        $collected_orders = array();
        try {
            $collected_orders = $this->woocommerce->get('orders/'.$wcorder_id);
        } catch (HttpClientException $e) {
            $this->output .= "There has been an error".PHP_EOL;
            $this->output .=  $e->getMessage() .PHP_EOL;
            $this->output = "-------------------------------".PHP_EOL;
            $this->output .=  print_r($e->getRequest(), true) .PHP_EOL;
            $this->output = "-------------------------------".PHP_EOL;
            $this->output .=  print_r($e->getResponse(), true) .PHP_EOL;
            $this->output .= "-------------------------------".PHP_EOL;
            $this->output .= "Full output".PHP_EOL;
            $this->output .=  print_r($e, true) .PHP_EOL;
            if ($_SERVER['HTTP_USER_AGENT'] == '3PLPLUSAGENT')
            {
                Email::sendCronError($e, "NOA");
                return;
            }
            else
            {
                $this->return_array['import_error'] = true;
                $this->return_array['import_error_string'] .= print_r($e->getMessage(), true);
                return $this->return_array;
            }
        }

        //echo "<pre>",print_r($collected_orders),"</pre>";die();
        if($orders = $this->procNoaOrders($collected_orders))
        {
            $this->addNoaOrders($orders);
        }
        else
        {
            $this->return_array['error'] = true;
        }
        return $this->return_array;
    }

    public function getBBOrders()
    {
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "BIG BOTTLE ORDER IMPORTING FOR ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        $this->woocommerce = new Client(
            'https://www.thebigbottleco.com',
            Config::get('BBWOOCONSUMERRKEY'),
            Config::get('BBWOOCONSUMERSECRET'),
            [
                'wp_api' => true,
                'version' => 'wc/v1',
                'query_string_auth' => true
            ]
        );
        $collected_orders = array();
        try {
            $page = 1;
            $next_page = $this->woocommerce->get('orders', array('status' => 'processing', 'orderby' => 'date', 'per_page' => 100, 'page' => $page));
            //$next_page = $this->woocommerce->get('orders/100595');
            $collected_orders = $next_page;
            while(count($next_page))
            {
                ++$page;
                $next_page = $this->woocommerce->get('orders', array('status' => 'processing', 'orderby' => 'date', 'per_page' => 100, 'page' => $page));
                $collected_orders = array_merge($collected_orders, $next_page);
            }
            //$collected_orders = $this->woocommerce->get('orders', array('status' => 'processing', 'orderby' => 'date', 'per_page' => 100));
        } catch (HttpClientException $e) {
            $this->output .=  $e->getMessage() .PHP_EOL;
            //$output .=  $e->getRequest() .PHP_EOL;
            $this->output .=  print_r($e->getResponse(), true) .PHP_EOL;
            if ($_SERVER['HTTP_USER_AGENT'] == '3PLPLUSAGENT')
            {
                Email::sendCronError($e, "Big Bottle");
                return;
            }
            else
            {
                $this->return_array['import_error'] = true;
                $this->return_array['import_error_string'] .= print_r($e->getMessage(), true);
                return $this->return_array;
            }
        }
        if($orders = $this->procBBOrders($collected_orders))
        {
            //echo "<pre>",print_r($this->bboitems),"</pre>";die();
            $this->addBBOrders($orders);
        }
        Logger::logOrderImports('order_imports/big_bottle', $this->output); //die();
        //if (php_sapi_name() !='cli')
        if ($_SERVER['HTTP_USER_AGENT'] != '3PLPLUSAGENT')
        {
            return $this->return_array;
        }
    }

    public function getOnePlateOrders()
    {
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "ONE PLATE ORDER IMPORTING FOR ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        $this->woocommerce = new Client(
            'https://www.oneplate.co',
            Config::get('ONEPLATEWOOCONSUMERRKEY'),
            Config::get('ONEPLATEWOOCONSUMERSECRET'),
            [
                'wp_api' => true,
                'version' => 'wc/v2',
                'query_string_auth' => true
            ]
        );
        $collected_orders = array();
        try {
            $page = 1;
            $next_page = $this->woocommerce->get('orders', array('status' => 'processing', 'orderby' => 'date', 'order' => 'asc', 'per_page' => 100, 'page' => $page));
            $collected_orders = $next_page;
            /*
            while(count($next_page))
            {
                ++$page;
                $next_page = $this->woocommerce->get('orders', array('status' => 'processing', 'orderby' => 'date', 'per_page' => 100, 'page' => $page));
                $collected_orders = array_merge($collected_orders, $next_page);
            }
            */
            //$collected_orders = $this->woocommerce->get('orders', array('status' => 'processing', 'orderby' => 'date', 'per_page' => 100));
        } catch (HttpClientException $e) {
            $this->output .= "There has been an error".PHP_EOL;
            $this->output .=  $e->getMessage() .PHP_EOL;
            $this->output = "-------------------------------".PHP_EOL;
            $this->output .=  print_r($e->getRequest(), true) .PHP_EOL;
            $this->output = "-------------------------------".PHP_EOL;
            $this->output .=  print_r($e->getResponse(), true) .PHP_EOL;
            $this->output .= "-------------------------------".PHP_EOL;
            $this->output .= "Full output".PHP_EOL;
            $this->output .=  print_r($e, true) .PHP_EOL;
            if ($_SERVER['HTTP_USER_AGENT'] == '3PLPLUSAGENT')
            {
                Email::sendCronError($e, "NOA");
                return;
            }
            else
            {
                $this->return_array['import_error'] = true;
                $this->return_array['import_error_string'] .= print_r($e->getMessage(), true);
                return $this->return_array;
            }
        }
        //echo "<pre>",print_r($collected_orders),"</pre>";die();
        if($orders = $this->procOnePlateOrders($collected_orders))
        {
            //echo "<pre>",print_r($orders),"</pre>";die();
            $this->addNoaOrders($orders);
        }
        Logger::logOrderImports('order_imports/noa', $this->output); //die();
        //if (php_sapi_name() !='cli')
        if ($_SERVER['HTTP_USER_AGENT'] != '3PLPLUSAGENT')
        {
            return $this->return_array;
        }
    }

    public function getNuchevOrders()
    {
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "NUCHEV ORDER IMPORTING FOR ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        $this->woocommerce = new Client(
            'https://www.oli6.com',
            Config::get('NUWOOCONSUMERRKEY'),
            Config::get('NUWOOCONSUMERSECRET'),
            [
                'version' => 'v3', // WooCommerce API version
                'wp_api' => false,
                'query_string_auth' => true
            ]
        );
        $collected_orders = array();
        try {
            $page = 1;
            $next_page = $this->woocommerce->get('orders', array('status' => 'processing', 'orderby' => 'date', 'order' => 'asc', 'per_page' => 100, 'page' => $page));
            //$next_page = $this->woocommerce->get('orders/100595');
            $collected_orders = $next_page;
            /*
            ++$page;
            while( count($next_page) )
            {
                ++$page;
                //$next_page = $this->woocommerce->get('orders', array('status' => 'processing', 'orderby' => 'date', 'per_page' => 100, 'page' => $page));
                $next_page = $this->woocommerce->get('orders', array('status' => 'processing', 'filter' => array('orderby' => 'date', 'limit' => 100, 'offset' => $page)));
                $collected_orders = array_merge($collected_orders, $next_page);
            }
            */
            //$collected_orders = $this->woocommerce->get('orders', array('status' => 'processing', 'orderby' => 'date', 'per_page' => 100));
        } catch (HttpClientException $e) {
            $this->output .=  $e->getMessage() .PHP_EOL;
            if ($_SERVER['HTTP_USER_AGENT'] == '3PLPLUSAGENT')
            {
                Email::sendCronError($e, "Nuchev");
                return;
            }
            else
            {
                $this->return_array['import_error'] = true;
                $this->return_array['import_error_string'] .= print_r($e->getMessage(), true);
                return $this->return_array;
            }
        }
        //echo "<pre>",print_r($collected_orders),"</pre>";die();
        if($orders = $this->procNuchevOrders($collected_orders['orders']))
        {
            //echo "<pre>",print_r($orders),"</pre>";
            //echo "<pre>",print_r($this->nuchevoitems),"</pre>";die();
            $this->addNuchevOrders($orders);
        }
        Logger::logOrderImports('order_imports/nuchev', $this->output); //die();
        //if (php_sapi_name() !='cli')
        if ($_SERVER['HTTP_USER_AGENT'] != '3PLPLUSAGENT')
        {
            return $this->return_array;
        }
    }

    private function addNuchevOrders($orders)
    {
        foreach($orders as $o)
        {
            //check for errors first
            $item_error = false;
            $error_string = "";
            foreach($this->nuchevoitems[$o['client_order_id']] as $item)
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
                $message .= "<p>Nuchev Order ID: {$o['client_order_id']}</p>";
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
                    Email::sendNuchevImportError($message);

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
                'client_id'             => 5,
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
            if($o['eparcel_express'] == 1) $vals['eparcel_express'] = 1;
            $itp = array($this->nuchevoitems[$o['client_order_id']]);
            $order_number = $this->controller->order->addOrder($vals, $itp);
            $this->output .= "Inserted Order: $order_number".PHP_EOL;
            $this->output .= print_r($vals,true).PHP_EOL;
            $this->output .= print_r($this->nuchevoitems[$o['client_order_id']], true).PHP_EOL;
            ++$this->return_array['import_count'];
            $this->output .= "Updating woocommerce status to completed fo order id ".$o['client_order_id'].PHP_EOL;
            try{
                //$this->woocommerce->put('orders/'.$o['client_order_id'], array('status' => 'completed'));   ancient versions of woocommerce and wordpress in use here
                $this->woocommerce->put('orders/'.$o['client_order_id'], array( 'order' => array('status' => 'completed')));
            }
            catch (HttpClientException $e) {
                $this->output .=  $e->getMessage() .PHP_EOL;
                //$output .=  $e->getRequest() .PHP_EOL;
                $this->output .=  print_r($e->getResponse(), true) .PHP_EOL;
            }

        }
    }

    private function addNoaOrders($orders)
    {
        foreach($orders as $o)
        {
            //check for errors first
            $item_error = false;
            $error_string = "";
            foreach($this->noaoitems[$o['client_order_id']] as $item)
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
                $message .= "<p>NOA Order ID: {$o['client_order_id']}</p>";
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
                    Email::sendNoaImportError($message);

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
                'client_id'             => 59,
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
            if($o['eparcel_express'] == 1) $vals['eparcel_express'] = 1;
            $itp = array($this->noaoitems[$o['client_order_id']]);
            $order_number = $this->controller->order->addOrder($vals, $itp);
            $this->output .= "Inserted Order: $order_number".PHP_EOL;
            $this->output .= print_r($vals,true).PHP_EOL;
            $this->output .= print_r($this->noaoitems[$o['client_order_id']], true).PHP_EOL;
            ++$this->return_array['import_count'];
            $this->output .= "Updating woocommerce status to completed fo order id ".$o['client_order_id'].PHP_EOL;
            try{
                $this->woocommerce->put('orders/'.$o['client_order_id'], array('status' => 'completed'));
            }
            catch (HttpClientException $e) {
                $this->output .=  $e->getMessage() .PHP_EOL;
                //$output .=  $e->getRequest() .PHP_EOL;
                $this->output .=  print_r($e->getResponse(), true) .PHP_EOL;
            }

        }
    }

    private function addBBOrders($orders)
    {
        foreach($orders as $o)
        {
            //check for errors first
            $item_error = false;
            $error_string = "";
            foreach($this->bboitems[$o['client_order_id']] as $item)
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
                $message .= "<p>BB Order ID: {$o['client_order_id']}</p>";
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
                    Email::sendBBImportError($message);

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
                'client_id'             => 6,
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
            if(isset($o['satchels'])) $vals['satchels'] = $o['satchels'];
            $itp = array($this->bboitems[$o['client_order_id']]);
            $order_number = $this->controller->order->addOrder($vals, $itp);
            $this->output .= "Inserted Order: $order_number".PHP_EOL;
            $this->output .= print_r($vals,true).PHP_EOL;
            $this->output .= print_r($this->bboitems[$o['client_order_id']], true).PHP_EOL;
            ++$this->return_array['import_count'];
             /*change status in woocommerce */
            $this->output .= "Updating woocommerce status to completed fo order id ".$o['client_order_id'].PHP_EOL;
            try{
                $this->woocommerce->put('orders/'.$o['client_order_id'], array('status' => 'completed'));
            }
            catch (HttpClientException $e) {
                $this->output .=  $e->getMessage() .PHP_EOL;
                //$output .=  $e->getRequest() .PHP_EOL;
                $this->output .=  print_r($e->getResponse(), true) .PHP_EOL;
            }
        }
    }

    private function addTTOrders($orders)
    {
        foreach($orders as $o)
        {
            //check for errors first
            $item_error = false;
            $error_string = "";
            foreach($this->ttoitems[$o['client_order_id']] as $item)
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
                    Email::sendBBImportError($message);

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
                'client_id'             => 66,
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
            $itp = array($this->ttoitems[$o['client_order_id']]);
            $order_number = $this->controller->order->addOrder($vals, $itp);
            $this->output .= "Inserted Order: $order_number".PHP_EOL;
            $this->output .= print_r($vals,true).PHP_EOL;
            $this->output .= print_r($this->ttoitems[$o['client_order_id']], true).PHP_EOL;
            ++$this->return_array['import_count'];
             /*change status in woocommerce */
            $this->output .= "Updating woocommerce status to completed fo order id ".$o['client_order_id'].PHP_EOL;
            try{
                $this->woocommerce->put('orders/'.$o['client_order_id'], array('status' => 'completed'));
            }
            catch (HttpClientException $e) {
                $this->output .=  $e->getMessage() .PHP_EOL;
                //$output .=  $e->getRequest() .PHP_EOL;
                $this->output .=  print_r($e->getResponse(), true) .PHP_EOL;
            }
        }
    }

    private function procOnePlateOrders($the_orders)
    {
        //$this->output .= print_r($collected_orders,true).PHP_EOL;
        //echo "<pre>",print_r($the_orders),"</pre>";die();
		if(count($the_orders) == 0)
			return false;
        $orders = array();
        if(!isset($the_orders[0]))
            $collected_orders[] = $the_orders;
        else
            $collected_orders = $the_orders;
		
		//echo "<pre>",print_r($the_orders),"</pre>";die();
        if(count($collected_orders) > 0)
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
                    'client_order_id'       => $o['id'],
                    'errors'                => 0,
                    'tracking_email'        => $o['billing']['email'],
                    'ship_to'               => $o['shipping']['first_name']." ".$o['shipping']['last_name'],
                    'company_name'          => $o['shipping']['company'],
                    'date_ordered'          => strtotime( $o['date_created'] ),
                    'status_id'             => $this->controller->order->ordered_id,
                    'eparcel_express'       => 0,
                    'signature_req'         => 0,
                    'contact_phone'         => $o['billing']['phone'],
                    'import_error'          => false,
                    'import_error_string'   => ''
                );
                //if(strtolower($o['shipping_lines'][0]['method_title']) == "express shipping") $order['eparcel_express'] = 1;
                if( !filter_var($o['billing']['email'], FILTER_VALIDATE_EMAIL) )
                {
                    $order['errors'] = 1;
                    $order['error_string'] = "<p>The customer email is not valid</p>";
                }
                //validate address
                $ad = array(
                    'address'   => $o['shipping']['address_1'],
                    'address_2' => $o['shipping']['address_2'],
                    'suburb'    => $o['shipping']['city'],
                    'state'     => $o['shipping']['state'],
                    'postcode'  => $o['shipping']['postcode'],
                    'country'   => $o['shipping']['country']
                );
                if($ad['country'] == "AU")
                {
                    if(strlen($ad['address']) > 40 || strlen($ad['address_2']) > 40 || strlen($order['company_name'])  > 40)
                    {
                        $order['errors'] = 1;
                        $order['error_string'] .= "<p>Addresses cannot have more than 40 characters</p>";
                    }
                    $aResponse = $this->controller->BigBottleEparcel->ValidateSuburb($ad['suburb'], $ad['state'], str_pad($ad['postcode'],4,'0',STR_PAD_LEFT));

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
                $order['sort_order'] = ($ad['country'] == "AU")? 2:1;
                $qty = 0;
                foreach($o['line_items'] as $item)
                {

                        $product = $this->controller->item->getItemBySku($item['sku']);
                        if(!$product)
                        {
                            $items_errors = true;
                            $mm .= "<li>Could not find {$item['name']} in WMS based on {$item['sku']}</li>";
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
                if(empty($o['customer_note']))
                {
                    $delivery_instructions =  "Please leave in a safe place out of the weather";
                }
                else
                {
                    $delivery_instructions = $o['customer_note'];
                }
                $order['instructions'] = $delivery_instructions;
                //echo "<pre>",print_r($order),"</pre>";die();
                if($items_errors)
                {
                    $message = "<p>There was a problem with some items</p>";
                    $message .= "<ul>".$mm."</ul>";
                    $message .= "<p>Orders with these items will not be processed at the moment</p>";
                    $message .= "<p>BB Order ID: {$order['client_order_id']}</p>";
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
                        //Email::sendNoaImportError($message);
                    }
                    else
                    {
                        $this->return_array['error_string'] .= $message;
                        ++$this->return_array['error_count'];
                    }
                }
                else
                {
                    $order['quantity'] = $qty;
                    //$order['weight'] = Config::get('BBBOX_WEIGHTS')[$qty];
                    $order['weight'] = $weight;
                    if($qty > 1 || !empty($o['shipping']['company'])) $order['signature_req'] = 1;
                    $order['items'] = $items;
                    $orders_items[$o['id']] = $items;
                    $order = array_merge($order, $ad);
                    $orders[] = $order;
                }
            }//endforeach order
            echo "<pre>",print_r($orders),"</pre>";die();
            $this->oneplateoitems = $this->controller->allocations->createOrderItemsArray($orders_items);

            return $orders;
        }//end if count orders
        else
        {
            $this->output .= "=========================================================================================================".PHP_EOL;
            $this->output .= "No New Orders".PHP_EOL;
            $this->output .= "=========================================================================================================".PHP_EOL;
        }
        return false;
    }

    private function procBBOrders($collected_orders)
    {
        //$this->output .= print_r($collected_orders,true).PHP_EOL;
        //echo "<pre>",print_r($collected_orders),"</pre>";//die();
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
                    'client_order_id'       => $o['id'],
                    'errors'                => 0,
                    'tracking_email'        => $o['billing']['email'],
                    'ship_to'               => $o['shipping']['first_name']." ".$o['shipping']['last_name'],
                    'company_name'          => $o['shipping']['company'],
                    'date_ordered'          => strtotime( $o['date_created'] ),
                    'status_id'             => $this->controller->order->ordered_id,
                    'eparcel_express'       => 0,
                    'signature_req'         => 0,
                    'contact_phone'         => $o['billing']['phone'],
                    'import_error'          => false,
                    'import_error_string'   => ''
                );
                if(strtolower($o['shipping_lines'][0]['method_title']) == "express shipping") $order['eparcel_express'] = 1;
                if( !filter_var($o['billing']['email'], FILTER_VALIDATE_EMAIL) )
                {
                    $order['errors'] = 1;
                    $order['error_string'] = "<p>The customer email is not valid</p>";
                }
                //validate address
                $ad = array(
                    'address'   => $o['shipping']['address_1'],
                    'address_2' => $o['shipping']['address_2'],
                    'suburb'    => $o['shipping']['city'],
                    'state'     => $o['shipping']['state'],
                    'postcode'  => $o['shipping']['postcode'],
                    'country'   => $o['shipping']['country']
                );
                if($ad['country'] == "GB" || $ad['country'] == "US" || $ad['country'] == "CA")
                {
                    $message = "<p>BB Order ID: {$order['client_order_id']}</p>";
                    $message .= "<p>Customer: {$order['ship_to']}</p>";
                    $message .= "<p>Address: {$ad['address']}</p>";
                    $message .= "<p>{$ad['address_2']}</p>";
                    $message .= "<p>{$ad['suburb']}</p>";
                    $message .= "<p>{$ad['state']}</p>";
                    $message .= "<p>{$ad['postcode']}</p>";
                    $message .= "<p>{$ad['country']}</p>";
                    //if (php_sapi_name() == 'cli')
                    if ($_SERVER['HTTP_USER_AGENT'] == '3PLPLUSAGENT')
                    {
                        Email::sendBBInternationOrder($message);
                        continue;
                    }
                    else
                    {
                        //$order['import_error'] = true;
                        //$order['import_error_string'] .= "<li>This is an order for the UK, US, or Canada and cannot be processed through the WMS</li>";
                    }
                }
                elseif( !($ad['country'] == "NZ" || $ad['country'] == "AU" ) )
                {
                    $message = "<p>BB Order ID: {$order['client_order_id']}</p>";
                    $message .= "<p>Customer: {$order['ship_to']}</p>";
                    $message .= "<p>Address: {$ad['address']}</p>";
                    $message .= "<p>{$ad['address_2']}</p>";
                    $message .= "<p>{$ad['suburb']}</p>";
                    $message .= "<p>{$ad['state']}</p>";
                    $message .= "<p>{$ad['postcode']}</p>";
                    $message .= "<p>{$ad['country']}</p>";
                    //if (php_sapi_name() == 'cli')
                    if ($_SERVER['HTTP_USER_AGENT'] == '3PLPLUSAGENT')
                    {
                        Email::sendBBInternationOrder($message, "Big Bottle Order With Address For ".$ad['country']);
                        continue;
                    }
                    else
                    {
                        //$order['import_error'] = true;
                        //$order['import_error_string'] .= "<li>This is a non AU or NZ order. It must be entered manually</li>";
                    }
                }
                if($ad['country'] == "AU")
                {
                    if(strlen($ad['address']) > 40 || strlen($ad['address_2']) > 40 || strlen($order['company_name'])  > 40)
                    {
                        $order['errors'] = 1;
                        $order['error_string'] .= "<p>Addresses cannot have more than 40 characters</p>";
                    }
                    $aResponse = $this->controller->BigBottleEparcel->ValidateSuburb($ad['suburb'], $ad['state'], str_pad($ad['postcode'],4,'0',STR_PAD_LEFT));

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
                $order['sort_order'] = ($ad['country'] == "AU")? 2:1;
                $qty = $ar_qty = 0;
                foreach($o['line_items'] as $item)
                {
                    //$bb = new BigBottle($item['name'], $item['quantity'], $item['sku']);
                    $product = $this->controller->item->getItemBySku($item['sku']);
                    if(!$product)
                    {
                        $items_errors = true;
                        $mm .= "<li>Could not find {$item['name']} in WMS based on {$item['sku']}</li>";
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
                        if( in_array($item_id, Config::get('BB_ADRANGE_IDS') ) )
                        {
                            $weight += $product['weight'];
                            $ar_qty += $item['quantity'];
                            $order['satchels'] = 1;
                        }
                        else
                        {
                            $qty += $item['quantity'];
                        }
;
                    }

                }
                if( ($qty + $ar_qty) > 1 || !empty($o['shipping']['company'])) $order['signature_req'] = 1;////////////////////////////////////////
                if(empty($o['customer_note']))
                {
                    if( $qty > 1 || !empty($o['shipping']['company']) )
                        $delivery_instructions =  "";
                    else
                        $delivery_instructions =  "Please leave in a safe place out of the weather";
                }
                else
                {
                    $delivery_instructions = $o['customer_note'];
                }
                $order['instructions'] = $delivery_instructions;
                //echo "<pre>",print_r($order),"</pre>";die();
                if($items_errors)
                {
                    $message = "<p>There was a problem with some items</p>";
                    $message .= "<ul>".$mm."</ul>";
                    $message .= "<p>Orders with these items will not be processed at the moment</p>";
                    $message .= "<p>BB Order ID: {$order['client_order_id']}</p>";
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
                        Email::sendBBImportError($message);
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
                    $order['quantity'] = $qty + $ar_qty;
                    $order['weight'] = Config::get('BBBOX_WEIGHTS')[$qty] + $weight;
                    //if($qty > 1 || !empty($o['shipping']['company'])) $order['signature_req'] = 1;
                    $order['items'] = $items;
                    $orders_items[$o['id']] = $items;
                    $order = array_merge($order, $ad);
                    $orders[] = $order;
                }
            }//endforeach order
            //echo "<pre>",print_r($orders),"</pre>";die();
            $this->bboitems = $this->controller->allocations->createOrderItemsArray($orders_items);

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

    private function procTTOrders($collected_orders)
    {
        //$this->output .= print_r($collected_orders,true).PHP_EOL;
        //echo "<pre>",print_r($collected_orders),"</pre>";//die();
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
                    'client_order_id'       => $o['id'],
                    'errors'                => 0,
                    'tracking_email'        => $o['billing']['email'],
                    'ship_to'               => $o['shipping']['first_name']." ".$o['shipping']['last_name'],
                    'company_name'          => $o['shipping']['company'],
                    'date_ordered'          => strtotime( $o['date_created'] ),
                    'status_id'             => $this->controller->order->ordered_id,
                    'eparcel_express'       => 0,
                    'signature_req'         => 0,
                    'contact_phone'         => $o['billing']['phone'],
                    'import_error'          => false,
                    'import_error_string'   => ''
                );
                //if(strtolower($o['shipping_lines'][0]['method_title']) == "express shipping") $order['eparcel_express'] = 1;
                if( !filter_var($o['billing']['email'], FILTER_VALIDATE_EMAIL) )
                {
                    $order['errors'] = 1;
                    $order['error_string'] = "<p>The customer email is not valid</p>";
                }
                //validate address
                $ad = array(
                    'address'   => $o['shipping']['address_1'],
                    'address_2' => $o['shipping']['address_2'],
                    'suburb'    => $o['shipping']['city'],
                    'state'     => $o['shipping']['state'],
                    'postcode'  => $o['shipping']['postcode'],
                    'country'   => $o['shipping']['country']
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
                $order['sort_order'] = ($ad['country'] == "AU")? 2:1;
                $qty = 0;
                foreach($o['line_items'] as $item)
                {
                    $product = $this->controller->item->getItemBySku($item['sku']);
                    if(!$product)
                    {
                        $items_errors = true;
                        $mm .= "<li>Could not find {$item['name']} in WMS based on {$item['sku']}</li>";
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
                if($qty > 1 || !empty($o['shipping']['company'])) $order['signature_req'] = 1;////////////////////////////////////////
                if(empty($o['customer_note']))
                {
                    if( $qty > 1 || !empty($o['shipping']['company']) )
                        $delivery_instructions =  "";
                    else
                        $delivery_instructions =  "Please leave in a safe place out of the weather";
                }
                else
                {
                    $delivery_instructions = $o['customer_note'];
                }
                $order['instructions'] = $delivery_instructions;
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
                        Email::sendTTImportError($message);
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
                    $order['weight'] = $weight;
                    //if($qty > 1 || !empty($o['shipping']['company'])) $order['signature_req'] = 1;
                    $order['items'] = $items;
                    $orders_items[$o['id']] = $items;
                    $order = array_merge($order, $ad);
                    $orders[] = $order;
                }
            }//endforeach order
            //echo "<pre>",print_r($orders),"</pre>";//die();
            $this->ttoitems = $this->controller->allocations->createOrderItemsArray($orders_items);

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

    private function procNuchevOrders($collected_orders)
    {
        //$this->output .= print_r($collected_orders,true).PHP_EOL;
        //echo "<pre>",print_r($collected_orders),"</pre>";die();
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
                    'client_order_id'       => $o['id'],
                    'errors'                => 0,
                    'tracking_email'        => $o['billing_address']['email'],
                    'ship_to'               => $o['shipping_address']['first_name']." ".$o['shipping_address']['last_name'],
                    'company_name'          => $o['shipping_address']['company'],
                    'date_ordered'          => strtotime( $o['created_at'] ),
                    'status_id'             => $this->controller->order->ordered_id,
                    'eparcel_express'       => 0,
                    'signature_req'         => 0,
                    'contact_phone'         => $o['billing_address']['phone'],
                    'import_error'          => false,
                    'import_error_string'   => '',
                    'weight'                => 0
                );
                if(!empty($o['shipping_lines']) && strtolower($o['shipping_lines'][0]['method_id']) == "express shipping") $order['eparcel_express'] = 1;
                if( !filter_var($o['billing_address']['email'], FILTER_VALIDATE_EMAIL) )
                {
                    $order['errors'] = 1;
                    $order['error_string'] = "<p>The customer email is not valid</p>";
                }
                //validate address
                $ad = array(
                    'address'   => $o['shipping_address']['address_1'],
                    'address_2' => $o['shipping_address']['address_2'],
                    'suburb'    => $o['shipping_address']['city'],
                    'state'     => $o['shipping_address']['state'],
                    'postcode'  => $o['shipping_address']['postcode'],
                    'country'   => $o['shipping_address']['country']
                );
                if($ad['country'] == "AU")
                {
                    if(strlen($ad['address']) > 40 || strlen($ad['address_2']) > 40 || strlen($order['company_name'])  > 40)
                    {
                        $order['errors'] = 1;
                        $order['error_string'] .= "<p>Addresses cannot have more than 40 characters</p>";
                    }
                    $aResponse = $this->controller->NuchevEparcel->ValidateSuburb($ad['suburb'], $ad['state'], str_pad($ad['postcode'],4,'0',STR_PAD_LEFT));

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
                $order['sort_order'] = ($ad['country'] == "AU")? 2:1;
                $qty = 0;
                foreach($o['line_items'] as $item)
                {
                    //$bb = new BigBottle($item['name'], $item['quantity'], $item['sku']);
                    $product = $this->controller->item->getItemBySku($item['sku']);
                    if(!$product)
                    {
                        $items_errors = true;
                        $mm .= "<li>Could not find {$item['name']} in WMS based on {$item['sku']}</li>";
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
                    }

                }
                if(!empty($o['shipping']['company'])) $order['signature_req'] = 1;////////////////////////////////////////
                if(empty($o['customer_note']))
                {
                    if( !empty($o['shipping']['company']) )
                        $delivery_instructions =  "";
                    else
                        $delivery_instructions =  "Please leave in a safe place out of the weather";
                }
                else
                {
                    $delivery_instructions = $o['customer_note'];
                }
                $order['instructions'] = $delivery_instructions;
                //echo "<pre>",print_r($order),"</pre>";die();
                if($items_errors)
                {
                    $message = "<p>There was a problem with some items</p>";
                    $message .= "<ul>".$mm."</ul>";
                    $message .= "<p>Orders with these items will not be processed at the moment</p>";
                    $message .= "<p>BB Order ID: {$order['client_order_id']}</p>";
                    $message .= "<p>Customer: {$order['ship_to']}</p>";
                    $message .= "<p>Address: {$ad['address']}</p>";
                    $message .= "<p>{$ad['address_2']}</p>";
                    $message .= "<p>{$ad['suburb']}</p>";
                    $message .= "<p>{$ad['state']}</p>";
                    $message .= "<p>{$ad['postcode']}</p>";
                    $message .= "<p>{$ad['country']}</p>";

                    //if (php_sapi_name() == 'cli')
                    if ($_SERVER['HTTP_USER_AGENT'] == '3PLPLUSAGENT')
                    {
                        Email::sendNuchevImportError($message);
                    }
                    else
                    {
                        $message .= "<p class='bold'>If you manually enter this order into the WMS, you will need to update its status in woo-commerce, so it does not get imported tomorrow</p>";
                        $this->return_array['error_string'] .= $message;
                        ++$this->return_array['error_count'];
                    }
                }
                else
                {
                    $order['quantity'] = $qty;
                    //$order['weight'] = Config::get('BBBOX_WEIGHTS')[$qty];
                    if($qty > 1 || !empty($o['shipping_address']['company'])) $order['signature_req'] = 1;
                    $order['items'] = $items;
                    $orders_items[$o['id']] = $items;
                    $order = array_merge($order, $ad);
                    $orders[] = $order;
                }
            }//endforeach order
            //echo "<pre>",print_r($orders),"</pre>";die();
            $this->nuchevoitems = $this->controller->allocations->createOrderItemsArray($orders_items);

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
}