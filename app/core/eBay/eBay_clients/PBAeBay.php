<?php
/**
 * PBA implementation of the eBayAPI class.
 *
 *
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

 class PBAeBay extends EbayAPI
 {
    private $client_id = 87;

    protected $devID;
    protected $appID;
    protected $certID;
    protected $clientID;

    protected $paypalEmailAddress;
    protected $ruName;
    protected $APIHost;
    protected $authToken;
    protected $refreshToken;
    protected $scope;
    protected $authCode;

    protected $isLive;
    protected $table;
    protected $line_id;

    public function __construct($controller)
    {
        parent::__construct($controller);
        $this->table    = "ebay_access_tokens";
        $this->devID    = 'beaed030-6fea-4467-aafb-2b415518d84c';
        $this->appID    = 'MarkSoll-PBAFSG-PRD-5418204ca-f642538e';
        $this->certID   = 'PRD-418204ca8801-818f-4441-94d4-d28c';
        $this->clientID = 'MarkSoll-PBAFSG-PRD-5418204ca-f642538e';
        $this->ruName   = 'Mark_Solly-MarkSoll-PBAFSG-xuwmap';
        $this->scope    = 'https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/commerce.identity.readonly';
    }

    public function connect()
    {
        $db = Database::openConnection();
        $this->line_id = $db->queryValue($this->table, array('client_id' => $this->client_id));
        $access_tokens = $db->queryByID($this->table, $this->line_id) ;
        //echo "<pre>",print_r($access_tokens),"</pre>";
        if(empty($access_tokens['code']))
        {
            die('An eBay AuthCode is Required');
        }
        else
        {
            $this->authCode = $access_tokens['code'];
            $this->authToken = $access_tokens["access_token"];
            $this->refreshToken = $access_tokens['refresh_token'];
            if( time() >= $access_tokens['refresh_expires'] )
            {
                $this->authorizationToken(array(
                    'clientID'      => $this->clientID,
                    'certID'        => $this->certID,
                    'refreshToken'  => $this->refreshToken,
                    'scope'         => $this->scope,
                    'authCode'      => $this->authCode,
                    'ruName'        => $this->ruName
                ));//need to send an email so this works
            }
            elseif( time() >= $access_tokens['access_expires'] )
            {
                $this->authToken = $this->refreshToken(array(
                    'clientID'      => $this->clientID,
                    'certID'        => $this->certID,
                    'refreshToken'  => $this->refreshToken,
                    'scope'         => $this->scope,
                    'authCode'      => $this->authCode,
                    'ruName'        => $this->ruName
                ));
            }
        }
        //echo "<p>authToken: ".$this->authToken."</p>";
        //die( "current: ".time()." expires: ".$access_tokens['access_expires']);
        $this->paypalEmailAddress= 'PAYPAL_EMAIL_ADDRESS';
    }

    public function getCurrentOrders()
    {
        $this->ua = (isset($this->controller->request->params['args']['ua']))?$this->controller->request->params['args']['ua']:"FSG";
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "Performance Brands Australia EBAY ORDER IMPORTING FOR ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        $s_action = "sell/fulfillment/v1/order?filter=orderfulfillmentstatus:%7BNOT_STARTED%7CIN_PROGRESS%7D";

        $response = $this->sendGetRequest($s_action, $this->authToken);
        $collected_orders = json_decode($response, true);

        //echo "<pre>",print_r($collected_orders),"</pre>"; die();
        if($orders = $this->procOrders($collected_orders))
        {
            $this->addPBAOrders($orders);
        }
        Logger::logOrderImports('order_imports/pbaebay', $this->output); //die();
        if ($this->ua != "CRON" )
        {
            //return $this->return_array;
        }
        else
        {
            //Email::sendPBAShopifyImportSummary($this->return_array,"Home Course Golf");
        }
        echo "<pre>",print_r($this->return_array),"</pre>";
    }

    private function addPBAOrders($orders)
    {
        echo "addPBAOrders<pre>",print_r($orders),"</pre>";return;
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
                    //$this->sendItemErrorEmail($args);
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
                'is_ebay'               => 1,
                'ebay_id'               => $o['ebay_id']
            );
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