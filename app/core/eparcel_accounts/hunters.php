<?php
/********************************************************************
 *
 * 		/php-includes/hunters.php
 *
 * 		Manages the Hunters API functions
 *
 *********************************************************************/
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
//include_once("$root/misc-functions/helper_functions.php");
class Hunters
{

	protected $USER_NAME    		= HUNTERS_UNAME;
	protected $PWD    			    = HUNTERS_PWD;
    protected $API_HOST             = HUNTERS_HOST;
	protected $CUSTOMER_CODE;
    protected $curl_options;
    protected $sandbox              = false;

	const   API_SCHEME   = 'https://';
    //const   API_HOST     = HUNTERS_HOST;
    const   API_BASE_URL = 'rest/hxws';

	const   HEADER_EOL = "\r\n";


	/**
	 * constructor.
	 *
	 */
	function __construct($customer_code = "3KG", $test = false)
	{
	    //echo "<p>line33H</p>";
	    if($customer_code == "PLU")
        {
            $this->CUSTOMER_CODE = HUNTERS_PLUCUSTOMER_CODE;
        }
        elseif($customer_code == "3KG")
        {
            $this->CUSTOMER_CODE = HUNTERS_3KGCUSTOMER_CODE;
        }
        else
        {
            exit();
        }

        if($test)
        {
            $this->CUSTOMER_CODE = HUNTERS_TEST_CUSTOMER_CODE;
            $this->USER_NAME = HUNTERS_TEST_UNAME;
            $this->PWD = HUNTERS_TEST_PWD;
            $this->API_HOST = HUNTERS_TEST_HOST;
            $this->sandbox = true;
        }
        //echo "<p>line54H</p>";
        $this->curl_options = array(
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_PROXY => false,
            CURLOPT_ENCODING => '',
            CURLOPT_VERBOSE => true,
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => array('Content-type: application/json', 'Authorization: Basic '.base64_encode($this->USER_NAME.':'.$this->PWD)),
        );
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
        global $portal, $session;

        $request = array(
            'customerCode'      =>  $this->CUSTOMER_CODE,
            'fromLocation'      =>  array(
                "suburbName"    =>  $portal->threepl_address['suburb'],
                "postCode"      =>  $portal->threepl_address['postcode'],
                "state"         =>  $portal->threepl_address['state']
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
        global $portal, $session, $root;

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
                    "suburbName"    =>  $portal->threepl_address['suburb'],
                    "addressLine1"  =>  $portal->threepl_address['address'],
                    "addressLine2"  =>  "",
                    "postCode"      =>  $portal->threepl_address['postcode'],
                    "state"         =>  $portal->threepl_address['state'],
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

    public function getDetails($od, $cd = false, $cld = false, $picked = true)
    {
        global $db, $portal, $session;
        $packages = $db->queryData("SELECT * FROM orders_packages WHERE order_id = {$od['id']}");
        if(!$cd)
            $cd = $db->queryByID('customers', $od['customer_id']);
        if(!$cld)
            $cld = $db->queryByID('clients', $od['client_id']);
        $contact = $cd['name'];
        $phone = $od['phone'];
        if(empty($od['phone']))
        {
            $phone =  "03 8512 1444";
            $contact = "3plplus";
        }
        $order_id = $od['id'];
        $q = "
            SELECT
                oi.*, i.name, i.weight, i.sku, i.price, i.can_backorder, i.width, i.depth, i.height, i.hunters_goods_type, i.satchel_small, i.satchel_large, i.pre_packed
            FROM
                orders_items oi JOIN items i ON i.id = oi.item_id
            WHERE
                oi.order_id = $order_id
        ";
        $ad = array(
            'address'   =>  $od['address'],
            'address_2' =>  $od['address_2'],
            'state'     =>  $od['state'],
            'suburb'    =>  $od['suburb'],
            'postcode'  =>  $od['postcode'],
            'country'   =>  $od['country'],
            'phone'     =>  $phone
        );
        if($picked)
            $q .= " AND oi.picked = 1";
        $items = $db->queryData($q);
        $delivery_instructions = (!empty($od['instructions']))? $od['instructions'] : "Please leave in a safe place out of the weather";
        $ship_to = (empty($od['ship_to']))? $cd['name']: $od['ship_to'];
        if($od['signature_req'] == 1)
            $delivery_intsructions = (!empty($od['instructions']))? $od['instructions'] : "";
        $details = array(
            'reference1'        =>  $cld['hunters_ref'],
            'primaryService'    =>  'RF',
            'to_address'    =>  array(
                'name'          =>  $ship_to,
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
        $small_satchels = 0;
        $large_satchels = 0;
        $do_satchels = false;
        $goods_array = array();
        if(count($packages))
        {
            foreach($packages as $p)
            {
                $details['goods'][] = array(
                    'pieces'    =>  1,
                    'typeCode'  =>  'CTN',
                    'width'     =>  $p['width'],
                    'height'    =>  $p['height'],
                    'depth'     =>  $p['depth'],
                    'weight'    =>  ceil($p['weight'])
                );
            }
        }
        elseif($od['client_id'] == 58) //reel2reel
        {
            $details['goods'][] = array(
                'pieces'    =>  1,
                'typeCode'  =>  'CTN',
                'width'     =>  17,
                'height'    =>  5,
                'depth'     =>  30,
                'weight'    =>  1
            );
        }
        elseif($od['client_id'] == 5) //NUCHEV
        {

            $total_cans = $total_toys = $total_washes = 0;
            $total_sachets = 0;
            $weight = 0;
            foreach($items as $i)
            {
                if($i['item_id'] == 5807 || $i['item_id'] == 5808 || $i['item_id'] == 5809 )   //sachets
                {
                    $total_sachets += $i['qty'];
                }
                if($i['hunters_goods_type'] == 7 || $i['pre_packed'] > 0)
                {
                    $w = $i['width'];
                    $h = $i['height'];
                    $d = $i['depth'];
                    $weight += $i['weight'];
                    $type = $portal->getHuntersGoodsTypeCode($i['hunters_goods_type']);
                }
                elseif($i['item_id'] == 5856) // oli the goat book
                {
                    //do nothing
                }
                /*
                elseif($i['item_id'] == 5892) // bodywash
                {

                } */
                elseif($i['item_id'] == 5855 || $i['item_id'] == 6014 || $i['item_id'] == 10941)  //ugg boots and goats and jellycats
                {
                    $total_toys += $i['qty'];
                }
                elseif($i['item_id'] == 277 || $i['item_id'] == 5861 || $i['item_id'] == 5891)   ///sippy cups
                {
                    $total_toys += $i['qty'] / 2;
                }
                else
                {
                    $total_cans += $i['qty'];
                }
                $array['item_reference'] = $i['item_id'];
            }
            $total_toys = ceil($total_toys);
            if($total_toys > 0 || $total_cans > 0 || $total_sachets > 0)
            {
                if($total_toys > 0)
                {
                    //$tboxes = $portal->nuchevsoftbox_dimensions[$total_toys];
                    $tboxes = $portal->getNuchevSoftBoxes($total_toys);
                    foreach($tboxes as $b)
                    {
                        list($w, $d, $h) = $b['dimensions'];
                        $details['goods'][] = array(
                            'pieces'    =>  $b['count'],
                            'typeCode'  =>  'CTN',
                            'width'     =>  $w,
                            'height'    =>  $h,
                            'depth'     =>  $d,
                            'weight'    =>  ceil($b['weight'])
                        );
                    }
                }
                if($total_cans > 0)
                {
                    //$boxes = $portal->nuchevbox_dimensions[$total_cans];
                    $boxes = $portal->getNuchevBoxes($total_cans);
                    foreach($boxes as $b)
                    {
                        list($w, $d, $h) = $b['dimensions'];
                        $details['goods'][] = array(
                            'pieces'    =>  $b['count'],
                            'typeCode'  =>  'CTN',
                            'width'     =>  $w,
                            'height'    =>  $h,
                            'depth'     =>  $d,
                            'weight'    =>  ceil($b['weight'])
                        );
                    }
                }
                if($total_sachets > 0 && ($total_cans == 0 && $total_toys == 0))
                {
                    $array = array();
                    if($total_sachets % 150 === 0)
                    {
                        $sachet_box = 1;
                        while($sachet_box <= $total_sachets / 150)
                        {
                            $details['goods'][] = array(
                                'pieces'    =>  1,
                                'typeCode'  =>  'CTN',
                                'width'     =>  42,
                                'height'    =>  19,
                                'depth'     =>  28,
                                'weight'    =>  5
                            );
                            ++$sachet_box;
                        }
                        $total_sachets = $total_sachets - ( ($sachet_box - 1) * 150);
                    }
                    elseif($total_sachets > 150)
                    {
                        $sachet_box = 1;
                        while($sachet_box <= $total_sachets / 150)
                        {
                            $details['goods'][] = array(
                                'pieces'    =>  1,
                                'typeCode'  =>  'CTN',
                                'width'     =>  42,
                                'height'    =>  19,
                                'depth'     =>  28,
                                'weight'    =>  5
                            );
                            ++$sachet_box;
                        }
                        $total_sachets = $total_sachets - ( ($sachet_box - 1) * 150);
                    }
                    if($total_sachets > 50)
                    {
                        $c = 1;
	                    while($c <= ceil($total_sachets / 50))
                        {
                            $details['goods'][] = array(
                                'pieces'    =>  1,
                                'typeCode'  =>  'ENV',
                                'width'     =>  24,
                                'height'    =>  2,
                                'depth'     =>  30,
                                'weight'    =>  3
                            );
                            ++$c;
                        }

                    }
                    elseif($total_sachets > 30)
                    {
                        $c = 1;
	                    while($c <= ceil($total_sachets / 30))
                        {
                            $details['goods'][] = array(
                                'pieces'    =>  1,
                                'typeCode'  =>  'ENV',
                                'width'     =>  21,
                                'height'    =>  2,
                                'depth'     =>  25,
                                'weight'    =>  2
                            );
                            ++$c;
                        }
                    }
                    elseif($total_sachets > 0)
                    {
                        $details['goods'][] = array(
                            'pieces'    =>  1,
                            'typeCode'  =>  'ENV',
                            'width'     =>  21,
                            'height'    =>  2,
                            'depth'     =>  25,
                            'weight'    =>  ceil($total_sachets * 0.05 + 0.025)
                        );
                    }
                }
            }
            else
            {
                $details['goods'][] = array(
                    'pieces'    =>  1,
                    'typeCode'  =>  $type,
                    'width'     =>  $w,
                    'height'    =>  $h,
                    'depth'     =>  $d,
                    'weight'    =>  ceil($weight)
                );
            }
        }
        elseif($od['client_id'] == 6) //BIG BOTTLE
        {
            $total_bottles = 0;
            foreach($items as $i)
            {
                $total_bottles += $i['qty'];
            }
            list($w, $d, $h) = $portal->bbbox_dimensions[$total_bottles];
            $details['goods'][] = array(
                'pieces'    =>  1,
                'typeCode'  =>  $portal->getHuntersGoodsTypeCode($i['hunters_goods_type']),
                'width'     =>  $w,
                'height'    =>  $h,
                'depth'     =>  $d
            );
        }
        else
        {
            $weight = 0;
            foreach($items as $i)
            {
                if($i['hunters_goods_type'] == 20)
                {
                    $do_satchels = true;
                    $weight += $i['weight'];
                    if( !empty($i['satchel_large']) )  $large_satchels += $i['satchel_large'];
                    if( !empty($i['satchel_small']) )  $small_satchels += $i['satchel_small'];
                }
                else
                {
                    $details['goods'][] = array(
                        'pieces'    =>  $i['qty'],
                        'typeCode'  =>  $portal->getHuntersGoodsTypeCode($i['hunters_goods_type']),
                        'width'     =>  $i['width'],
                        'height'    =>  $i['height'],
                        'depth'     =>  $i['depth'],
                        'weight'    =>  ceil($i['weight'])
                    );
                    //$goods_array[] = $array;
                }
            }
        }
        if($do_satchels)
        {
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
                $details['goods'][] = array(
                    'pieces'    =>  $whole_large,
                    'typeCode'  =>  $portal->getHuntersGoodsTypeCode(20),
                    'width'     =>  43,
                    'height'    =>  32,
                    'depth'     =>  14
                );
            }
            if($small_satchels)
            {
                $details['goods'][] = array(
                    'pieces'    =>  $whole_small,
                    'typeCode'  =>  $portal->getHuntersGoodsTypeCode(20),
                    'width'     =>  23,
                    'height'    =>  34,
                    'depth'     =>  8
                );
            }
        }

        return $details;
    }


}//end class
/*	Initialise the hunter object	*/
$hunters = new Hunters();

?>
