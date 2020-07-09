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
    protected $API_KEY ;
    protected $ACCOUNT_NO;


    const   API_SCHEME   = 'https://';
    const   API_BASE_URL = 'webservices.directfreight.com.au/Dispatch/api/';

    const   HEADER_EOL = "\r\n";


    public function __construct(Controller $controller)
    {
        $this->controller = $controller;


            $this->API_KEY    = Config::get('DIRECT_FREIGHT_PRICING_KEY');
            $this->ACCOUNT_NO = Config::get('DIRECT_FREIGHT_ACC_NUMBER');


        //$this->ACCOUNT_NO = 22;
    }

    protected function sendPostRequest($action, $data = array())
    {
        $url = directfreight::API_SCHEME . directfreight::API_BASE_URL . $action;
        //die($url);
        $data_string = json_encode($data);
        //die($data_string);
        $headers = array(
            'Content-Type: application/json',
            'Authorisation: '. $this->API_KEY ,
            'AccountNumber: '.$this->ACCOUNT_NO
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
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


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

    public function getTracking($consignment_id)
    {
        $response = $this->sendGetRequest('/booking/get-job-statuses?customerCode='.$this->CUSTOMER_CODE.'&trackingNumber='.$consignment_id);
        list($a_headers,$a_data) = $this->getResponse($response);
        return json_decode($a_data[0], true);
    }

    public function getQuote($data_array, $client = "Filmshot Graphics")
    {
        $fsg_address = Config::get("FSG_ADDRESS");
        $request = array(
            'SuburbFrom'            => $fsg_address['suburb'],
            'PostcodeFrom'          => $fsg_address['postcode'],
            'SuburbTo'              => $data_array['ReceiverDetails']['Suburb'],
            'PostcodeTo'            => $data_array['ReceiverDetails']['Postcode'],
            'ConsignmentLineItems'  => $data_array['ConsignmentLineItems']
        );
        $response = $this->sendPostRequest('GetConsignmentPrice/', $request);
        return $response;
    }

    public function createConsignments($data_array, $client = "FSG Printing and 3PL")
    {


        if(date('H', time()) > 14)
        {
            $new_date = strtotime( date("Y-m-d", time()).' +1 Weekday' );
            $earliest = date("Y-m-d 10:00", $new_date);
            $latest = date("Y-m-d 16:00", $new_date);
        }
        else
        {
            $h = date('H', time()) + 1;
            $earliest = date("Y-m-d $h:00", time());
            $latest = date("Y-m-d 16:00", time());
        }


        $request = array(
            'customerCode'      =>  $this->CUSTOMER_CODE,
            'reference1'        =>  $data_array['reference1'],
            'primaryService'    =>  $data_array['primaryService'],
            'connoteFormat'     =>  'Thermal',
            'stops'             =>  array(
                array(
                    "name"          =>  "3PL Plus",
                    "suburbName"    =>  $threepl_address['suburb'],
                    "addressLine1"  =>  $threepl_address['address'],
                    "addressLine2"  =>  "",
                    "postCode"      =>  $threepl_address['postcode'],
                    "state"         =>  $threepl_address['state'],
                    "instructions"  =>  "",
                    "contact"       =>  array(
                        "name"  =>  "3plplus",
                        "phone" =>  "03 8512 1444"
                    ),
                    "timeWindow" =>  array(
                        "earliest"  => $earliest,
                        "latest"    => $latest
                    )
                ),
                $data_array['to_address']
            ),
            'goods'     =>  $data_array['goods']
        );


        //echo "<pre>",print_r($request),"</pre>";die();
        //echo json_encode($request);
        /*
        $ds = date("Ymd");
    	//echo "$root/dhl_errors/error_".$ds.".txt";
    	if(!$handle = fopen("$root/logs/hunterslog_".$ds.".txt", 'a')) die('fopen error');
    	fwrite($handle, "\r\n\r\n------------- --- ----------------");
    	fwrite($handle, "\r\n\r\n Date/Time: ".date("d/m/Y, g:i:s a"));
        fwrite($handle, "\r\n".json_encode($request));
    	fwrite($handle, "\r\n".var_export($response, true));
    	fclose($handle);
        */
        $response = $this->sendPostRequest('/booking/book-job', $request);
        list($a_headers,$a_data) = $this->getResponse($response);
        return json_decode($a_data[0], true);
        //return $request;
    }

    public function getDetails($od, $items)
    {
        $ci = $this->controller->client->getClientInfo($od['client_id']);
        $packages = $this->controller->order->getPackagesForOrder($od['id']);
        $parcels = Packaging::getPackingForOrder($od,$items,$packages);
        $delivery_instructions = (!empty($od['instructions']))? $od['instructions'] : "Please leave in a safe place out of the weather";
        if($od['signature_req'] == 1)
            $delivery_intsructions = (!empty($od['instructions']))? $od['instructions'] : "";


        $details = array(
            'ConsignmentId'         => $od['id'],
            'IsDangerousGoods'      => false
        );
        $rd = array(
            'ConsignmentId'         => $od['id'],
            'IsDangerousGoods'      => false,
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

        $details['ReceiverDetails'] = $rd;
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
        return $details;
    }

 }
 ?>