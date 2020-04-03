<?php

/**
 * The Eparcel class.
 *
 * The base class for all Eparcel location classes.
 * It provides reusable controller logic.
 * The extending classes can be used as part of the controller.
 *
 
 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */
 class Eparcel{

    protected $controller;
    protected $API_KEY ;
    protected $API_PWD ;
    protected $ACCOUNT_NO;

    const    API_SCHEME   = 'https://';
    const    API_HOST     = 'digitalapi.auspost.com.au';
    const    API_PORT     = 443;                            // ssl port
    const    API_BASE_URL = '/test/shipping/v1/';        // for production use, remove '/test'
    //const    API_BASE_URL = '/shipping/v1/';        // for production use, remove '/test'
    const   HEADER_EOL = "\r\n";

    private $fSock;         // socket handle


    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
        $this->API_KEY    = Config::get('API_KEY');
        $this->API_PWD    = Config::get('API_PWD');
        $this->ACCOUNT_NO = Config::get('ACCOUNT_NO');
    }

    protected function createSocket()
    {
        $i_timeout = 15;        // seconds
        if ( ($this->fSock = fsockopen( Eparcel::API_SCHEME . Eparcel::API_HOST, Eparcel::API_PORT, $errno, $errstr, $i_timeout) ) === false )
        {
            return $this->controller->error(500);
        }
    }

    protected function buildHttpHeaders($s_type,$s_action,$n_content_len = 0,$b_incl_accno = false)
    {
        $a_headers   = array();
        $a_headers[] = $s_type . ' ' . Eparcel::API_BASE_URL . $s_action . ' HTTP/1.1';
        $a_headers[] = 'Authorization: ' . 'Basic ' . base64_encode($this->API_KEY . ':' . $this->API_PWD);
        $a_headers[] = 'Host: ' . Eparcel::API_HOST;
        if ($n_content_len) {
            $a_headers[] = 'Content-Type: application/json';
            $a_headers[] = 'Content-Length: ' .
                           $n_content_len;     /* Content-Length is a mandatory header field to avoid HTTP 500 errors */
        }
        $a_headers[] = 'Accept: */*';
        if ($b_incl_accno) {
            $a_headers[] = 'Account-Number: ' . $this->ACCOUNT_NO;
        }
        $a_headers[] = 'Cache-Control: no-cache';
        $a_headers[] = 'Connection: close';
        return $a_headers;
    }

    protected function sendGetRequest($s_action)
    {
        $url = Eparcel::API_SCHEME . Eparcel::API_HOST . Eparcel::API_BASE_URL . $s_action;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        //curl_setopt($ch, CURLOPT_TIMEOUT, 30);  auspost response is really f**ken slow!
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_USERPWD, $this->API_KEY . ":" . $this->API_PWD);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic '. base64_encode($this->API_KEY . ":" . $this->API_PWD),
            'account-number: '.$this->ACCOUNT_NO)
        );
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err)
        {
            die('Could not write to Eparcel API '.$err);
        }
        else
        {
            return $result;
        }
    }

    protected function sendPostRequest($s_action, $a_data)
    {
        $data_string = json_encode($a_data);
        echo $data_string; //die();
        $url = eParcel::API_SCHEME . eParcel::API_HOST . eParcel::API_BASE_URL . $s_action;
        //echo $url;
        //echo $this->ACCOUNT_NO;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        //curl_setopt($ch, CURLOPT_TIMEOUT, 30);  auspost response is really f**ken slow!
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_USERPWD, $this->API_KEY . ":" . $this->API_PWD);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string),
            'Authorization: Basic '. base64_encode($this->API_KEY . ":" . $this->API_PWD),
            'account-number: '.$this->ACCOUNT_NO)
        );
        $result = curl_exec($ch);
        //echo "<pre>",print_r($result),"</pre>"; die();
        $err = curl_error($ch);
        curl_close($ch);
        if ($err)
        {
            die('Could not write to eParcel API '.$err);
        }
        else
        {
            return $result;
        }
    }

    protected function sendPutRequest($s_action,$a_data)
    {
       $data_string = json_encode($a_data);
        //echo $data_string;
        $url = eParcel::API_SCHEME . eParcel::API_HOST . eParcel::API_BASE_URL . $s_action;
        //echo $url;
        //echo $this->ACCOUNT_NO;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        //curl_setopt($ch, CURLOPT_TIMEOUT, 30);  auspost response is really f**ken slow!
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_USERPWD, $this->API_KEY . ":" . $this->API_PWD);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string),
            'Authorization: Basic '. base64_encode($this->API_KEY . ":" . $this->API_PWD),
            'account-number: '.$this->ACCOUNT_NO)
        );
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err)
        {
            die('Could not write to eParcel API '.$err);
        }
        else
        {
            return $result;
        }
    }

    protected function sendDeleteRequest($s_action)
    {
        $url = eParcel::API_SCHEME . eParcel::API_HOST . eParcel::API_BASE_URL . $s_action;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        //curl_setopt($ch, CURLOPT_TIMEOUT, 30);  auspost response is really f**ken slow!
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_USERPWD, $this->API_KEY . ":" . $this->API_PWD);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            //'Content-Length: ' . strlen($data_string),
            'Authorization: Basic '. base64_encode($this->API_KEY . ":" . $this->API_PWD),
            'account-number: '.$this->ACCOUNT_NO)
        );
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err)
        {
            die('Could not write to eParcel API '.$err);
        }
        else
        {
            return $result;
        }
    }

    public function GetAccountDetails()
    {
        $response = $this->sendGetRequest('accounts/' . $this->ACCOUNT_NO);
        return json_decode($response, true);
    }

    public function CreateShipments($a_shipments)
    {
        $response = $this->sendPostRequest('shipments', $a_shipments);
        print_r($response);
        return json_decode($response, true);
    }

    public function DeleteShipment($shipment_id)
    {
        $response = $this->sendDeleteRequest('shipments?shipment_ids='.$shipment_id);
        return json_decode($response, true);
    }

    public function CreateLabels($a_labels)
    {

        $response = $this->sendPostRequest('labels',$a_labels);
        return json_decode($response, true);
    }

    public function GetLabel($request_id)
    {
    	$response = $this->sendGetRequest('labels/' . $request_id);
        return json_decode($response, true);
    }

    public function GetItemPrices($a_items)
    {
        $response = $this->sendPostRequest('prices/items',$a_items);
        return json_decode($response, true);
    }

    public function CreateOrderFromShipment($a_shipmentorder)
    {
        $response = $this->sendPutRequest('orders',$a_shipmentorder);
        return json_decode($response, true);
    }

    public function UpdateShipment($a_shipment, $shipment_id)
    {
        $response = $this->sendPutRequest('shipments/'.$shipment_id, $a_shipment);
        return json_decode($response, true);
    }

    public function GetOrderSummary($order_id, $ship_id)
    {
        $today = date('YMd');
        $response = $this->sendGetRequest('accounts/' . $this->ACCOUNT_NO. '/orders/' . $order_id . '/summary');
        //file_put_contents(PUBLIC_ROOT.'eparcel_orders/order_summary'.$today.'.pdf', $response);
        $this->controller->eparcelorder->addSummary($response, $ship_id);
        //echo json_decode($response, true);
    }

    public function GetTracking($conid)
    {
        $response = $this->sendGetRequest('track/' . $conid);
        return json_decode($response, true);
    }

    public function ValidateSuburb($suburb = null, $state = null, $postcode = null)
    {
        $response = $this->sendGetRequest('address?suburb='. rawurlencode($suburb) .'&state='. rawurlencode($state) .'&postcode=' . rawurlencode($postcode));
        return json_decode($response, true);
    }

    public function GetAPIKey()
    {
        return $this->API_KEY;
    }

    public function GetAPISecret()
    {
        return $this->API_PWD;
    }

    public function GetShipments($from = 0, $to = 200, $params = array())
    {
        $query_string = 'offset='.$from.'&number_of_shipments='.$to;
        foreach($params as $key => $value)
        {
            $query_string .= '&'.$key.'='.$value;
        }
        $response = $this->sendGetRequest('shipments?'.$query_string);
        return json_decode($response, true);
    }

    public function GetShipment($id)
    {
        $response = $this->sendGetRequest('shipments/'.$id);
        return json_decode($response, true);
    }

    public function GetQuote($a_shipments)
    {
        $response = $this->sendPostRequest('prices/shipments', $a_shipments);
        return json_decode($response, true);
    }

    public function getShipmentDetails($od, $items, $use_express = false)
    {
        $express = ($od['eparcel_express'] == 1);

        if(!$express)
        {
            $express = $use_express;
        }
        $order_id = $od['id'];
        $ad = array(
            'address'   =>  $od['address'],
            'address_2' =>  $od['address_2'],
            'state'     =>  $od['state'],
            'suburb'    =>  $od['suburb'],
            'postcode'  =>  $od['postcode'],
            'country'   =>  $od['country'],
            'phone'     =>  $od['contact_phone']
        );
        //$items = $this->controller->order->getItemsForOrder($order_id);
        $delivery_instructions = (!empty($od['instructions']))? $od['instructions'] : "Please leave in a safe place out of the weather";
        if(empty($od['ref_1']))
        {
            $ref_1 = strtoupper(str_replace(" ", "", $this->controller->client->getClientName($od['client_id'])));
        }
        else
        {
            $ref_1 = $od['ref_1'];
        }
        if($od['signature_req'] == 1)
            $delivery_instructions = (!empty($od['instructions']))? $od['instructions'] : "";
        $shipment = array(
            'shipment_reference'		=> 	$order_id,
            'email_tracking_enabled'	=>	!is_null($od['tracking_email']),
            'from'						=>	array(),
            'to'						=>	array(),
            'items'						=>	array(),
            "sender_references"			=>	array($ref_1, $od['order_number']),

        );
        $shipment['to'] = array(
    		'name'	   				=>	$od['ship_to'],
    		'lines'					=>	array(),
    		'suburb'				=>	trim($od['suburb']),
    		'postcode'				=>	trim($od['postcode']),
    		'state'					=>	trim($od['state']),
            'country'				=>	trim($od['country']),
            'delivery_instructions'	=>	$delivery_instructions
    	);
        if(!empty($od['company_name'])) $shipment['to']['business_name'] = $od['company_name'];
        if(!empty($od['tracking_email'])) $shipment['to']['email'] = $od['tracking_email'];
        if(!empty($od['contact_phone'])) $shipment['to']['phone'] = $od['contact_phone'];
        $shipment['to']['lines'][] = $od['address'];
        if(!empty($od['address_2'])) $shipment['to']['lines'][] = $od['address_2'];
        $threepl_address = Config::get("THREEPL_ADDRESS");
        $shipment['from'] = array(
            'name'      =>  '3PLPLUS',
            'lines'		=>	array($threepl_address['address']),
            'suburb'	=>	$threepl_address['suburb'],
            'postcode'	=>	$threepl_address['postcode'],
            'state'		=>	$threepl_address['state'],
            'country'	=>  $threepl_address['country']
        );
        $packages = $this->controller->order->getPackagesForOrder($order_id);
        $weight = 0;
        $array = array();
        if($ad['country'] == "AU")
        {
           	$array['authority_to_leave'] = ($od['signature_req'] == 0);
        }
        else
        {
            $array['commercial_value'] = false;
            $array['classification_type'] = 'GIFT';
            if($ad['country'] == "CA")
                $array['classification_type'] = 'SAMPLE';
        }
        $val = 0;
        foreach($items as $i)
        {
            $ival = ($i['price'] == 0)? $i['qty'] : $i['price'] * $i['qty'];
            if($od['client_id'] == 6 && $ad['country'] != "AU")
                    $ival = $i['qty'] * 1.81;
            $val += $ival;
        }
        $parcels = Packaging::getPackingForOrder($od,$items,$packages, $val);
        foreach($parcels as $p)
        {
            $array['item_reference'] = $p['item_reference'];
            $array['product_id'] = $this->getEparcelChargeCode($ad, $p['weight'], $express);
            $array['width'] = $p['width'];
            $array['height'] = $p['height'];
            $array['length'] = $p['depth'];
            $array['weight'] = $p['weight'];
            $array['item_contents'] = array();
            if($ad['country'] != "AU")
            {
                $pval = round($val/count($parcels), 2);
                if(empty($this->controller->client->getProductsDescription($od['client_id'])))
                {
                    if(empty($items[0]['description']))
                    {
                        $description = $items[0]['name'];
                    }
                    else
                    {
                        $description = $items[0]['description'];
                    }
                }
                else
                {
                    $description = $this->controller->client->getProductsDescription($od['client_id']);
                }
                $array['item_contents'][] = array(
                    'description'   =>  $description,
                    'value'         =>  $pval,
                    'quantity'      =>  1
                );
            }
            $shipment['items'][] = $array;

        }

        return $shipment;
    }

     /*
    public function getShipmentDetails($od, $cd = false, $cld = false, $picked = true, $use_express = false)
    {
        else
        {
            $weight = 0;
            $array = array();
            if($ad['country'] == "AU")
            {
               	$array['authority_to_leave'] = ($od['signature_req'] == 0);
            }
            else
            {
                $array['commercial_value'] = false;
                $array['classification_type'] = 'GIFT';
            }
            foreach($items as $i)
            {
                if($i['hunters_goods_type'] == 20)
                {
                    $do_satchels = true;
                    $description = (empty($i['description']))? $i['name']: $i['description'];
                    $description = mb_strimwidth( $description , 0 , 40 ); //auspost will not allow this to be more than 40 characters
                    $val = ($i['price'] == 0)? 1.00 : $i['price'];
                    $weight += $i['weight'];
                    if( !empty($i['satchel_large']) )  $large_satchels += $i['satchel_large'];
                    if( !empty($i['satchel_small']) )  $small_satchels += $i['satchel_small'];
                    continue;
                }
                else
                {
                    $array['product_id'] = getEparcelChargeCode($ad, $i['weight'], $express);
                    $array['width'] = $i['width'];
                    $array['height'] = $i['height'];
                    $array['length'] = $i['depth'];
                    $array['weight'] = $i['weight'];

                }
                $description = (empty($i['description']))? $i['name']: $i['description'];
                $description = mb_strimwidth( $description , 0 , 40 ); //auspost will not allow this to be more than 40 characters
                if($ad['country'] != "AU")
                {
                    $val = ($i['price'] == 0)? 1.00 : $i['price'];
                    $array['item_contents'][] = array(
                        'description'	=>  $description,
                        'quantity'		=>	$i['qty'],
                        'value'			=>	$val,
                        //'value'			=>	$i['price'] * $i['qty'],
                        //'weight'		=>	$weight,
                    );

                }
                $array['item_reference'] = $i['item_id'];
                $shipment['items'][] = $array;
            }

        }
        if($do_satchels)
        {
            $array = array();
            if($ad['country'] == "AU")
            {
               	$array['authority_to_leave'] = ($od['signature_req'] == 0);
            }
            else
            {
                $array['commercial_value'] = false;
                $array['classification_type'] = 'GIFT';
            }
            $whole_small = ceil($small_satchels);

            if($whole_small > 1)
            {
                $large_satchels += floor($whole_small / 2);
                $whole_small = $whole_small % 2;
            }
            $whole_large = ceil($large_satchels);
            $large_space = round($whole_large - $large_stachels, 1, PHP_ROUND_HALF_DOWN);
            if($large_space >= 0.5)
            {
                --$whole_small;
                $small_stachels = $whole_small > 0;
            }
            if($large_satchels)
            {
                $array['product_id'] = getEparcelChargeCode($ad, $weight, $express);
                $array['width'] = 43;
                $array['height'] = 32;
                $array['length'] = 14;
                $array['weight'] = $weight;
                if($ad['country'] != "AU")
                {
                    $array['item_contents'][] = array(
                		'description'	=>  $description,
                		'quantity'		=>  1,
                		'value'			=>	$val
                	);
                }

                $shipment['items'][] = $array;
            }
            if($small_satchels)
            {
                $array['product_id'] = getEparcelChargeCode($ad, $weight, $express);
                $array['width'] = 23;
                $array['height'] = 34;
                $array['length'] = 8;
                $array['weight'] = $weight;
                if($ad['country'] != "AU")
                {
                    $array['item_contents'][] = array(
                		'description'	=>  $description,
                		'quantity'		=>  1,
                		'value'			=>	$val
                	);
                }
                $shipment['items'][] = $array;
            }

        }
        return $shipment;
    }
    */

    protected function getEparcelChargeCode($ad, $weight = 0, $expresspost = false)
    {
        return "7E55";
        $pti8_countries = array(
            "BE",
            "CA",
            "CN",
            "HR",
            "DK",
            "EE",
            "FR",
            "DE",
            "HK",
            "HU",
            "IE",
            "IL",
            "LT",
            "MY",
            "MT",
            "NL",
            "NZ",
            "PL",
            "PT",
            "SG",
            "SI",
            "ES",
            "SE",
            "GB",
            "US"
        );
        if($ad['country'] == "AU")
        {
            if($expresspost) return '7J85';
            return '7D85';
        }
        else
        {
            //if( $weight > 22 || !in_array($ad['country'], $pti8_countries) )
            if( $weight > 22 )
            {
                return 'ECM8';
                //return 'AIR8';
            }
            else
            {
                return 'PTI8';
            }
        }
    }
}
