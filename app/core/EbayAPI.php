<?php

/**
 * The Ebay class.
 *
 * Interacts with the Ebay API
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

 class EbayAPI
 {
    public $userToken;
    public $controller;

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

    private $isLive = true;
    private $table = "";

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
        if($this->isLive)
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
        else
        {
            $this->table    = "ebay_access_tokens_sb";
            $this->devID    = 'beaed030-6fea-4467-aafb-2b415518d84c';
            $this->appID    = 'MarkSoll-PBAFSG-SBX-a4198d68c-6ede1508';
            $this->certID   = 'SBX-4198d68c17e5-d20b-413a-9a6a-b93e';
            $this->clientID = 'MarkSoll-PBAFSG-SBX-a4198d68c-6ede1508';
            $this->ruName   = 'Mark_Solly-MarkSoll-PBAFSG-foldelyct';
            $this->serverUrl  = 'https://api.sandbox.ebay.com';
            $this->authURL = "https://auth.sandbox.ebay.com/";
            $this->scope    = '
https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/buy.order.readonly https://api.ebay.com/oauth/api_scope/buy.guest.order https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.marketplace.insights.readonly https://api.ebay.com/oauth/api_scope/commerce.catalog.readonly https://api.ebay.com/oauth/api_scope/buy.shopping.cart https://api.ebay.com/oauth/api_scope/buy.offer.auction https://api.ebay.com/oauth/api_scope/commerce.identity.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.email.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.phone.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.address.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.name.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.status.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.item.draft https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/sell.item https://api.ebay.com/oauth/api_scope/sell.reputation https://api.ebay.com/oauth/api_scope/sell.reputation.readonly';
        }
    }

//Publicly Callable Functions
    public function getCurrentOrders()
    {
        $s_action = "sell/fulfillment/v1/order?filter=orderfulfillmentstatus:%7BNOT_STARTED%7CIN_PROGRESS%7D";
        $response = $this->sendGetRequest($s_action);
        return json_decode($response, true);
    }




//Background Helper Functions

    protected function sendGetRequest($s_action)
    {
        $url = $this->serverUrl."/".$s_action;
        //die($url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        $codeAuth = base64_encode($this->authToken);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer '.$this->authToken
        ));
        $result = curl_exec($ch);
        $err = curl_error($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        if ($err)
        {
            die('Could not write to eBay API '.$err);
        }
        else
        {
            return $result;
        }
    }

//Authorisation Functions
    public function init()
    {
        $db = Database::openConnection();
        $access_tokens = $db->queryByID($this->table, 1) ;
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
                $this->authorizationToken();
            }
            elseif( time() >= $access_tokens['access_expires'] )
            {
                $this->refreshToken();
            }
        }
        //die( "current: ".time()." expires: ".$access_tokens['access_expires']);
        $this->paypalEmailAddress= 'PAYPAL_EMAIL_ADDRESS';
    }

    private function firstAuthAppToken() {
        $db = Database::openConnection();

        $url = $this->authURL."/oauth2/authorize?client_id=".$this->clientID."&response_type=code&redirect_uri=".$this->ruName."&scope=".$this->scope;

        //$response = file_get_contents($url);
        die($url);

        $url_components = parse_url($url);
        parse_str($url_components['query'], $params);
        $this->authCode = $params['code'];
        $db->updateDatabaseFields($this->table, array(
            'code'              => $params['code'],
            'access_expires'    => time() + $params['expires_in'],
            'refresh_expires'    => time() + 60*60*24*365.25*1.5 //18 months
        ), 1);
    }

    public function authorizationToken()
    {
        $db = Database::openConnection();
        $link = $this->serverUrl."/identity/v1/oauth2/token";
        $codeAuth = base64_encode($this->clientID.':'.$this->certID);
        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic '.$codeAuth
        ));
        //curl_setopt($ch, CURLHEADER_SEPARATE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=authorization_code&code=".$this->authCode."&redirect_uri=".$this->ruName);
        $response = curl_exec($ch);
        $json = json_decode($response, true);
        //echo "<pre>",print_r($json),"</pre>"; die();
        $info = curl_getinfo($ch);
        curl_close($ch);
        if($json != null)
        {
            if(isset($json['error']))
            {
                echo "<pre>",print_r($json),"</pre>";
                die("ebay token error");
            }
            else
            {
                $this->authToken = $json["access_token"];
                $this->refreshToken = $json["refresh_token"];
                $db->updateDatabaseFields($this->table, array(
                    'access_token'      => $json['access_token'],
                    'access_expires'    => time() + $json['expires_in'],
                    'refresh_token'     => $json['refresh_token'],
                    'refresh_expires'   => time() + $json['refresh_token_expires_in']
                ), 1);
            }
        }
    }

    public function refreshToken()
    {
        $link = $this->serverUrl."/identity/v1/oauth2/token";
        $codeAuth = base64_encode($this->clientID.':'.$this->certID);
        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic '.$codeAuth
        ));
        //echo $this->refreshToken;
        //curl_setopt($ch, CURLHEADER_SEPARATE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=refresh_token&refresh_token=".$this->refreshToken."&scope=https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/commerce.identity.readonly");
        $response = curl_exec($ch);
        $json = json_decode($response, true);
        $info = curl_getinfo($ch);
        curl_close($ch);
        if($json != null)
        {
            if(isset($json['error']))
            {
                echo "<pre>",print_r($json),"</pre>";
                die("ebay token error");
            }
            else
            {
                $this->authToken = $json["access_token"];
                $db = Database::openConnection();
                $db->updateDatabaseFields($this->table, array(
                    'access_token'      => $json['access_token'],
                    'access_expires'    => time() + $json['expires_in']
                ), 1);
            }
        }
        //echo "<pre>",print_r($json),"</pre>";
        //die("did a refresh");
    }
}//end class