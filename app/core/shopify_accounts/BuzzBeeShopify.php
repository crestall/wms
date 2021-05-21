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
            'name'      =>  'Buzz Bee Australia (via FSG 3PL)',
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
            'fields'                => 'id,created_at,order_number,email,total_weight,shipping_address,line_items,shipping_lines,customer'
        );
        try {
            $order_id = "3859592249495";
            $collected_orders[] = $this->shopify->Order->get($params);
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
        //Also need to check for customer collect and no FSG handling
        $order_count = count($collected_orders);
        echo "<h1>Collected $order_count Orders</h1>";

        foreach($collected_orders as $coi => $co)
        {
            $order_id = $co['id'];
            $order_number = $co['order_number'];
            echo "<p>Doing order: $order_number ($order_id)</p>";
            //echo "THE ORDER<pre>",print_r($co),"</pre>";
            echo "<p>=========================================</p>";
            $item_count = count($collected_orders[$coi]['line_items']);
            echo "<p>ITEM COUNT IS $item_count</p>";
            $order_fulfillments = $this->shopify->Order($order_id)->FulfillmentOrder->get();
            //echo "The Fulfillments<pre>",print_r($order_fulfillments),"</pre>";
            foreach($order_fulfillments as $of)
            {
                if(!preg_match("/FSG/i", $of['assigned_location']['name']))
                {
                    foreach($of['line_items'] as $ofli)
                    {
                        $line_item_id = $ofli['line_item_id'];
                        //echo "<p>$line_item_id does not belong</p>";
                        //$olii = array_search($line_item_id, $co['line_items']);
                        $key = array_search($line_item_id, array_column($co['line_items'], 'id'));
                        unset($collected_orders[$coi]['line_items'][$key]);
                        //echo "<p>Gonna delete line_itemm with index $key</p>";
                    }
                }
            }
            $item_count = count($collected_orders[$coi]['line_items']);
            echo "<p>ITEM COUNT IS NOW $item_count</p>";
            if( $item_count == 0 )
            {
                unset($collected_orders[$coi]);
            }
            echo "<p>=========================================</p>";
            /*
            $collected_orders[$coi]['total_weight'] = $co['total_weight']/1000;
            if( isset($co['shipping_lines']) && !empty($co['shipping_lines']) )
            {
                if(preg_match("/FSG/i", $co['shipping_lines'][0]['code']))
                {
                    if(!isset($co['shipping_address']))
                    {
                        $collected_orders[$coi]['shipping_address'] = array(
                            'first_name'    => $co['customer']['first_name'],
                            'address1'      => $this->from_address_array['lines'][0],
                            'phone'         => $co['customer']['phone'],
                            'city'          => $this->from_address_array['suburb'],
                            'zip'           => $this->from_address_array['postcode'],
                            'province'      => $this->from_address_array['state'],
                            'country'       => $this->from_address_array['country'],
                            'last_name'     => $co['customer']['last_name'],
                            'address2'      => '',
                            'company'       => $co['customer']['default_address']['company'],
                            'latitude'      => '',
                            'longitude'     => '',
                            'name'          => $co['customer']['default_address']['name'],
                            'country_code'  => $this->from_address_array['country'],
                            'province_code' => $this->from_address_array['state']
                        );
                        $collected_orders[$coi]['pickup'] = 1;
                    }
                    else
                    {
                        $collected_orders[$coi]['pickup'] = 0;
                    }
                }
                else
                {
                    unset($collected_orders[$coi]);
                }
            }
            else
            {
                unset($collected_orders[$coi]);
            }
            */
        }
        //echo "AFTER<pre>",print_r($collected_orders),"</pre>";
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

    private function isFSGOrder()
    {

    }


} // end class
 ?>