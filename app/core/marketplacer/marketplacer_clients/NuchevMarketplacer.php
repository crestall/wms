<?php
/**
 * Nuchev location for the marketplacer class.
 *
 *
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */
class NuchevMarketplacer extends Marketplacer{

    private $client_id = 5;
    private $from_address_array = array();
    private $config = array();

    public function __construct($controller)
    {
        parent::__construct($controller);
        $this->ua = (isset($this->controller->request->params['args']['ua']))?$this->controller->request->params['args']['ua']:"FSG";
        $this->config = array(
            'ShopUrl'        => 'https://woolworths.marketplacer.com/api/v2/client/',
            'ApiKey'         => Config::get('NUCHEVMARKETPLACERAPIKEY'),
            'Username'       => Config::get('NUCHEVMARKETPLACERUSERNAME'),
            'Password'       => Config::get('NUCHEVMARKETPLACERPASSWORD')
        );

        //echo "<pre>",print_r($this->config),"</pre>";die();

        $from_address = Config::get("FSG_ADDRESS");
        $this->from_address_array = array(
            'name'      =>  'Nuchev - OLI6 (via FSG 3PL)',
            'lines'		=>	array($from_address['address']),
            'suburb'	=>	$from_address['suburb'],
            'postcode'	=>	$from_address['postcode'],
            'state'		=>	$from_address['state'],
            'country'	=>  $from_address['country']
        );
    }

    public function fulfillAnOrder($invoice_id, $consignment_id, $carrier)
    {
        $data = [
            "data"  => [
                "type"          => "invoices",
                "attributes"    => [
                    "postage_tracking"  => $consignment_id,
                    "postage_carrier"   => $carrier
                ]
            ]
        ];
        $this->sendPutRequest("/invoices/".$invoice_id."/sent", $this->config, $data);
    }

    public function getOrders()
    {
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "Nuchev MARKETPLACER ORDER IMPORTING FOR ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;

        $endpoint = "/invoices?since=2021-12-01T00:00:00Z&status=paid&include=line_items,customer";
        $collected_orders = $this->sendGetRequest($endpoint, $this->config);
        if($orders = $this->procOrders($collected_orders['data']))
        {
            $this->addNuchevOrders($orders);
        }
        //die('procOrders failed');
        Logger::logOrderImports('order_imports/nuchev', $this->output); //die();
        if ($this->ua != "CRON" )
        {
            return $this->return_array;
        }
        else
        {
            Email::sendNuchevMarketplacerImportSummary($this->return_array);
        }
        //echo "<pre>",print_r($this->return_array),"</pre>";
    }

    private function addNuchevOrders($orders)
    {
        //echo "<pre>",print_r($orders),"</pre>";//die();
        $nuchevoitems = $this->controller->allocations->createOrderItemsArray($orders['orders_items']);
        unset($orders['orders_items']);
        //echo "<pre>",print_r($nuchevoitems),"</pre>";die();
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
            foreach($nuchevoitems[$o['client_order_id']] as $item)
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
                    'email_function'        => "sendNuchevMarketplacerImportError",
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
                'state'                 => $o['state'],
                'suburb'                => $o['suburb'],
                'postcode'              => $o['postcode'],
                'country'               => $o['country'],
                'contact_phone'         => $o['contact_phone'],
                'is_marketplacer'       => 1,
                'marketplacer_id'       => $o['marketplacer_id']
            );
            if($o['signature_req'] == 1) $vals['signature_req'] = 1;
            if($o['eparcel_express'] == 1) $vals['express_post'] = 1;
            $itp = array($nuchevoitems[$o['client_order_id']]);
            //$itp = array($o['items'][$o['client_order_id']]);
            $order_number = $this->controller->order->addOrder($vals, $itp);
            $this->output .= "Inserted Order: $order_number".PHP_EOL;
            $this->output .= print_r($vals,true).PHP_EOL;
            $this->output .= print_r($o['items'][$o['client_order_id']], true).PHP_EOL;
            ++$this->return_array['import_count'];
            $this->return_array['imported_orders'][] = $o['client_order_id'];
        }
    }
}//end class
?>