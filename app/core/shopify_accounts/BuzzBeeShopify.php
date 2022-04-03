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

    public $shop_name;

    public function __construct($controller)
    {
        parent::__construct($controller);
        $this->ua = (isset($this->controller->request->params['args']['ua']))?$this->controller->request->params['args']['ua']:"FSG";
        $this->shop_name = "BUZZ BEE";
        $this->config = array(
            'ShopUrl'        => 'https://buzzbeeaustralia.myshopify.com/',
            'ApiKey'         => Config::get('BBSHOPIFYAPIKEY'),
            'Password'       => Config::get('BBSHOPIFYAPIPASS')
        );

        //echo "BUZZBEE<pre>",print_r($this->config),"</pre>";die();

        $from_address = Config::get("FSG_ADDRESS");
        $this->from_address_array = array(
            'name'      =>  'Buzz Bee Australia (via FSG 3PL)',
            'lines'		=>	array($from_address['address']),
            'suburb'	=>	$from_address['suburb'],
            'postcode'	=>	$from_address['postcode'],
            'state'		=>	$from_address['state'],
            'country'	=>  $from_address['country']
        );
    }

    public function getAnOrder($order_no)
    {
        if(!$order_no)
        {
            return false;
        }
        $return_array = array(
            'error'                 =>  false,
            'response_string'       =>  '',
            'import_error'          =>  false,
            'import_error_string'   =>  ''
        );
        $shopify = $this->resetConfig($this->config);
        $collected_orders = array();
        $params = array(
            'fields'          => 'id,created_at,order_number,email,total_weight,shipping_address,line_items,shipping_lines,customer',
            'name'            => $order_no
        );
        try {
            //$order_id = "3859592249495";
            $collected_orders = $shopify->Order->get($params);
            //$collected_orders = $shopify->Order->get($params);
        } catch (Exception $e) {
            //echo "<pre>",print_r($e),"</pre>";die();
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
        //echo "<pre>UNFILTERED",print_r($collected_orders),"</pre>";
        $filtered_orders = $this->filterForFSG($collected_orders);
        //echo "<p>----------------------------------------------------------------------------------------------------------------</p>";
        //echo "<pre>FILTERED",print_r($filtered_orders),"</pre>";die();
        foreach($filtered_orders as $foi => $fo)
        {
            //if(!isset($fo['shipping_address']))
            if(strtolower($fo['shipping_lines'][0]['code']) == "pickup")
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
        }
        if($orders = $this->procOrders($filtered_orders))
        {
            $this->addBuzzBeeOrders($orders);;
        }
        //echo "RETURN ARRAY<pre>",print_r($this->return_array),"</pre>"; die();
        Logger::logOrderImports('order_imports/bba', $this->output); //die();
        if ($this->ua != "CRON" )
        {
            return $this->return_array;
        }
        //echo "<pre>",print_r($this->return_array),"</pre>";
    }

    public function getOrders()
    {
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "Buzz Bee Australia ORDER IMPORTING FOR ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        //echo "<p>getting BUZZ bee orders</p>";
        $collected_orders = array();
        $ids = "3899189788823, 3899170783383";
        $params = array(
            'status'                => 'open',
            'financial_status'      => 'paid',
            'fulfillment_status'    => 'unfulfilled',
            'fields'                => 'id,created_at,order_number,email,total_weight,shipping_address,line_items,shipping_lines,customer,tags',
            //'ids'					=> $ids,
            'limit'                 =>  250,
            'since_id'              => '3670246097047'
        );
        $shopify = $this->resetConfig($this->config);
        try {
            //$order_id = "3859592249495";
            //$collected_orders[] = $this->shopify->Order($order_id)->get($params);
            $collected_orders = $shopify->Order->get($params);
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
        //echo "COLLECTED<pre>",print_r($collected_orders),"</pre>";die();
        //Also need to check for customer collect and no FSG handling
        $order_count = count($collected_orders);
        //echo "<h1>Collected $order_count Orders</h1>";
        $filtered_orders = $this->filterForFSG($collected_orders);
        $filtered_count = count($filtered_orders);
        //echo "<h1>There are $filtered_count Orders Left</h1>";die();
        echo "FILTERED<pre>",print_r($filtered_orders),"</pre>"; die();
        foreach($filtered_orders as $foi => $fo)
        {
            //if(!isset($fo['shipping_address']))
            if( !empty($fo['shipping_lines']) && strtolower($fo['shipping_lines'][0]['code']) == "pickup" )
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
        }
        if($orders = $this->procOrders($filtered_orders))
        {
            $this->addBuzzBeeOrders($orders);;
        }
        //echo "RETURN ARRAY<pre>",print_r($this->return_array),"</pre>"; die();
        Logger::logOrderImports('order_imports/bba', $this->output); //die();
        if ($this->ua != "CRON" )
        {
                return $this->return_array;
        }
        else
        {
                Email::sendBuzzBeeShopifyImportSummary($this->return_array);
        }
        //echo "<pre>",print_r($this->return_array),"</pre>";
    }

    private function filterForFSG($collected_orders)
    {
        $shopify = $this->resetConfig($this->config);
        //echo "<pre>",print_r($collected_orders),"</pre>"; //die();
        foreach($collected_orders as $coi => $co)
        {
            //echo "<pre>",print_r($collected_orders[$coi]),"</pre>";
            $order_id = $co['id'];
            $order_number = $co['order_number'];
            //echo "<p>Doing $order_number which has an index of $coi</p>";
            try {
                $order_fulfillments = $shopify->Order($order_id)->FulfillmentOrder->get();
            } catch (Exception $e) {
                echo "In the Filter<pre>",print_r($e),"</pre>";die();
            }
            //echo "<pre>Order Fulfillments for $order_number",print_r($order_fulfillments),"</pre>";
            foreach($order_fulfillments as $of)
            {
                if( !preg_match("/FSG/i", $of['assigned_location']['name']) || $of['status'] == 'closed' )
                {
                    //Not For FSG or already closed the fulfillment
                    foreach($of['line_items'] as $ofli)
                    {
                        $line_item_id = $ofli['line_item_id'];
                        $key = array_search($line_item_id, array_column($collected_orders[$coi]['line_items'], 'id'));
                        if( !preg_match("/FSG/i", $of['assigned_location']['name']) )
                        {
                            //echo "<p>Gonna delete \$collected_orders[$coi]['line_items'][$key] cos its not for us</p>";
                            unset($collected_orders[$coi]['line_items'][$key]);
                        }
                        elseif( isset($collected_orders[$coi]['line_items'][$key]['fulfillment_status']) && $collected_orders[$coi]['line_items'][$key]['fulfillment_status'] == 'fulfilled')
                        {
                            //echo "<p>Gonna delete \$collected_orders[$coi]['line_items'][$key] cos it is already fulfilled</p>";
                            unset($collected_orders[$coi]['line_items'][$key]);
                        }
                    }
                }
            }
            $item_count = count($collected_orders[$coi]['line_items']);
            //echo "<pre>Line Items",print_r($co['line_items']),"</pre>";
            if( $item_count == 0 || !isset($collected_orders[$coi]['shipping_address']) )
            {
                //echo "<p>Gonna remove $order_number</p>";
                unset($collected_orders[$coi]);
            }
            //echo "<p>-------------------------------------------------------------------------------------------------------</p>";
        }
        return $collected_orders;
    }

    public function fulfillAnOrder($order_id, $consignment_id, $tracking_url, $items)
    {
        $shopify = $this->resetConfig($this->config);
        $fulfill_items = array();
        foreach($items as $i)
        {
            if(!empty($i['shopify_line_item_id']))
                $fulfill_items[] = array('id' => $i['shopify_line_item_id']);
        }
        try {
            $post_body = [
                "location_id" => 54288547991,               //Get this from elsewhere in case it changes
                "tracking_number" => $consignment_id,
                "notify_customer" => true,
                "line_items"    => $fulfill_items,
            ];
            if($tracking_url)
                $post_body['tracking_urls'] = [$tracking_url];
            $shopify->Order($order_id)->Fulfillment->post($post_body);
        } catch (Exception $e) {
            //echo "<pre>",print_r($e),"</pre>";die();
            $this->output .=  "----------------------------------------------------------------------" .PHP_EOL;
            $this->output .=  "Error fulfilling $order_id" .PHP_EOL;
            //$this->output .=  $e->getMessage() .PHP_EOL;
            $this->output .=  print_r($e->getResponse(), true) .PHP_EOL;
            $this->output .=  "----------------------------------------------------------------------" .PHP_EOL;
        }
    }

    private function addBuzzBeeOrders($orders)
    {
        $bboitems = $this->controller->allocations->createOrderItemsArray($orders['orders_items']);
        //echo "BBOTEMS<pre>",print_r($bboitems),"</pre>";die();
        unset($orders['orders_items']);

        foreach($orders as $o)
        {
            //check for errors first
            $item_error = false;
            $error_string = "";
            $import_error = false;
            $import_error_string = "";
            $items_errors = false;
            $items_errors_string = "";
            if($o['items_errors'])
            {
                $items_errors = true;
                $items_errors_string .= $o['items_errors_string'];
            }
            foreach($bboitems[$o['client_order_id']] as $item)
            {
                //echo "Doing {$o['client_order_id']}<pre>",print_r($item),"</pre>";
                if($item['item_error'])
                {
                    $item_error = true;
                    $error_string .= $item['item_error_string'];
                }
                if($item['import_error'])
                {
                    $import_error = true;
                    $import_error_string .= $item['import_error_string'];
                }
            }
            if($items_errors || $item_error || $import_error)
            {
                $args = array(
                    'import_error'          => $import_error,
                    'import_error_string'   => $import_error_string,
                    'item_error'            => $item_error,
                    'item_error_string'     => $error_string,
                    'items_errors'          => $items_errors,
                    'items_errors_string'   => $items_errors_string,
                    'email_function'        => "sendBBImportError",
                    'od'                    => $o
                );
                //echo "THE ARGS for {$o['client_order_id']}<pre>",print_r($args),"</pre>";
                ++$this->return_array['error_count'];
                //$this->return_array['error_string'] .= $message;
                $this->return_array['error_orders'][] = $o['client_order_id'];
                if ($this->ua == "CRON" )
                {
                    $this->sendItemErrorEmail($args);
                }
                else
                {
                    $args['send_no_message'] = 1;
                    $message = $this->sendItemErrorEmail($args);
                    $this->return_array['error_string'] .= $message;
                }
                continue;
            }
            //die("No Errors ?!");
            //insert the order
            $client_id = $this->client_id;
            $vals = array(
                'client_order_id'       => $o['client_order_id'],
                'client_id'             => $client_id,
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
                'contact_phone'         => $o['contact_phone'],
                'is_shopify'            => 1,
                'is_buzzbee'            => 1,
                'shopify_id'            => $o['shopify_id']
            );
            if($o['signature_req'] == 1) $vals['signature_req'] = 1;
            if(isset($o['pickup']) )
                $vals['pickup'] = 1;
            if($o['eparcel_express'] == 1) $vals['express_post'] = 1;
            $itp = array($bboitems[$o['client_order_id']]);
            ///$itp = array($o['items'][$o['client_order_id']]);
            $order_number = $this->controller->order->addOrder($vals, $itp);
            $this->output .= "Inserted Order: $order_number".PHP_EOL;
            $this->output .= print_r($vals,true).PHP_EOL;
            $this->output .= print_r($o['items'][$o['client_order_id']], true).PHP_EOL;
            ++$this->return_array['import_count'];
            $this->return_array['imported_orders'][] = $o['client_order_id'];
        }
    }

} // end class
 ?>