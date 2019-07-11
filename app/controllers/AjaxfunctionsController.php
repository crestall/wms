<?php

/**
 * Ajax Functions controller
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class ajaxfunctionsController extends Controller
{

    public function beforeAction()
    {
        parent::beforeAction();
        $actions = [
            'adjustAllocationForm',
            'calcOriginPick',
            'deactivateUser',
            'deleteClientLocation',
            'deletePackage',
            'fulfillOrder',
            'getABox',
            'getAddress',
            'getAnOrderByNumber',
            'getASummary',
            'getItemByBarcode',
            'getItems',
            'getItemsInLocation',
            'getOrderByConID',
            'getScannedItem',
            'getSuburbs',
            'getUnfulfilledAdmin',
            'getOrderTrends',
            'getPickErrors',
            'getShippingQuotes',
            'getTopProducts',
            'calculateTruckCost',
            'checkRoleNames',
            'checkSkus',
            'checkBarcodes',
            'checkBoxBarcodes',
            'checkLocations',
            'reactivateUser',
            'recordDispatch',
            'selectCourier',
            'updateAllocation',
            'updateFreightCharge',
            'updateLocation',
            'updateOrderComments',
            'updateWarningLevel'
        ];
        $this->Security->config("validateForm", false);
        $this->Security->requireAjax($actions);
    }

    public function updateAllocation()
    {
        $post_data = array();
        $data = array(
            'error'     =>  false,
            'feedback'  =>  ''
        );
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $order_items = array();
        //print_r($this->request->data['allocation']);die();
        foreach($this->request->data['allocation'] as $item_id => $array)
        {
            //echo "<pre>",print_r($array),"</pre>"; die();
            $location = array();
            $location[] = array(
                'location_id'   => $array['location_id'],
                'qty'           => $array['qty']
            );
            $order_items[] = array(
                'locations' => $location,
                'item_id'   => $item_id
            );
        }

        if($this->order->updateItemsForOrder($order_items, $order_id))
        {
            //do nothing
        }
        else
        {
            $data['error'] = true;
            $data['feedback'] = 'A database error has occurred. Please try again';
        }

        $this->view->renderJson($data);
    }

    public function deactivateUser()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        $this->user->deactivateUser($this->request->data['userid']);
    }

    public function getItemsInLocation()
    {
        echo "<pre>",print_r($this->request),"</pre>"; die();

    }

    public function reactivateUser()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        $this->user->reactivateUser($this->request->data['userid']);
    }

    public function calcOriginPick()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; //die();
        foreach($this->request->query as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $rails = $joins = $ground_clips = $rail_joiners = $tile_feet = $tin_feet = $z_bolts = 0;
        $bank_count = count($this->request->query['banks']);
        foreach($this->request->query['banks'] as $bank)
        {
            $rc = ceil($bank['qty'] / 2);
            $rj = floor($bank['qty'] / 4) * 2;
            $rails += $rc;
            $joins += $bank['qty'] - 1;
            $ground_clips += 2 * $bank['qty'];
            $rail_joiners += $rj;
        }
        if($this->request->query['roof_type'] == "tin")
        {
            $tin_feet = 4 * $rails + 2;
        }
        elseif($this->request->query['roof_type'] == "tile")
        {
            $tile_feet = $z_bolts = 4 * $rails + 2;
        }
        ++$rails;
        ++$rail_joiners;
        $end_clamps = 4 * $bank_count + 4;
        $interclamps = 2 * $joins + 4;
        $earth_lugs = 2 * $bank_count + 2;

        $rails = array(
            'id'    => 11650,
            'qty'   => $rails
        );
        $end_clamps = array(
            'id'    => 11651,
            'qty'   => $end_clamps
        );
        $interclamps = array(
            'id'    => 11655,
            'qty'   => $interclamps
        );
        $earth_lugs = array(
            'id'    => 11654,
            'qty'   => $earth_lugs
        );
        $ground_clips = array(
            'id'    => 11653,
            'qty'   => $ground_clips
        );
        $rail_joiners = array(
            'id'    => 11659,
            'qty'   => $rail_joiners
        );
        $tin_feet = array(
            'id'    => 11660,
            'qty'   => $tin_feet
        );
        $tile_feet = array(
            'id'    => 11661,
            'qty'   => $tile_feet
        );
        $z_bolts = array(
            'id'    => 11838,
            'qty'   => $z_bolts
        );

        $parts = array(
            'rails'         => $rails,
            'end_clamps'    => $end_clamps,
            'inter_clamps'   => $interclamps,
            'earth_lugs'    => $earth_lugs,
            'ground_clips'  => $ground_clips,
            'rail_joiners'  => $rail_joiners,
            'tin_feet'      => $tin_feet,
            'tile_feet'     => $tile_feet,
            'z_bolts'       => $z_bolts
        );

        /*echo "<pre>",print_r($rails),"</pre>";

        echo "<p>Rails: $rails</p>";;
        echo "<p>End Clamps: $end_clamps</p>";
        echo "<p>Inter Clamps: $interclamps</p>";
        echo "<p>Earth Lugs: $earth_lugs</p>";
        echo "<p>Ground Clips: $ground_clips</p>";
        echo "<p>Rail Joiners: $rail_joiners</p>";
        echo "<p>Tin Feet: $tin_feet</p>";
        echo "<p>Tile Feet: $tile_feet</p>";
        */
        $this->view->render(Config::get('VIEWS_PATH') . 'forms/origin_order_parts.php', [
            'parts' => $parts
        ]);
    }

    public function getScannedItem()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        $data = array(
            'error'         => false,
            'error_string'  => '',
            'item_count'    => 0,
            'item_id'       => 0
        );
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $item = $this->item->getItemByBarcode($barcode);
        if(empty($item))
        {
            $data['error'] = true;
            $data['error_string'] = "No item found for $barcode";
        }
        elseif($item['client_id'] != $clientid)
        {
            $data['error'] = true;
            $data['error_string'] = "That item does not belong to the client";
        }
        elseif( ($count = $this->order->getItemCountInOrder($orderid, $item['id'])) == 0)
        {
            $data['error'] = true;
            $data['error_string'] = "That item is not in this order";
        }
        else
        {
            $data['item_id'] = $item['id'];
            $data['item_count'] = $count;
        }
        $this->view->renderJson($data);
    }

    public function getOrderByConID()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        $data = array(
            'error'         => false,
            'error_string'  => '',
            'html'      => ''
        );
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $items = $this->order->getItemsForOrderByConId($con_id, $client_id);
        if(!count($items))
        {
            $data['error'] = true;
        }
        $html = $this->view->render(Config::get('VIEWS_PATH') . 'orders/orders_items.php', [
            'items' =>  $items
        ]);
        $data['html'] = $html;
        $this->view->renderJson($data);
    }

    public function updateOrderComments()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        foreach($this->request->data['updates'] as $order_id => $comment)
        {
            $this->order->updateOrderValue('3pl_comments', $comment, $order_id);
        }
    }

    public function fulfillSolarorder()
    {
       //echo "<pre>",print_r($this->request),"</pre>"; die();
        $data = array(
            'error'         => false,
            'not_logged'    => false,
            'error_string'  => '',
            'feedback'      => ''
        );

        $order_ids = ( is_array($this->request->data['order_ids']) )? $this->request->data['order_ids']: (array)$this->request->data['order_ids'];
        Session::set('feedback',"<h2><i class='far fa-check-circle'></i>Orders Have Been Fulfilled</h2>");
        Session::set('errorfeedback',"<h2><i class='far fa-times-circle'></i>These Orders Could Not Be Fulfilled</h2><p>Reasons are listed below</p>");
        Session::set('showfeedback', false);
        Session::set('showerrorfeedback', false);

        $this->orderfulfiller->fulfillSolarOrder($order_ids);

        if(Session::getAndDestroy('showfeedback') == false)
        {
            Session::destroy('feedback');
        }
        if(Session::getAndDestroy('showerrorfeedback') == false)
        {
            Session::destroy('errorfeedback');
        }


        $this->view->renderJson($data);
    }

    public function fulfillOrder()
    {
       //echo "<pre>",print_r($this->request),"</pre>"; die();
        $data = array(
            'error'         => false,
            'not_logged'    => false,
            'error_string'  => '',
            'feedback'      => ''
        );
        /*
        if(!Session::getIsLoggedIn())
        {
            $data['not_logged'] = true;
        }
        else
        {*/
            $order_ids = ( is_array($this->request->data['order_ids']) )? $this->request->data['order_ids']: (array)$this->request->data['order_ids'];
            Session::set('feedback',"<h2><i class='far fa-check-circle'></i>Orders Have Been Fulfilled</h2>");
            Session::set('errorfeedback',"<h2><i class='far fa-times-circle'></i>These Orders Could Not Be Fulfilled</h2><p>Reasons are listed below</p>");
            Session::set('showfeedback', false);
            Session::set('showerrorfeedback', false);
            if($this->request->data['courier_id'] == $this->courier->eParcelId || $this->request->data['courier_id'] == $this->courier->eParcelExpressId)
            {
                $this->orderfulfiller->fulfillEparcelOrders($order_ids);
            }
            elseif($this->request->data['courier_id'] == $this->courier->huntersId || $this->request->data['courier_id'] == $this->courier->huntersPluId || $this->request->data['courier_id'] == $this->courier->huntersPalId)
            {
                $this->orderfulfiller->fulfillHuntersOrders($order_ids);
            }
            elseif($this->request->data['courier_id'] == $this->courier->threePlTruckId)
            {
                $this->orderfulfiller->fulfillOurTruckOrder();
            }
            elseif($this->request->data['courier_id'] == $this->courier->localId)
            {
                $this->orderfulfiller->fulfillLocalOrder();
            }
            elseif($this->request->data['courier_id'] == $this->courier->vicLocalId)
            {
                $this->orderfulfiller->fulfillVicLocalOrder($order_ids);
            }
            elseif($this->request->data['courier_id'] == $this->courier->cometLocalId)
            {
                $this->orderfulfiller->fulfillCometOrder($order_ids);
            }
            elseif($this->request->data['courier_id'] == $this->courier->directFreightId)
            {
                $this->orderfulfiller->fulfillDirectFreightOrder($order_ids);
            }
            else
            {
                Session::set('showerrorfeedback', true);
                $_SESSION['errorfeedback'] .= "<p>The selected courier could not be located in the system</p>";
            }
            if(Session::getAndDestroy('showfeedback') == false)
            {
                Session::destroy('feedback');
            }
            if(Session::getAndDestroy('showerrorfeedback') == false)
            {
                Session::destroy('errorfeedback');
            }
        //}

        $this->view->renderJson($data);
    }

    public function selectCourier()
    {
        //echo "<pre>",print_r($this->request->data['order_ids']),"</pre>"; die();
        $data = array(
            'error'         => false,
            'error_string'  => '',
            'feedback'      => ''
        );
        Session::set('feedback',"<h2><i class='far fa-check-circle'></i>Couriers Have Been Assigned</h2>");
        Session::set('errorfeedback',"<h2><i class='far fa-times-circle'></i>These Orders Could Not Be Assigned</h2><p>Reasons are listed below</p>");
        Session::set('showfeedback', false);
        Session::set('showerrorfeedback', false);
        //foreach($this->request->data['order_ids'] as $order_id => $courier_id)
        foreach($this->request->data['order_ids'] as $details)
        {
            $order_id = $details['order_id'];
            $courier_id = $details['courier_id'];
            /************************   DO NOT UPDATE IF COURIER ALREADY CHOSEN!!!  *******************************/
                if($this->order->courierAssigned($order_id)) continue;
            /************************   DO NOT UPDATE IF COURIER ALREADY CHOSEN!!!  *******************************/
            $this->courierselector->assignCourier($order_id, $courier_id, "", $details['ip']);
        }
        if(Session::getAndDestroy('showfeedback') == false)
        {
            Session::destroy('feedback');
        }
        if(Session::getAndDestroy('showerrorfeedback') == false)
        {
            Session::destroy('errorfeedback');
        }
        $this->view->renderJson($data);
    }

    public function updateWarningLevel()
    {
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $this->item->updateWarningLevel($post_data);
    }

    public function deleteClientLocation()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $this->clientslocation->deleteAllocation($this->request->data['id']);
        Session::set('feedback', 'That location has had its allocation removed');
    }

    public function getABox()
    {
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $data = array(
            'no_box'    =>  false,
            'barcode'   =>  $barcode,
            'box_count' =>  0
        );

        $item = $this->item->getItemByBoxBarcode($barcode);

        if( !empty($item) )
        {
            $data['barcode'] = $item['barcode'];
            $data['box_count'] = $item['per_box'];
        }
        else
        {
            $data['no_box'] = true;
        }
        $this->view->renderJson($data);
    }

    public function getASummary()
    {
        $post_data = array();
        $return_array = array(
            'error'     =>  false,
            'feedback'  =>  ''
        );
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $summary_number = substr($barcode, 0, 12);
        $summary = $this->pickorder->getSummaryByBarcode($summary_number);
        //die($summary);
        if(!empty($summary))
        {
            if($summary['picked'] > 0)
            {
                $picked_date = date('d/m/Y', $summary['picked']);
                $picked_by = $this->user->getUserName( $summary['picked_by'] );
                $return_array['error'] = true;
                $return_array['error_string'] = "This has already been picked by $picked_by on $picked_date";
            }
            else
            {
                $scan_data = unserialize($summary['order_ids']);
                uasort($scan_data, function($a, $b) {
                    return $a['location_name'] > $b['location_name'];
                });
                $form = $this->view->render(Config::get('VIEWS_PATH') . 'forms/orderpicking.php', [
                    'scan_data' => $scan_data
                ]);
                $return_array['items'] = $form;
                $return_array['order_number'] = $summary_number;
            }
        }
        else
        {
            $return_array['error'] = true;
            $return_array['error_string'] = "No Summary Found For That Barcode: $summary_number";
        }
        $this->view->renderJson($return_array);
    }

    public function getAnOrderByNumber()
    {
        $post_data = array();
        $data = array(
            'error'     =>  false,
            'feedback'  =>  ''
        );
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $order_number = substr($barcode, 0, 12);
        $order = $this->order->getOrderByBarcode($barcode, $order_number);

        if( !empty($order) )
        {
            /*  */
            if($order['status_id'] == $this->order->packed_id)
            {
                $data['error'] = true;
                $data['error_string'] = "This order has already been checked and packed";
            }
            elseif($order['status_id'] == $this->order->fulfilled_id)
            {
                $data['error'] = true;
                $data['error_string'] = "This order has been fulfilled";
            }
            else
            {
                $data['order_number'] = $order['order_number'];
                $data['order_id'] = $order['id'];
                $items = $this->order->getItemsForOrderNoLocations($order['id']);

                $form = $this->view->render(Config::get('VIEWS_PATH') . 'forms/orderpacking.php', [
                    'items' => $items,
                    'order' => $order
                ]);
                $data['items'] = $form;
            }
        }
        else
        {
            $data['error'] = true;
            $data['error_string'] = "No Order Found For That Barcode: $barcode";
        }
        $this->view->renderJson($data);

    }

    public function recordDispatch()
    {
        $post_data = array();
        $data = array(
            'error'     =>  false,
            'feedback'  =>  ''
        );
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $order      = $this->order->getOrderByConId($consignment_id);
        $recording  = $this->order->getOrderDispatchByConId($consignment_id);

        if(empty($order))
        {
            $data['error'] = true;
            $data['error_string'] = "No Order Found For That Barcode: $consignment_id";
        }
        elseif($recording['parcels_scanned'] == $order['labels'])
        {
            $data['error'] = true;
            $data['error_string'] = "All items for that order have already been recorded as dispatched";
        }
        else
        {
            $this->order->recordDispatch($post_data, $recording);
        }
        $this->view->renderJson($data);
    }

    public function getItemByBarcode()
    {
        $barcode = $this->request->data['barcode'];
        $item = $this->item->getItemForClientByBarcode(array(
            'barcode'   => $barcode,
            'sku'       => $barcode,
            'client_id' => $this->request->data['client_id']
        ));

        $this->view->render(Config::get('VIEWS_PATH') . 'forms/scantoinventory.php', [
            'item'        =>  $item,
            'barcode'     =>  $barcode,
            'client_id'   =>  $this->request->data['client_id']
        ]);
    }

    public function updateLocation()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        $post_data = array();
        $data = array(
            'error'     =>  false,
            'feedback'  =>  ''
        );
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if(!$this->dataSubbed($location))
        {
            $data['error'] = true;
            $data['feedback'] .= "The location name is required";
        }
        elseif($this->location->getLocationId($location) && $location != $current_location)
        {
            $data['error'] = true;
            $data['feedback'] = "This name is already in use.\nLocation names need to be unique";
        }
        if(!$data['error'])
        {
            $this->location->updateLocation($post_data);
        }
        $this->view->renderJson($data);
    }

    public function getShippingQuotes()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; //die();

        $od = $this->order->getOrderDetail($this->request->data['order_id']);
        $items = $this->order->getItemsForOrder($od['id']);
        $eparcel_details            = $this->Eparcel->getShipmentDetails($od, $items);
        $eparcel_express_details    = $this->Eparcel->getShipmentDetails($od, $items, true);
        $eparcel_shipments['shipments'][0]  = $eparcel_details;
        $eeparcel_shipments['shipments'][0] = $eparcel_express_details;

        $eparcel_response = $this->Eparcel->GetQuote($eparcel_shipments);
        //echo "<pre>",print_r($eparcel_response),"</pre>"; //die();
        $express_response = $this->Eparcel->GetQuote($eeparcel_shipments);
        //echo "<pre>",print_r($express_response),"</pre>"; die();
        if(isset($eparcel_response['errors']))
        {
            $eparcel_charge = "";
            $eparcel_express_charge = "<div class='errorbox'><p>".$eparcel_response['errors'][0]['message']."</p></div>";
        }
        else
        {
            $eparcel_express_charge = "$".number_format($express_response['shipments'][0]['shipment_summary']['total_cost'], 2);
            $eparcel_charge = "$".number_format($eparcel_response['shipments'][0]['shipment_summary']['total_cost'], 2);
        }

        $this->view->render(Config::get('VIEWS_PATH') . 'dashboard/shipping_quotes.php', [
            'od'                        =>  $od,
            'express'                   =>  $od['eparcel_express'] == 1,
            'items'                     =>  $items,
            'eparcel_charge'            =>  $eparcel_charge,
            'eparcel_express_charge'    =>  $eparcel_express_charge,
            'client_name'               =>  $this->client->getClientName($od['client_id']),
            'ship_to'                   =>  $od['ship_to'],
            'address_string'            =>  $this->request->data['address_string']
        ]);
    }

    public function addPackageForm()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; //die();
        $order_ids = implode(",", $this->request->data['order_ids']) ;
        $this->view->render(Config::get('VIEWS_PATH') . 'dashboard/add_package.php', [
            'order_ids' =>  $order_ids
        ]);
    }

    public function adjustAllocationForm()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; //die();
        $od = $this->order->getOrderDetail($this->request->data['order_id']);
        $items = $this->order->getItemsForOrder($od['id']);
        //echo "<pre>",print_r($items),"</pre>"; //die();
        $this->view->render(Config::get('VIEWS_PATH') . 'dashboard/adjust_allocation.php', [
            'items'         => $items,
            'order_number'  => $od['order_number'],
            'order_id'      => $od['id']
        ]);
    }

    public function deletePackage()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        $this->order->deletePackage($this->request->data['lineid']);
    }

    public function cancelOrders()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        $this->order->cancelOrders($this->request->data['orderids']);
    }

    public function cancelSolarorders()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        $this->solarorder->cancelOrders($this->request->data['orderids']);
    }

    public function cancelServiceorders()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        $this->solarservicejob->cancelJobs($this->request->data['orderids']);
    }

    public function cancelPickup()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        $this->pickup->cancelPickup($this->request->data['pickupid']);
    }

    public function updateFreightCharge()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; //die();
        foreach($this->request->data['updates'] as $update)
        {
            foreach($update as $order_id => $charge)
            {
                $this->order->updateOrderValue('total_cost', $charge, $order_id);
            }
        }
        Session::set('feedback', "<h2><i class='far fa-check-circle'></i>Those charges have been updated</h2>");
    }

    public function getAddress()
    {
        $data = $this->address->getAutocompleteAddress($this->request->query['term']);
        $this->view->renderJson($data);
    }

    public function getItems()
    {
        //echo "<pre>",print_r($this->request),"</pre>";
        $data = $this->item->getAutocompleteItems($this->request->query, $this->order->fulfilled_id);
        $this->view->renderJson($data);
    }

    public function getSolarItems()
    {
        //echo "<pre>",print_r($this->request),"</pre>";
        $data = $this->item->getAutocompleteSolarItems($this->request->query, $this->order->fulfilled_id);
        $this->view->renderJson($data);
    }

    public function getAllItems()
    {
        //echo "<pre>",print_r($this->request),"</pre>";
        $data = $this->item->getAutocompleteAllItems($this->request->query, $this->order->fulfilled_id);
        $this->view->renderJson($data);
    }

    public function checkBarcodes()
    {
        //echo "<pre>",print_r($this->request),"</pre>";die();
        $request = trim($this->request->query['barcode']);
        $current_barcode = isset($this->request->query['current_barcode'])? trim($this->request->query['current_barcode']) : "";
        $this->view->renderBoolean($this->item->checkBarcodes($request, $current_barcode));
    }

    public function checkBoxBarcodes()
    {
        $request = trim($this->request->query['box_barcode']);
        $current_barcode = isset($this->request->query['current_barcode'])? trim($this->request->query['current_barcode']) : "";
        $this->view->renderBoolean($this->item->checkBoxBarcodes($request, $current_barcode));
    }

    public function checkSkus()
    {
        //echo "<pre>",print_r($this->request),"</pre>";die();
        $request = trim($this->request->query['sku']);
        $current_sku = isset($this->request->query['current_sku'])? trim($this->request->query['current_sku']) : "";
        $this->view->renderBoolean($this->item->checkSkus($request, $current_sku));
    }

    public function checkLocations()
    {
        //echo "<pre>",print_r($this->request),"</pre>";die();
        $request = trim($this->request->query['location']);
        $this->view->renderBoolean($this->location->checkLocation($request));
    }

    public function checkRoleNames()
    {
        //echo "<pre>",print_r($this->request),"</pre>";die();
        $request = trim($this->request->query['name']);
        $current_name = isset($this->request->query['current_name'])? trim($this->request->query['current_name']) : "";
        $this->view->renderBoolean($this->user->checkRoleNames($request, $current_name));
    }

    public function getSuburbs()
    {
        //echo "<pre>",print_r($this->request),"</pre>";
        $data = $this->Postcode->getAutocompleteSuburb($this->request->query['term']);
        $this->view->renderJson($data);
    }

    public function getUnfulfilledAdmin()
    {
        //echo "<pre>",print_r($this->request),"</pre>";
        $client_id  = ($this->request->data['client_id'] > 0)? $this->request->data['client_id'] : 0;
        $courier_id = ($this->request->data['courier_id'] >= 0)? $this->request->data['courier_id'] : -1;
        $orders = $this->order->getHomePageOrders($client_id, $courier_id);
        $tableHTML = $this->view->render(Config::get('VIEWS_PATH') . 'dashboard/admin_table.php', [
            'orders'        =>  $orders,
            'client_id'     =>  $client_id,
            'courier_id'    =>  $courier_id
        ]);
        $this->view->renderJson(array("data" => $tableHTML));
    }

    public function getOrderTrends()
    {
        $data = $this->order->getOrderTrends($this->request->data['from'], $this->request->data['to'], $this->request->data['client_id']);
        $this->view->renderJson($data);
    }

    public function getPickErrors()
    {
        $data = $this->order->getPickErrors($this->request->data['from'], $this->request->data['to']);
        $this->view->renderJson($data);
    }

    public function getClientActivity()
    {
        $data = $this->order->getClientActivity($this->request->data['from'], $this->request->data['to']);
        $this->view->renderJson($data);
    }

    public function getTopProducts()
    {
        $data = $this->order->getTopProducts($this->request->data['from'], $this->request->data['to'], $this->request->data['client_id']);
        $this->view->renderJson($data);
    }

    public function isAuthorized(){
        return true;
    }
}
?>