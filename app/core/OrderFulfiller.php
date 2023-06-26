<?php

/**
 * The orderfullfiller class.
 *
 * fulfills orders
 * reduces stock, contacts couriers to create an order, closes an order out

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;

 class OrderFulfiller{

     protected $controller;
     private $output;

     /**
      * Constructor
      *
      * @param Controller $controller
      */
    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
    }


    public function fulfillEparcelOrders(Array $order_ids)
    {
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "FULFILLING EPARCEL ORDERS ON ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        $eparcel_clients = array();
        foreach($order_ids as $id)
        {
            $od = $this->controller->order->getOrderDetail($id);
            if($od['status_id'] == $this->controller->order->picked_id || $od['status_id'] == $this->controller->order->packed_id)
            {
                Session::set('showfeedback', true);
                if(!array_key_exists($od['client_id'], $eparcel_clients))
        		{
                    $eparcel_clients[$od['client_id']]['request'] = array(
        	            'order_reference'	=>	Utility::generateRandString(),
                    	'payment_method'	=>	'CHARGE_TO_ACCOUNT',
                    	'shipments'			=>	array()
        			);
        		}
                $eparcel_clients[$od['client_id']]['request']['shipments'][] = array('shipment_id'	=>	$od['eparcel_shipment_id']);
                $eparcel_clients[$od['client_id']]['order_ids'][] = $id;
                $eparcel_clients[$od['client_id']]['order_details'][] = $od;
            }
            else
            {
                Session::set('showerrorfeedback', true);
        	    $_SESSION['errorfeedback'] .= "<h3>{$od['order_number']} has not had the labels or pickslip printed</h3><p>Please do at least one and try again</p>";
            }
        }
        if(count($eparcel_clients))
        {
            $this->createEparcelOrder($eparcel_clients);
        }
        else
        {
            $this->output .= "No orders to fulfill".PHP_EOL;
        }
        $this->recordOutput("order_fulfillment/eparcel");
    }


    public function fulfillLocalOrder()
    {
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "FULFILLING LOCAL COURIER ORDERS ON ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        $db = Database::openConnection();
        //echo "<pre>",print_r($this->controller->request->data),"</pre>";die();
        $od = $this->controller->order->getOrderDetail($this->controller->request->data['order_ids']);

        $postage_charge = $this->controller->request->data['local_charge'];
        $handling_charge = $od['handling_charge'];
        if($this->controller->request->data['inc_gst'] > 0)
            $gst = ($handling_charge + $postage_charge) * 0.1;
        else
            $gst = $handling_charge * 0.1;
        $total_cost = $handling_charge + $postage_charge + $gst;
        $o_values = array(
            'status_id'			=>	$this->controller->order->fulfilled_id,
            'date_fulfilled'	=>	time(),
            'consignment_id'    =>  $this->controller->request->data['consignment_id'],
            'postage_charge'    =>  $postage_charge,
            'gst'               =>  $gst,
            'total_cost'        =>  $total_cost
        );
        $db->updateDatabaseFields('orders', $o_values, $this->controller->request->data['order_ids']);
        //order is now fulfilled, reduce stock
        $items = $this->controller->order->getItemsForOrder($this->controller->request->data['order_ids']);
        $this->output .= "Reducing Stock and recording movement fo order id: ".$this->controller->request->data['order_ids'].PHP_EOL;
        $this->removeStock($items, $this->controller->request->data['order_ids']);
        if(SITE_LIVE) //only send emails and update shopify if we are live and not testing
        {
            $this->sendTrackingEmails($od);
            if($od['is_shopify'] == 1)
            {
                $this->updateShopify($od, $items);
            }
        }
        $this->recordOutput('order_fulfillment/local');
        Session::set('showfeedback', true);
        $_SESSION['feedback'] .= "<p>Order number {$od['order_number']} has been recorded as dispatched by {$od['courier_name']}</p>";
    }

        public function fulfillDirectFreightOrder($order_ids)
    {
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "FULFILLING DIRECT FREIGHT ORDERS ON ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        $db = Database::openConnection();
        //echo "<pre>",print_r($this->controller->request->data),"</pre>";//die();
        //$od = $this->controller->order->getOrderDetail($this->controller->request->data['order_ids']);
        //$order_ids = $this->controller->request->data['order_ids'];
        $order_ids = ( is_array($order_ids) )? $order_ids: (array)$order_ids;
        //echo "<pre>",var_dump($order_ids),"</pre>";die();
        foreach($order_ids as $id)
        {
            $od = $this->controller->order->getOrderDetail($id);
            if($od['status_id'] == $this->controller->order->picked_id || $od['status_id'] == $this->controller->order->packed_id)
            {

                $response = $this->controller->directfreight->finaliseConsignment($od['consignment_id']);
                /**/ if($response['ResponseCode'] != 300)
                {
                    //Session::set('showerrorfeedback', true);
        	        //$_SESSION['errorfeedback'] .= "<h3>{$od['order_number']} Could not be finalised by Direct Freight</h3><p>The Error is ".$response['ResponseMessage']."</p>";
                }
                else
                {
                    $dfe_order = $response['ConnoteList'][0];
                    /**/ if($dfe_order['ResponseCode'] != 200)
                    {
                        Session::set('showerrorfeedback', true);
        	            $_SESSION['errorfeedback'] .= "<h3>{$od['order_number']} Could not be finalised by Direct Freight</h3><p>The Error is ".$dfe_order['ResponseMessage']."</p>";
                    }
                    else
                    {
                        Session::set('showfeedback', true);
                        $o_values = array(
                            //'status_id'			=>	$this->controller->order->fulfilled_id,
                            'status_id'			=>	4,
                            'date_fulfilled'	=>	time()
                        );
                        $this->output .= "Updating Orders for order ID: $id".PHP_EOL;
                        $this->output .= print_r($o_values, true).PHP_EOL;
                        $db->updateDatabaseFields('orders', $o_values, $id);

                        //order is now fulfilled, reduce stock
                        $items = $this->controller->order->getItemsForOrder($id);
                        $this->output .= "Reducing Stock and recording movement for order id: ".$id.PHP_EOL;
                        $this->removeStock($items, $id);
                        if(SITE_LIVE && $od['is_marketplacer'] === 0) //only send emails if we are live and not testing and none for marketplacer
                        {
                            $this->sendTrackingEmails($od);
                        }
                        if($od['is_shopify'] == 1)
                        {
                            $this->updateShopify($od, $items, "https:://directfreight.com.au");
                        }
                        if($od['is_ebay'] == 1)
                        {
                            $this->updateEbay($od, $items, "Direct Freight");
                        }
                        if($od['is_marketplacer'] == 1)
                        {
                            $this->updatMarketplacer($od, "Direct Freight Express");
                        }
                        if($od['is_woocommerce'] == 1 && $od['client_id'] == 87)
                        {
                            $this->output .= "Sending DF Tracking info to woo-commerce".PHP_EOL;
                            $woocommerce_id = $od['client_order_id'];
                            $tracking = array(
                                "tracking_number"           => $od['consignment_id'],
                                "custom_tracking_provider"  => "Direct Freight Express",
                                "custom_tracking_link"      => "https:://directfreight.com.au"
                            );
                            if(
                                Curl::sendSecurePOSTRequest(
                                    'https://golfperformancestore.com.au/wp-json/wc-shipment-tracking/v3/orders/'.$woocommerce_id.'/shipment-trackings',
                                    $tracking,
                                    Config::get('PBAWOOCONSUMERRKEY'),
                                    Config::get('PBAWOOCONSUMERSECRET')
                                )
                            )
                            {
                                //tracking updated, close the order in woo-commerce
                                $this->output .= "Trying to complete order in woo-commerce".PHP_EOL;
                                $woo = new Client(
                                    'https://golfperformancestore.com.au',
                                    Config::get('PBAWOOCONSUMERRKEY'),
                                    Config::get('PBAWOOCONSUMERSECRET'),
                                    [
                                        'wp_api' => true,
                                        'version' => 'wc/v3',
                                        'query_string_auth' => true
                                    ]
                                );
                                try{
                                    $woo->put('orders/'.$woocommerce_id, array('status' => 'completed'));
                                }
                                catch (HttpClientException $e) {
                                    $this->output .= "ERRORERRORERRORERRORERRORERRORERRORERRORERRORERRORERRORERRORERRORERRORERROR".PHP_EOL;
                                    $this->output .=  $e->getMessage() .PHP_EOL;
                                    //$output .=  $e->getRequest() .PHP_EOL;
                                    $this->output .=  print_r($e->getResponse(), true) .PHP_EOL;
                                    //die($output);
                                }
                            }
                        }

                        $this->recordOutput('order_fulfillment/direct');
                        Session::set('showfeedback', true);
                        $_SESSION['feedback'] .= "<p>Order number {$od['order_number']} has been recorded as dispatched by Direct Freight with Consignment ID: ".$od['consignment_id']."</p>";
                    }
                }
            }
            else
            {
                Session::set('showerrorfeedback', true);
        	    $_SESSION['errorfeedback'] .= "<h3>{$od['order_number']} has not had the labels or pickslip printed</h3><p>Please do at least one and try again</p>";
            }
       }
    }

    public function fulfillFSGTruckOrder()
    {
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "FULFILLING FSG Delivery ORDERS ON ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        $db = Database::openConnection();
        //echo "<pre>",print_r($this->controller->request->data),"</pre>";die();
        $od = $this->controller->order->getOrderDetail($this->controller->request->data['order_ids']);
        //save to truck table
        $t_vals = array(
            'client_id'     =>  $od['client_id'],
            'order_id'      =>  $this->controller->request->data['order_ids'],
            'charge'        =>  $this->controller->request->data['truck_charge'],
            'date'          =>  time(),
            'entered_by'    =>  Session::getUserId(),
            'address'       =>  $od['address'],
            'address_2'     =>  $od['address_2'],
            'suburb'        =>  $od['suburb'],
            'state'         =>  $od['state'],
            'postcode'      =>  $od['postcode'],
            'country'       =>  $od['country']
        );
        $db->insertQuery('truck_usage', $t_vals);
        $o_values = array(
            'status_id'			=>	$this->controller->order->fulfilled_id,
            'date_fulfilled'	=>	time(),
            'consignment_id'    =>  $this->controller->request->data['consignment_id'],
            'total_cost'        =>  $this->controller->request->data['truck_charge']
        );
        $db->updateDatabaseFields('orders', $o_values, $this->controller->request->data['order_ids']);
        if($od['is_shopify'] == 1)
        {
            $this->updateShopify($od, $items);
        }
        //order is now fulfilled, reduce stock
        $items = $this->controller->order->getItemsForOrder($this->controller->request->data['order_ids']);
        $this->output .= "Reducing Stock and recording movement fo order id: ".$this->controller->request->data['order_ids'].PHP_EOL;
        $this->removeStock($items, $this->controller->request->data['order_ids']);
        $this->recordOutput('order_fulfillment/3pl_truck');
        Session::set('showfeedback', true);
        $_SESSION['feedback'] .= "<p>Order number {$od['order_number']} has been recorded as dispatched by our an FSG truck</p>";
    }

