<?php
/**
 * BuzzBee location for the shopify class.
 *
 *
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class BuzzBeeShopify extends Shopify
{
    private $client_id = 89;
    private $from_address_array = array();
    private $config = array();

    private $shopify;

    private $bboitems;

    public function init()
    {
        //parent::__construct($controller);
        $this->ua = (isset($this->controller->request->params['args']['ua']))?$this->controller->request->params['args']['ua']:"FSG";
        $this->config = array(
            'ShopUrl'        => 'https://buzzbeeaustralia.myshopify.com/',
            'ApiKey'         => Config::get('BBSHOPIFYAPIKEY'),
            'Password'       => Config::get('BBSHOPIFYAPIPASS')
        );

        $from_address = Config::get("FSG_ADDRESS");
        $this->from_address_array = array(
            'name'      =>  'Freedom Publishing Books (via FSG 3PL)',
            'lines'		=>	array($from_address['address']),
            'suburb'	=>	$from_address['suburb'],
            'postcode'	=>	$from_address['postcode'],
            'state'		=>	$from_address['state'],
            'country'	=>  $from_address['country']
        );

        try{
            $this->shopify = new PHPShopify\ShopifySDK($this->config);
        } catch (Exception $e) {
            echo "<pre>",print_r($e),"</pre>";die();
            $this->output .=  $e->getMessage() .PHP_EOL;
            $this->output .=  print_r($e->getResponse(), true) .PHP_EOL;
            if ($this->ua == "CRON" )
            {
                Email::sendCronError($e, "Buzz Bee Australia");
                return;
            }
            else
            {
                $this->return_array['import_error'] = true;
                $this->return_array['import_error_string'] .= print_r($e->getMessage(), true);
                return $this->return_array;
            }
        }
    }

    public function getOrders()
    {
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "Buzz Bee Australia ORDER IMPORTING FOR ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;

        $collected_orders = array();
        $params = array(
            'status'                => 'open',
            'financial_status'      => 'paid',
            'fulfillment_status'    => 'unshipped',
            'fields'                => 'id,created_at,order_number,email,total_weight,shipping_address,line_items,shipping_lines'
        );
        try {
            $collected_orders = $this->shopify->Order->get($params);
        } catch (Exception $e) {
            echo "<pre>",print_r($e),"</pre>";die();
            $this->output .=  $e->getMessage() .PHP_EOL;
            $this->output .=  print_r($e->getResponse(), true) .PHP_EOL;
            if ($this->ua == "CRON" )
            {
                    Email::sendCronError($e, "Buzz Bee Australia");
                    return;
            }
            else
            {
                    $this->return_array['import_error'] = true;
                    $this->return_array['import_error_string'] .= print_r($e->getMessage(), true);
                    return $this->return_array;
            }
        }
        //BUZZBEE has it in grams!!!
        foreach($collected_orders as $coi => $co)
        {
            $collected_orders[$coi]['total_weight'] = $co['total_weight']/1000;
        }

        echo "<pre>",print_r($collected_orders),"</pre>"; die();
        //return $collected_orders;
        if($orders = $this->procOrders($collected_orders))
        {
            $this->output .= "===========================   Sending Orders  =========================".PHP_EOL;
            Logger::logOrderImports('order_imports/bba', $this->output);
            return $orders;
            //$this->addPBAOrders($orders);
        }
        $this->output .= "===========================   Falsy  =========================".PHP_EOL;
        Logger::logOrderImports('order_imports/bba', $this->output); //die();
    }




} // end class
 ?>