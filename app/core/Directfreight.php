<?php
/**
 * The Directfreight class.
 *
 * Manages interactions with the Direct Freight API.
 * It provides reusable controller logic.
 * The extending classes can be used as part of the controller.

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */
 class Directfreight{
    protected $controller;
    protected $USER_NAME;
    protected $PWD;
    protected $API_HOST;
    protected $CUSTOMER_CODE;
    protected $curl_options;
    protected $test = true;
    protected $PRICING_KEY;
    protected $CONSIGNMENT_KEY;
    protected $GENERAL_KEY;
    protected $ACCOUNT_NO;
    protected $SITE_ID;


    const   API_SCHEME   = 'https://';
    const   API_BASE_URL = 'webservices.directfreight.com.au/Dispatch/api/';

    const   HEADER_EOL = "\r\n";


    public function __construct(Controller $controller)
    {
        $this->controller = $controller;

        if($this->test)
        {
            $this->CONSIGNMENT_KEY = "DAB85BAB-F8F5-4B93-9290-C7EEA012B176";
            $this->PRICING_KEY = "977998B6-48FB-4AB0-8D4D-AEB641906C0E";
            $this->GENERAL_KEY = "26D189FD-FDAF-4C79-95F2-5042A3CD9097";
            $this->ACCOUNT_NO = "21483";
            $this->SITE_ID = "1548";
        }
        else
        {
            $this->CONSIGNMENT_KEY = Config::get('DIRECT_FREIGHT_CONSIGNMENT_KEY');
            $this->PRICING_KEY = Config::get('DIRECT_FREIGHT_PRICING_KEY');
            $this->GENERAL_KEY = Config::get('DIRECT_FREIGHT_GENERAL_KEY');
            $this->ACCOUNT_NO = Config::get('DIRECT_FREIGHT_ACC_NUMBER');
            $this->SITE_ID = Config::get('DIRECT_FREIGHT_SITE_ID');
        }
        //$this->ACCOUNT_NO = 22;
    }

    protected function sendPostRequest($action, $data = array(), $area = "PRICING")
    {
        $url = directfreight::API_SCHEME . directfreight::API_BASE_URL . $action;
        $key = $this->{$area."_KEY"};
        $data_string = json_encode($data);

        require_once '/usr/share/pear/HTTP/Request2.php';
        //echo "got the file";
        $request = new HTTP_Request2();
        $request->setUrl($url);
        $request->setMethod(HTTP_Request2::METHOD_POST);
        $request->setConfig(array(
            'follow_redirects' => TRUE
        ));
        $request->setHeader(array(
            'Content-Type: application/json',
            'Authorisation: '. $key ,
            'AccountNumber: '.$this->ACCOUNT_NO,
            'SiteId: '.$this->SITE_ID
        ));
        $request->setBody($data_string);
        try
        {
            $response = $request->send();
            if ($response->getStatus() == 200)
            {
                echo 'ok call :'.$response->getBody();
            }
            else
            {
                echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
                $response->getReasonPhrase();
            }
        }
        catch(HTTP_Request2_Exception $e)
        {
            echo 'Error: ' . $e->getMessage();
        }
    }

    protected function sendGetRequest($action)
    {
        $url = directfreight::API_SCHEME . $this->API_HOST . directfreight::API_BASE_URL . $action;
        $ch = curl_init();
        curl_setopt_array ( $ch, $this->curl_options );
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err)
        {
            die('Could not write to Driect Freight API '.$err);
        }
        else
        {
            return $result;
        }
    }

    protected function getResponse($response)
    {
        $a_hdrs = $a_data = array();
        $b_in_hdrs = true;
        $lines = explode("\r\n", $response);
        foreach($lines as $line)
        {
            if ($b_in_hdrs)
            {
                $line = trim($line);
                if ($line == '')
                {
                    $b_in_hdrs = false;
                }
                else
                {
                    $a_hdrs[] = $line;
                }
            }
            else
            {
                $a_data[] = $line;
            }
        }
        return array($a_hdrs,$a_data);
    }

    public function getQuote($data_array, $client = "Filmshot Graphics")
    {
        $fsg_address = Config::get("FSG_ADDRESS");
        $request = array(
            'SuburbFrom'            => $fsg_address['suburb'],
            'PostcodeFrom'          => $fsg_address['postcode'],
            'SuburbTo'              => $data_array['ConsignmentList'][0]['ReceiverDetails']['Suburb'],
            'PostcodeTo'            => $data_array['ConsignmentList'][0]['ReceiverDetails']['Postcode'],
            'ConsignmentLineItems'  => $data_array['ConsignmentList'][0]['ConsignmentLineItems']
        );
        $response = $this->sendPostRequest('GetConsignmentPrice/', $request, "PRICING");
        return $response;
    }

    public function createConsignment($details)
    {
        $response = $this->sendPostRequest('GetConsignmentPrice/', $details, "CONSIGNMENT");
        return $response;

    }

    public function getDetails($od, $items)
    {
        $ci = $this->controller->client->getClientInfo($od['client_id']);
        $details = array(
            'ConsignmentId'     => $od['id'],
            'CustomerReference' => $ci['products_description'],
            'IsDangerousGoods'  => false
        );
        $delivery_instructions = (!empty($od['instructions']))? $od['instructions'] : "Please leave in a safe place out of the weather";
        if($od['signature_req'] == 1)
            $delivery_intsructions = "";

        $details['ReceiverDetails'] = array(
            'ReceiverName'          => $od['ship_to'],
            'AddressLine1'          => $od['address'],
            'AddressLine2'          => $od['address_2'],
            'Suburb'                => $od['suburb'],
            'State'                 => $od['state'],
            'Postcode'              => $od['postcode'],
            'ReceiverContactMobile' => $od['contact_phone'],
            'ReceiverContactEmail'  => $od['tracking_email'],
            'IsAuthorityToLeave'    => $od['signature_req'] == 0,
            'DeliveryInstructions'  => $delivery_instructions
        );
        $packages = $this->controller->order->getPackagesForOrder($od['id']);
        $parcels = Packaging::getPackingForOrder($od,$items,$packages);

        foreach($parcels as $p)
        {
            $array = array();
            $array['SenderLineReference'] = $p['item_reference'];
            $array['RateType'] = $p['type_code'];
            $array['Items'] = $p['pieces'];
            $array['Width'] = ceil($p['width']);
            $array['Height'] = ceil($p['height']);
            $array['Length'] = ceil($p['depth']);
            $array['KGS'] = ceil($p['weight']);
            $details['ConsignmentLineItems'][] = $array;
        }
        $consignment_list = array(
            'ConsignmentList'   => array()
        );
        $consignment_list['ConsignmentList'][] = $details;
        return $consignment_list;
    }

 }
 ?>