private function createEparcelOrder($eparcel_clients)
    {
        //echo "<pre>",print_r($eparcel_clients),"</pre>";return();
        $db = Database::openConnection();
        $c = 0;
        foreach($eparcel_clients as $client_id => $array)
        {
            $client_details = $this->controller->client->getClientInfo(87);
            $eParcelClass = "Eparcel";
            if(!is_null($client_details['eparcel_location']))
                $eParcelClass = $client_details['eparcel_location']."Eparcel";
            /* */
            $response = $this->controller->{$eParcelClass}->CreateOrderFromShipment($array['request']);
            $this->output .= "eParcel create order response".PHP_EOL;
            $this->output .= print_r($response, true);
            //echo "RESPONSE<pre>",print_r($response),"</pre>";
            if(isset($response['errors']))
        	{
        	    Session::set('showerrorfeedback', true);
        	    $_SESSION['errorfeedback'] .= "<h3>{$array['order_details'][$c]['order_number']} had some errors when submitting to eParcel</h3>";
        		foreach($response['errors'] as $e)
        		{
        			$_SESSION['errorfeedback'] .= "<h3>Error Code: ".$e['code']."</h3><h4>".$e['name']."</h4><p>".$e['message']."</p>";
        		}
        	}
            else
            {
                /*  */
        		$order_id = $response['order']['order_id'];
                $values = array(
                   	'manifest_id'	=>	$order_id,
        			'total_cost'	=>  $response['order']['order_summary']['total_cost'],
        			"total_gst"		=>	$response['order']['order_summary']['total_gst'],
        			'status'		=>	$response['order']['order_summary']['status'],
        			'create_date'	=>	time(),
                    'client_id'     =>  $client_id
        		);
        		$eparcel_order_id = $db->insertQuery('eparcel_orders', $values);

        		$this->controller->{$eParcelClass}->GetOrderSummary($order_id, $eparcel_order_id);

        		foreach($array['order_ids'] as $id)
        		{
        		    //echo "<p>Doing order id $id</p>";
        	        $o_values = array(
        				'eparcel_order_id'	=>	$eparcel_order_id,
                        //'eparcel_order_id'	=>	9334,
        				//'status_id'			=>	$this->controller->order->fulfilled_id,
                        'status_id'			=>	4,
        				'date_fulfilled'	=>	time()
                        //'date_fulfilled'      => 1680785757
        			);
                    $this->output .= "Updating Orders".PHP_EOL;
                    $this->output .= print_r($o_values, true).PHP_EOL;
        			$db->updateDatabaseFields('orders', $o_values, $id);

                    $od = $this->controller->order->getOrderDetail($id);
                    $items = $this->controller->order->getItemsForOrder($id);
                    if(SITE_LIVE && $od['is_marketplacer'] === 0) //only send emails if we are live and not testing and none for marketplacer
                    {
                        $this->sendTrackingEmails($od);
                    }
                    if($od['is_shopify'] == 1)
                    {
                        $this->updateShopify($od,$items, "https://auspost.com.au/track/".$od['consignment_id']);
                    }
                    if($od['is_ebay'] == 1)
                    {
                        $this->updateEbay($od, $items, "Australia Post");
                    }
                    if($od['is_marketplacer'] == 1)
                    {
                        $this->updatMarketplacer($od, "Eparcel");
                    }
                    if($od['is_woocommerce'] == 1 && $od['client_id'] == 87)
                    {
                        $this->output .= "Sending Eparcel Tracking info to woo-commerce".PHP_EOL;
                        $woocommerce_id = $od['client_order_id'];
                        $tracking = array(
                            "tracking_number"   => $od['consignment_id'],
                            "tracking_provider" => "Australia Post"
                        );
                        if(
                            Curl::sendSecurePOSTRequest(
                                'https://golfperformancestore.com.au/wp-json/wc-shipment-tracking/v3/orders/'.$woocommerce_id.'/shipment-trackings',
                                $tracking,
                                Config::get('PBAWOOCONSUMERRKEY'),
                                Config::get('PBAWOOCONSUMERSECRET')
                            )
                        )
                        {
                            //tracking updated, close the order in woo-commerce
                            $this->output .= "Trying to complete order in woo-commerce".PHP_EOL;
                            $woo = new Client(
                                'https://golfperformancestore.com.au',
                                Config::get('PBAWOOCONSUMERRKEY'),
                                Config::get('PBAWOOCONSUMERSECRET'),
                                [
                                    'wp_api' => true,
                                    'version' => 'wc/v3',
                                    'query_string_auth' => true
                                ]
                            );
                            try{
                                $woo->put('orders/'.$woocommerce_id, array('status' => 'completed'));
                            }
                            catch (HttpClientException $e) {
                                $this->output .= "ERRORERRORERRORERRORERRORERRORERRORERRORERRORERRORERRORERRORERRORERRORERROR".PHP_EOL;
                                $this->output .=  $e->getMessage() .PHP_EOL;
                                //$output .=  $e->getRequest() .PHP_EOL;
                                $this->output .=  print_r($e->getResponse(), true) .PHP_EOL;
                                //die($output);
                            }
                        }
                        else
                        {
                            $this->output .= "ERRORERRORERRORERRORERRORERRORERRORERRORERRORERRORERRORERRORERRORERRORERROR".PHP_EOL;
                            $this->output .= "COULD NOT CHANGE STATUS FOR ".$woocommerce_id.PHP_EOL;
                        }
                    }

                    if($od['client_id'] == 7)
                    {
                        //FREEDOM
                        //$this->controller->FreedomMYOB->callTask('addCommentFromWMS',array('Company_UID'=>'theCompanyId','invoiceUID'=>'0001','comment'=>'test'
                    }
                    //order is now fulfilled, reduce stock
                    $items = $this->controller->order->getItemsForOrder('119196');
                   $this->output .= "Reducing Stock and recording movement for order id: 119196".PHP_EOL;
                   $this->removeStock($items, $id);
        		}
            //die();
                Session::set('showfeedback', true);
               $_SESSION['feedback'] .= "<p>Manifest ID: $order_id successfully created and submitted to eParcel</p>";
               // $_SESSION['feedback'] .=  print_r($eparcel_clients)."</pre>";
                //$_SESSION['feedback'] .= "<p>Manifest ID: AP04177950 successfully created and submitted to eParcel</p>";
            }
            ++$c;
        }
    }

    private function removeStock($items, $order_id)
    {
        $db = Database::openConnection();
        foreach($items as $i)
        {
            $qty = $i['qty'];
            $item_id = $i['item_id'];
            $location_id = $i['location_id'];
            $item_d = $db->queryByID('items', $item_id);
            //remove from stock
            $this->output .= "Removing from stock : UPDATE items_locations SET qty = qty - $qty WHERE item_id = $item_id AND location_id = $location_id".PHP_EOL;
            $db->query("UPDATE items_locations SET qty = qty - $qty WHERE item_id = $item_id AND location_id = $location_id");
            //item movement reason
            $reason_id = $this->controller->stockmovementlabels->getLabelId("Order Fulfillment");
            $im_values = array(
            	'reason_id'	    =>	$reason_id,
            	'qty_out'	    =>	$qty,
            	'item_id'	    =>	$item_id,
            	'order_id'	    =>	$order_id,
            	'date'		    =>	time(),
                'entered_by'    =>  Session::getUserId() ,
                'location_id'   =>  $location_id
            );
            $this->output .= "Inserting movement reason".PHP_EOL;
            $this->output .= print_r($im_values, true).PHP_EOL;
            $db->insertQuery('items_movement', $im_values);
            $cc =  $db->queryValue('items_locations', array('item_id' => $item_id, 'location_id' => $location_id), 'qty') ;
            if($cc <= 0)
            {
                $check = $db->queryRow("SELECT * FROM items_locations WHERE location_id = $location_id AND qty > 0");
                if(empty($check))
                {
                    $db->query("UPDATE clients_bays SET date_removed = ".time()." WHERE location_id = $location_id AND client_id = {$item_d['client_id']} AND date_removed = 0");
                    if($item_d['double_bay'] > 0)
                    {
                        $this_location = $db->queryValue('locations', array('id' => $location_id), 'location');
                        $next_location = substr($this_location, 0, -1)."b";
                        $next_location_id = $db->queryValue('locations', array('location' => $next_location));
                        $db->query("UPDATE clients_locations SET date_removed = ".time()." WHERE location_id = $next_location_id AND client_id = {$item_d['client_id']} AND date_removed = 0");
                    }
                }
            }
            //stock notifications
            $this->checkStockLevels($item_id);
        }
    }

    private function checkStockLevels($item_id)
    {
        $db = Database::openConnection();
        $available = $this->controller->item->getAvailableStock($item_id, $this->controller->order->fulfilled_id);
        $s_check = $db->queryRow("SELECT name, low_stock_warning, client_id FROM items WHERE id = $item_id");

        if( $available < $s_check['low_stock_warning'])
        {
            $cd = $db->queryByID('clients', $s_check['client_id']);
            Email::sendLowStockWarning($s_check['name'], $cd['inventory_email'], $cd['inventory_contact']);
        }
    }

    private function sendTrackingEmails($od)
    {
        if( !empty($od['tracking_email']) )
        {
            if($od['client_id'] == 59) //NOA
            {
                $this->output .= "Sending Noa Sleep confirmation for order id: ".$od['id'].PHP_EOL;
                Email::sendNoaConfirmEmail($od['id']);
            }
            elseif($od['client_id'] == 82) //Oneplate
            {
                $this->output .= "Sending One Plate confirmation for order id: ".$od['id'].PHP_EOL;
                Email::sendOnePlateTrackingEmail($od['id']);
            }
            elseif($od['client_id'] == 86 || $od['client_id'] == 87 || $od['client_id'] == 89) //BDS and PBA and Buzzbee
            {
                //Do SFA
                $this->output .= "Not Sending confirmation for BDS or PBA for order id: ".$od['id'].PHP_EOL;
            }
            else
            {
                 $this->output .= "Sending tracking email for {$od['order_number']} for order id: ".$od['id'].PHP_EOL;
                Email::sendTrackingEmail($od['id']);
            }
            $this->controller->order->updateOrderValue('customer_emailed', 1, $od['id']);
        }
    }

    private function updateWooCommerce()
    {

    }

    private function updatMarketplacer($od, $carrier_code)
    {
        $this->output .= "Sending order id: {$od['id']} to market placer for fulfillment".PHP_EOL;
        $this->controller->NuchevMarketplacer->fulfillAnOrder($od['marketplacer_id'], $od['consignment_id'], $carrier_code);
        $this->output .= "Marketplacer fulfillment complete".PHP_EOL;
    }

    private function updateEbay($od, $items, $carrier_code)
    {
        $this->output .= "Sending order id: {$od['id']} to eBay for fulfillment".PHP_EOL;
        $this->controller->PBAeBay->connect();
        $this->controller->PBAeBay->fulfillAnOrder($od['ebay_id'], $items, $carrier_code, $od['consignment_id']);
        $this->output .= "eBay fulfillment complete".PHP_EOL;
    }

    private function updateShopify($od,$items, $tracking_url = false)
    {
        if($od['is_voicecaddy'] == 1)
        {
            $this->controller->PbaVoiceCaddyShopify->fulfillAnOrder($od['shopify_id'], $od['consignment_id'], $tracking_url, $items);
        }
        elseif($od['is_homecoursegolf'] == 1)
        {
            $this->controller->PbaHomeCourseGolfShopify->fulfillAnOrder($od['shopify_id'], $od['consignment_id'], $tracking_url, $items);
        }
        elseif($od['is_superspeedgolf'] == 1)
        {
            $this->controller->PbaSuperspeedGolfShopify->fulfillAnOrder($od['shopify_id'], $od['consignment_id'], $od['fulfillmentorder_id'], $tracking_url, $items);
        }
        elseif($od['is_rukket'] == 1)
        {
            $this->controller->PbaRukketGolfShopify->fulfillAnOrder($od['shopify_id'], $od['consignment_id'],$od['fulfillmentorder_id'], $tracking_url, $items);
        }
        elseif($od['is_arccosgolf'] == 1)
        {
            $this->controller->PbaArccosGolfShopify->fulfillAnOrder($od['shopify_id'], $od['consignment_id'],$od['fulfillmentorder_id'], $tracking_url, $items);
        }
        elseif($od['is_buzzbee'] == 1)
        {
            $this->controller->BuzzBeeShopify->fulfillAnOrder($od['shopify_id'], $od['consignment_id'], $tracking_url, $items);
            $this->output .= "Fullfilled order id: {$od['id']} in shopify for BUZZBEE".PHP_EOL;
        }
        else
        {
            $this->controller->PbaPerfectPracticeGolfShopify->fulfillAnOrder($od['shopify_id'], $od['consignment_id'], $tracking_url, $items);
        }
        $this->output .= "Fullfilled order id: {$od['id']} in shopify".PHP_EOL;
    }

    private function recordOutput($file)
    {
        Logger::logOrderFulfillment($file, $this->output);
    }
}
