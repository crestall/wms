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
    protected $table;
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
        $this->table    = "ebay_access_tokens";
    }

    public function getCurrentOrders(){}
    public function fulfillAnOrder($ebay_order_id, $items, $carrier_code, $consignment_id){}

//Background Helper Functions
    protected function sendPostRequest($s_action, $authToken, $aData = array())
    {
        $data_string = json_encode($aData);
        //die($data_string);
        $url = $this->serverUrl."/".$s_action;
        //die($url);
        //die("authToken: ".$authToken);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        $codeAuth = base64_encode($authToken);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string),
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

    protected function procOrders($collected_orders)
    {
        $orders = array();
        $the_orders = $collected_orders['orders'];
        //echo "<pre>",print_r($the_orders),"</pre>"; //die();
        if(count($the_orders))
        {
            $allocations = array();
            $orders_items = array();
            foreach($the_orders as $i => $o)
            {
                if($o['orderPaymentStatus'] == "FULLY_REFUNDED")
                    continue;
                //echo "Order with index $i<pre>",print_r($o),"</pre>";
                $items_errors = false;
                $weight = 0;
                $mm = "";
                $items = array();
                //$o = trimArray($o);
                $email = ( isset($o['fulfillmentStartInstructions'][0]['shippingStep']['shipTo']['email']) )? $o['fulfillmentStartInstructions'][0]['shippingStep']['shipTo']['email'] : NULL;
                $phone = ( isset($o['fulfillmentStartInstructions'][0]['shippingStep']['shipTo']['primaryPhone']['phoneNumber']) )? $o['fulfillmentStartInstructions'][0]['shippingStep']['shipTo']['primaryPhone']['phoneNumber'] : NULL;
                $order = array(
                    'error_string'          => '',
                    'items'                 => array(),
                    'ref2'                  => '',
                    'client_order_id'       => $o['salesRecordReference'],
                    'errors'                => 0,
                    'tracking_email'        => $email,
                    'ship_to'               => $o['fulfillmentStartInstructions'][0]['shippingStep']['shipTo']['fullName'],
                    'date_ordered'          => strtotime( $o['creationDate'] ),
                    'status_id'             => $this->controller->order->ordered_id,
                    'eparcel_express'       => 0,
                    'signature_req'         => 0,
                    'contact_phone'         => $phone,
                    'items_errors'          => false,
                    'items_errors_string'   => '<ul>',
                    'is_ebay'               => 1,
                    'ebay_id'               => $o['orderId']
                );
                if( !filter_var($email, FILTER_VALIDATE_EMAIL) )
                {
                    $order['errors'] = 1;
                    $order['error_string'] = "<p>The customer email is not valid</p>";
                }
                //validate address
                //ebay are a pack!!!
                $state = isset($o['fulfillmentStartInstructions'][0]['shippingStep']['shipTo']['contactAddress']['stateOrProvince'])?
                    $o['fulfillmentStartInstructions'][0]['shippingStep']['shipTo']['contactAddress']['stateOrProvince']:
                    "";
                $ad = array(
                    'address'   => $o['fulfillmentStartInstructions'][0]['shippingStep']['shipTo']['contactAddress']['addressLine1'],
                    'address_2' => NULL,
                    'suburb'    => $o['fulfillmentStartInstructions'][0]['shippingStep']['shipTo']['contactAddress']['city'],
                    'state'     => $state,
                    'postcode'  => $o['fulfillmentStartInstructions'][0]['shippingStep']['shipTo']['contactAddress']['postalCode'],
                    'country'   => $o['fulfillmentStartInstructions'][0]['shippingStep']['shipTo']['contactAddress']['countryCode']
                );
                if( isset($o['fulfillmentStartInstructions'][0]['shippingStep']['shipTo']['contactAddress']['addressLine2']) )
                    $ad['address_2'] = $o['fulfillmentStartInstructions'][0]['shippingStep']['shipTo']['contactAddress']['addressLine2'];
                //echo "The address array<pre>",print_r($ad),"</pre>";
                if($ad['country'] == "AU")
                {
                    if(strlen($ad['address']) > 40 || strlen($ad['address_2']) > 40)
                    {
                        $order['errors'] = 1;
                        $order['error_string'] .= "<p>Addresses cannot have more than 40 characters</p>";
                    }
                    //echo "<p>------------------------------------------------</p>";continue;
                    $aResponse = $this->controller->Eparcel->ValidateSuburb($ad['suburb'], $ad['state'], str_pad($ad['postcode'],4,'0',STR_PAD_LEFT));

                    if(isset($aResponse['errors']))
                    {
                        $order['errors'] = 1;
                        foreach($aResponse['errors'] as $e)
                        {
                            $order['error_string'] .= "<p>{$e['message']}</p>";
                        }
                    }
                    elseif($aResponse['found'] === false)
                    {
                        $order['errors'] = 1;
                        $order['error_string'] .= "<p>Postcode does not match suburb or state</p>";
                    }
                }
                else
                {
                    if( strlen( $ad['address'] ) > 50 || strlen( $ad['address_2'] ) > 50 )
                    {
                        $order['errors'] = 1;
                        $order['error_string'] .= "<p>International addresses cannot have more than 50 characters</p>";
                    }
                    if( strlen($order['ship_to']) > 30 )
                    {
                        $order['errors'] = 1;
                        $order['error_string'] .= "<p>International names and company names cannot have more than 30 characters</p>";
                    }
                }
                if(!preg_match("/(?:[A-Za-z].*?\d|\d.*?[A-Za-z])/i", $ad['address']) && (!preg_match("/(?:care of)|(c\/o)|( co )/i", $ad['address'])))
                {
                    $order['errors'] = 1;
                    $order['error_string'] .= "<p>The address is missing either a number or a word</p>";
                }
                //$order['sort_order'] = ($ad['country'] == "AU")? 2:1;
                $qty = 0;
                foreach($o['lineItems'] as $item)
                {
                    $sku = ( isset($item['sku']) )? $item['sku'] : NULL;
                    $product = $this->controller->item->getItemBySku($sku);
                    if(!$product)
                    {
                        $order['items_errors'] = true;
                        $items_errors = true;
                        $is = (empty($item['sku']))? "NO SKU SENT" : $item['sku'];
                        $mm .= "<li>Could not find {$item['title']} in WMS based on $is</li>";
                        $order['items_errors_string'] .= "<li>Could not find {$item['title']} in WMS based on {$is}</li>";
                    }
                    else
                    {
                        $n_name = $product['name'];
                        $item_id = $product['id'];
                        $items[] = array(
                            'qty'           =>  $item['quantity'],
                            'id'            =>  $item_id,
                            'ebay_line_item_id'  => $item['lineItemId'],
                            'whole_pallet'  => false
                        );
                        $qty += $item['quantity'];
                        $weight += $product['weight'] * $item['quantity'];
                    }

                }
                $order['instructions'] = "Please leave in a safe place out of the weather";
                if( isset($o['buyerCheckoutNotes']) && !empty($o['buyerCheckoutNotes']) )
                    $order['instructions'] = $o['buyerCheckoutNotes'];
                $order['items_errors_string'] .= "</ul>";
                if($items_errors)
                {
                    $message = "<p>There was a problem with some items</p>";
                    $message .= "<ul>".$mm."</ul>";
                    $message .= "<p>Orders with these items will not be processed at the moment</p>";
                    $message .= "<p>Client Order ID: {$order['client_order_id']}</p>";
                    $message .= "<p>Customer: {$order['ship_to']}</p>";
                    $message .= "<p>Address: {$ad['address']}</p>";
                    $message .= "<p>{$ad['address_2']}</p>";
                    $message .= "<p>{$ad['suburb']}</p>";
                    $message .= "<p>{$ad['state']}</p>";
                    $message .= "<p>{$ad['postcode']}</p>";
                    $message .= "<p>{$ad['country']}</p>";
                    if ($this->ua == "CRON" && SITE_LIVE )
                    {
                        Email::sendPBAImportError($message);
                        $this->return_array['error_string'] .= $message;
                        ++$this->return_array['error_count'];
                        $this->return_array['error_orders'][] = $order['client_order_id'];
                    }
                    else
                    {
                        $this->return_array['error_string'] .= $message;
                        ++$this->return_array['error_count'];
                        $this->return_array['error_orders'][] = $order['client_order_id'];
                    }
                }
                else
                {
                    $order['quantity'] = $qty;
                    $order['weight'] = $weight;
                    $order['items'][$o['salesRecordReference']] = $items;
                    $orders_items[$o['salesRecordReference']] = $items;
                    $order = array_merge($order, $ad);
                    $orders[] = $order;
                }
            }//endforeach order
            //die("Endforeach");
            $orders['orders_items'] = $orders_items;
            $this->output .= "===========================   Gonna send em back  =========================".PHP_EOL;
            return $orders;
        }//end if count orders
        else
        {
            $this->output .= "=========================================================================================================".PHP_EOL;
            $this->output .= "No New Orders";
            $this->output .= "=========================================================================================================".PHP_EOL;
        }
        return false;
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
            'refresh_expires'   => time() + $param['refresh_token_expires_in']  //time() + 60*60*24*365.25*1.5 //18 months
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
        //echo "This is the JSON<pre>",print_r($json),"</pre>"; die();
        //email the JSON so we know
        Email::sendPBAEbayNeedsUpdate($json);
        die();




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

    protected function refreshTokens(array $args)
    {
       //echo "ARGS<pre>",print_r($args),"</pre>";
        extract($args);
        //ebay are a PACK!!!!!!!!! url encoding is the key to making it work!!!!! Not mentioned in the docs anywhere!!!!!
        $scope = urlencode($scope);

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

    protected function sendItemErrorEmail($args)
    {
        $defaults = array(
            'import_error'  => false,
            'import_error_string'   => '',
            'item_error'            => false,
            'item_error_string'     => '',
            'items_errors'          => false,
            'items_errors_string'   => '',
            'email_function'        => false
        );
        $args = array_merge($defaults, $args);
        //echo "<pre>",print_r($args),"</pre>";die();
        extract($args);
        if( !$email_function )
            return;
        $message = "<p>There was a problem with some items</p>";
        if($import_error)
            $message .= $import_error_string;
        if($item_error)
            $message .= $item_error_string;
        if($items_errors)
            $message .= $items_errors_string;
        $message .= "<p>Orders with these items will not be processed at the moment</p>";
        $message .= "<p>Order ID: {$od['client_order_id']}</p>";
        $message .= "<p>Customer: {$od['ship_to']}</p>";
        $message .= "<p>Address: {$od['address']}</p>";
        $message .= "<p>{$od['address_2']}</p>";
        $message .= "<p>{$od['suburb']}</p>";
        $message .= "<p>{$od['state']}</p>";
        $message .= "<p>{$od['postcode']}</p>";
        $message .= "<p>{$od['country']}</p>";

        //echo "<pre>",print_r($args),"</pre>";
        //echo "<p>$message</p>";
        //die();
        if(isset($send_no_message))
           return $message;
        Email::{$email_function}($message);
        return true;
    }

}//end class