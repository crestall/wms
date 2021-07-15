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

    protected $serverUrl = 'https://api.ebay.com';
    protected $authURL = 'https://auth.ebay.com';
    protected $output;
    protected $return_array = array(
        'import_count'          => 0,
        'imported_orders'       => array(),
        'error_orders'          => array(),
        'import_error'          => false,
        'error'                 => false,
        'error_count'           => 0,
        'error_string'          => '',
        'import_error_string'   => ''
    );
    protected $ua;
    protected $order_items;

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
    }

//Background Helper Functions

    protected function sendGetRequest($s_action, $authToken)
    {
        $url = $this->serverUrl."/".$s_action;
        //die($url);
        //die("authToken: ".$authToken);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        $codeAuth = base64_encode($authToken);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer '.$authToken
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
    /* This one doesn't work*/


    public function firstAuthAppToken() {
        $db = Database::openConnection();

        //$url = $this->authURL."/oauth2/authorize?client_id=".$this->clientID."&response_type=code&redirect_uri=".$this->ruName."&scope=".$this->scope;

        $url = $this->authURL."/oauth2/authorize?client_id=MarkSoll-PBAFSG-PRD-5418204ca-f642538e&response_type=code&redirect_uri=Mark_Solly-MarkSoll-PBAFSG-xuwmap&scope=https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/commerce.identity.readonly";


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




    protected function authorizationToken(array $args)
    {
        extract($args);
        $db = Database::openConnection();
        $link = $this->serverUrl."/identity/v1/oauth2/token";
        $codeAuth = base64_encode($this->clientID.':'.$certID);
        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic '.$codeAuth
        ));
        //curl_setopt($ch, CURLHEADER_SEPARATE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=authorization_code&code=".$authCode."&redirect_uri=".$ruName);
        $response = curl_exec($ch);
        $json = json_decode($response, true);
        echo "<pre>",print_r($json),"</pre>"; die();
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
                ), $this->line_id);
            }
        }
    }

    protected function refreshToken(array $args)
    {
       //echo "ARGS<pre>",print_r($args),"</pre>";
        extract($args);

        //ebay are a PACK!!!!!!!!!
        $scope = html_encode($scope);


        $link = $this->serverUrl."/identity/v1/oauth2/token";
        //echo "<p>Link: $link</p>"; //die();
        $codeAuth = base64_encode($clientID.':'.$certID);
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=refresh_token&refresh_token='.$refreshToken.'&scope='.$scope,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic '.$codeAuth,
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));
        $response = curl_exec($ch);

        if ($response === FALSE) {
            printf("cUrl error (#%d): %s<br>\n", curl_errno($ch),
                   htmlspecialchars(curl_error($ch)));
            rewind($verbose);
            $verboseLog = stream_get_contents($verbose);
            echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
            die();
        }

        //echo "response<pre>",print_r($response),"</pre>"; die();
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
                $db = Database::openConnection();
                $db->updateDatabaseFields($this->table, array(
                    'access_token'      => $json['access_token'],
                    'access_expires'    => time() + $json['expires_in']
                ), $this->line_id);
                return $json['access_token'];
            }
        }
        //echo "JSON<pre>",print_r($json),"</pre>";
        //die("did a refresh");
        return false;
    }
}//end class