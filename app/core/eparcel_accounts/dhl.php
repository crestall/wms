<?php
/********************************************************************
 *
 * 		/php-includes/dhl.php
 *
 * 		Manages the DHL API functions
 *
 *********************************************************************/
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
include_once("$root/misc-functions/helper_functions.php");
//include_once("$root/php-includes/classes/Encoding.php");
class dhl
{

	private $USER_ID    		= DHL_USER_ID;
	private $PWD    			= DHL_PASSWORD;
	private $CUSTOMER_PREFIX 	= DHL_CUSTOMERPREFIX;
	private $ACCESS_TOKEN;

	const   API_SCHEME   = 'https://';
	//const   API_HOST     = 'sandbox.dhlecommerce.asia';
    const   API_HOST     = 'api.dhlecommerce.dhl.com';
    const   API_BASE_URL = '/rest';
    const   API_VERSION  = '/v2';
	const   API_SOLDTO	 = DHL_SOLDTO;
	const	API_PICKUP	 = DHL_PICKUP;
	//const    API_PORT     = 443;                            // ssl port

	const   HEADER_EOL = "\r\n";

	private $fSock;         // socket handle

	/**
	 * constructor.
	 *
	 */
	function __construct()
	{
		global $db, $session;
		$q = "SELECT token FROM DHL_access_token WHERE expires > :now";
		$t = $db->queryRow($q, array('now' => $session->time));
		$this->ACCESS_TOKEN = (empty($t) || empty($t['token']))? $this->getAccessToken(): $t['token'];
	}

	private function getAccessToken()
	{
		global $db, $session;
        $url = dhl::API_SCHEME . dhl::API_HOST . '/rest/v1/OAuth/AccessToken?clientId='.$this->USER_ID.'&password='.$this->PWD;
        //die($url);
        $ch = curl_init();
	    $headers = array(
		    'Accept: application/json',
		    'Content-Type: application/json',
	    );
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
		$err = curl_error($ch);
		curl_close($ch);
		if ($err)
		{
			throw new Exception('Could not write to DHL API '.$err);
		}
		else
		{
			$response = json_decode($result, true);
			$token = $response["accessTokenResponse"]['token'];
			$expires = $session->time + $response["accessTokenResponse"]['expires_in_seconds'];
			$values = array(
	          	'token'		=>	$token,
				'expires'	=>	$expires
			);
			$db->updateFirstDatabaseRow('DHL_access_token', $values);
			return $token;
		}
        return false;
	}

