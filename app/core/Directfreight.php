<?php
/**
 * The Directfreight class.
 *
 * Manages interactions with the Direct Freight API.
 * It provides reusable controller logic.
 * The extending classes can be used as part of the controller.

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */
 class Directfreight{
    protected $controller;
    protected $USER_NAME;
    protected $PWD;
    protected $API_HOST;
    protected $CUSTOMER_CODE;
    protected $curl_options;
    protected $test = false;
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
            $this->CONSIGNMENT_KEY = "1A13554A-81D9-46B8-BDAD-B490E34B2B09";
            $this->PRICING_KEY = "977998B6-48FB-4AB0-8D4D-AEB641906C0E";
            $this->GENERAL_KEY = "BA4992DA-8C23-406C-9C8E-5B19B1EAAD73";
            $this->ACCOUNT_NO = "21483";
            $this->SITE_ID = "1548";
        }
        else
        {
            $this->CONSIGNMENT_KEY = Config::get('DIRECT_FREIGHT_CONSIGNMENT_KEY');
            $this->PRICING_KEY = Config::get('DIRECT_FREIGHT_PRICING_KEY');
            $this->GENERAL_KEY = Config::get('DIRECT_FREIGHT_GENERAL_KEY');
            $this->ACCOUNT_NO = Config::get('DIRECT_FREIGHT_ACC_NUMBER');
            $this->SITE_ID = 0;
        }
        //$this->ACCOUNT_NO = 22;
    }

    protected function sendPostRequest($action, $data = array(), $area = "PRICING")
    {
        $url = directfreight::API_SCHEME . directfreight::API_BASE_URL . $action;
        //die($url);
        $data_string = json_encode($data);
        //die($data_string);
        $key = $this->{$area."_KEY"};
        $headers = array(
            'Content-Type: application/json',
            'Authorisation: '. $key ,
            'AccountNumber: '.$this->ACCOUNT_NO,
            'SiteId: '.$this->SITE_ID
        );
        $ch = curl_init();
        /* */
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
        //curl_setopt_array ( $ch, $this->curl_options );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,  CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);


        $result = curl_exec($ch);

        if ($result === FALSE) {
            printf("cUrl error (#%d): %s<br>\n", curl_errno($ch),
                   htmlspecialchars(curl_error($ch)));
            rewind($verbose);
            $verboseLog = stream_get_contents($verbose);
            echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
            die();
        }
        $err = curl_error($ch);
        curl_close($ch);
        if ($err)
        {
            die('Could not write to Direct Freight API '.$err);
        }
        else
        {
            return $result;
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
        //echo "<pre>",print_r($details),"</pre>";die();
        $response = $this->sendPostRequest('AddConsignment/', $details, "CONSIGNMENT");
        //echo $response; die();
        //list($a_headers,$a_data) = $this->getResponse($response);
        return json_decode($response,true);
        //return $response;
    }

    public function getConsignmentCharges($con_id)
    {
        $conNoteList = array(
            "ConnoteList"   => array()
        );
        $conNoteList['ConnoteList'][] = array(
            'Connote'   => $con_id
        );
        $response = $this->sendPostRequest('GetConsignmentCharge/', $conNoteList, "CONSIGNMENT");
        return json_decode($response,true);
    }

    public function finaliseConsignment($con_id)
    {
        $conNoteList = array(
            "ConnoteList"   => array()
        );
        $conNoteList['ConnoteList'][] = array(
            'Connote'   => $con_id
        );
        $response = $this->sendPostRequest('FinaliseConsignment/', $conNoteList, "CONSIGNMENT");
        return json_decode($response,true);
    }

    public function trackConsignment($con_id)
    {
        $conNoteList = array(
            "ConnoteList"   => array()
        );
        $conNoteList['ConnoteList'][] = array(
            'Connote'   => $con_id
        );
        //return json_encode($conNoteList);
        $response = $this->sendPostRequest('TrackConsignment/', $conNoteList, "PRICING");
        //echo $response; die();
        return json_decode($response,true);
    }

    public function bookCollection($request)
    {
        $response = $this->sendPostRequest('BookPickup/', $request, "GENERAL");
        //echo $response; die();
        return json_decode($response,true);
    }

    public function getLabels($connotes, $label_print_status = "REPRINT")
    {
        $ConnoteList = array(
            "LabelPrintStatus"  => $label_print_status,
            "ConnoteList"       => $connotes
        );
        $response = $this->sendPostRequest('GetConsignmentLabel/', $ConnoteList, "CONSIGNMENT");
        return json_decode($response, true);
    }

    public function getProductionShipmentDetails($sd)
    {
        $cname = $this->controller->productionjob->getJobCustomer($sd['job_id']);
        $details = array(
            'ConsignmentId'     => $sd['job_id'],
            'CustomerReference' => $cname,
            'IsDangerousGoods'  => false
        );
        $delivery_instructions = (!empty($sd['delivery_instructions']))? $sd['delivery_instructions'] : "Please leave in a safe place out of the weather";
        if($sd['signature_required'] == 1)
            $delivery_instructions = (!empty($sd['delivery_instructions']))? $sd['delivery_instructions'] : "";
        $details['ReceiverDetails'] = array(
            'ReceiverName'          => $sd['ship_to'],
            'ReceiverContactName'   => $sd['attention'],
            'AddressLine1'          => $sd['address'],
            'Suburb'                => $sd['suburb'],
            'State'                 => $sd['state'],
            'Postcode'              => $sd['postcode'],
            'IsAuthorityToLeave'    => $sd['signature_required'] == 0,
            'DeliveryInstructions'  => $delivery_instructions
        );
        $details['ReceiverDetails']['AddressLine2'] = (!empty($sd['address_2']))? $sd['address_2'] : "";
        $details['ReceiverDetails']['ReceiverContactMobile'] = (!empty($sd['contact_phone']))? $sd['contact_phone']: "";
        $details['ReceiverDetails']['ReceiverContactEmail'] = (!empty($sd['tracking_email']))? $sd['tracking_email'] : "";
        $packages = $this->controller->productionjobsshipment->getPackagesForShipment($sd['id']);
        $parcels = Packaging::getPackingForShipment($packages);
        foreach($parcels as $p)
        {
            $array = array();
            $array['SenderLineReference'] = $od['order_number'];
            $array['RateType'] = $p['type_code'];
            $array['Items'] = $p['pieces'];
            $array['Width'] = ceil($p['width']);
            $array['Height'] = ceil($p['height']);
            $array['Length'] = ceil($p['depth']);
            $array['KGS'] = ceil($p['weight'] * $p['pieces']);
            $details['ConsignmentLineItems'][] = $array;
        }
        $consignment_list = array(
            'ConsignmentList'   => array()
        );
        $consignment_list['ConsignmentList'][] = $details;
        return $consignment_list;
    }

    public function getDetails($od, $items)
    {
        $ci = $this->controller->client->getClientInfo($od['client_id']);
        $details = array(
            'ConsignmentId'     => $od['id'],
            'CustomerReference' => $ci['client_name'],
            'IsDangerousGoods'  => false
        );
        $delivery_instructions = (!empty($od['instructions']))? $od['instructions'] : "Please leave in a safe place out of the weather";
        if($od['signature_req'] == 1)
            $delivery_intsructions = "";
        $ReceiverName = (empty($od['company_name']))? $od['ship_to']:$od['company_name'];
        $details['ReceiverDetails'] = array(
            'ReceiverName'          => $ReceiverName,
            'ReceiverContactName'   => $od['ship_to'],
            'AddressLine1'          => $od['address'],
            'Suburb'                => $od['suburb'],
            'State'                 => $od['state'],
            'Postcode'              => $od['postcode'],
            'IsAuthorityToLeave'    => $od['signature_req'] == 0,
            'DeliveryInstructions'  => $delivery_instructions
        );
        $details['ReceiverDetails']['AddressLine2'] = (!empty($od['address_2']))? $od['address_2'] : "";
        $details['ReceiverDetails']['ReceiverContactMobile'] = (!empty($od['contact_phone']))? $od['contact_phone']: "";
        $details['ReceiverDetails']['ReceiverContactEmail'] = (!empty($od['tracking_email']))? $od['tracking_email'] : "";
        $packages = $this->controller->order->getPackagesForOrder($od['id']);
        $parcels = Packaging::getPackingForOrder($od,$items,$packages);

        foreach($parcels as $p)
        {
            $array = array();
            $array['SenderLineReference'] = $od['order_number'];
            $array['RateType'] = $p['type_code'];
            $array['Items'] = $p['pieces'];
            $array['Width'] = ceil($p['width']);
            $array['Height'] = ceil($p['height']);
            $array['Length'] = ceil($p['depth']);
            $array['KGS'] = ceil($p['weight'] * $p['pieces']);
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