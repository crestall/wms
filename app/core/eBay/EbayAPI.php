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

    protected $serverUrl;
    protected $authURL;
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
        $this->serverUrl  = 'https://api.ebay.com';
        $this->authURL = 'https://auth.ebay.com';
    }

//Background Helper Functions

    protected function sendGetRequest($s_action, $authToken)
    {
        $url = $this->serverUrl."/".$s_action;
        //die($url);
        die("authToken: ".$authToken);
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
    /* This one doesn't work


    protected function firstAuthAppToken() {
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

    
    */

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
                ), $this->line_id);
            }
        }
    }

    protected function refreshToken(array $args)
    {
        extract($args);
        $link = $this->serverUrl."/identity/v1/oauth2/token";
        $codeAuth = base64_encode($clientID.':'.$certID);
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
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=refresh_token&refresh_token=".$refreshToken."&scope=".$scope);
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
                //$this->authToken = $json["access_token"];
                $db = Database::openConnection();
                $db->updateDatabaseFields($this->table, array(
                    'access_token'      => $json['access_token'],
                    'access_expires'    => time() + $json['expires_in']
                ), $this->line_id);
                return $json['access_token'];
            }
        }
        echo "JSON<pre>",print_r($json),"</pre>";
        die("did a refresh");
        return false;
    }
}//end class