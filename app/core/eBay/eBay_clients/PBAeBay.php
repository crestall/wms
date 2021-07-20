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

    public function __construct()
    {
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
            //$this->addPBAOrders($orders);
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
 }