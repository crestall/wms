<?php

/**
 * The courierselector class.
 *
 * handles the selection and allocation of couriers to jobs
 *
 
 * @author     Mark Solly <mark.solly@fsg.com.au>
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
     protected $item_count;
     protected $sku_count;
     protected $weight;
     protected $handling_charge;

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
        $this->item_count = $this->controller->order->getItemCountForOrder($order_id);
        $this->sku_count = $this->controller->order->getSKUCountForOrder($order_id);
        $packages = $this->controller->order->getPackagesForOrder($order_id);
        $parcels = Packaging::getPackingForOrder($this->order_details,$this->items,$packages);
        $this->weight = 0;
        foreach($parcels as $parc)
        {
            $this->weight += $parc['weight'];
        }
        $this->handling_charge = $this->getHandlingCharge($this->order_details['client_id']);
        //die('handling charge '.$this->handling_charge);
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
            strtolower($od['country']) != "au" ||
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
        if($this->order_details['client_id'] == 87) //PBA
            $this->controller->$eParcelClass->setFromAddress(['name' => "Performance Brands Aust (via FSG 3PL)"]);
        if($this->order_details['client_id'] == 89) //BUZZBEE
            $this->controller->$eParcelClass->setFromAddress(['name' => "BuzzBee Aust (via FSG 3PL)"]);
        if($this->order_details['client_id'] == 91) //BACK 2 BASICS
            $this->controller->$eParcelClass->setFromAddress(['name' => "Back2Basics Golf (via FSG 3PL)"]);
        if($this->order_details['client_id'] == 82) //ONEPLATE
            $this->controller->$eParcelClass->setFromAddress(['name' => "OnePlate (via FSG 3PL)"]);
        $eparcel_details = $this->controller->{$eParcelClass}->getShipmentDetails($this->order_details, $this->items, $express);
        //echo "<pre>",print_r(json_encode($eparcel_details)),"</pre>"; die();
        $eparcel_shipments['shipments'][0] = $eparcel_details;
        $this->controller->$eParcelClass->resetFromAddress();
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
            $order_values['handling_charge'] = $this->handling_charge;
            //$postage = $order_values['postage_charge'] = round($sResponse['shipments'][0]['shipment_summary']['total_cost_ex_gst'] * 1.35 , 2);
            $postage = $this->getPostageCharge($this->order_details['client_id'], $sResponse['shipments'][0]['shipment_summary']['total_cost_ex_gst']);
            $order_values['postage_charge'] = $postage;
            if($this->order_details['country'] == "AU")
            {
                $order_values['gst'] = round(($this->handling_charge + $postage) * 0.1, 2);
                $order_values['total_cost'] = round(($this->handling_charge + $postage) * 1.1, 2);
            }
            else
            {
                $order_values['gst'] = round( $this->handling_charge * 0.1, 2 );
                $order_values['total_cost'] = round( $this->handling_charge * 1.1 + $postage , 2);
            }
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
            'courier_id'        => $this->controller->courier->localId,
            'courier_name'      => $courier_name,
            'handling_charge'   => $this->handling_charge
        );
        if($this->addBubblewrap())
            $order_values['bubble_wrap'] = 1;
        $db->updateDatabaseFields('orders', $order_values, $order_id);
        $_SESSION['courierfeedback'] .= "<p>Order number: {$this->order_details['order_number']} has been successfully assigned to $courier_name</p>";
    }

    private function assignDirectFreight($order_id)
    {
        //die('Assigning Direct Freight');
        if($this->chooseEparcel())
        {
            Session::set('showcouriererrorfeedback', true);
    	    $_SESSION['couriererrorfeedback'] = "<h3><i class='far fa-times-circle'></i>{$this->order_details['order_number']} had some errors when submitting to DirectFreight</h3>";
    		$_SESSION['couriererrorfeedback'] .= "<p>This address can only be serviced by Australia Post</p>";
            return false;
        }
        $db = Database::openConnection();
        $df_details = $this->controller->directfreight->getDetails($this->order_details, $this->items);
        //echo "<pre>",print_r($df_details),"</pre>"; die();
        $response = $this->controller->directfreight->createConsignment($df_details);
        //echo "<pre>",print_r($response),"</pre>"; die();

        if($response['ResponseCode'] != 300)
        {
            Session::set('showcouriererrorfeedback', true);
    	    $_SESSION['couriererrorfeedback'] = "<h3><i class='far fa-times-circle'></i>{$this->order_details['order_number']} had some errors when submitting to DirectFreight</h3>";
    		$_SESSION['couriererrorfeedback'] .= "<p>".$response['ResponseMessage']."</p>";
            return false;
        }
        else
        {
            $consignment = $response['ConsignmentList'][0];
            if($consignment['ResponseCode'] != 200)
            {
                Session::set('showcouriererrorfeedback', true);
        	    $_SESSION['couriererrorfeedback'] = "<h3><i class='far fa-times-circle'></i>{$this->order_details['order_number']} had some errors when submitting to DirectFreight</h3>";
        		$_SESSION['couriererrorfeedback'] .= "<h4>".$consignment['ResponseMessage']."</h4>";
                return false;
            }
            else
            {
                //echo "<pre>",print_r($response),"</pre>"; die();
                //All good, set the courier
                $surcharges = Utility::getDFSurcharges($df_details['ConsignmentList'][0]['ConsignmentLineItems']);
                $order_values['handling_charge'] = $this->handling_charge;
                $order_values['consignment_id'] = $consignment['Connote'];
                //$postage = $order_values['postage_charge'] = round( ($consignment['TotalCharge'] + $surcharges) * 1.35 * DF_FUEL_SURCHARGE , 2);
                $postage = $this->getPostageCharge($this->order_details['client_id'],  ($consignment['TotalCharge'] + $surcharges) * DF_FUEL_SURCHARGE);
                $order_values['postage_charge'] = $postage;
                if($this->order_details['country'] == "AU")
                {
                    $order_values['gst'] = round(($this->handling_charge + $postage) * 0.1, 2);
                    $order_values['total_cost'] = round(($this->handling_charge + $postage) * 1.1, 2);
                }
                else
                {
                    $order_values['gst'] = round( $this->handling_charge * 0.1, 2 );
                    $order_values['total_cost'] = round( $this->handling_charge * 1.1 + $postage , 2);
                }
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

            $df_details = $this->controller->directfreight->getDetails($this->order_details, $this->items);
            $dfresponse = $this->controller->directfreight->getQuote($df_details);
            $df_response = json_decode($dfresponse,true);

            if(!isset($sResponse['errors']))
                $ep = ($sResponse['shipments'][0]['shipment_summary']['total_cost'] > 0)? round($sResponse['shipments'][0]['shipment_summary']['total_cost'] * 1.1,2) : "";
            else
                $ep = "";

            if($df_response['ResponseCode'] == 300)
                $df = round($df_response['TotalFreightCharge'] * 1.1 * DF_FUEL_SURCHARGE, 2);
            else
                $df = "";
            if(!empty($df) && !empty($ep))
            {
                $cs = array(
                    $this->controller->courier->eParcelId           =>  $ep,
                    $this->controller->courier->directFreightId     =>  $df,
                );

                //$min = min(array_filter(array($h3kg,$ep, $hplu, $hpal)));
                $min = min(array_filter(array($df,$ep)));
                $courier_id = array_search($min, $cs);

                //echo "<p>Will assign $order_id to $courier_id for $min</p>";
                $this->assignCourier($order_id, $courier_id);
            }
        }
    }

    public function getPostageCharge($client_id, $courier_carge)
    {
        //FREEDOM
        if($client_id == 7)
        {
            return round($courier_carge * 1.4 , 2);
        }
        //ONE PLATE
        if($client_id == 82)
        {
            return round($courier_carge * 1.1 , 2);
        }
        //BDS
        if($client_id == 86)
        {
            //return round($courier_carge * 1.2 , 2);
            return round($courier_carge * 1.3 , 2); 
        }
        //PBA
        if($client_id == 87)
        {
            return round($courier_carge * 1.3 , 2);
        }
        //Everyone Else
        return round($courier_carge * 1.35 , 2);
    }

    public function getHandlingCharge($client_id)
    {
        //BDS
        if($client_id == 86)
        {
            if($this->item_count < 10)
                return 4;
            if($this->item_count < 50)
                return 5;
            if($this->item_count <= 100)
                return 10;
            if($this->item_count > 100)
                return 15;

           /*  return (3 + 0.55 * $this->sku_count + 0.12 * $this->item_count);*/
        }
        //PBA and BACK2BASICS
        if($client_id == 87 || $client_id == 91)
        {
            if($this->item_count == 1)
                return 3;
            if($this->item_count <= 5)
                return 5;
            return 10;
        }
        //BuzzBee
        if($client_id == 89)
        {
            if($this->weight < 5)
                return 3;
            if($this->weight < 20)
                return 5;
            if($this->weight >= 20)
                return 10;
        }
        //Everyone else
        return 0;
    }

}
