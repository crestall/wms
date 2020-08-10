<?php

/**
 * The courierselector class.
 *
 * handles the selection and allocation of couriers to jobs
 *
 
 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */
 class CourierSelector{

     /**
      * controller
      *
      * @var Controller
      */
     protected $controller;
     protected $order_details;
     protected $client_details;
     protected $items;

     /**
      * Constructor
      *
      * @param Controller $controller
      */
    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
    }

    public function assignCourier($order_id, $courier_id, $courier_name = "", $ip = 0)
    {
        //die('dcourier_id '.$courier_id);
        $this->order_details = $this->controller->order->getOrderDetail($order_id);
        $this->client_details = $this->controller->client->getClientInfo($this->order_details['client_id']);
        $this->items =  $this->controller->order->getItemsForOrder($order_id);
        if($this->order_details['eparcel_express'] > 0 && $courier_id == $this->controller->courier->eParcelId)
        {
            $courier_id = $this->controller->courier->eParcelExpressId;
        }
        if($courier_id == $this->controller->courier->eParcelId)
            $this->assignEparcel($order_id, $courier_id, false, $ip);
        elseif($courier_id == $this->controller->courier->eParcelExpressId)
            $this->assignEparcel($order_id, $courier_id, true, $ip);
        elseif($courier_id == $this->controller->courier->fsgId)
            $this->assignFSG($order_id);
        elseif($courier_id == $this->controller->courier->localId)
            $this->assignLocal($order_id, $courier_name);
        elseif($courier_id == $this->controller->courier->directFreightId)
            $this->assignDirectFreight($order_id);
        elseif($courier_id == 0)
            $this->assignBest($order_id);
    }

    public function chooseEparcel($od = array())
    {
        if(empty($od))
        {
            $od = $this->order_details;
        }
        if(
            $od['eparcel_express'] == 1 ||
            preg_match("/(p(?:\.|ost)? ?o(?:\.|ffice)?)? ?box/i", $od['address'], $matches) ||
            preg_match("/parcel ?locker/i", $od['address'], $matches) ||
            preg_match("/parcel ?pickup/i", $od['address'], $matches) ||
            preg_match("/locked bag/i", $od['address'], $matches) ||
            preg_match("/cmb /i", $od['address'], $matches) ||
            $od['postcode'] == 3351 ||
            $od['client_id'] == 6           //Only eparcel for big bottle
        )
            return true;
        return false;
    }

    private function assignEparcel($order_id, $courier_id, $express = false, $ip = 0)
    {
        $db = Database::openConnection();
        $eParcelClass = "Eparcel";
        if(!is_null($this->client_details['eparcel_location']))
            $eParcelClass = $this->client_details['eparcel_location']."Eparcel";
        $oi_ids = array();
    	foreach($this->items as $i)
    	{
            $oi_ids[$i['line_id']] = $i['item_id'];
    	}
        $eparcel_details = $this->controller->{$eParcelClass}->getShipmentDetails($this->order_details, $this->items, $express);
        //echo "<pre>",print_r($eparcel_details),"</pre>"; die();
        $eparcel_shipments['shipments'][0] = $eparcel_details;
        if($ip == 0)
        {
            $eparcel_response = $this->controller->{$eParcelClass}->GetQuote($eparcel_shipments);
            if(!isset($eparcel_response['errors']))
            {
                if( $eparcel_response['shipments'][0]['shipment_summary']['total_cost'] > Config::get('MAX_SHIPPING_CHARGE') )
                {
            	    Session::set('showcouriererrorfeedback', true);
            	    $_SESSION['couriererrorfeedback'] .= "<h3>Please check the value for {$this->order_details['order_number']}</h3>";
                    $_SESSION['couriererrorfeedback'] .= "<h4>The quoted eParcel charge is $".number_format($eparcel_response['shipments'][0]['shipment_summary']['total_cost'], 2)."</h4>";
                    return;
                }
            }
        }
       	$sResponse = $this->controller->{$eParcelClass}->CreateShipments($eparcel_shipments);
        //echo "<pre>",print_r($sResponse),"</pre>"; die();
        if(isset($sResponse['errors']))
    	{
    	    Session::set('showcouriererrorfeedback', true);
    	    $_SESSION['couriererrorfeedback'] .= "<h3>{$this->order_details['order_number']} had some errors when submitting to eParcel</h3>";
    		foreach($sResponse['errors'] as $e)
    		{
    			$_SESSION['couriererrorfeedback'] .= "<h3>Error Code: ".$e['code']."</h3><h4>".$e['name']."</h4><p>".$e['message']."</p>";
    		}
    	}
        else
        {
            Session::set('showcourierfeedback', true);
            $order_values['eparcel_shipment_id'] = $sResponse['shipments'][0]['shipment_id'];;
            $order_values['consignment_id'] = $sResponse['shipments'][0]['items'][0]['tracking_details']['consignment_id'];
            $order_values['total_cost'] = round($sResponse['shipments'][0]['shipment_summary']['total_cost'] * 1.35 * 1.1, 2); //GST already include 35% markup add 10% for fuel
            /*********** charge FREEDOM more *******************/
                if($this->order_details['client_id'] == 7)
                {
                    $order_values['total_cost'] = round($sResponse['shipments'][0]['shipment_summary']['total_cost'] * 1.4 * 1.1, 2);
                }
            /*********** end charge FREEDOM more *******************/
            $order_values['charge_code'] = $sResponse['shipments'][0]['items'][0]['product_id'];
            $order_values['labels'] = count($eparcel_details['items']);
            $order_values['courier_id'] = $courier_id;
            foreach($sResponse['shipments'][0]['items'] as $item_array)
            {
            	$oi_id = array_search($item_array['item_reference'], $oi_ids);
                //echo "<p>will update at $oi_id to {$item_array['tracking_details']['article_id']}</p>";
                $vals = array(
                    'article_id'        =>   $item_array['tracking_details']['article_id'],
                    'consignment_id'    =>   $item_array['tracking_details']['consignment_id']
                );
                $db->updateDatabaseFields('orders_items', $vals, $oi_id);
            }
            if($this->addBubblewrap())
                $order_values['bubble_wrap'] = 1;
            $db->updateDatabaseFields('orders', $order_values, $order_id);
            $_SESSION['courierfeedback'] .= "<p>Order number: {$this->order_details['order_number']} has been successfully submitted to eParcel</p>";
        }
    }

    private function assignFSG($order_id)
    {
        $db = Database::openConnection();
        Session::set('showcourierfeedback', true);
        $order_values = array(
            'courier_id'    => $this->controller->courier->fsgId
        );
        if($this->addBubblewrap())
            $order_values['bubble_wrap'] = 1;
        $db->updateDatabaseFields('orders', $order_values, $order_id);
        $_SESSION['courierfeedback'] .= "<p>Order number: {$this->order_details['order_number']} has been successfully assigned to FSG Deliveries</p>";
    }

    private function assignLocal($order_id, $courier_name)
    {
        $db = Database::openConnection();
        Session::set('showcourierfeedback', true);
        $order_values = array(
            'courier_id'    => $this->controller->courier->localId,
            'courier_name'  => $courier_name
        );
        if($this->addBubblewrap())
            $order_values['bubble_wrap'] = 1;
        $db->updateDatabaseFields('orders', $order_values, $order_id);
        $_SESSION['courierfeedback'] .= "<p>Order number: {$this->order_details['order_number']} has been successfully assigned to $courier_name</p>";
    }

    private function assignDirectFreight($order_id)
    {
        //die('Assigning Direct Freight');
        $db = Database::openConnection(); 
        $df_details = $this->controller->directfreight->getDetails($this->order_details, $this->items);
        //echo "<pre>",print_r($df_details),"</pre>"; die();
        $response = $this->controller->directfreight->createConsignment($df_details);
        //echo "<pre>",print_r($response),"</pre>"; die();

        if($response['ResponseCode'] != 300)
        {
            Session::set('showcouriererrorfeedback', true);
    	    $_SESSION['couriererrorfeedback'] = "<h3><i class='far fa-times-circle'></i>{$this->order_details['order_number']} had some errors when submitting to DirectFreight</h3>";
    		$_SESSION['couriererrorfeedback'] .= "<h4>".$response['ResponseMessage']."</p>";
            return false;
        }
        else
        {
            $consignment = $response['ConsignmentList'][0];
            if($consignment['ResponseCode'] != 200)
            {
                Session::set('showcouriererrorfeedback', true);
        	    $_SESSION['couriererrorfeedback'] .= "<h3><i class='far fa-times-circle'></i>{$this->order_details['order_number']} had some errors when submitting to DirectFreight</h3>";
        		$_SESSION['couriererrorfeedback'] .= "<h4>".$consignment['ResponseMessage']."</h4>";
                return false;
            }
            else
            {
                //echo "<pre>",print_r($response),"</pre>"; die();
                //All good, set the courier
                $order_values['consignment_id'] = $consignment['Connote'];
                $order_values['total_cost'] = round($consignment['TotalCharge'] * 1.35 * 1.1 * DF_FUEL_SURCHARGE, 2); //GST and 35% markup and fuel surcharge
                /*********** charge FREEDOM more *******************/
                    if($this->order_details['client_id'] == 7)
                    {
                        $order_values['total_cost'] = round($consignment['TotalCharge'] * 1.4 * 1.1 * DF_FUEL_SURCHARGE, 2);
                    }
                /*********** end charge FREEDOM more *******************/
                $order_values['courier_id'] = $this->controller->courier->directFreightId;
                $order_values['label_url'] = $response['LabelURL'];
                if($this->addBubblewrap())
                    $order_values['bubble_wrap'] = 1;
                if($db->updateDatabaseFields('orders', $order_values, $order_id))
                {
                    $_SESSION['courierfeedback'] .= "<p>Order number: {$this->order_details['order_number']} has been successfully submitted to Direct Freight</p>";
                    Session::set('showcourierfeedback', true);
                    return true;
                }
                else
                {
                    Session::set('showcouriererrorfeedback', true);
            	    $_SESSION['couriererrorfeedback'] = "<h3>Sorry a database error has occurred</h3>";
            		$_SESSION['couriererrorfeedback'] .= "<h4>Please try again in a moment</h4>";
                    return false;
                }
            }
        }
    }

    private function addBubblewrap()
    {
        if($this->client_details['use_bubblewrap'] > 0)
            return true;
        foreach($this->items as $item)
        {
            if($item['requires_bubblewrap'] > 0)
                return true;
        }
        return false;
    }

    private function assignBest($order_id)
    {
        if($this->chooseEparcel())
        {
            $this->assignCourier($order_id, $this->controller->courier->eParcelId);
        }
        else
        {
            $eparcel_details = $this->controller->Eparcel->getShipmentDetails($this->order_details, $this->items);
            $eparcel_shipments['shipments'][0]  = $eparcel_details;
            $sResponse = $this->controller->Eparcel->GetQuote($eparcel_shipments);

            /*
            $h_details = $this->controller->Hunters3KG->getDetails($this->order_details, $this->items);
            $h3kg_result = $this->controller->Hunters3KG->getQuote($h_details);
            $hplu_result = $this->controller->HuntersPLU->getQuote($h_details);
            //$hpal_result = $this->controller->HuntersPAL->getQuote($h_details);*/
             /*
            if( empty($h3kg_result) || isset($h3kg_result['errorCode']))
                $h3kg = "";
            else
                $h3kg = ($h3kg_result[0]['fee']*1.1 > 0)? $h3kg_result[0]['fee']*1.1*Config::get('HUNTERS_FUEL_SURCHARGE') : "";
            if( empty($hplu_result) || isset($hplu_result['errorCode']))
                $hplu = "";
            else
                $hplu = ($hplu_result[0]['fee']*1.1 > 0)? $hplu_result[0]['fee']*1.1*Config::get('HUNTERS_FUEL_SURCHARGE') : "";

            if( empty($hpal_result) || isset($hpal_result['errorCode']))
                $hpal = "";
            else
                $hpal = ($hpal_result[0]['fee']*1.1 > 0)? $hpal_result[0]['fee']*1.1*Config::get('HUNTERS_FUEL_SURCHARGE') : "";
            */
            if(!isset($sResponse['errors']))
                $ep = ($sResponse['shipments'][0]['shipment_summary']['total_cost'] > 0)? $sResponse['shipments'][0]['shipment_summary']['total_cost'] : "";
            else
                $ep = "";

            $cs = array(
                $this->controller->courier->eParcelId    =>  $ep,
                //$this->controller->courier->huntersId    =>  $h3kg,
                //$this->controller->courier->huntersPluId =>  $hplu,
                //$this->controller->courier->huntersPalId =>  $hpal
            );

            if($this->order_details['client_id'] == 59)
            {
                //no eparcel for NOA
               // $min = min(array_filter(array($h3kg, $hplu, $hpal)));
                $min = min(array_filter(array($h3kg, $hplu)));
                $courier_id = array_search($min, $cs);
            }
            else
            {
                //$min = min(array_filter(array($h3kg,$ep, $hplu, $hpal)));
                $min = min(array_filter(array($h3kg,$ep, $hplu)));
                $courier_id = array_search($min, $cs);
            }

            $this->assignCourier($order_id, $courier_id);
        }
    }

}
