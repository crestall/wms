<?php
/**
 * PBA home course golf location for the shopify class.
 *
 *
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class PbaHomeCourseGolfShopify extends Shopify
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
            'ShopUrl'        => 'https://homecoursegolf.myshopify.com',
            'ApiKey'         => Config::get('PBAHOMECOURSEGOLFSHOPIFYAPIKEY'),
            'Password'       => Config::get('PBAHOMECOURSEGOLFSHOPIFYAPIPASS')
        );

        $from_address = Config::get("FSG_ADDRESS");
        $this->from_address_array = array(
            'name'      =>  'Home Course Golf (via FSG 3PL)',
            'lines'		=>	array($from_address['address']),
            'suburb'	=>	$from_address['suburb'],
            'postcode'	=>	$from_address['postcode'],
            'state'		=>	$from_address['state'],
            'country'	=>  $from_address['country']
        );
    }

    public function getOrders()
    {
        //die($this->controller->request->params['args']['ua']);
        $this->ua = (isset($this->controller->request->params['args']['ua']))?$this->controller->request->params['args']['ua']:"FSG";
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "Performance Brands Australia ORDER IMPORTING FOR ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        $shopify = $this->resetConfig($this->config);
        $collected_orders = array();
        $params = array(
            'status'            => 'open',
            'financial_status'  => 'paid',
        );
        try {
            $collected_orders = $shopify->Order->get($params);
        } catch (Exception $e) {
                echo "<pre>",print_r($e),"</pre>";die();
                $this->output .=  $e->getMessage() .PHP_EOL;
                $this->output .=  print_r($e->getResponse(), true) .PHP_EOL;
                if ($this->ua == "CRON" )
                {
                        Email::sendCronError($e, "Voice Caddy");
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
        if($orders = $this->procOrders($collected_orders))
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
                Email::sendPBAShopifyImportSummary($this->return_array,"Home Course Golf");
        }
        //echo "<pre>",print_r($this->return_array),"</pre>";
    }

    public function fulfillAnOrder($order_id, $consignment_id, $tracking_url)
    {
        $shopify = $this->resetConfig($this->config);
        $shopify->Order($order_id)->Fulfillment->post([
            "location_id" => $shopify->Location->get()[0]['id'],
            "tracking_number" => $consignment_id,
            "tracking_urls" => [$tracking_url],
            "notify_customer" => true
        ]);
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
            foreach($pbaoitems[$o['client_order_id']] as $item)
            {
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
            if($o['items_errors'] || $item_error || $import_error)
            {
                $args = array(
                    'import_error'          => $import_error,
                    'import_error_string'   => $import_error_string,
                    'item_error'            => $item_error,
                    'item_error_string'     => $error_string,
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
                'is_shopify'            => 1,
                'is_homecoursegolf'     => 1,
                'shopify_id'            => $o['shopify_id']
            );
            if($o['signature_req'] == 1) $vals['signature_req'] = 1;
            if($o['eparcel_express'] == 1) $vals['express_post'] = 1;
            $itp = array($pbaoitems[$o['client_order_id']]);
            //$itp = array($o['items'][$o['client_order_id']]);
            $order_number = $this->controller->order->addOrder($vals, $itp);
            $this->output .= "Inserted Order: $order_number".PHP_EOL;
            $this->output .= print_r($vals,true).PHP_EOL;
            $this->output .= print_r($o['items'][$o['client_order_id']], true).PHP_EOL;
            ++$this->return_array['import_count'];
            $this->return_array['imported_orders'][] = $o['client_order_id'];
        }
    }
}