	private function sendPostRequest($action, $data = array(), $ver = dhl::API_VERSION)
	{
	    //print_r($data);
		//$data_string = json_encode(utf8ize($data));
        global $root;
        $data_string = json_encode( $data, JSON_UNESCAPED_UNICODE );
        //echo json_last_error();
        //echo "<p>Data: string: ".$data_string."</p>"; die();
        //file_put_contents("$root/data/json_request_".date("ymd").".txt", $data_string);
        if(empty($data_string))
        {
            $response['labelResponse']['bd']['responseStatus']['code'] = 500;
            $response['labelResponse']['bd']['responseStatus']['messageDetails'][0]['messageDetail'] = "Encoding error. Please resave the csv file as UTF-8";
            return json_encode($response);
        }
        $url = dhl::API_SCHEME . dhl::API_HOST . dhl::API_BASE_URL . $ver . $action;
		$ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );
        $result = curl_exec($ch);
		$err = curl_error($ch);
		curl_close($ch);
        if ($err)
		{
			die('Could not write to DHL API '.$err);
		}
		else
		{
			return $result;
		}
	}

    public function printLabels( $details, $shipper_name = "3PL Plus" )
	{
		global $session, $portal;
        $date_time = date('c', $session->time);
        $label_request = array(
			"labelRequest"	=> array(
              	"hdr"	=>	array(
                  	"messageType"		=>	"LABEL",
    				"messageDateTime"	=>	$date_time,
                    "accessToken"		=>	$this->ACCESS_TOKEN,
    				"messageVersion"	=>	"1.4",
                    "messageLanguage"   =>  "en"
    			),
    			"bd"	=>	array(
                     "pickupAccountId"	=>	dhl::API_PICKUP,
                     "soldToAccountId"	=>	dhl::API_SOLDTO,
                     "pickupAddress"	=>	array(
    				 	"name"		=>	"3PL Plus",
                        "address1"	=>	$portal->threepl_address['address'],
    					"city"		=>	$portal->threepl_address['suburb'],
    					"country"	=>	$portal->threepl_address['country'],
                        "state"     =>  $portal->threepl_address['state'],
                        "postCode"  =>  $portal->threepl_address['postcode']
    				 ),
    				 "shipperAddress"	=>	array(
                     	"name"		=>	"3PL Plus",
                        "address1"	=>	$portal->threepl_address['address'],
    					"city"		=>	$portal->threepl_address['suburb'],
    					"country"	=>	$portal->threepl_address['country'],
                        "state"     =>  $portal->threepl_address['state'],
                        "postCode"  =>  $portal->threepl_address['postcode']
    				 ),
    				 "shipmentItems"	=>	array(

                     ),
    				 "label"			=>	array(
                     	"pageSize"	=>  "400x600",
    					"format"	=>	"PNG",
    					"layout"	=>  "4x1"
                     )
    			)
			)
		);
		/* */
        $di = 0;
        foreach($details as $d)
        {
		    $shipment_id = $this->CUSTOMER_PREFIX.str_pad($d['order']['id'],8,'0',STR_PAD_LEFT);
		    $weight = round( $d['order']['weight'] );
            $label_request['labelRequest']['bd']['shipmentItems'][$di] = array(
                "consigneeAddress"		=>	array(
	                    "name"		=>	$d['customer']['name'],
						"address1"	=>	$d['customer']['address1'],
						"city"		=>	$d['customer']['city'],
						"country"	=>	$d['customer']['country'],
						"postCode"	=>	$d['customer']['postcode'],
	                    "email"		=>	$d['customer']['email'],
                        "phone"     =>  $d['customer']['phone']
            	),
            	"shipmentID"			=>  $shipment_id,
                "packageDesc"			=>  $d['order']['product_description'],
            	'totalWeight'			=>	$weight,
            	'totalWeightUOM'		=>  "G",
            	'dimensionUOM'			=>	NULL,
            	'height'				=>	NULL,
            	'length'				=>	NULL,
            	'width'					=>	NULL,
            	'customerReference1'	=>	$d['order']['ref_1'],
            	'customerReference2'	=>	$d['order']['ref_2'],
            	'productCode'			=>	'PPS',
                'incoterm'				=>	'DDU',
            	'totalValue'			=>	$d['order']['value'],
                //'totalValue'			=>	19.95,
            	'currency'				=>	'AUD',
                'shipmentContents'		=>	array(),
            );
            if(isset($d['customer']['address3']) && !empty($d['customer']['address3']))
                $label_request['labelRequest']['bd']['shipmentItems'][$di]['consigneeAddress']['address3'] = $d['customer']['address3'];
            if(isset($d['customer']['address2']) && !empty($d['customer']['address2']))
                $label_request['labelRequest']['bd']['shipmentItems'][$di]['consigneeAddress']['address2'] = $d['customer']['address2'];
            if(isset($d['customer']['companyname']) && !empty($d['customer']['companyname']))
                $label_request['labelRequest']['bd']['shipmentItems'][$di]['consigneeAddress']['companyName'] = $d['customer']['companyname'];
            foreach($d['items'] as $i)
    		{
    			$label_request['labelRequest']['bd']['shipmentItems'][$di]['shipmentContents'][] = array(
                  	'skuNumber'			=>	$i['sku'],
    				'description'		=>	$i['description'],
    				'itemValue'			=>	$i['value'],
    				'itemQuantity'		=>	(int) $i['qty'],
    				'countryOfOrigin'	=>  'AU',
    				'hsCode'			=>	null,
                    'grossWeight'       =>  $i['weight'],
                    'weightUOM'         =>  "G"
    			);
    		}
            ++$di;
        }


		//echo "<pre>",print_r($label_request),"</pre>";
       //return $label_request; exit();
		$response = $this->sendPostRequest('/Label', $label_request);
        //echo json_encode($label_request);die();
        //echo "<pre>",print_r(json_decode($response, true)),"</pre>"; die();
		return json_decode($response, true);
        //return $label_request;
	}

    public function rePrintLabel( $order_id )
    {
        $date_time = date('c', $session->time);
		$shipment_id = $this->CUSTOMER_PREFIX.str_pad($order_id,8,'0',STR_PAD_LEFT);
        $reprint_request = array(
            "labelReprintRequest"   =>  array(
                "hdr"	=>	array(
                    "messageType"		=>	"LABELREPRINT",
        			"messageDateTime"	=>	$date_time,
                    "accessToken"		=>	$this->ACCESS_TOKEN,
        			"messageVersion"	=>	"1.4",
                    "messageLanguage"   =>  "en"
        		),
                "bd"	=>	array(
                    "pickupAccountId"	=>	dhl::API_PICKUP,
                    "soldToAccountId"	=>	dhl::API_SOLDTO,
                    "shipmentItems"     =>  array(
                        array(
                           "shipmentID"    =>  $shipment_id
                        )
                    )
                )
            )
        );
        $response = $this->sendPostRequest('/Label/Reprint', $reprint_request);
        return json_decode($response, true);
        //return($reprint_request);
    }

	public function printLabel( $details, $shipper_name = "3PL Plus" )
	{
		global $session, $portal, $root;
		$date_time = date('c', $session->time);
		$shipment_id = $this->CUSTOMER_PREFIX.str_pad($details['order']['id'],8,'0',STR_PAD_LEFT);
		$weight = round($details['order']['weight'] * 100);
        $label_request = array(
			"labelRequest"	=> array(
              	"hdr"	=>	array(
                  	"messageType"		=>	"LABEL",
    				"messageDateTime"	=>	$date_time,
                    "accessToken"		=>	$this->ACCESS_TOKEN,
    				"messageVersion"	=>	"1.4",
                    "messageLanguage"   =>  "en"
    			),
    			"bd"	=>	array(
                     "pickupAccountId"	=>	dhl::API_PICKUP,
                     "soldToAccountId"	=>	dhl::API_SOLDTO,
                     "pickupAddress"	=>	array(
    				 	"name"		=>	"3PL Plus",
                        "address1"	=>	$portal->threepl_address['address'],
    					"city"		=>	$portal->threepl_address['suburb'],
    					"country"	=>	$portal->threepl_address['country'],
                        "state"     =>  $portal->threepl_address['state'],
                        "postCode"  =>  $portal->threepl_address['postcode']
    				 ),
    				 "shipperAddress"	=>	array(
                     	"name"		=>	$shipper_name,
                        "address1"	=>	$portal->threepl_address['address'],
    					"city"		=>	$portal->threepl_address['suburb'],
    					"country"	=>	$portal->threepl_address['country'],
                        "state"     =>  $portal->threepl_address['state'],
                        "postCode"  =>  $portal->threepl_address['postcode']
    				 ),
    				 "shipmentItems"	=>	array(
                        array(
                         	"consigneeAddress"		=>	array(
        	                    "name"		=>	$details['customer']['name'],
        						"address1"	=>	$details['customer']['address'],
        						"city"		=>	$details['customer']['city'],
        						"country"	=>	$details['customer']['country'],
        						"postCode"	=>	$details['customer']['postcode'],
        	                    "email"		=>	$details['customer']['email']
        					),
        					"shipmentID"			=>  $shipment_id,
                            "packageDesc"			=>  $details['order']['product_description'],
        					'totalWeight'			=>	$weight,
        					'totalWeightUOM'		=>  "G",
        					'dimensionUOM'			=>	NULL,
        					'height'				=>	NULL,
        					'length'				=>	NULL,
        					'width'					=>	NULL,
        					'customerReference1'	=>	$details['order']['ref_1'],
        					'customerReference2'	=>	(string)$details['order']['ref_2'],
        					'productCode'			=>	'PPS',
                            'incoterm'				=>	'DDU',
        					'totalValue'			=>	$details['order']['value'],
        					'currency'				=>	'AUD',
                            'shipmentContents'		=>	array(),
        				 )
                     ),
    				 "label"			=>	array(
                     	"pageSize"	=>  "400x600",
    					"format"	=>	"PNG",
    					"layout"	=>  "4x1"
                     )
    			)
			)
		);
        if(isset($details['customer']['address3']) && !empty($details['customer']['address3']))
            $label_request['labelRequest']['bd']['shipmentItems'][0]['consigneeAddress']['address3'] = $details['customer']['address3'];
        if(isset($details['customer']['address2']) && !empty($details['customer']['address2']))
            $label_request['labelRequest']['bd']['shipmentItems'][0]['consigneeAddress']['address2'] = $details['customer']['address2'];
        if(isset($details['customer']['companyname']) && !empty($details['customer']['companyname']))
            $label_request['labelRequest']['bd']['shipmentItems'][0]['consigneeAddress']['companyName'] = $details['customer']['companyname'];
		/* */
		foreach($details['items'] as $i)
		{
			$label_request['labelRequest']['bd']['shipmentItems'][0]['shipmentContents'][] = array(
              	'skuNumber'			=>	$i['sku'],
				'description'		=>	$i['description'],
				'itemValue'			=>	$i['value'],
				'itemQuantity'		=>	(int) $i['qty'],
				'countryOfOrigin'	=>  'AU',
				'hsCode'			=>	null,
                'grossWeight'       =>  $i['weight'],
                'weightUOM'         =>  "G"
			);
		}

        //return $label_request; exit();

		$response = $this->sendPostRequest('/Label', $label_request);
        /*
        $ds = date("Ymd");
    	//echo "$root/dhl_errors/error_".$ds.".txt";
    	if(!$handle = fopen("$root/logs/dhllog_".$ds.".txt", 'a')) die('fopen error');
    	fwrite($handle, "\r\n\r\n------------- --- ----------------");
    	fwrite($handle, "\r\n\r\n Date/Time: ".date("d/m/Y, g:i:s a"));
        fwrite($handle, "\r\n".var_export($label_request, true));
    	fwrite($handle, "\r\n".var_export($response, true));
    	fclose($handle);
        */
		return json_decode($response, true);
	}

    public function deleteLabel( $shipment_id )
    {
        global $session;
        $date_time = date('c', $session->time);
        //$shipment_id = $this->CUSTOMER_PREFIX.str_pad($order_id,8,'0',STR_PAD_LEFT);
        $request = array(
            'deleteShipmentReq' =>  array(
                'hdr'   =>  array(
                    "messageType"		=>	"DELETESHIPMENT",
    				"messageDateTime"	=>	$date_time,
                    "accessToken"		=>	$this->ACCESS_TOKEN,
    				"messageVersion"	=>	"1.4",
                    "messageLanguage"   =>  "en"
                ),
                'bd'    =>  array(
                     "pickupAccountId"	=>	dhl::API_PICKUP,
                     "soldToAccountId"	=>	dhl::API_SOLDTO,
                     "shipmentItems"    =>  array(
                        "shipmentID"    =>  $shipment_id
                     )
                )
            )
        );
        $response = $this->sendPostRequest('/Label/Delete', $request);
		return json_decode($response, true);
    }

    public function editShipment( $details, $shipper_name = "3PL Plus" )
    {
        global $session, $portal, $root;
		$date_time = date('c', $session->time);
		$shipment_id = $this->CUSTOMER_PREFIX.str_pad($details['order']['id'],8,'0',STR_PAD_LEFT);
		$weight = round($details['order']['weight'] * 100);
        $label_request = array(
			"labelRequest"	=> array(
              	"hdr"	=>	array(
                  	"messageType"		=>	"EDITSHIPMENT",
    				"messageDateTime"	=>	$date_time,
                    "accessToken"		=>	$this->ACCESS_TOKEN,
    				"messageVersion"	=>	"1.4",
                    "messageLanguage"   =>  "en"
    			),
    			"bd"	=>	array(
                     "pickupAccountId"	=>	dhl::API_PICKUP,
                     "soldToAccountId"	=>	dhl::API_SOLDTO,
                     "pickupAddress"	=>	array(
    				 	"name"		=>	"3PL Plus",
                        "address1"	=>	$portal->threepl_address['address'],
    					"city"		=>	$portal->threepl_address['suburb'],
    					"country"	=>	$portal->threepl_address['country'],
                        "state"     =>  $portal->threepl_address['state'],
                        "postCode"  =>  $portal->threepl_address['postcode']
    				 ),
    				 "shipperAddress"	=>	array(
                     	"name"		=>	$shipper_name,
                        "address1"	=>	$portal->threepl_address['address'],
    					"city"		=>	$portal->threepl_address['suburb'],
    					"country"	=>	$portal->threepl_address['country'],
                        "state"     =>  $portal->threepl_address['state'],
                        "postCode"  =>  $portal->threepl_address['postcode']
    				 ),
    				 "shipmentItems"	=>	array(
                        array(
                         	"consigneeAddress"		=>	array(
        	                    "name"		=>	$details['customer']['name'],
        						"address1"	=>	$details['customer']['address'],
        						"city"		=>	$details['customer']['city'],
        						"country"	=>	$details['customer']['country'],
        						"postCode"	=>	$details['customer']['postcode'],
        	                    "email"		=>	$details['customer']['email']
        					),
        					"shipmentID"			=>  $shipment_id,
                            "packageDesc"			=>  $details['order']['product_description'],
        					'totalWeight'			=>	$weight,
        					'totalWeightUOM'		=>  "G",
        					'dimensionUOM'			=>	NULL,
        					'height'				=>	NULL,
        					'length'				=>	NULL,
        					'width'					=>	NULL,
        					'customerReference1'	=>	$details['order']['ref_1'],
        					'customerReference2'	=>	(string)$details['order']['ref_2'],
        					'productCode'			=>	'PPS',
                            'incoterm'				=>	'DDU',
        					'totalValue'			=>	$details['order']['value'],
        					'currency'				=>	'AUD',
                            'shipmentContents'		=>	array(),
        				 )
                     ),
    				 "label"			=>	array(
                     	"pageSize"	=>  "400x600",
    					"format"	=>	"PNG",
    					"layout"	=>  "4x1"
                     )
    			)
			)
		);
        if(isset($details['customer']['address3']) && !empty($details['customer']['address3']))
            $label_request['labelRequest']['bd']['shipmentItems'][0]['consigneeAddress']['address3'] = $details['customer']['address3'];
        if(isset($details['customer']['address2']) && !empty($details['customer']['address2']))
            $label_request['labelRequest']['bd']['shipmentItems'][0]['consigneeAddress']['address2'] = $details['customer']['address2'];
        if(isset($details['customer']['companyname']) && !empty($details['customer']['companyname']))
            $label_request['labelRequest']['bd']['shipmentItems'][0]['consigneeAddress']['companyName'] = $details['customer']['companyname'];
		/* */
		foreach($details['items'] as $i)
		{
			$label_request['labelRequest']['bd']['shipmentItems'][0]['shipmentContents'][] = array(
              	'skuNumber'			=>	$i['sku'],
				'description'		=>	$i['description'],
				'itemValue'			=>	$i['value'],
				'itemQuantity'		=>	(int) $i['qty'],
				'countryOfOrigin'	=>  'AU',
				'hsCode'			=>	null,
                'grossWeight'       =>  $i['weight'],
                'weightUOM'         =>  "G"
			);
		}

        //return $label_request; exit();

		$response = $this->sendPostRequest('/Label/Edit', $label_request);
        /*
        $ds = date("Ymd");
    	//echo "$root/dhl_errors/error_".$ds.".txt";
    	if(!$handle = fopen("$root/logs/dhllog_".$ds.".txt", 'a')) die('fopen error');
    	fwrite($handle, "\r\n\r\n------------- --- ----------------");
    	fwrite($handle, "\r\n\r\n Date/Time: ".date("d/m/Y, g:i:s a"));
        fwrite($handle, "\r\n".var_export($label_request, true));
    	fwrite($handle, "\r\n".var_export($response, true));
    	fclose($handle);
        */
		return json_decode($response, true);
    }

    public function doCloseout( $shipment_ids, $shipper_name = "3PL Plus" )
	{
        global $session, $portal;
        $date_time = date('c', $session->time);

        $request = array(
            'closeOutRequest'   =>  array(
                'hdr'   =>  array(
                    "messageType"		=>	"CLOSEOUT",
    				"messageDateTime"	=>	$date_time,
                    "accessToken"		=>	$this->ACCESS_TOKEN,
    				"messageVersion"	=>	"1.3"
                ),
                'bd'    =>  array(
                    "pickupAccountId"	=>	dhl::API_PICKUP,
                    "soldToAccountId"	=>	dhl::API_SOLDTO,
                    "handoverMethod"    =>  2,
                    "generateHandover"  =>  "Y",
                    "shipmentItems"     =>  array()
                )
            )
        );
        /* */
        foreach($shipment_ids as $sid)
        {
            $request['closeOutRequest']['bd']['shipmentItems'][]['shipmentID'] = "$sid";
        }

        //print_r($request);
        $response = $this->sendPostRequest('/Order/Shipment/CloseOut', $request);
		return json_decode($response, true);
    }

    public function getTracking($con_id)
    {
        global $session;
        $date_time = date('c', $session->time);
        $request = array(
            'trackItemRequest'   =>  array(
                'hdr'   =>  array(
                    "messageType"		=>	"TRACKITEM",
    				"messageDateTime"	=>	$date_time,
                    "accessToken"		=>	$this->ACCESS_TOKEN,
    				"messageVersion"	=>	"1.0"
                ),
                'bd'    =>  array(
                    "pickupAccountId"	    =>	dhl::API_PICKUP,
                    "soldToAccountId"	    =>	dhl::API_SOLDTO,
                    "trackingReferenceNumber"   =>  array(
                        $con_id
                    )
                )
            )
        );
        //echo json_encode($request);//die();
        $response = $this->sendPostRequest('/Tracking', $request, '/v3');
		return json_decode($response, true);
    }

    public function getShipmentDetails($od, $cd, $ad, $cld, $picked = true)
    {
        global $db, $portal, $session;
        $packages = $db->queryData("SELECT * FROM orders_packages WHERE order_id = {$od['id']}");
        $product_description =  (empty($cld['products_description']))? "The general description of the shipment":$cld['products_description'];
        $items = $portal->getItemsForOrder($od['id']);
        $weight = 0;
        $ref_1 = $cld['hunters_ref'];
        $ref_2 = $od['order_number'];
        $ship_to = (empty($od['ship_to']))? $cd['name']: $od['ship_to'];
        if(count($packages))
        {
            foreach($packages as $p)
            {
                $weight += $p['weight'];
            }
        }
        else
        {
            foreach($items as $i)
            {
                $weight += $i['weight'] * $i['qty'];
            }
        }

        $details = array(
         	"order"		=>	array(
               	"id"					=>	$od['order_number'],
            	"weight"				=>	$weight,
            	"ref_1"					=>	$ref_1,
            	"ref_2"					=>	$ref_2,
            	"value"					=>	0,
            	"product_description"	=>	$product_description
            ),
            "customer"	=>	array(
                "name"		=>	$ship_to,
            	"address"	=>	$ad['address'],
            	"city"		=>	$ad['suburb'],
            	"postcode"	=>	$ad['postcode'],
            	"email"		=>	$cd['email'],
            	"country"	=>	$ad['country'],
                "phone"     =>  $cd['phone']
            ),
            "items"		=>	array()
        );
        if( !empty($cd['company']) )      $details['customer']['companyname'] = $cd['company'];
        if( !empty($ad['address_2']) )    $details['customer']['address2'] = $ad['address_2'];
        $value = 0;
        foreach($items as $i)
        {
            $value += $i['price'] * $i['qty'];
            $description = (empty($i['description']))? "the item description": $i['description'];
            $item_value = $i['price'] * $i['qty'];
            if( (float)number_format( $item_value, 2 ) == $item_value ) $item_value += 0.01;
            $details['items'][] = array(
                "sku"			=>	$i['sku'],
            	"description"	=>	$description,
                "value"			=>	$item_value,
            	"qty"			=>	$i['qty'],
                "weight"        =>  $i['qty'] * $i['weight'] * 1000
            );
        }
        if( (float)number_format( $value, 2 ) == $value ) $value += 0.01;
            $details['order']['value'] = $value;
        return $details;
    }

}//end class
/*	Initialise the dhl object	*/
//$dhl = new dhl;

?>
