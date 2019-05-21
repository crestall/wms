<?php

/**
 * The orderfullfiller class.
 *
 * fulfills orders
 * reduces stock, contacts couriers to create an order, closes an order out
 
 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */
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
                //Session::set('showfeedback', true);
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

    public function fulfillHuntersOrders(Array $order_ids)
    {
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "FULFILLING HUNTERS ORDERS ON ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;

        $db = Database::openConnection();

        foreach($order_ids as $id)
        {
            $od = $this->controller->order->getOrderDetail($id);
            if($od['status_id'] == $this->controller->order->picked_id || $od['status_id'] == $this->controller->order->packed_id)
            {
                $this->output .= "----------------------------------------------------------------------------------------------------".PHP_EOL;
                $this->output .= "Doing Order Number: ".$od['order_number']." Using ".$this->controller->courier->getCourierName($od['courier_id']).PHP_EOL;
                $db->updateDatabaseFields('orders', array('status_id' => $this->controller->order->fulfilled_id, 'date_fulfilled' => time()), $id);
                //order is now fulfilled, reduce stock
                $items = $this->controller->order->getItemsForOrder($id);
                $this->output .= "Reducing Stock and recording movement fo order id: $id".PHP_EOL;
                $this->removeStock($items, $id);
                if( !empty($od['tracking_email']) )
                {
                    if($od['client_id'] == 59)
                    {
                        $this->output .= "Sending Noa Sleep confirmation".PHP_EOL;
                        Email::sendNoaConfirmEmail($id);
                    }
                    else
                    {
                        $this->output .= "Sending tracking email for {$od['order_number']}".PHP_EOL;
                        Email::sendTrackingEmail($id);
                    }
                }
                if($od['client_id'] == 52) //figure8
                {
                    $this->notifyFigure8($od);
                }
                Session::set('showfeedback', true);
                $_SESSION['feedback'] .= "<p>{$od['order_number']} has been successfully fulfilled</p>";
            }
            else
            {
                Session::set('showerrorfeedback', true);
        	    $_SESSION['errorfeedback'] .= "<h3>{$od['order_number']} has not had the labels or pickslip printed</h3><p>Please do at least one and try again</p>";
            }

        }
        $this->recordOutput("order_fulfillment/hunters");
    }

    public function fulfillLocalOrder()
    {
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "FULFILLING LOCAL COURIER ORDERS ON ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        $db = Database::openConnection();
        //echo "<pre>",print_r($this->controller->request->data),"</pre>";die();
        $od = $this->controller->order->getOrderDetail($this->controller->request->data['order_ids']);

        $o_values = array(
            'status_id'			=>	$this->controller->order->fulfilled_id,
            'date_fulfilled'	=>	time(),
            'consignment_id'    =>  $this->controller->request->data['consignment_id'],
            'total_cost'        =>  $this->controller->request->data['local_charge']
        );
        $db->updateDatabaseFields('orders', $o_values, $this->controller->request->data['order_ids']);
        //order is now fulfilled, reduce stock
        $items = $this->controller->order->getItemsForOrder($this->controller->request->data['order_ids']);
        $this->output .= "Reducing Stock and recording movement fo order id: ".$this->controller->request->data['order_ids'].PHP_EOL;
        $this->removeStock($items, $this->controller->request->data['order_ids']);

        if( !empty($od['tracking_email']) )
        {
            if($od['client_id'] == 59)
            {
                $this->output .= "Sending Noa Sleep confirmation".PHP_EOL;
                Email::sendNoaConfirmEmail($od['id']);
            }
            else
            {
                 $this->output .= "Sending tracking email for {$od['order_number']}".PHP_EOL;
                //$mailer->sendTrackingEmail($id);
                Email::sendTrackingEmail($od['id']);
            }

        }
        if($od['client_id'] == 52) //figure8
        {
            $this->notifyFigure8($od);
        }
        $this->recordOutput('order_fulfillment/local');
        Session::set('showfeedback', true);
        $_SESSION['feedback'] .= "<p>Order number {$od['order_number']} has been recorded as dispatched by {$od['courier_name']}</p>";
    }

    public function fulfillDirectFreightOrder()
    {
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "FULFILLING DIRECT FREIGHT ORDERS ON ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        $db = Database::openConnection();
        //echo "<pre>",print_r($this->controller->request->data),"</pre>";die();
        $od = $this->controller->order->getOrderDetail($this->controller->request->data['order_ids']);

        $o_values = array(
            'status_id'			=>	$this->controller->order->fulfilled_id,
            'date_fulfilled'	=>	time(),
            'consignment_id'    =>  $this->controller->request->data['consignment_id'],
            'total_cost'        =>  $this->controller->request->data['local_charge']
        );
        $db->updateDatabaseFields('orders', $o_values, $this->controller->request->data['order_ids']);
        //order is now fulfilled, reduce stock
        $items = $this->controller->order->getItemsForOrder($this->controller->request->data['order_ids']);
        $this->output .= "Reducing Stock and recording movement for order id: ".$this->controller->request->data['order_ids'].PHP_EOL;
        $this->removeStock($items, $this->controller->request->data['order_ids']);

        if( !empty($od['tracking_email']) )
        {
            if($od['client_id'] == 59)
            {
                $this->output .= "Sending Noa Sleep confirmation".PHP_EOL;
                Email::sendNoaConfirmEmail($od['id']);
            }
            else
            {
                 $this->output .= "Sending tracking email for {$od['order_number']}".PHP_EOL;
                //$mailer->sendTrackingEmail($id);
                Email::sendTrackingEmail($od['id']);
            }

        }
        if($od['client_id'] == 52) //figure8
        {
            $this->notifyFigure8($od);
        }
        $this->recordOutput('order_fulfillment/direct');
        Session::set('showfeedback', true);
        $_SESSION['feedback'] .= "<p>Order number {$od['order_number']} has been recorded as dispatched by Direct Freight</p>";
    }

    public function fulfillVicLocalOrder($order_ids)
    {
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "FULFILLING VIC LOCAL ORDERS ON ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        $db = Database::openConnection();
        $emails_to_send = array();
        foreach($order_ids as $id)
        {
            $od = $this->controller->order->getOrderDetail($id);
            if($od['status_id'] == $this->controller->order->picked_id || $od['status_id'] == $this->controller->order->packed_id)
            {
                $this->output .= "----------------------------------------------------------------------------------------------------".PHP_EOL;
                $this->output .= "Doing Order Number: ".$od['order_number']." Using ".$this->controller->courier->getCourierName($od['courier_id']).PHP_EOL;
                $db->updateDatabaseFields('orders', array('status_id' => $this->controller->order->fulfilled_id, 'date_fulfilled' => time(), 'total_cost' => Config::get('VIC_LOCAL_CHARGE')), $id);
                //order is now fulfilled, reduce stock
                $items = $this->controller->order->getItemsForOrder($id);
                $this->output .= "Reducing Stock and recording movement for order id: $id".PHP_EOL;
                $this->removeStock($items, $id);
                if($od['client_id'] == 59)
                {
                    $emails_to_send[] = $od['client_order_id'];
                }
                Session::set('showfeedback', true);
                $_SESSION['feedback'] .= "<p>{$od['order_number']} has been successfully fulfilled</p>";
            }
            else
            {
                Session::set('showerrorfeedback', true);
        	    $_SESSION['errorfeedback'] .= "<h3>{$od['order_number']} has not had the labels or pickslip printed</h3><p>Please do at least one and try again</p>";
            }
        }
        if( count($emails_to_send) )
        {
            $this->output .= "Sending Noa Sleep confirmations".PHP_EOL;
            Email::sendNoaLocalConfirmEmail($emails_to_send);
        }
        $this->recordOutput("order_fulfillment/viclocal");
    }

    public function fulfillCometOrder($order_ids)
    {
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "FULFILLING COMET ORDERS ON ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        $db = Database::openConnection();
        $emails_to_send = array();
        foreach($order_ids as $id)
        {
            $od = $this->controller->order->getOrderDetail($id);
            if($od['status_id'] == $this->controller->order->picked_id || $od['status_id'] == $this->controller->order->packed_id)
            {
                $this->output .= "----------------------------------------------------------------------------------------------------".PHP_EOL;
                $this->output .= "Doing Order Number: ".$od['order_number']." Using ".$this->controller->courier->getCourierName($od['courier_id']).PHP_EOL;
                $db->updateDatabaseFields('orders', array('status_id' => $this->controller->order->fulfilled_id, 'date_fulfilled' => time(), 'total_cost' => Config::get('COMET_VIC_CHARGE')), $id);
                //order is now fulfilled, reduce stock
                $items = $this->controller->order->getItemsForOrder($id);
                $this->output .= "Reducing Stock and recording movement for order id: $id".PHP_EOL;
                $this->removeStock($items, $id);
                if($od['client_id'] == 59)
                {
                    $emails_to_send[] = $od['client_order_id'];
                }
                Session::set('showfeedback', true);
                $_SESSION['feedback'] .= "<p>{$od['order_number']} has been successfully fulfilled</p>";
            }
            else
            {
                Session::set('showerrorfeedback', true);
        	    $_SESSION['errorfeedback'] .= "<h3>{$od['order_number']} has not had the labels or pickslip printed</h3><p>Please do at least one and try again</p>";
            }
        }
        if( count($emails_to_send) )
        {
            $this->output .= "Sending Noa Sleep confirmations".PHP_EOL;
            Email::sendNoaCometConfirmEmail($emails_to_send);
        }
        $this->recordOutput("order_fulfillment/cometlocal");
    }

    public function fulfillSolarOrder($order_ids)
    {
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "FULFILLING SOLAR ORDERS ON ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        $this->output .= "=========================================================================================================".PHP_EOL;
        $db = Database::openConnection();
        foreach($order_ids as $id)
        {
            $od = $this->controller->solarorder->getOrderDetail($id);
            if($od['status_id'] == $this->controller->order->picked_id || $od['status_id'] == $this->controller->order->packed_id)
            {
                $this->output .= "----------------------------------------------------------------------------------------------------".PHP_EOL;
                $this->output .= "Doing Order ID: ".$od['id'].PHP_EOL;
                $db->updateDatabaseFields('solar_orders', array('status_id' => $this->controller->order->fulfilled_id, 'date_fulfilled' => time() ), $id);
                //order is now fulfilled, reduce stock
                $items = $this->controller->solarorder->getItemsForOrder($id);

                $this->output .= "Reducing Stock and recording movement for order id: $id".PHP_EOL;
                $this->removeStock($items, $id);

                Session::set('showfeedback', true);
                $_SESSION['feedback'] .= "<p>{$od['id']} has been successfully fulfilled</p>";
            }
            else
            {
                Session::set('showerrorfeedback', true);
        	    $_SESSION['errorfeedback'] .= "<h3>{$od['order_number']} has not had the labels or pickslip printed</h3><p>Please do at least one and try again</p>";
            }
        }
        $this->recordOutput("order_fulfillment/solar");
    }

    public function fulfillOurTruckOrder()
    {
        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "FULFILLING 3PLTruck ORDERS ON ".date("jS M Y (D), g:i a (T)").PHP_EOL;
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
        //order is now fulfilled, reduce stock
        $items = $this->controller->order->getItemsForOrder($this->controller->request->data['order_ids']);
        $this->output .= "Reducing Stock and recording movement fo order id: ".$this->controller->request->data['order_ids'].PHP_EOL;
        $this->removeStock($items, $this->controller->request->data['order_ids']);
        $this->recordOutput('order_fulfillment/3pl_truck');
        Session::set('showfeedback', true);
        $_SESSION['feedback'] .= "<p>Order number {$od['order_number']} has been recorded as dispatched by our truck</p>";
    }

    private function createEparcelOrder($eparcel_clients)
    {
        $db = Database::openConnection();
        $c = 0;
        foreach($eparcel_clients as $client_id => $array)
        {
            $client_details = $this->controller->client->getClientInfo($client_id);
            $eParcelClass = "Eparcel";
            if(!is_null($client_details['eparcel_location']))
                $eParcelClass = $client_details['eparcel_location']."Eparcel";
            /* */
            $response = $this->controller->{$eParcelClass}->CreateOrderFromShipment($array['request']);
            $this->output .= "eParcel create order response".PHP_EOL;
            $this->output .= print_r($response, true);

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
        	        $o_values = array(
        				'eparcel_order_id'	=>	$eparcel_order_id,
                        //'eparcel_order_id'	=>	1322,
        				'status_id'			=>	$this->controller->order->fulfilled_id,
        				'date_fulfilled'	=>	time()
        			);
                    $this->output .= "Updating Orders".PHP_EOL;
                    $this->output .= print_r($o_values, true).PHP_EOL;
        			$db->updateDatabaseFields('orders', $o_values, $id);

                    $od = $this->controller->order->getOrderDetail($id);
                    if( !empty($od['tracking_email']) )
                    {
                        if($od['client_id'] != 69)
                        {
                            $this->output .= "Sending tracking email for {$od['order_number']}".PHP_EOL;
                            //$mailer->sendTrackingEmail($id);
                            Email::sendTrackingEmail($id);
                            $this->controller->order->updateOrderValue('customer_emailed', 1, $id);
                        }
                    }
                    if($od['client_id'] == 52) //figure8
                    {
                        $this->notifyFigure8($od);
                    }
                    //order is now fulfilled, reduce stock
                    $items = $this->controller->order->getItemsForOrder($id);
                    $this->output .= "Reducing Stock and recording movement fo order id: $id".PHP_EOL;
                    $this->removeStock($items, $id);
        		}
                Session::set('showfeedback', true);
                $_SESSION['feedback'] .= "<p>Manifest ID: $order_id successfully created and submitted to eParcel</p>";
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

    private function sendTrackingEmail()
    {

    }

    private function recordOutput($file)
    {
        Logger::logOrderFulfillment($file, $this->output);
    }

    private function notifyFigure8($od, $url = "http://autom8.figure8services.com.au/engage/jobPartsConsignmentStore.php")
    {
        $db = Database::openConnection();
        $courier = $db->queryValue('couriers', array('id' => $od['courier_id']), 'name');
        if($courier == "Local")
        {
            $courier = $od['courier_name'];
        }
        $data = array(
            'partsOrderedGroupid'   => $od['customer_order_id'],
            'courier_name'          => $courier,
            'consignment_id'        => $od['consignment_id']
        );
        $this->output .= "Notifying Figure 8".PHP_EOL;
        $this->output .= print_r($data, true).PHP_EOL;
        Curl::sendPostRequest($url, $data, 'form');
    }

}
