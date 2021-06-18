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

    public function getOrders()
    {
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "Buzz Bee Australia ORDER IMPORTING FOR ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        //echo "<p>getting BUZZ bee orders</p>";
        $collected_orders = array();
        $ids = "3888625451159, 3899189788823, 3899170783383";
        $params = array(
            'status'                => 'open',
            'financial_status'      => 'paid',
            'fulfillment_status'    => 'unfulfilled',
            'fields'                => 'id,created_at,order_number,email,total_weight,shipping_address,line_items,shipping_lines,customer',
            'ids'					=> $ids
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
        //echo "COLLECTED<pre>",print_r($collected_orders),"</pre>";
        //Also need to check for customer collect and no FSG handling
        $order_count = count($collected_orders);
        //echo "<h1>Collected $order_count Orders</h1>";
        $filtered_orders = $this->filterForFSG($collected_orders);
        $filtered_count = count($filtered_orders);
        //echo "<h1>There are $filtered_count Orders Left</h1>";

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
        }
        //echo "FILTERED<pre>",print_r($filtered_orders),"</pre>";
        //die();
        //return $collected_orders;
        if($orders = $this->procOrders($filtered_orders))
        {
            $this->addBuzzBeeOrders($orders);;
        }
        echo "RETURN ARRAY<pre>",print_r($this->return_array),"</pre>"; die();
        Logger::logOrderImports('order_imports/bba', $this->output); //die();
        if ($this->ua != "CRON" )
        {
                return $this->return_array;
        }
        else
        {
                Email::sendPBAShopifyImportSummary($this->return_array,"Home Course Golf");
        }
        //echo "<pre>",print_r($this->return_array),"</pre>";
    }

    private function filterForFSG($collected_orders)
    {
        $shopify = $this->resetConfig($this->config);
        foreach($collected_orders as $coi => $co)
        {
            $order_id = $co['id'];
            $order_number = $co['order_number'];
            try {
                $order_fulfillments = $shopify->Order($order_id)->FulfillmentOrder->get();
            } catch (Exception $e) {
                echo "In the Filter<pre>",print_r($e),"</pre>";die();
            }

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

    public function fulfillAnOrder($order_id, $consignment_id, $tracking_url)
    {
        $shopify = $this->resetConfig($this->config);
        $shopify->Order($order_id)->Fulfillment->post([
            "location_id" => 54288547991,               //Get this from elsewhere in case it changes
            "tracking_number" => $consignment_id,
            "tracking_urls" => [$tracking_url],
            "notify_customer" => true
        ]);
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

            foreach($bboitems[$o['client_order_id']] as $item)
            //foreach($o['items'][$o['client_order_id']] as $item)
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

                if ($this->ua != "CRON" )
                {
                    ++$this->return_array['error_count'];
                    $this->return_array['error_string'] .= $message;
                }
                elseif(SITE_LIVE)
                {
                    ++$this->return_array['error_count'];
                    $this->return_array['error_string'] .= $message;
                    $this->return_array['error_orders'][] = $o['client_order_id'];
                    Email::sendPBAImportError($message);
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
            //$itp = array($o['items'][$o['client_order_id']]);
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