<?php

/**
 * Ajax Functions controller
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class ajaxfunctionsController extends Controller
{

    public function beforeAction()
    {
        parent::beforeAction();
        $actions = [
            'adjustAllocationForm',
            'addJobRunsheets',
            'addQuotePackage',
            'bulkMoveStock',
            'calcOriginPick',
            'consolidateOrders',
            'deactivateUser',
            'deleteClientLocation',
            'deleteConfiguration',
            'deleteFinishers',
            'deletePackage',
            'doRunsheets',
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
            'getOrderItems',
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
            'procGetQuotes',
            'reactivateUser',
            'recordDispatch',
            'removeCourier',
            'removeJobFromRunsheet',
            'selectCourier',
            'updateAllocation',
            'updateFreightCharge',
            'updateLocation',
            'updateOrderComments',
            'updateStockMovementReason',
            'updateWarningLevel',
            'updateJobStatus'
        ];
        $this->Security->config("validateForm", false);
        $this->Security->requireAjax($actions);
    }

    public function deleteFinishers()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        foreach($this->request->data['finisherids'] as $finisher_id)
        {
            $this->productionfinisher->deactivateFinisher($finisher_id);
        }
        Session::set('feedback',"<h2><i class='far fa-check-circle'></i>Finisher(s) Have Been Deleted</h2>");
    }

    public function updateJobStatus()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        foreach($this->request->data['jobids'] as $supdate)
        {
            $this->productionjob->updateJobStatus($supdate['jobid'], $supdate['statusid']);
        }
        Session::set('feedback',"<h2><i class='far fa-check-circle'></i>Status have been updated</h2>");
    }

    public function addQuotePackage()
    {
        $i = $this->request->data['i'];
        $data = array(
            'error'     =>  false,
            'feedback'  =>  '',
            'html'      =>  ''
        );
        $html = $this->view->render(Config::get('VIEWS_PATH') . 'layout/page-includes/add_quote_package.php', [
            'i'     =>  $i
        ]);
        $data['html'] = $html;
        $this->view->renderJson($data);
    }

    public function addFinisherContact()
    {
        $i = $this->request->data['i'];
        $data = array(
            'error'     =>  false,
            'feedback'  =>  '',
            'html'      =>  ''
        );
        $html = $this->view->render(Config::get('VIEWS_PATH') . 'layout/page-includes/add_finisher_contact.php', [
            'i'     =>  $i
        ]);
        $data['html'] = $html;
        $this->view->renderJson($data);
    }

    public function procGetQuotes()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $data = array(
            'error'     =>  false,
            'feedback'  =>  '<ul>',
            'html'      =>  ''
        );
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if(!$suburb || strlen($suburb = trim($suburb)) == 0)
        {
            $data['error'] = true;
            $data['feedback'] .= "<li>A delivery suburb is required</li>";
        }
        if(!$state || strlen($state = trim($state)) == 0)
        {
            $data['error'] = true;
            $data['feedback'] .= "<li>A delivery state is required</li>";
        }
        if(!$postcode || strlen($postcode = trim($postcode)) == 0)
        {
            $data['error'] = true;
            $data['feedback'] .= "<li>A delivery postcode is required</li>";
        }
        $eparcel_items = array();
        $express_items = array();
        $df_items = array();
        $product_id = "3D85";
        $express_product_id = "3J85";
        foreach($this->request->data['items'] as $item)
        {
            if(!$item['weight'] || strlen($item['weight'] = trim($item['weight'])) == 0)
            {
                $data['error'] = true;
                $data['feedback'] .= "<li>A weight is required for all items</li>";
            }
            else
            {
                for($a = 1; $a <= $item['count']; ++$a)
                {
                    $this_eparcel_item = array(
                        "product_id"    => $product_id,
                        "length"        => $item['length'],
                        "height"        => $item['height'],
                        "width"         => $item['width'],
                        "weight"        => $item['weight']
                    );
                    $this_express_item = array(
                        "product_id"    => $express_product_id,
                        "length"        => $item['length'],
                        "height"        => $item['height'],
                        "width"         => $item['width'],
                        "weight"        => $item['weight']
                    );
                    $eparcel_items[] = $this_eparcel_item;
                    $express_items[] = $this_express_item;
                }
                $rate_type = isset($item['pallet'])? "PALLET" : "ITEM";
                $this_df_item = array(
                    "RateType"  => $rate_type,
                    "Items"     => $item['count'],
                    "Kgs"       => ceil($item['weight']),
                    "Length"    => $item['length'],
                    "Width"     => $item['width'],
                    "Height"    => $item['height']
                );
                $df_items[] = $this_df_item;
            }
        }
        $data['feedback'] .= "</ul>";
        if(!$data['error'])
        {
            $eparcel_shipment = array(
                'from'  =>	array(
                    'suburb'    => 'BAYSWATER',
                    'state'     => 'VIC',
                    'postcode'  => 3153
                ),
                'to'    =>	array(
                    'suburb'    => $suburb,
                    'state'     => $state,
                    'postcode'  => $postcode
                ),
                'items' =>	$eparcel_items
            );
            $express_shipment = array(
                'from'  =>	array(
                    'suburb'    => 'BAYSWATER',
                    'state'     => 'VIC',
                    'postcode'  => 3153
                ),
                'to'    =>	array(
                    'suburb'    => $suburb,
                    'state'     => $state,
                    'postcode'  => $postcode
                ),
                'items' =>	$express_items
            );
            $direct_freight_shipment = array(
                'ConsignmentList'   => array(
                    array(
                        'ReceiverDetails'   => array(
                            'Suburb'    => $suburb,
                            'Postcode'  => $postcode
                        ),
                        'ConsignmentLineItems'  => $df_items
                    )
                )
            );
            $eparcel_shipments['shipments'][0]  = $eparcel_shipment;
            $express_shipments['shipments'][0]  = $express_shipment;
            $eparcel_response = $this->Eparcel->GetQuote($eparcel_shipments);
            $express_response = $this->Eparcel->GetQuote($express_shipments);
            $df_r = $this->directfreight->getQuote($direct_freight_shipment);
            $df_response = json_decode($df_r,true);
            $html = $this->view->render(Config::get('VIEWS_PATH') . 'orders/shipping_quotes.php', [
                'eparcel_response'  => $eparcel_response,
                'df_response'       => $df_response,
                'express_response'  => $express_response
            ]);
            $data['html'] .= $html;
        }
        $this->view->renderJson($data);
    }

    public function consolidateOrders()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; //die();
        $post_data = array();
        $data = array(
            'error'     =>  false,
            'feedback'  =>  ''
        );
        Session::set('feedback',"<h2><i class='far fa-check-circle'></i>Orders Have Been Consolidated</h2>");
        Session::set('errorfeedback',"<h2><i class='far fa-times-circle'></i>These Orders Could Not Be Consolidated</h2>");
        Session::set('showfeedback', false);
        Session::set('showerrorfeedback', false);
        //get first order details
        $fo = $this->order->getOrderDetail($this->request->data['order_ids'][0]);
        $address_array = array(
            'address'   => $fo['address'],
            'suburb'    => $fo['suburb'],
            'state'     => $fo['state'],
            'postcode'  => $fo['postcode'],
            'country'   => $fo['country']
        );
        $error = false;
        $client_order_id = $fo['client_order_id'];
        $update_ids = array();
        for( $i = 1; $i < count($this->request->data['order_ids']); ++$i)
        {
            if(!$this->order->addressMatch($address_array, $this->request->data['order_ids'][$i]))
            {
                Session::set('showerrorfeedback', true);
                $_SESSION['errorfeedback'] .= "<p>Not all orders appear to be going to the same address</p>";
                $error = true;
            }
            else
            {
                $update_ids[] = $this->request->data['order_ids'][$i];
            }
        }
        if(!$error)
        {
            //echo "<pre>",print_r($update_ids),"</pre>"; die();
            $local_courier_id = $this->courier->getLocalId();
            $courier_name = "consolidated with {$fo['order_number']}";
            $pdfs = array();
            if(!empty($fo['uploaded_file']))
            {
                $pdfs[] = array(
                	'file'		    =>	UPLOADS.$fo['client_id']."/".$fo['uploaded_file'],
                    'orientation'	=>	'P'
                );
            }

            foreach($update_ids as $old_id)
            {
                //get the consoldating order details
                $this_order = $this->order->getOrderDetail($old_id);
                //consolidate the order items
                $this->order->consolidateOrders($old_id, $fo['id']);
                //update order values
                $new_order_values = array(
                    'courier_id'        => $local_courier_id,
                    'courier_name'      => $courier_name,
                    'date_fulfilled'    => time(),
                    'status_id'         => $this->order->fulfilled_id
                );
                $this->order->updateOrderValues($new_order_values, $old_id);
                //deal with the invoices
                if(!empty($this_order['uploaded_file']))
                {
                    $pdfs[] = array(
                    	'file'		    =>	UPLOADS.$this_order['client_id']."/".$this_order['uploaded_file'],
                        'orientation'	=>	'P'
                    );
                }
                //store new client order ids
                $client_order_id .= (empty($client_order_id))? $this_order['client_order_id'] : ", ".$this_order['client_order_id'];
            }
            //echo "<p>$client_order_id</p>";
            //echo "PDFS<pre>",print_r($pdfs),"</pre>"; die();
            if(count($pdfs))
            {
                $upcount = 1;
                $filename = "invoice";
                $name = "invoice.pdf";
                $upload_dir = "/client_uploads/".$fo['client_id']."/";
                if ( ! is_dir(DOC_ROOT.$upload_dir))
                            mkdir(DOC_ROOT.$upload_dir);
    			while(file_exists(DOC_ROOT.$upload_dir.$name))
                {
                    $name = $filename."_".$upcount.".pdf";
                    ++$upcount;
                }
                $pdf = new Mympdf();
                $pdf->mergePDFFilesToServer($pdfs, $name, DOC_ROOT.$upload_dir);
                $uploaded_file = $name;
            }
            $new_vals = array(
                'client_order_id'   => $client_order_id
            );
            if(isset($uploaded_file))
                $new_vals['uploaded_file'] = $uploaded_file;
            $this->order->updateOrderValues($new_vals, $fo['id']);
            Session::set('showfeedback', true);
        }
        //echo "<pre>",print_r($data),"</pre>"; //die();
        //echo "<pre>",print_r($fo),"</pre>"; die();
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

    public function encryptSomeShit()
    {
        //echo "<pre>",print_r($this->request->),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $encrypted_value = Encryption::encrypt($string);
        die($encrypted_value);
        $data = array(
            'error'             =>  false,
            'encryptedvalue'    =>  $encrypted_value,
            'error_string'      =>  ''
        );
        $this->view->renderJson($data);
    }

    public function bulkMoveStock()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        $post_data = array();
        $data = array(
            'error'     =>  false,
            'feedback'  =>  ''
        );
        foreach($this->request->data['ids'] as $array)
        {
            echo "<pre>",print_r($array),"</pre>";
        }
        die();
        $this->view->renderJson($data);
    }

    public function getOrderItemsForSerials()
    {
        $order_number = $this->request->data['ordernumber'];
        $data = array(
            'error'     =>  false,
            'feedback'  =>  '',
            'html'      =>  ''
        );
        $order = $this->order->getOrderByOrderNumber($order_number);
        $items = $this->order->getItemsForOrderNoLocations($order['id']);
        if(!count($items))
        {
            $data['error'] = true;
            $data['feedback'] = 'No items found for that order number';
        }

        $html = $this->view->render(Config::get('VIEWS_PATH') . 'forms/add_serials.php', [
            'items'     =>  $items,
            'order_id'  =>  $order['id']
        ]);
        $data['html'] = $html;
        $this->view->renderJson($data);
    }

    public function removeCourier()
    {
        $order_id = $this->request->data['order_id'];
        $data = array(
            'error'     =>  false,
            'feedback'  =>  ''
        );
        $this->order->removeCourier($order_id);
        Session::set('feedback',"<h2><i class='far fa-check-circle'></i>Courier has been removed</h2>");
        $this->view->renderJson($data);
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
        foreach($this->request->data['allocation'] as $item_id => $marray)
        {
            //echo "<pre>",print_r($marray),"</pre>";
            foreach($marray as $key => $array)
            {
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
        }
        //echo "<pre>",print_r($order_items),"</pre>";die();
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

    public function deactivateLocation()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        $this->location->deactivateLocation($this->request->data['locationid']);
    }

    public function reactivateLocation()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        $this->location->reactivateLocation($this->request->data['locationid']);
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
        $order = $this->order->getOrderByConId($con_id);
        if(!count($items))
        {
            $data['error'] = true;
        }
        $html = $this->view->render(Config::get('VIEWS_PATH') . 'orders/orders_items.php', [
            'items' =>  $items,
            'order' =>  $order
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
            elseif($this->request->data['courier_id'] == $this->courier->fsgId)
            {
                $this->orderfulfiller->fulfillFSGTruckOrder();
            }
            elseif($this->request->data['courier_id'] == $this->courier->localId)
            {
                $this->orderfulfiller->fulfillLocalOrder();
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
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $data = array(
            'error'         => false,
            'error_string'  => '',
            'feedback'      => ''
        );
        Session::set('courierfeedback',"<h2><i class='far fa-check-circle'></i>Couriers Have Been Assigned</h2>");
        Session::set('couriererrorfeedback',"<h2><i class='far fa-times-circle'></i>These Orders Could Not Be Assigned</h2><p>Reasons are listed below</p>");
        Session::set('showcourierfeedback', false);
        Session::set('showcouriererrorfeedback', false);
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
        if(Session::getAndDestroy('showcourierfeedback') == false)
        {
            Session::destroy('courierfeedback');
        }
        if(Session::getAndDestroy('showcouriererrorfeedback') == false)
        {
            Session::destroy('couriererrorfeedback');
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

    public function deleteConfiguration()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $this->configuration->deleteConfiguration($this->request->data['id']);
        Session::set('feedback', 'That configuration value has been deleted');
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

    public function updateStockMovementReason()
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
        $post_data['active'] = ($active == 'true')? 1 : 0;
        if(isset($locked))
        {
            $post_data['locked'] = ($locked == 'true')? 1 : 0;
        }

        if(!$this->dataSubbed($reason))
        {
            $data['error'] = true;
            $data['feedback'] .= "The reason is required";
        }
        elseif($this->stockmovementlabels->getLabelId($reason) && $reason != $current_reason)
        {
            $data['error'] = true;
            $data['feedback'] = "This reason is already in use.\nReasons need to be unique";
        }
        if(!$data['error'])
        {
            $this->stockmovementlabels->updateLabel($post_data);
        }
        $this->view->renderJson($data);
    }

    public function getShippingQuotes()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; //die();
        $df_charge = 0;
        $od = $this->order->getOrderDetail($this->request->data['order_id']);
        $client_details = $this->client->getClientInfo($od['client_id']);
        $eParcelClass = "Eparcel";
        if(!is_null($client_details['eparcel_location']))
            $eParcelClass = $client_details['eparcel_location']."Eparcel";
        $items = $this->order->getItemsForOrder($od['id']);
        $eparcel_details            = $this->{$eParcelClass}->getShipmentDetails($od, $items);
        //echo "<pre>",print_r(json_encode($eparcel_details)),"</pre>"; die();
        $eparcel_express_details    = $this->{$eParcelClass}->getShipmentDetails($od, $items, true);
        $eparcel_shipments['shipments'][0]  = $eparcel_details;
        $eeparcel_shipments['shipments'][0] = $eparcel_express_details;
        /*  */



        $eparcel_response = $this->{$eParcelClass}->GetQuote($eparcel_shipments);
        //echo "<pre>",print_r($eparcel_response),"</pre>"; //die();
        $express_response = $this->{$eParcelClass}->GetQuote($eeparcel_shipments);
        //echo "<pre>",print_r(json_encode($express_response)),"</pre>"; //die();
        if(isset($eparcel_response['errors']))
        {
            $eparcel_charge = "";
            $eparcel_express_charge = "<div class='errorbox'><p>".$eparcel_response['errors'][0]['message']."</p></div>";
        }
        else
        {
            $eparcel_express_charge = "$".number_format($express_response['shipments'][0]['shipment_summary']['total_cost'] * 1.35 * 1.1, 2);
            $eparcel_charge = "$".number_format($eparcel_response['shipments'][0]['shipment_summary']['total_cost'] * 1.35 * 1.1, 2);
            /*********** charge FREEDOM more *******************/
                if($od['client_id'] == 7)
                {
                    $eparcel_express_charge = "$".number_format($express_response['shipments'][0]['shipment_summary']['total_cost'] * 1.4 * 1.1, 2);
                    $eparcel_charge = "$".number_format($eparcel_response['shipments'][0]['shipment_summary']['total_cost'] * 1.4 * 1.1, 2);
                }
            /*********** charge FREEDOM more *******************/
            /*********** special deals for OnePlate *******************/
                if($od['client_id'] == 82)
                {
                    $eparcel_express_charge = "$".number_format($express_response['shipments'][0]['shipment_summary']['total_cost'] * 1.1 * 1.1, 2);
                    $eparcel_charge = "$".number_format($eparcel_response['shipments'][0]['shipment_summary']['total_cost'] * 1.1 * 1.1, 2);
                }
            /*********** special deals for OnePlate *******************/
        }
        if($this->courierselector->chooseEparcel($od))
        {
            $df_charge = "<div class='errorbox'><p>This address can only be serviced by Australia Post</p></div>";
        }
        else
        {
            $df_details = $this->directfreight->getDetails($od, $items);
            //echo "<pre>",print_r(json_encode($df_details)),"</pre>"; //die();
            $df_r = $this->directfreight->getQuote($df_details);
            $df_response = json_decode($df_r,true);
            //echo "<pre>",var_dump($df_response),"</pre>"; die();
            if($df_response['ResponseCode'] == 300)
            {
                $df_charge = "$".number_format($df_response['TotalFreightCharge'] * 1.35 * 1.1 * DF_FUEL_SURCHARGE, 2);
                /*********** special deals for OnePlate *******************/
                    if($od['client_id'] == 82)
                    {
                        $df_charge = "$".number_format($df_response['TotalFreightCharge'] * 1.1 * 1.1 * DF_FUEL_SURCHARGE, 2);
                    }
                /*********** special deals for OnePlate *******************/
                /*********** charge FREEDOM more *******************/
                    if($od['client_id'] == 7)
                    {
                        $df_charge = "$".number_format($df_response['TotalFreightCharge'] * 1.4 * 1.1 * DF_FUEL_SURCHARGE, 2);
                    }
                /*********** charge FREEDOM more *******************/
            }
            else
            {
                $df_charge = "<div class='errorbox'><p>".$df_response['ResponseMessage']."</p></div>";
            }
        }
        $this->view->render(Config::get('VIEWS_PATH') . 'dashboard/shipping_quotes.php', [
            'od'                        =>  $od,
            'express'                   =>  $od['eparcel_express'] == 1,
            'items'                     =>  $items,
            'eparcel_charge'            =>  $eparcel_charge,
            'eparcel_express_charge'    =>  $eparcel_express_charge,
            'client_name'               =>  $this->client->getClientName($od['client_id']),
            'ship_to'                   =>  $od['ship_to'],
            'address_string'            =>  $this->request->data['address_string'],
            'eparcel_details'           =>  $eparcel_details,
            'df_charge'                 =>  $df_charge
        ]);
    }

    public function addPackageForm()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; //die();
        $order_ids = implode(",", $this->request->data['order_ids']) ;
        $this->view->render(Config::get('VIEWS_PATH') . 'dashboard/add_package.php', [
            'order_ids' =>  $this->request->data['order_ids'],
            'client_id' =>  $this->request->data['client_id']
        ]);
    }

    public function addOrderPackageForm()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; //die();
        $order_id = $this->request->data['order_id'] ;
        $this->view->render(Config::get('VIEWS_PATH') . 'dashboard/add_package1.php', [
            'order_id' =>  $order_id
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

    public function updateJobstatusOrder()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        $this->jobstatus->updateHeirarchy($this->request->data['status']);
    }

    public function cancelPickup()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        $this->pickup->cancelPickup($this->request->data['pickupid']);
    }

    public function removeJobFromRunsheet()
    {
        $data = array(
            'error'     =>  false,
            'feedback'  =>  ''
        );
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        if($this->runsheet->removeJob($this->request->data['job_id'], $this->request->data['runsheet_id']))
        {
            Session::set('feedback', "<h2><i class='far fa-check-circle'></i>That job has been removed from the runsheet</h2><p>It can now be added to another</p>");
        }
        else
        {
            Session::set('errorfeedback',"<h2><i class='far fa-times-circle'></i>There has been a database error</h2><p>The job has not been removed</p>");
        }
        $this->view->renderJson($data);
    }

    public function removeOrderFromRunsheet()
    {
        $data = array(
            'error'     =>  false,
            'feedback'  =>  ''
        );
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        if($this->runsheet->removeOrder($this->request->data['order_id'], $this->request->data['runsheet_id']))
        {
            Session::set('feedback', "<h2><i class='far fa-check-circle'></i>That order has been removed from the runsheet</h2><p>It can now be added to another</p>");
        }
        else
        {
            Session::set('errorfeedback',"<h2><i class='far fa-times-circle'></i>There has been a database error</h2><p>The job has not been removed</p>");
        }
        $this->view->renderJson($data);
    }

    public function removeTasksFromRunsheet()
    {
        $data = array(
            'error'     =>  false,
            'feedback'  =>  ''
        );
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        if($this->runsheet->removeTasks($this->request->data['task_ids'], $this->request->data['runsheet_id']))
        {
            Session::set('feedback', "<h2><i class='far fa-check-circle'></i>The select task(s) have been removed from the runsheet</h2><p>It/They can now be added to another</p>");
        }
        else
        {
            Session::set('errorfeedback',"<h2><i class='far fa-times-circle'></i>There has been a database error</h2><p>The tasks has not been removed</p>");
        }
        $this->view->renderJson($data);
    }

    public function completeRunsheetTasks()
    {
        $data = array(
            'error'     =>  false,
            'feedback'  =>  ''
        );
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        if($this->runsheet->completeTasks($this->request->data['task_ids'], $this->request->data['runsheet_id']))
        {
            Session::set('feedback', "<h2><i class='far fa-check-circle'></i>The select task(s) have been marked as complete</h2>");
        }
        else
        {
            Session::set('errorfeedback',"<h2><i class='far fa-times-circle'></i>There has been a database error</h2><p>The tasks has not been completed</p>");
        }
        $this->view->renderJson($data);
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

    public function getJobCustomer()
    {
        //echo "<pre>",print_r($this->request),"</pre>";
        $data = $this->productioncustomer->getAutocompleteCustomer($this->request->query['customer']);
        $this->view->renderJson($data);
    }

    public function getJobFinisher()
    {
        //echo "<pre>",print_r($this->request),"</pre>";
        $data = $this->productionfinisher->getAutocompleteFinisher($this->request->query['finisher']);
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

    public function checkJobIds()
    {
        //echo "<pre>",print_r($this->request),"</pre>";die();
        $request = trim($this->request->query['job_id']);
        $current_jobid = isset($this->request->query['current_jobid'])? trim($this->request->query['current_jobid']) : "";
        $this->view->renderBoolean($this->productionjob->checkJobIds($request, $current_jobid));
    }

    public function addOrderRunsheets()
    {
        $data = array(
            'error'     =>  false,
            'feedback'  =>  ''
        );
        //echo "<pre>",print_r($this->request->data),"</pre>";//die();
        $runsheets = array();
        foreach($this->request->data['runsheets'] as $rs)
        {
            if(!isset($runsheets[$rs['timestamp']]))
            {
                $runsheets[$rs['timestamp']]['driver_id'] = 0;
                $runsheets[$rs['timestamp']]['orders'] = array();
            }
            $runsheets[$rs['timestamp']]['orders'][] = $rs['order_id'];
        }
        ksort($runsheets, SORT_NUMERIC);
        //echo "<pre>",print_r($runsheets),"</pre>";die();
        $this->runsheet->addRunsheet($runsheets);
        Session::set('feedback', "<h2><i class='far fa-check-circle'></i>Those Runsheet(s) have been created/updated</h2>");
        $this->view->renderJson($data);
    }

    public function addJobRunsheets()
    {
        $data = array(
            'error'     =>  false,
            'feedback'  =>  ''
        );
        //echo "<pre>",print_r($this->request->data),"</pre>";//die();
        $runsheets = array();
        foreach($this->request->data['runsheets'] as $rs)
        {
            if(!isset($runsheets[$rs['timestamp']]))
            {
                $runsheets[$rs['timestamp']]['driver_id'] = 0;
                $runsheets[$rs['timestamp']]['jobs'] = array();
            }
            $runsheets[$rs['timestamp']]['jobs'][] = $rs['job_id'];
        }
        ksort($runsheets, SORT_NUMERIC);
        //echo "<pre>",print_r($runsheets),"</pre>";die();
        $this->runsheet->addRunsheet($runsheets);
        Session::set('feedback', "<h2><i class='far fa-check-circle'></i>Those Runsheet(s) have been created/updated</h2>");
        $this->view->renderJson($data);
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

    public function checkDriverNames()
    {
        //echo "<pre>",print_r($this->request),"</pre>";die();
        $request = trim($this->request->query['name']);
        $current_name = isset($this->request->query['current_name'])? trim($this->request->query['current_name']) : "";
        $this->view->renderBoolean($this->driver->checkDriverNames($request, $current_name));
    }

    public function checkJobStatusNames()
    {
        //echo "<pre>",print_r($this->request),"</pre>";die();
        $name = trim($this->request->query['name']);
        $current_name = isset($this->request->query['current_name'])? trim($this->request->query['current_name']) : "";
        $this->view->renderBoolean($this->jobstatus->checkStatusNames($name, $current_name));
    }

    public function checkFinisherCatNames()
    {
        //echo "<pre>",print_r($this->request),"</pre>";die();
        $name = trim($this->request->query['name']);
        $current_name = isset($this->request->query['current_name'])? trim($this->request->query['current_name']) : "";
        $this->view->renderBoolean($this->finishercategories->checkCategoryNames($name, $current_name));
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

    public function getWeeklyOrderTrends()
    {
        $data = $this->order->getWeeklyOrderTrends($this->request->data['from'], $this->request->data['to'], $this->request->data['client_id']);
        $this->view->renderJson($data);
    }

    public function getDailyOrderTrends()
    {
        $data = $this->order->getDailyOrderTrends($this->request->data['from'], $this->request->data['to'], $this->request->data['client_id']);
        $this->view->renderJson($data);
    }

    public function getPickErrors()
    {
        $data = $this->order->getPickErrors($this->request->data['from'], $this->request->data['to']);
        $this->view->renderJson($data);
    }

    public function getAdminWeeklyClientActivity()
    {
        $data = $this->order->getWeeklyOrderTrends($this->request->data['from'], $this->request->data['to']);
        $this->view->renderJson($data);
    }

    public function getAdminDailyClientActivity()
    {
        $data = $this->order->getDailyOrderTrends($this->request->data['from'], $this->request->data['to']);
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
        $action = $this->request->param('action');
        $role = Session::getUserRole();
        //$role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        $resource = "ajaxfunctions";

        if($action == "updateJobStatus")
        {
            Permission::deny(['production sales', 'production sales admin'], $resource, $action);
        }
        else
        {
            return true;
        }
    }
}
?>