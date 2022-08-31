<?php
/**
 * PBA arccos course golf location for the shopify class.
 *
 *
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class PbaArccosGolfShopify extends Shopify
{
    private $client_id = 87;
    private $from_address_array = array();
    private $config = array();

    //protected $shopify;

    public function __construct($controller)
    {
        parent::__construct($controller);
        $this->ua = (isset($this->controller->request->params['args']['ua']))?$this->controller->request->params['args']['ua']:"FSG";
        $this->config = array(
            'ShopUrl'        => 'https://arccos-golf-au.myshopify.com',
            'ApiKey'         => Config::get('ARCCOSSAPIKEY'),
            'Password'       => Config::get('ARCCOSSAPIPASS')
        );

        $from_address = Config::get("FSG_ADDRESS");
        $this->from_address_array = array(
            'name'      =>  'Arccos Course Golf (via FSG 3PL)',
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
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "Performance Brands Australia Arccos Importing Order $order_no  ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        $return_array = array(
            'error'                 =>  false,
            'response_string'       =>  '',
            'import_error'          =>  false,
            'import_error_string'   =>  ''
        );
        $shopify = $this->resetConfig($this->config);
        $collected_orders = array();
        $params = array(
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
        echo "COLLECTED<pre>",print_r($collected_orders),"</pre>"; //die();
        $filtered_orders = $this->filterForAlreadyCollected($collected_orders);
        echo "FILTERED<pre>",print_r($filtered_orders),"</pre>"; die();
        if($orders = $this->procOrders($filtered_orders))
        //if($orders = $this->procOrders($collected_orders))
        {
            $this->addPBAOrders($orders);
        }
        Logger::logOrderImports('order_imports/pba', $this->output); //die();
        if ($this->ua != "CRON" )
        {
            return $this->return_array;
        }
        else
        {
            //Email::sendPBAShopifyImportSummary($this->return_array,"Arccos Golf");
        }
        echo "<pre>",print_r($this->return_array),"</pre>";
    }

    public function getOrders()
    {
        //die($this->controller->request->params['args']['ua']);
        $this->ua = (isset($this->controller->request->params['args']['ua']))?$this->controller->request->params['args']['ua']:"FSG";
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "Performance Brands Australia Arccos ORDER IMPORTING FOR ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        $shopify = $this->resetConfig($this->config);
        $collected_orders = array();
        $params = array(
            'status'            => 'open',
            'financial_status'  => 'paid',
            'since_id'          => '4535257989296'
        );
        try {
            $collected_orders = $shopify->Order->get($params);
        } catch (Exception $e) {
                //echo "<pre>",print_r($e),"</pre>";die();
                $this->output .=  $e->getMessage() .PHP_EOL;
                $this->output .=  print_r($e->getResponse(), true) .PHP_EOL;
                if ($this->ua == "CRON" )
                {
                        Email::sendCronError($e, "Arccoss");
                        return;
                }
                else
                {
                        $this->return_array['import_error'] = true;
                        $this->return_array['import_error_string'] .= print_r($e->getMessage(), true);
                        return $this->return_array;
                }
        }
        //echo "<pre>",print_r($collected_orders),"</pre>"; die();
        $filtered_orders = $this->filterForAlreadyCollected($collected_orders);
        if($orders = $this->procOrders($filtered_orders))
        {
            $this->addPBAOrders($orders);
        }
        Logger::logOrderImports('order_imports/pba', $this->output); //die();
        if ($this->ua != "CRON" )
        {
            return $this->return_array;
        }
        else
        {
            Email::sendPBAShopifyImportSummary($this->return_array,"Arccos Golf");
        }
        //echo "<pre>",print_r($this->return_array),"</pre>";
    }

    public function fulfillAnOrder($order_id, $consignment_id, $tracking_url, $items)
    {
        $shopify = $this->resetConfig($this->config);
        $location1_id = $shopify->Location->get()[1]['id'];
        $location2_id = $shopify->Location->get()[2]['id'];
        $fulfill_items = array();
        foreach($items as $i)
        {
            if(!empty($i['shopify_line_item_id']))
                $fulfill_items[] = array('id' => $i['shopify_line_item_id']);
            if(!empty($i['shopify_line_item_location_id']))
                $location_id = $i['shopify_line_item_location_id'];
        }
        $post_body = [
            "location_id" => 67319627953,
            "tracking_number" => $consignment_id,
            "notify_customer" => true,
            "line_items"    => $fulfill_items,
        ];
        if($tracking_url)
            $post_body['tracking_urls'] = [$tracking_url];
        //create the fulfillment
        try {
            Logger::logOrderFulfillment("shopify", "using location 67319627953".PHP_EOL);
            $shopify->Order($order_id)->Fulfillment->post($post_body);
            $fulfillment_id = $shopify->Order($order_id)->Fulfillment->get()[0]['id'];
        }
        catch (Exception $e){
            try{
                $post_body['location_id'] = $location1_id;
                Logger::logOrderFulfillment("shopify", "changed location to ".$location1_id.PHP_EOL);
                $shopify->Order($order_id)->Fulfillment->post($post_body);
                $fulfillment_id = $shopify->Order($order_id)->Fulfillment->get()[0]['id'];
            }
            catch (Exception $e){
                try{
                    $post_body['location_id'] = $location2_id;
                    Logger::logOrderFulfillment("shopify", "changed location to ".$location2_id.PHP_EOL);
                    $shopify->Order($order_id)->Fulfillment->post($post_body);
                    $fulfillment_id = $shopify->Order($order_id)->Fulfillment->get()[0]['id'];
                }
                catch (Exception $e){
                    echo "<pre>",print_r($e),"</pre>";die();
                }
            }
        }
        //complete the fulfillment
        //complete the fulfillment
        try {
            $shopify->Order($order_id)->Fulfillment($fulfillment_id)->complete();
        }
        catch (Exception $e){
            echo "<pre>",print_r($e),"</pre>";die();
        }
    }

    private function addPBAOrders($orders)
    {
        $pbaoitems = $this->controller->allocations->createOrderItemsArray($orders['orders_items']);
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
            foreach($pbaoitems[$o['client_order_id']] as $item)
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
                    'email_function'        => "sendPBAImportError",
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
                '3pl_comments'          => "Send With eParcel",
                'is_shopify'            => 1,
                'is_arccosgolf'         => 1,
                'shopify_id'            => $o['shopify_id']
            );
            if($o['signature_req'] == 1) $vals['signature_req'] = 1;
            if($o['eparcel_express'] == 1) $vals['express_post'] = 1;
            $itp = array($pbaoitems[$o['client_order_id']]);
            //$itp = array($o['items'][$o['client_order_id']]);
            $order_number = $this->controller->order->addOrder($vals, $itp);
            $this->output .= "Inserted Order: $order_number".PHP_EOL;
            //$this->output .= print_r($vals,true).PHP_EOL;
            //$this->output .= print_r($o['items'][$o['client_order_id']], true).PHP_EOL;
            $shopify_tags = (isset($o['shopify_tags']) && !empty($o['shopify_tags']))? $o['shopify_tags'].",sent_to_fsg": "sent_to_fsg";
            $this->addTag($this->config, $o['shopify_id'], $shopify_tags);
            $this->output .= "Added tags: $shopify_tags".PHP_EOL;
            ++$this->return_array['import_count'];
            $this->return_array['imported_orders'][] = $o['client_order_id'];
        }
    }
}