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
    protected $serverUrl;
    protected $authURL;
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
        $this->serverUrl  = 'https://api.ebay.com';
        $this->authURL = 'https://auth.ebay.com';
        $this->scope    = '
https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/commerce.identity.readonly';
    }

    public function connect()
    {
        $db = Database::openConnection();
        $this->line_id = $db->queryValue($this->table, array('client_id' => $this->client_id));
        $access_tokens = $db->queryByID($this->table, $this->line_id) ;
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
                $this->authorizationToken();//need to send an email so this works
            }
            elseif( time() >= $access_tokens['access_expires'] )
            {
                $this->refreshToken();
            }
        }
        //die( "current: ".time()." expires: ".$access_tokens['access_expires']);
        $this->paypalEmailAddress= 'PAYPAL_EMAIL_ADDRESS';
    }

    public function getCurrentOrders()
    {
        $s_action = "sell/fulfillment/v1/order?filter=orderfulfillmentstatus:%7BNOT_STARTED%7CIN_PROGRESS%7D";
        $response = $this->sendGetRequest($s_action);
        $collected_orders = json_decode($response, true);
        //return json_decode($response, true);
        $orders = $this->processOrders($collected_orders);
        return $orders;
    }

    private function processOrders($collected_orders)
    {
        return $collected_orders;
    }
 }