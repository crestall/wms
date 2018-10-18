<?php
/**
 * The Hunters class.
 *
 * Manages interactions with the HuntersExpress API.
 * It provides reusable controller logic.
 * The extending classes can be used as part of the controller.

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */
 class Hunters{
    protected $controller;
    protected $USER_NAME;
    protected $PWD;
    protected $API_HOST;
    protected $CUSTOMER_CODE;
    protected $curl_options;
    protected $sandbox = false;

    const   API_SCHEME   = 'https://';
    const   API_BASE_URL = 'rest/hxws';

    const   HEADER_EOL = "\r\n";


    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
    }

    protected function sendPostRequest($action, $data = array())
    {
        $url = hunters::API_SCHEME . $this->API_HOST . hunters::API_BASE_URL . $action;
        $data_string = json_encode($data);
        $ch = curl_init();
        curl_setopt_array ( $ch, $this->curl_options );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err)
        {
            die('Could not write to Hunters API '.$err);
        }
        else
        {
            return $result;
        }
    }

    protected function sendGetRequest($action)
    {
        $url = hunters::API_SCHEME . $this->API_HOST . hunters::API_BASE_URL . $action;
        $ch = curl_init();
        curl_setopt_array ( $ch, $this->curl_options );
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err)
        {
            die('Could not write to Hunters API '.$err);
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

    public function getQuote($data_array, $client = "3PL Plus")
    {
        $threepl_address = Config::get("THREEPL_ADDRESS");
        $request = array(
            'customerCode'      =>  $this->CUSTOMER_CODE,
            'fromLocation'      =>  array(
                "suburbName"    =>  $threepl_address['suburb'],
                "postCode"      =>  $threepl_address['postcode'],
                "state"         =>  $threepl_address['state']
            ),
            'toLocation'        =>  array(
                "suburbName"    =>  $data_array['to_address']['suburbName'],
                "postCode"      =>  $data_array['to_address']['postCode'],
                "state"         =>  $data_array['to_address']['state']
            ),
            'goods'             =>  $data_array['goods']
        );
        //echo "<pre>",print_r($request),"</pre>";
        //echo json_encode($request);
        $response = $this->sendPostRequest('/quote/get-quote', $request);
        list($a_headers,$a_data) = $this->getResponse($response);
        //echo "<pre>",print_r($a_data),"</pre>";
        //json_decode($a_date[0], true);
        return json_decode($a_data[0], true);
        //return $request;
    }

    public function bookJob($data_array, $client = "3PL Plus")
    {
        $threepl_address = Config::get("THREEPL_ADDRESS");

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
        $contact = $od['ship_to'];
        $phone = $od['contact_phone'];
        if(empty($od['contact_phone']))
        {
            $phone =  "03 8512 1444";
            $contact = "3plplus";
        }
        $order_id = $od['id'];

        $ad = array(
            'address'   =>  $od['address'],
            'address_2' =>  $od['address_2'],
            'state'     =>  $od['state'],
            'suburb'    =>  $od['suburb'],
            'postcode'  =>  $od['postcode'],
            'country'   =>  $od['country'],
            'phone'     =>  $phone
        );
        $ref = (empty($od['ref_1']))? strtoupper(str_replace(" ", "", $this->controller->client->getClientName($od['client_id']))) : $od['ref_1'];
        $delivery_instructions = (!empty($od['instructions']))? $od['instructions'] : "Please leave in a safe place out of the weather";
        if($od['signature_req'] == 1)
            $delivery_intsructions = (!empty($od['instructions']))? $od['instructions'] : "";
        $details = array(
            'reference1'        =>  $ref,
            'primaryService'    =>  'RF',
            'to_address'    =>  array(
                'name'          =>  $od['ship_to'],
                'suburbName'    =>  $ad['suburb'],
                'addressLine1'  =>  $ad['address'],
                'addressLine2'  =>  $ad['address_2'],
                'postCode'      =>  $ad['postcode'],
                'state'         =>  $ad['state'],
                'instructions'  =>  $delivery_instructions,
                'contact'       =>  array(
                    "name"  =>  $contact,
                    "phone" =>  $phone
                )
            )
        );
        $val = 0;
        foreach($items as $i)
        {
            $ival = ($i['price'] == 0)? $i['qty'] : $i['price'] * $i['qty'];
            if($od['client_id'] == 6 && $ad['country'] != "AU")
                    $ival = $i['qty'] * 1.81;
            $val += $ival;
        }
        $packages = $this->controller->order->getPackagesForOrder($order_id);
        $parcels = Packaging::getPackingForOrder($od,$items,$packages, $val);
        $array = array();
        foreach($parcels as $p)
        {
            $array['typeCode'] = $p['type_code'];
            $array['pieces'] = $p['pieces'];
            $array['width'] = $p['width'];
            $array['height'] = $p['height'];
            $array['depth'] = $p['depth'];
            $array['weight'] = ceil($p['weight']);
            $details['goods'][] = $array;
        }
        return $details;
    }

 }
 ?>