<?php
/**
 * BuzzBee location for the shopify class.
 *
 *
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class PbaShopify extends Shopify
{
    private $client_id = 87;
    private $from_address_array = array();
    private $config = array();

    private $shopify;

    public function init()
    {
        //parent::__construct($controller);
        $this->ua = (isset($this->controller->request->params['args']['ua']))?$this->controller->request->params['args']['ua']:"FSG";
        $this->config = array(
            'ShopUrl'        => 'https://perfect-practice-golf-au.myshopify.com/',
            'ApiKey'         => Config::get('PBASHOPIFYAPIKEY'),
            'Password'       => Config::get('PBASHOPIFYAPIPASS')
        );

        $from_address = Config::get("FSG_ADDRESS");
        $this->from_address_array = array(
            'name'      =>  'Perfect Practice Golf (via FSG 3PL)',
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
                Email::sendCronError($e, "Perfect Practice Golf");
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

    public function getPBAOrders()
    {
            //die($this->controller->request->params['args']['ua']);
            $this->ua = (isset($this->controller->request->params['args']['ua']))?$this->controller->request->params['args']['ua']:"FSG";
            $this->output = "=========================================================================================================".PHP_EOL;
            $this->output .= "Performance Brands Australia ORDER IMPORTING FOR ".date("jS M Y (D), g:i a (T)").PHP_EOL;
            $this->output .= "=========================================================================================================".PHP_EOL;

            $collected_orders = array();
            $params = array(
                    'status'    => 'open'
            );
            try {
                $collected_orders = $this->shopify->Order->get($params);
            } catch (Exception $e) {
                    echo "<pre>",print_r($e),"</pre>";die();
                    $this->output .=  $e->getMessage() .PHP_EOL;
                    $this->output .=  print_r($e->getResponse(), true) .PHP_EOL;
                    if ($this->ua == "CRON" )
                    {
                            Email::sendCronError($e, "Perfect Practice Golf");
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
                    Email::sendPBAShopifyImportSummary($this->return_array);
            }
            //echo "<pre>",print_r($this->return_array),"</pre>";
    }

    private function addPBAOrders($orders)
    {
        $this->pbaoitems = $this->controller->allocations->createOrderItemsArray($orders['orders_items']);
        unset($orders['orders_items']);
        foreach($orders as $o)
        {
            //check for errors first
            $item_error = false;
            $error_string = "";

            foreach($this->pbaoitems[$o['client_order_id']] as $item)
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
                'shopify_id'            => $o['shopify_id']
            );
            if($o['signature_req'] == 1) $vals['signature_req'] = 1;
            if($o['eparcel_express'] == 1) $vals['express_post'] = 1;
            //$itp = array($this->pbaoitems[$o['client_order_id']]);
            $itp = array($o['items'][$o['client_order_id']]);
            $order_number = $this->controller->order->addOrder($vals, $itp);
            $this->output .= "Inserted Order: $order_number".PHP_EOL;
            $this->output .= print_r($vals,true).PHP_EOL;
            $this->output .= print_r($o['items'][$o['client_order_id']], true).PHP_EOL;
            ++$this->return_array['import_count'];
            $this->return_array['imported_orders'][] = $o['client_order_id'];
        }
    }
}