<?php
/**
 * BuzzBee location for the shopify class.
 *
 *
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */
use PHPShopify\Exception\CurlException;

class BuzzBeeShopify extends Shopify
{
    private $client_id = 89;
    private $from_address_array = array();
    private $config = array();

    private $shopify;

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
            'name'      =>  'Buzz Bee Australia (via FSG 3PL)',
            'lines'		=>	array($from_address['address']),
            'suburb'	=>	$from_address['suburb'],
            'postcode'	=>	$from_address['postcode'],
            'state'		=>	$from_address['state'],
            'country'	=>  $from_address['country']
        );

        try{
            $this->shopify = new PHPShopify\ShopifySDK($this->config);
            //echo "BUZZ BEE<pre>",var_dump($this->shopify),"</pre>";die();
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
        //echo "BUZZ BEE<pre>",var_dump($this->shopify),"</pre>";die();
        $collected_orders = array();
        $params = array(
            'status'                => 'open'
        );
        //echo "BUZZ BEE<pre>",var_dump($params),"</pre>";die();
        try {
            $collected_orders = $this->shopify->Order->get($params);
        } catch (Throwable $e) {
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
        //Also need to check for customer collect and no FSG handling
        $order_count = count($collected_orders);
        echo "<h1>Collected $order_count Orders</h1>";
        $filtered_orders = $this->filterForFSG($collected_orders);
        $filtered_count = count($filtered_orders);
        echo "<h1>There are $filtered_count Orders Left</h1>";

        foreach($filtered_orders as $foi => $fo)
        {
            if(!isset($fo['shipping_address']))
            {
                $filtered_orders[$foi]['shipping_address'] = array(
                    'first_name'    => $fo['customer']['first_name'],
                    'address1'      => $this->from_address_array['lines'][0],
                    'phone'         => $fo['customer']['phone'],
                    'city'          => $this->from_address_array['suburb'],
                    'zip'           => $this->from_address_array['postcode'],
                    'province'      => $this->from_address_array['state'],
                    'country'       => $this->from_address_array['country'],
                    'last_name'     => $fo['customer']['last_name'],
                    'address2'      => '',
                    'company'       => $fo['customer']['default_address']['company'],
                    'latitude'      => '',
                    'longitude'     => '',
                    'name'          => $fo['customer']['default_address']['name'],
                    'country_code'  => $this->from_address_array['country'],
                    'province_code' => $this->from_address_array['state']
                );
                $filtered_orders[$foi]['pickup'] = 1;
            }
            else
            {
                $filtered_orders[$foi]['pickup'] = 0;
            }
        }
        echo "FILTERED<pre>",print_r($filtered_orders),"</pre>";
        die();
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

    private function filterForFSG($collected_orders)
    {
        foreach($collected_orders as $coi => $co)
        {
            $order_id = $co['id'];
            $order_number = $co['order_number'];
            $order_fulfillments = $this->shopify->Order($order_id)->FulfillmentOrder->get();
            foreach($order_fulfillments as $of)
            {
                if(!preg_match("/FSG/i", $of['assigned_location']['name']))
                {
                    foreach($of['line_items'] as $ofli)
                    {
                        $line_item_id = $ofli['line_item_id'];
                        $key = array_search($line_item_id, array_column($co['line_items'], 'id'));
                        unset($collected_orders[$coi]['line_items'][$key]);
                    }
                }
            }
            $item_count = count($collected_orders[$coi]['line_items']);
            if( $item_count == 0 )
            {
                unset($collected_orders[$coi]);
            }
        }
        return $collected_orders;
    }


} // end class
 ?>