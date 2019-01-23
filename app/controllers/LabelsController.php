<?php

/**
 * Labels controller
 *

 Handles generation and printing of labels

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class LabelsController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
        $this->Security->config("validateForm", false);
    }

    public function eparcelLabels()
    {
        //echo "<pre>",print_r($this->request),"</pre>";die();
        $do_ep = false;
        $do_pp = false;
        $ep_group = array(
            "group"			=>	'Express Post',
                "layout"		=>	'A4-3pp',
                "branded"		=>	0,
                "left_offset"	=>	0,
                "top_offset"	=>	0
        );

        $pp_group = array(
            "group"			=>	'Parcel Post',
                "layout"		=>	'A4-4pp',
                "branded"		=>	0,
                "left_offset"	=>	0,
                "top_offset"	=>	0
        );

        $request = array(
            	"preferences"	=>	array(
                	"type"		=>	"PRINT",
                "groups"	=>	array()
            ),
            "shipments"		=>  array()
        );
        $i = 0;
        $eparcel_clients = array();
        $single_order = (count($this->request->data['orders']) == 1);
        //$eparcel_clients[$od['client_id']]['do_ep'] = false;
        //$eparcel_clients[$od['client_id']]['do_pp'] = false;
        foreach($this->request->data['orders'] as $id)
        {
            $order_id = $id;
            $od = $this->order->getOrderDetail($id);
            $client = $this->client->getClientInfo($od['client_id']);
            $eParcelClass = "Eparcel";
            if(!is_null($client['eparcel_location']))
                $eParcelClass = $client['eparcel_location']."Eparcel";
            if($od['labels'] == 0)
            {
                $items = $this->order->getItemsForOrder($id);
                $details = $this->{$eParcelClass}->getShipmentDetails($od, $items);
                $this->order->updateOrderValue('labels', count( $details['items'] ), $id);
            }



            $courier = $this->courier->getCourierName($od['courier_id']);
            if($courier == "eParcel" || $courier == "eParcel Express")
            {
                //echo "<p>Shipment id: {$od['eparcel_shipment_id']}</p>";
                if(!array_key_exists($od['client_id'], $eparcel_clients))
                {
                    $eparcel_clients[$od['client_id']]['request']= array(
                        "preferences"	=>	array(
                        	"type"		=>	"PRINT",
                            "groups"	=>	array(
                            )
                        ),
                    );
                }
                $eparcel_clients[$od['client_id']]["client"] = $client;
                if($od['eparcel_express'] == 1 || $courier == "eParcel Express")
                {
                    $eparcel_clients[$od['client_id']]['do_ep'] = true;
                }
                if($od['eparcel_express'] == 0 || $courier != "eParcel Express")
                {
                	$eparcel_clients[$od['client_id']]['do_pp'] = true;
                }
                $eparcel_clients[$od['client_id']]["order_ids"][] = $id;
                //$eparcel_clients[$od['client_id']]['request']['shipments'][$i] = array("shipment_id"	=>	$od['eparcel_shipment_id'], 'ship_to' => $od['ship_to']);
                $eparcel_clients[$od['client_id']]['request']['shipments'][$i] = array("shipment_id"	=>	$od['eparcel_shipment_id']);
                ++$i;
                if(!isset($eparcel_clients[$od['client_id']]['do_ep'])) $eparcel_clients[$od['client_id']]['do_ep'] = false;
                if(!isset($eparcel_clients[$od['client_id']]['do_pp'])) $eparcel_clients[$od['client_id']]['do_pp'] = false;
            }
        }
        $order_id = ($single_order)? $order_id: 0;
        $error = false;
        $error_string = "";
        $request_ids = array();
        //echo "<pre>",print_r($eparcel_clients),"</pre>"; die();
        foreach($eparcel_clients as $client_id => $array)
        {
            //echo "<pre>",print_r($array),"</pre>";
            if($array['do_ep']) $array['request']['preferences']['groups'][] = $ep_group;
            if($array['do_pp']) $array['request']['preferences']['groups'][] = $pp_group;
            //echo "<pre>",print_r($array['request']),"</pre>"; continue;
            $lResponse = $this->{$eParcelClass}->CreateLabels($array['request']);
            //echo "<pre>",print_r($lResponse),"</pre>"; //die();
            /* */
            if(isset($lResponse['errors']))
            {
                $error_string = "";
                foreach($lResponse['errors'] as $e)
                {
                    $error_string .= "<h3>Error Code: ".$e['code']."</h3><h4>".$e['name']."</h4><p>".$e['message']."</p>";
                }
                $error = true;
            }
            elseif($lResponse['labels'][0]['status'] == "ERROR")
            {
                $error_string = "<p>Label generation failed</p>";
                $error = true;
            }
            else
            {
                $request_ids[] = $lResponse['labels'][0]['request_id'];
                foreach($array['order_ids'] as $oid)
                {
                    $this->order->updateStatus($this->order->packed_id, $oid);
                }
            }

        }
        //die();
        //render the page
        Config::setJsConfig('curPage', "eparcel-labels");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/default/", Config::get('VIEWS_PATH') . 'labels/eparcelLabels.php', [
            'page_title'    => "Generating eParcel Labels For {$client['client_name']}",
            'error'         => $error,
            'error_string'  => $error_string,
            'eParcelClass'  => $eParcelClass,
            'request_ids'   => $request_ids,
            'client_id'     => $od['client_id'],
            'order_id'      => $order_id,
            'order_ids'     => $eparcel_clients[$od['client_id']]["order_ids"]
        ]);
    }

    public function isAuthorized(){

        return true;
    }
}