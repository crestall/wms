<?php

/**
 * Shopify class.
 *
 * Interacts with the shopify api

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

use Automattic\WooCommerce\HttpClient\HttpClientException;

class Shopify{

    private $output;
    private $shopify;
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

    public function getTeamTimbuktuOrders()
    {
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "TeamTimbuktu ORDER IMPORTING FOR ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        $config = array(
            'ShopUrl'   => 'https://mister-timbuktu.myshopify.com/',
            'ApiKey'    => Config::get('TEAMTIMBUKTUAPIKEY'),
            'Password'  => Config::get('TEAMTIMBUKTUAPIPASS')
        );
        $this->shopify = new PHPShopify\ShopifySDK($config);
        $collected_orders = array();
        $params = array(
            'status'    => 'open',
            'fields'    => 'id,email,note,total_weight,phone,order_number,line_items,shipping_address'
        );
        try {
          $collected_orders = $this->shopify->Order->get();
        } catch (HttpClientException $e) {
            echo "<pre>",print_r($e),"</pre>";die();
            $this->output .=  $e->getMessage() .PHP_EOL;
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

        echo "<pre>",print_r($collected_orders),"</pre>";die();
        /*
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
        */
    }

}