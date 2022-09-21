<?php

/**
 * Form controller
 *
 * processes all forms on thee site
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class FormController extends Controller {

    /**
     * Initialization method.
     * load components, and optionally assign their $config
     *
     */
    public function initialize(){
        $action = $this->request->param('action');
        //die('action '.$action);
        if($action == "procLogin" || $action == "procForgotPassword" || $action == "procUpdatePassword")
        {
            //no auth component need for logging in
             $this->loadComponents([
                 'Security'
             ]);
        }
        else
        {
             $this->loadComponents([
                 'Auth' => [
                         'authenticate' => ['User']
                     ],
                 'Security'
             ]);
        }
    }

    public function beforeAction(){

        parent::beforeAction();
        $action = $this->request->param('action');
        $actions = [
            'printSwatchLabels',
            'procAddClientLocation',
            'procAddLocation',
            'procAddMiscToOrder',
            'procAddMiscTask',
            'procAddPackage',
            'procAddPackages',
            'procAddProductionCustomer',
            'procAddProductionFinisher',
            'procAddProductionJob',
            'procAddPurchaseOrder',
            'procAddressUpdate',
            'procAddServiceJob',
            'procAddSerials',
            'procAddSolarInstall',
            'procAddTljOrder',
            'procAddToStock',
            'procBasicProductAdd',
            'procBookCoverAdd',
            'procBookDelivery',
            'procBookAPickup',
            'procBookCourier',
            'procBookDirectFreight',
            'procBookPickup',
            'procBreakPacks',
            'procBulkOrderAdd',
            'procBulkProductionCustomerAdd',
            'procBulkProductionJobAdd',
            'procBulkProductionSupplierAdd',
            'procClientAdd',
            'procClientDailyReports',
            'procClientEdit',
            'procClientProductEdit',
            'procCompleteDelivery',
            'procCompletRunsheetTasks',
            'procContactUs',
            'procContainerUnload',
            'procCourierAdd',
            'procCourierEdit',
            'procDFCollection',
            'procDriverAdd',
            'procDriverEdit',
            'procEditProductionCustomer',
            'procEditProductionFinisher',
            'procEditServiceJob',
            'procEditInstall',
            'procEncryptSomeShit',
            'procFinisherCategoryAdd',
            'procForgotPassword',
            'procGoodsIn',
            'procGoodsOut',
            'procInventoryCompare',
            'procItemsUpdate',
            'procFinisherCategoryAdd',
            'procFinisherCategoryEdit',
            'procJobCustomerUpdate',
            'procJobDeliveryUpdate',
            'procJobDetailsUpdate',
            'procJobStatusAdd',
            'procJobStatusEdit',
            'procJobSupplierUpdate',
            'procLogin',
            'procMakePacks',
            'procMoveAllClientStock',
            'procMovementreasonAdd',
            'procOrderAdd',
            'procOrderCsvUpload',
            'procOrderCourierUpdate',
            'procOrderEdit',
            'procOrderUpload',
            'procOriginOrderAdd',
            'procPackItemEdit',
            'procPackOrder',
            'procPackTypeAdd',
            'procPackTypeEdit',
            'procPickOrder',
            'procPickupPutaways',
            'procPickupSearch',
            'procPrepareRunsheet',
            'procPickupUpdate',
            'procProductAdd',
            'procProductEdit',
            'procProductionJobDeliveryUpdate',
            'procProfileUpdate',
            'procQualityControl',
            'procRecordItemCollection',
            'procRecordPickup',
            'procReeceDepartmentCheck',
            'procReeceDepartmentUpload',
            'procReeceUserCheck',
            'procReeceUserUpload',
            'procRegisterNewStock',
            'procRepAdd',
            'procRepalletiseShrinkwrap',
            'procRepEdit',
            'procRunsheetCompletionUpdate',
            'procScanToInventory',
            'procShipmentAddressUpdate',
            'procSolarReturn',
            'procSolarTeamAdd',
            'procSolarTeamEdit',
            'procSolarTypeAdd',
            'procSolarTypeEdit',
            'procStockMovement',
            'procStoreAdd',
            'procStoreEdit',
            'procStoreChainAdd',
            'procStoreChainEdit',
            'procSubtractFromStock',
            'procSwatchCsvUpload',
            'procTransferLocation',
            'procTruckUsage',
            'procUpdatePassword',
            'procUserAdd',
            'procUserRoleAdd',
            'procUserRoleEdit'
        ];
        $this->Security->config("form", [ 'fields' => ['csrf_token']]);
        $this->Security->requirePost($actions);
    }

    public function procBookDirectFreight()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        //echo "<pre>",print_r($post_data),"</pre>";die();
        //error checking
        if(!isset($items) || !count($items))
            Form::setError('items', "At least one item must be selected");
        if( !$this->dataSubbed($deliver_to) )
        {
            Form::setError('deliver_to', 'A name is required');
        }
        if($this->dataSubbed($tracking_email))
        {
            if(!$this->emailValid($tracking_email))
            {
                Form::setError('tracking_email', 'The supplied email is not valid');
            }
        }
        $this->validateAddress($address, $suburb, $state, $postcode, $country, isset($ignore_address_error));
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //echo "ALL GOOD<pre>",print_r($post_data),"</pre>"; die();
            //Create the Direct Freight Consignment
            $deliver_to = (!empty($company_name))? $company_name.": ".$deliver_to:$deliver_to;
            $details = [
                "ConsignmentId"         => Utility::randomNumber(10),
                "ReceiverDetails"       => [
                    "ReceiverName"          => $deliver_to,
                    "AddressLine1"          => $address,
                    "Suburb"                => $suburb,
                    "State"                 => $state,
                    "Postcode"              => $postcode,
                    "IsAuthorityToLeave"    => ( isset($signature_req) )? false: true
                ],
                "ConsignmentLineItems"  => []
            ];
            foreach($items as $it)
            {
                $rate_type = (isset($it['pallet']))? "PALLET" : "ITEM";
                $package_description = (isset($it['pallet']))? "Plain Pallet" : "Carton of Goods";
                $details["ConsignmentLineItems"][] = [
                    "RateType"              => $rate_type,
                    "PackageDescription"    => $package_description,
                    "Items"                 => $it['count'],
                    "Kgs"                   => ceil($it['weight']),
                    "Length"                => $it['length'],
                    "Width"                 => $it['width'],
                    "Height"                => $it['height']
                ];
            }
            if(!empty($tracking_email)) $details['ReceiverDetails'][0]['ReceiverContactEmail'] = $tracking_email;
            if(!empty($contact_phone)) $details['ReceiverDetails'][0]['ReceiverContactMobile'] = $contact_phone;
            if(!empty($FSG_reference)) $details['CustomerReference'] = $FSG_reference;
            //create the consignment
            $con_list['ConsignmentList'][] = $details;
            $final_result = [];
            //echo "DETAILS<pre>",print_r($con_list),"</pre>";die();
            $con_result = $this->directfreight->createConsignment($con_list);
            if($con_result['ResponseCode'] != 300)
            {
                //Form::setError('general', $con_result['ResponseMessage']);
                //Session::set('value_array', $_POST);
                //Session::set('error_array', Form::getErrorArray());
                echo "<p>Create Consignment 300 Error: ".$con_result['ResponseMessage']."</p>";
            }
            else
            {
                $consignment = $con_result['ConsignmentList'][0];
                //echo "<p>Consignment: ".$consignment['Connote']."</p>";
                if($consignment['ResponseCode'] != 200)
                {
                    //Form::setError('general', $consignment['ResponseMessage']);
                    //Session::set('value_array', $_POST);
                    //Session::set('error_array', Form::getErrorArray());
                    echo "<p>Consignment 200 Error: ".$consignment['ResponseMessage']."</p>";
                }
                else
                {
                    //All good, get the charges
                    $connote = $consignment['Connote'];
                    $charges = $this->controller->directfreight->getConsignmentCharges($connote);
                    if($charges['ResponseCode'] != 300)
                    {
                        //Form::setError('general', $charges['ResponseMessage']);
                        //Session::set('value_array', $_POST);
                        //Session::set('error_array', Form::getErrorArray());
                        echo "<p>Charge 300 Error: ".$charges['ResponseMessage']."</p>";
                    }
                    else
                    {
                        $charge = $charges['ConnoteList'][0];
                        $finalise = $this->controller->directfreight->finaliseConsignment($connote);
                        $final_result['consignment'] = $consignment;
                        $final_result['charge'] = $charge;
                        $final_result['finalise'] = $finalise;
                    }
                }
            }
        }
        echo "FINAL RESULT<pre>",print_r($final_result),"</pre>"; die();
        //return
        return $this->redirector->to(PUBLIC_ROOT."courier-functions/book-direct-freight");
    }

    public function procBookCourier()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        echo "<pre>",print_r($post_data),"</pre>";
    }

    public function procRecordItemCollection()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        //echo "<pre>",print_r($post_data),"</pre>";
        if($client_id == "0")
        {
            Form::setError('client_id', 'A client is required');
        }
        if( !($this->dataSubbed($cartons) || $this->dataSubbed($pallets)))
        {
            Form::setError('counter', "At least one of these values is required");
        }
        else
        {
            if($this->dataSubbed($pallets))
            {
                if( filter_var( $pallets, FILTER_VALIDATE_INT ) === false || $pallets <= 0)
                {
                    Form::setError('counter', "Only positive whole numbers can be used for quantities");
                }
            }
            else
                $post_data['pallets'] = 0;
            if($this->dataSubbed($cartons))
            {
                if( filter_var( $cartons, FILTER_VALIDATE_INT ) === false || $cartons <= 0)
                {
                    Form::setError('counter', "Only positive whole numbers can be used for quantities");
                }
            }
            else
                $post_data['cartons'] = 0;
        }
        if( !$this->dataSubbed($courier) )
        {
            Form::setError('courier', "A courier name is required");
        }
        if(!$this->dataSubbed($charge))
        {
            Form::setError('charge', 'A charge amount is required');
        }
        elseif(!preg_match("/\b\d{1,3}(?:,?\d{3})*(?:\.\d{2})?\b/", $charge))
        {
            Form::setError('charge', 'Please enter a valid dollar amount');
        }
        $this->validateAddress($address, $suburb, $state, $postcode, "AU", false);
        $this->validateAddress($puaddress, $pusuburb, $pustate, $pupostcode, "AU", false, "pu");
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //echo "<pre>",print_r($post_data),"</pre>"; die();
            //record the pickup
            $this->itemscollection->addItemCollection($post_data);
            //set the feedback
            Session::set('feedback',"<h2><i class='far fa-check-circle'></i>That collection has been recorded</h2>");
        }
        //return
        return $this->redirector->to(PUBLIC_ROOT."data-entry/items-collection");
    }

    public function procRepalletiseShrinkwrap()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        if($client_id == "0")
        {
            Form::setError('client_id', 'A client is required');
        }
        if( !$this->dataSubbed($repalletise_count) && !$this->dataSubbed($shrinkwrap_count))
        {
            Form::setError('choose_one', 'At least one of these need to be filled');
        }
        else
        {
            if( ( $this->dataSubbed($repalletise_count) ) &&
             (filter_var($repalletise_count, FILTER_VALIDATE_INT, array('options' => array('min_range' => 0))) === false) )
            {
                Form::setError("repalletise_count", "Please enter positive whole numbers only");
            }
            if( ( $this->dataSubbed($shrinkwrap_count) ) &&
             (filter_var($shrinkwrap_count, FILTER_VALIDATE_INT, array('options' => array('min_range' => 0))) === false) )
            {
                Form::setError("shrinkwrap_count", "Please enter positive whole numbers only");
            }
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $this->repalletiseshrinkwrap->addData($post_data);
            //set the feedback
            Session::set('feedback',"<h2><i class='far fa-check-circle'></i>That data has been added to the system</h2>");
        }
        //return
        return $this->redirector->to(PUBLIC_ROOT."data-entry/repalletising-shrinkwrapping");
    }

    public function procContactUs()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        //robot catcher
        $load_time = time() - $loaded;
        if( $load_time < 10 && $this->dataSubbed($the_website) )
            return false;
        //end robot catcher
        if(!$this->dataSubbed($subject))
        {
            Form::setError('subject', "Please enter a subject");
        }
        if(!$this->dataSubbed($message))
        {
            Form::setError('message', "Please enter a message");
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //echo "ALL GOOD<pre>",print_r($post_data),"</pre>"; die();
            //Session::set('feedback',"<h2><i class='far fa-check-circle'></i>The Job Delivery Details Have Been Updated</h2>");
            if(Email::sendContactUsEmail($subject,$message))
            {
                Session::set('feedback',"<h2><i class='far fa-check-circle'></i>Your Message Has Been Sent</h2><p>We will be in contact soon</p>");
            }
            else
            {
                Session::set('value_array', $_POST);
                Session::set('feedback',"<h2><i class='far fa-times-circle'></i>Your Message Failed to Send</h2><p>Sorry, there has been an error</p><p>Please try again</p>");
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."contact/contact-us/");
    }

    public function procPickupSearch()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $_POST['pickups'] = $this->pickup->getSearchResults($this->request->data);
        Session::set('value_array', $_POST);
        Session::set('error_array', Form::getErrorArray());
        return $this->redirector->to(PUBLIC_ROOT."deliveries/pickup-search");
    }

    public function procDeliverySearch()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $_POST['deliveries'] = $this->delivery->getSearchResults($this->request->data);
        Session::set('value_array', $_POST);
        Session::set('error_array', Form::getErrorArray());
        return $this->redirector->to(PUBLIC_ROOT."deliveries/delivery-search");
    }

    public function procCompleteDelivery()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        $delivery_id = $this->request->data['delivery_id'];
        $client_id = $this->request->data['client_id'];
        //$delivery = $this->delivery->getDeliveryDetails($delivery_id);
        $items = $this->delivery->getItemsForDelivery($delivery_id);
        //echo "ITEMS<pre>",print_r($items),"</pre>"; die();
        $reason_id = $this->stockmovementlabels->getLabelId("Delivery Fulfillment");
        foreach($items as $item)
        {
            //remove stock
            $this->location->subtractFromLocation([
                'subtract_product_id'	    => $item['item_id'],
                'subtract_from_location'	=> $item['location_id'],
                'qty_subtract'				=> $item['qty'],
                'reference'					=> 'Delivery Fulfillment',
                'delivery_id'				=> $delivery_id,
                'reason_id'					=> $reason_id
            ]);
            //record removal from client bays
            $this->clientsbays->stockRemoved($client_id, $item['location_id'], $item['item_id']);
            //record removal from delivery client bays
            $this->deliveryclientsbay->stockRemoved($client_id, $item['location_id'], $item['item_id']);
        }
        //change delivery status
        $this->delivery->completeDelivery($delivery_id);
        //set the feedback
        Session::set('feedback',"<h2><i class='far fa-check-circle'></i>That delivery has been marked as complete</h2><p>It should <strong>NOT</strong> be showing below</p>");
        //return
        return $this->redirector->to(PUBLIC_ROOT."deliveries/manage-deliveries/client=$client_id");
    }

    public function procPickupPutaways()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        foreach($locations as $i => $l)
        {
            if(array_search($l['location_id'], array_column($locations, 'location_id')) != $i)
                Form::setError('item_errors', "Same location chosen more than once");
        }
        if(!preg_match('/[0-9]+\.?[0-9]{0,2}/', $repalletize_charge))
        {
            Form::setError('repalletize_charge', "Please enter a valid dollar and cents amount for the repalletize charge");
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
            return $this->redirector->to(PUBLIC_ROOT."deliveries/manage-pickup/pickup=$pickup_id#putaway_holder");
        }
        else
        {
            //echo "ALL GOOD<pre>",print_r($post_data),"</pre>"; die();
            foreach($locations as $i => $l)
            {
                //put items in locations
                $location_data = array(
                    'add_product_id'    => $l['item_id'],
                    'add_to_location'   => $l['location_id'],
                    'qty_add'           => $l['qty'],
                    'reference'         => 'Client Booked Pickup',
                    'reason_id'         => $this->stockmovementlabels->getLabelId("New Stock")
                );
                $this->location->addToLocation($location_data);
                //record client bay use
                $this->clientsbays->stockAdded($client_id, $l['location_id']);
                //record delivery client bay use
                $this->deliveryclientsbay->stockAdded([
                    'client_id'     => $client_id,
                    'location_id'   => $l['location_id'],
                    'size'          => $l['size'],
                    'item_id'       => $l['item_id']
                ]);
            }
            //add charges
            if($repalletize_charge > 0)
                $this->pickup->updateFieldValue('repalletize_charge', $repalletize_charge, $pickup_id) ;
            if($rewrap_charge > 0)
                $this->pickup->updateFieldValue('rewrap_charge', $rewrap_charge, $pickup_id) ;
            $pickup_charge = $this->pickup->getPickupCharge($pickup_id);
            $gst = round(($repalletize_charge + $rewrap_charge + $pickup_charge) * 0.1 , 2 );
            $total = $repalletize_charge + $rewrap_charge + $pickup_charge + $gst;
            $this->pickup->updateFieldValue('shipping_charge', $pickup_charge, $pickup_id) ;
            $this->pickup->updateFieldValue('gst', $gst, $pickup_id) ;
            $this->pickup->updateFieldValue('total_charge', $total, $pickup_id) ;
            //change the status of the pickup
            $this->pickup->markPickupComplete($pickup_id);
            Session::set('feedback',"<h2><i class='far fa-check-circle'></i>All Items Put Away</h2><p>This pickup is now available on the <a href='/reports/pickups-report/client=$client_id'>Reports Page</a>");
            return $this->redirector->to(PUBLIC_ROOT."deliveries/manage-pickups/client=$client_id");
        }
    }

    public function procBookAPickup()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        //echo "<pre>",print_r($post_data),"</pre>";die();
        $post_data['sdu'] = $this->deliveryurgency->getUrgencyId("Same Day");
        $pickup_id = $this->pickup->addPickup($post_data);
        if($manually_entered == 0)
        {
            Session::set('pickupfeedback',"<h2><i class='far fa-check-circle'></i>That Pickup has Been Booked</h2><p>It should be showing on the list below</p>");
            return $this->redirector->to(PUBLIC_ROOT."deliveries/view-pickups");
        }
        else
        {
            Session::set('feedback',"<h2><i class='far fa-check-circle'></i>That Pickup has Been Added To The System</h2><p>It can be managed below</p>");
            return $this->redirector->to(PUBLIC_ROOT."deliveries/manage-pickups/client=$client_id");
        }

    }

    public function procBookDelivery()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        //echo "<pre>",print_r($items),"</pre>";die();
        if(!isset($items) || !count($items))
            Form::setError('items', "At least one item must be selected");
        //$this->validateAddress($delivery_address, $delivery_suburb, $delivery_state, $delivery_postcode, "AU", isset($ignore_address_error), "delivery_");
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
            if($manually_entered == 0)
                return $this->redirector->to(PUBLIC_ROOT."deliveries/book-delivery");
            else
                return $this->redirector->to(PUBLIC_ROOT."deliveries/add-delivery/client=$client_id");
        }
        else
        {
            //echo "ALL GOOD<pre>",print_r($post_data),"</pre>"; die();
            $delivery_id = $this->delivery->addDelivery($post_data);
            if($manually_entered == 0)
            {
                Session::set('deliveryfeedback',"<h2><i class='far fa-check-circle'></i>That Delivery has Been Booked</h2><p>It should be showing on the list below</p>");
                return $this->redirector->to(PUBLIC_ROOT."deliveries/view-deliveries");
            }
            else
            {
                Session::set('feedback',"<h2><i class='far fa-check-circle'></i>That Delivery has Been Added To The System</h2><p>It can be managed below</p>");
                return $this->redirector->to(PUBLIC_ROOT."deliveries/manage-deliveries/client=$client_id");
            }
        }
    }

    public function procShipmentAddressUpdate()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        //echo "<pre>",print_r($post_data),"</pre>"; die();
        $this->validateAddress($address, $suburb, $state, $postcode, $country, isset($ignore_address_error));
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //echo "ALL GOOD<pre>",print_r($post_data),"</pre>"; die();
            $this->productionjobsshipment->updateJobShipmentAddress($post_data);
            Session::set('feedback',"<h2><i class='far fa-check-circle'></i>The Job Delivery Details Have Been Updated</h2>");
        }
        return $this->redirector->to(PUBLIC_ROOT."jobs/shipment-address-update/shipment={$shipment_id}/job={$job_id}");
    }

    public function procProductionJobDeliveryUpdate()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        $this->validateAddress($address, $suburb, $state, $postcode, $country, isset($ignore_address_error));
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
            Session::set('jobdeliverydetailserrorfeedback', "<h3><i class='far fa-times-circle'></i>Errors found in the form</h3><p>Please correct where shown and resubmit</p>");
        }
        else
        {
            //echo "ALL GOOD<pre>",print_r($post_data),"</pre>"; die();
            $this->productionjobsshipment->enterJobShipmentAddress($post_data);
            Session::set('jobdeliverydetailsfeedback',"<h3><i class='far fa-check-circle'></i>The Job Delivery Details Have Been Saved</h3><p>The changes should be showing below</p>");
        }
        return $this->redirector->to(PUBLIC_ROOT."jobs/create-shipment/job={$job_id}#deliverydetails");
    }

    public function procInventoryCompare()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if($_FILES['csv_file']["size"] > 0)
        {
            if ($_FILES['csv_file']['error']  === UPLOAD_ERR_OK)
            {
                $tmp_name = $_FILES['csv_file']['tmp_name'];
                $csv_array = array_map('str_getcsv', file($tmp_name));
                //echo "<pre>",print_r($csv_array),"</pre>"; die();
            }
            else
            {
            	$error_message = $this->file_upload_error_message($_FILES[$field]['error']);
                Form::setError('csv_file', $error_message);
            }
        }
        else
        {
            Form::setError('csv_file', 'please select a file to upload');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //echo "<pre>",print_r($csv_array),"</pre>";//die();
            /*
            [0] => SKU
            [1] => Name
            [2] => Count
            */
            //$line = 0;
            $skip_first = isset($header_row);
            $feedback_string = "<ul>";
            //Set up csv file
            $cols = array(
                "ISBN",
                "Name",
                "MYOB On Hand",
                "WMS On Hand",
                "Comments"
            );
            $rows = array();
            foreach($csv_array as $row)
            {
                if($skip_first)
                {
                    $skip_first = false;
                    //++$line;
                    continue;
                }
                //echo "<p>Checking ".$row[1]."</p>";
                $line = array(
                    $row[0],
                    $row[1],
                    $row[2]
                );
                $item = $this->item->getItemForClientByBarcode(array(
                    'barcode'   => $row[0],
                    'sku'       => $row[0],
                    'client_id' => $client_id
                ), -1);
                //echo "<pre>",print_r($item),"</pre";
                if(!$item)
                {
                    $line[] = "-";
                    $line[] = "Not Found In WMS";
                    //echo "<p>Need to check ".$row[1]."( ".$row[0]." ) on line: $line</p>";
                    //echo "<p>-------------------------------------------</p>";
                }
                else
                {
                    $wms_count = $this->item->getStockOnHand($item['id']);
                    $line[] = $wms_count;
                }
                $rows[] = $line;
                //++$line;
            }
            //echo "Rows<pre>",print_r($rows),"</pre>";
            $expire=time()+60;
            setcookie("fileDownload", "true", $expire, "/");
            $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "freedom_stock_compare".date("Ymd")]);
        }
    }

    public function procAddPurchaseOrder()
    {
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        //echo "<pre>",print_r($post_data),"</pre>"; die();
        //required fields
        if($finisher_id == 0)
        {
            Form::setError('finisher_name', 'The Supplier Name is required');
        }
        if(!$this->dataSubbed($date))
        {
            Form::setError('date', 'The date for this purchase order is required');
        }
        if(!$this->dataSubbed($required_date))
        {
            Form::setError('required_date', 'Please indicate when this is required');
        }
        foreach($poitems as $i => $array)
        {
            if(!$this->dataSubbed($array['qty']))
            {
                Form::setError('poitem_qty_'.$i, 'The quantity of each item is required');
            }
            if(!$this->dataSubbed($array['description']))
            {
                Form::setError('poitem_description_'.$i, 'Please enter a description for this item');
            }
        }

        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good add the purchase order
            $po_id = $this->purchaseorder->addPurchaseOrder($post_data);
            Session::set('feedback', "That Purchase Order has been added to the system.<br/>It can be viewed/edited <a href='/purchase-orders/view-update-purchase-order/po=".$po_id."'>HERE</a>");
        }
        return $this->redirector->to(PUBLIC_ROOT."purchase-orders/add-purchase-order");
    }

    public function procClientProductEdit()
    {

        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        //echo "<pre>",print_r($post_data),"</pre>"; die();
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A product name is required');
        }
        if($this->dataSubbed($image))
        {
            if(!preg_match('/https?/i', $image))
            {
                Form::setError('image', 'There is in error in the format of this URL');
            }
        }
        else
        {
            $post_data['image'] = NULL;
        }

        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, update details
            if($this->item->clientEditItem($post_data))
            {
                Session::set('feedback', "{$name}'s details have been updated in the system<br>The changes should be showing below");
            }
            else
            {
                Session::set('value_array', $_POST);
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."products/client-product-edit/product=$item_id");
    }

    public function procAddMiscTask()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        //echo "<pre>",print_r($post_data),"</pre>"; die();
        if( !$this->dataSubbed($deliver_to) )
        {
            Form::setError('deliver_to', 'A deliver to name is required');
        }
        $this->validateAddress($address, $suburb, 'VIC', $postcode, 'AU', isset($ignore_address_error));
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //echo "<pre>",print_r($post_data),"</pre>"; die();
            $this->runsheet->addTaskToRunsheet($post_data);
            Session::set('feedback', "<h2>That task has been added to the runsheet.</h2><p><a href='/runsheets/view-runsheets'>The details can be found here</a></p>");
        }
        return $this->redirector->to(PUBLIC_ROOT."runsheets/add-misc-task/runsheet=$runsheet_id");
    }

    public function procRunsheetCompletionUpdate()
    {
        $this->runsheet->updateCompletionStatus();
        Session::set('feedback', "<h2>Runsheet Status Have Been Updated.</h2><p></p>");
        return $this->redirector->to(PUBLIC_ROOT."admin-only/runsheet-completion-tidy");
    }

    public function procCompletRunsheetTasks()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        //echo "_POST<pre>",print_r($post_data),"</pre>"; die();
        if(!isset($tasks) || !count($tasks))
        {
            Form::setError('general', 'At least one task must be selected for complettion');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
            return $this->redirector->to(PUBLIC_ROOT."runsheets/finalise-runsheet/runsheet=$runsheet_id/driver=$driver_id");
        }
        else
        {
            $this->runsheet->completeTasks($post_data);
            Session::set('feedback', "<h2>Those Tasks Have Been Marked As Completed.</h2><p></p>");
        }
        return $this->redirector->to(PUBLIC_ROOT."runsheets/finalise-runsheets");
    }

    public function procPrepareRunsheet()
    {
        //echo "_POST<pre>",print_r($_POST),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        //echo "_POST<pre>",print_r($post_data),"</pre>"; die();
        if($driver_id == 0)
        {
            Form::setError('driver_id', 'A Driver is required');
        }
        $error = true;
        $tts = array();
        if( isset($tasks['jobs']) )
        {
            foreach($tasks['jobs'] as $job_id => $jd)
            {
                $task_id = $jd['task_id'];
                if(isset($jd['include']))
                {
                    $error = false;
                    if( !$this->dataSubbed($jd['address']) )
                    {
                        Form::setError('address_'.$task_id, 'An address is required');
                    }
                    if( !$this->dataSubbed($jd['shipto']) )
                    {
                        Form::setError('shipto_'.$task_id, 'A delivery name is required');
                    }
                    if( !$this->dataSubbed($jd['suburb']) )
                    {
                        Form::setError('suburb_'.$task_id, 'A suburb is required');
                    }
                    if( !$this->dataSubbed($jd['postcode']) )
                    {
                        Form::setError('postcode_'.$task_id, 'A postcode is required');
                    }
                    $aResponse = $this->Eparcel->ValidateSuburb($jd['suburb'], 'VIC', str_pad($jd['postcode'],4,'0',STR_PAD_LEFT));
                    $error_string = "";
                    if(isset($aResponse['errors']))
                    {
                        foreach($aResponse['errors'] as $e)
                        {
                            $error_string .= $e['message']." ";
                        }
                    }
                    elseif($aResponse['found'] === false)
                    {
                        $error_string .= "Postcode does not match suburb or state";
                    }
                    if(strlen($error_string))
                    {
                        Form::setError('postcode_'.$task_id, $error_string);
                    }
                    if(Form::$num_errors == 0)
                    {
                        $array = array(
                            'task_id'       => $task_id,
                            'driver_id'     => $driver_id,
                            'address'       => $jd['address'],
                            'suburb'        => $jd['suburb'],
                            'postcode'      => $jd['postcode'],
                            'deliver_to'    => $jd['shipto'],
                            'runsheet_id'   => $runsheet_id
                        );
                        if($this->dataSubbed($jd['address2']))
                            $array['address_2'] = $jd['address2'];
                        if($this->dataSubbed($jd['units']))
                            $array['units'] = $jd['units'];
                        if($this->dataSubbed($jd['attention']))
                            $array['attention'] = $jd['attention'];
                        if($this->dataSubbed($jd['delivery_instructions']))
                            $array['delivery_instructions'] = $jd['delivery_instructions'];
                        $tts[] = $array;
                    }
                }
            }
        }
        if( isset($tasks['orders']) )
        {
            foreach($tasks['orders'] as $order_id => $od)
            {
                $task_id = $od['task_id'];
                if(isset($od['include']))
                {
                    $error = false;
                    if( !$this->dataSubbed($od['address']) )
                    {
                        Form::setError('address_'.$task_id, 'An address is required');
                    }
                    if( !$this->dataSubbed($od['shipto']) )
                    {
                        Form::setError('shipto_'.$task_id, 'A delivery name is required');
                    }
                    if( !$this->dataSubbed($od['suburb']) )
                    {
                        Form::setError('suburb_'.$task_id, 'A suburb is required');
                    }
                    if( !$this->dataSubbed($od['postcode']) )
                    {
                        Form::setError('postcode_'.$task_id, 'A postcode is required');
                    }
                    $aResponse = $this->Eparcel->ValidateSuburb($od['suburb'], 'VIC', str_pad($od['postcode'],4,'0',STR_PAD_LEFT));
                    $error_string = "";
                    if(isset($aResponse['errors']))
                    {
                        foreach($aResponse['errors'] as $e)
                        {
                            $error_string .= $e['message']." ";
                        }
                    }
                    elseif($aResponse['found'] === false)
                    {
                        $error_string .= "Postcode does not match suburb or state";
                    }
                    if(strlen($error_string))
                    {
                        Form::setError('postcode_'.$task_id, $error_string);
                    }
                    if(Form::$num_errors == 0)
                    {
                        $array = array(
                            'task_id'       => $task_id,
                            'driver_id'     => $driver_id,
                            'address'       => $od['address'],
                            'suburb'        => $od['suburb'],
                            'postcode'      => $od['postcode'],
                            'deliver_to'    => $od['shipto'],
                            'runsheet_id'   => $runsheet_id
                        );
                        if($this->dataSubbed($od['address2']))
                            $array['address_2'] = $od['address2'];
                        if($this->dataSubbed($od['units']))
                            $array['units'] = $od['units'];
                        if($this->dataSubbed($od['delivery_instructions']))
                            $array['delivery_instructions'] = $od['delivery_instructions'];
                        $tts[] = $array;
                    }
                }
            }
        }
        if( isset($tasks['tasks']) )
        {
            foreach($tasks['tasks'] as $id => $td)
            {
                $task_id = $td['task_id'];
                if(isset($td['include']))
                {
                    $error = false;
                    if( !$this->dataSubbed($td['address']) )
                    {
                        Form::setError('address_'.$task_id, 'An address is required');
                    }
                    if( !$this->dataSubbed($td['shipto']) )
                    {
                        Form::setError('shipto_'.$task_id, 'A delivery name is required');
                    }
                    if( !$this->dataSubbed($td['suburb']) )
                    {
                        Form::setError('suburb_'.$task_id, 'A suburb is required');
                    }
                    if( !$this->dataSubbed($td['postcode']) )
                    {
                        Form::setError('postcode_'.$task_id, 'A postcode is required');
                    }
                    $aResponse = $this->Eparcel->ValidateSuburb($td['suburb'], 'VIC', str_pad($td['postcode'],4,'0',STR_PAD_LEFT));
                    $error_string = "";
                    if(isset($aResponse['errors']))
                    {
                        foreach($aResponse['errors'] as $e)
                        {
                            $error_string .= $e['message']." ";
                        }
                    }
                    elseif($aResponse['found'] === false)
                    {
                        $error_string .= "Postcode does not match suburb or state";
                    }
                    if(strlen($error_string))
                    {
                        Form::setError('postcode_'.$task_id, $error_string);
                    }
                    if(Form::$num_errors == 0)
                    {
                        $array = array(
                            'task_id'       => $task_id,
                            'driver_id'     => $driver_id,
                            'address'       => $td['address'],
                            'suburb'        => $td['suburb'],
                            'postcode'      => $td['postcode'],
                            'deliver_to'    => $td['shipto'],
                            'runsheet_id'   => $runsheet_id
                        );
                        if($this->dataSubbed($td['address2']))
                            $array['address_2'] = $td['address2'];
                        if($this->dataSubbed($td['units']))
                            $array['units'] = $td['units'];
                        if($this->dataSubbed($td['delivery_instructions']))
                            $array['delivery_instructions'] = $td['delivery_instructions'];
                        $tts[] = $array;
                    }
                }
            }
        }
        if($error)
        {
            Form::setError('general', 'At least one job or order needs to be selected');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //echo "JOBS<pre>",print_r($tasks['jobs']),"</pre>";
            //echo "ORDERS<pre>",print_r($tasks['orders']),"</pre>";
            //echo "POST DATA<pre>",print_r($post_data),"</pre>"; //die();
            //echo "WILL SAVE THE FOLLOWING<pre>",print_r($tts),"</pre>"; die();
            foreach($tts as $details)
            {
                $this->runsheet->updateTask($details);
            }
            Session::set('feedback', "<h2>Those details have been updated.</h2><p><a class='btn btn-sm btn-outline-fsg' href='/pdf/print-runsheet/runsheet=".$runsheet_id."'>Print Runsheet</a></p>");
        }
        return $this->redirector->to(PUBLIC_ROOT."runsheets/prepare-runsheet/runsheet=$runsheet_id");
    }

    public function procFinisherCategoryEdit()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        $response = array();
        $id = $this->request->data['line_id'];
        $post_data = array('id' => $id);
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        //echo "<pre>",print_r($post_data),"</pre>"; die();
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A Category name is required');
        }
        elseif($this->finishercategories->getCategoryId($name) && strtolower($name) != $currentname )
        {
            Form::setError('name', 'This Category is already in use. Category names need to be unique');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //edit the category
            if($this->finishercategories->editCategory($post_data))
            {
                Session::set('feedback', "That Category has been updated");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."production-settings/finisher-categories");
    }

    public function procFinisherCategoryAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        $response = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A Category name is required');
        }
        elseif($this->finishercategories->getCategoryId($name) )
        {
            Form::setError('name', 'This Category is already in use. Category names need to be unique');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //add the category
            if($this->finishercategories->addCategory($post_data))
            {
                Session::set('feedback', "That Category has been added");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."production-settings/finisher-categories");
    }

    public function procJobDeliveryUpdate()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        //if(!isset($held_in_store))
        if( !isset($held_in_store) && !isset($hold_in_store) )
            $this->validateAddress($address, $suburb, $state, $postcode, $country, isset($ignore_address_error));
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
            Session::set('jobdeliverydetailserrorfeedback', "<h3><i class='far fa-times-circle'></i>Errors found in the form</h3><p>Please correct where shown and resubmit</p>");
        }
        else
        {
            //echo "ALL GOOD<pre>",print_r($post_data),"</pre>"; die();
            $this->productionjob->updateJobAddress($post_data);
            Session::set('jobdeliverydetailsfeedback',"<h3><i class='far fa-check-circle'></i>The Job Delivery Details Have Been Updated</h3><p>The changes should be showing below</p>");
        }
        return $this->redirector->to(PUBLIC_ROOT."jobs/update-job/job={$job_id}#deliverydetails");
    }

    public function procTransferLocation()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( ($move_to_location == $move_from_location) && ($move_from_location != 0) )
        {
            Form::setError('move_from_location', 'Please select two <strong>different</strong> locations');
        }
        if(!isset($move_from_location) || $move_from_location == 0)
        {
            Form::setError('move_from_location', 'Please select a location to move from');
        }
        if(!isset($move_to_location) || $move_to_location == 0)
        {
            Form::setError('move_to_location', 'Please select a location to move to');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $this->location->moveAllItemsInLocation($move_from_location, $move_to_location);
            Session::set('feedback', "All items have been moved to the new Location");
        }
        return $this->redirector->to(PUBLIC_ROOT."/inventory/transfer-location");
    }

    public function procDriverEdit()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $id = $this->request->data['line_id'];
        $post_data = array('id' => $id);
        foreach($this->request->data as $field => $value)
        {
            //$field = strtok($field, "_");
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }

        if( !$this->dataSubbed($name) )
        {
            Form::setError('name_'.$id, 'A name is required');
        }
        elseif($this->driver->getDriverId($name) && strtolower($name) != strtolower($current_name) )
        {
            Form::setError('name_'.$id, 'This name is already in the system<br>Names must be unique');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
            if($this->driver->editDriver($post_data))
            {
                Session::set('feedback', "Those details have been updated");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT.$return_url);
    }

    public function procBookCoverAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A name is required');
        }
        elseif( $this->Bookcovers->getCoverId($name) )
        {
            Form::setError('name', 'This name is already in the system<br>Names must be unique');
        }
        if( filter_var( $qty, FILTER_VALIDATE_INT ) === false || $qty <= 0)
        {
            Form::setError('qty', 'Only use positive whole numbers here');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
            if($driver_id = $this->Bookcovers->addCover($post_data))
            {
                Session::set('feedback', "That Cover has been added to the system");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."inventory/book-covers");
    }

    public function procDriverAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A name is required');
        }
        elseif( $this->driver->getDriverId($name) )
        {
            Form::setError('name', 'This name is already in the system<br>Names must be unique');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
            if($driver_id = $this->driver->addDriver($post_data))
            {
                Session::set('feedback', "That Driver has been added to the system");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT.$return_url);
    }

    public function procBulkProductionSupplierAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if($_FILES['csv_file']["size"] > 0)
        {
            if ($_FILES['csv_file']['error']  === UPLOAD_ERR_OK)
            {
                $tmp_name = $_FILES['csv_file']['tmp_name'];
                $csv_array = array_map('str_getcsv', file($tmp_name));
                //echo "<pre>",print_r($csv_array),"</pre>"; //die();
            }
            else
            {
            	$error_message = $this->file_upload_error_message($_FILES[$field]['error']);
                Form::setError('csv_file', $error_message);
            }
        }
        else
        {
            Form::setError('csv_file', 'please select a file to upload');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            //Session::set('value_array', $_POST);
            //Session::set('error_array', Form::getErrorArray());
            echo "<pre>",print_r(Form::getErrorArray()),"</pre>";
        }
        else
        {
            //echo "<pre>",print_r($csv_array),"</pre>";die();
            /*
            [0] => ?Name
            [1] => Address 1
            [2] => Address 2
            [3] => Address 3
            [4] => Postcode
            [5] => Main State
            [6] => Main Postcode
            [7] => Counttry
            [8] => Telephone
            [9] => Mobile
            [10] => Email
            [11] => Web Site
            [12] => Salesperson
            */
            $added_supplier_count = $updated_supplier_count = 0;
            $skip_first = isset($header_row);
            $line = 1;
            $data_error_string = "<ul>";
            $import_suppliers = true;
            $suppliers = array();
            foreach($csv_array as $row)
            {
                if($skip_first)
                {
                    $skip_first = false;
                    ++$line;
                    continue;
                }
                $name = trim($row[0]);
                $phone = (empty(trim($row[9])))? (empty(trim($row[8])))? "" : trim($row[8]) : trim($row[9]);
                $supplier_id = $this->productionsupplier->getSupplierIdByName($name);
                $supplier_details = array(
                    'name'      => $name,
                    'phone'     => $phone,
                    'contact'   => trim($row[12]),
                    'email'     => trim($row[10]),
                    'address'   => trim($row[1]),
                    'address2'  => trim($row[2]),
                    'suburb'    => trim($row[4]),
                    'state'     => trim($row[5]),
                    'postcode'  => trim($row[6]),
                    'website'   => trim($row[11])
                );
                $supplier_details['country'] = (!empty($row[7]))? trim($row[7]) : "AU";

                if(!empty($supplier_id))
                {
                    $supplier_details['supplier_id'] = $supplier_id;
                    $this->productionsupplier->editSupplier($supplier_details);
                    //echo "<p>----------------------------------------------------------------------------------------------------</p>";
                    //echo "<p>Updated {$name}'s details</p>";
                    //echo "<p>----------------------------------------------------------------------------------------------------</p>";
                    ++$updated_supplier_count;
                }
                else
                {
                    $customer_id = $this->productionsupplier->addSupplier($supplier_details);
                    //echo "<p>----------------------------------------------------------------------------------------------------</p>";
                    //echo "<p>Added $name/p>";
                    //echo "<p>----------------------------------------------------------------------------------------------------</p>";
                    ++$added_supplier_count;
                }
            }

            echo "<p>----------------------------------------------------------------------------------------------------</p>";
            echo "<p>Added $added_supplier_count</p>";
            echo "<p>Updated $updated_supplier_count</p>";
            echo "<p>----------------------------------------------------------------------------------------------------</p>";
        }
    }

    public function procBulkProductionCustomerAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if($_FILES['csv_file']["size"] > 0)
        {
            if ($_FILES['csv_file']['error']  === UPLOAD_ERR_OK)
            {
                $tmp_name = $_FILES['csv_file']['tmp_name'];
                $csv_array = array_map('str_getcsv', file($tmp_name));
                //echo "<pre>",print_r($csv_array),"</pre>"; //die();
            }
            else
            {
            	$error_message = $this->file_upload_error_message($_FILES[$field]['error']);
                Form::setError('csv_file', $error_message);
            }
        }
        else
        {
            Form::setError('csv_file', 'please select a file to upload');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            //Session::set('value_array', $_POST);
            //Session::set('error_array', Form::getErrorArray());
            echo "<pre>",print_r(Form::getErrorArray()),"</pre>";
        }
        else
        {
            //echo "<pre>",print_r($csv_array),"</pre>";//die();
            /*
            [0] => Name
            [1] => Code
            [2] => Address 1
            [3] => Address 2
            [4] => Main Address 3
            [5] => Suburb
            [6] => State
            [7] => Postcode
            [8] => Country
            [9] => Telephone
            [10] => Mobile
            [11] => Email
            */
            $added_customer_count = $updated_customer_count = 0;
            $skip_first = isset($header_row);
            $line = 1;
            $data_error_string = "<ul>";
            $import_customers = true;
            $customers = array();
            foreach($csv_array as $row)
            {
                if($skip_first)
                {
                    $skip_first = false;
                    ++$line;
                    continue;
                }
                $name = trim($row[0]);
                $phone = (empty(trim($row[10])))? (empty(trim($row[9])))? "" : trim($row[9]) : trim($row[10]);
                $customer_id = $this->productioncustomer->geCustomerIdByName($name);
                $customer_details = array(
                    'name'  => $name,
                    'phone' => $phone,
                    'email' => trim($row[11]),
                    'address'   => trim($row[2]),
                    'address2'  => trim($row[3]),
                    'suburb'    => trim($row[5]),
                    'state'     => trim($row[6]),
                    'postcode'  => trim($row[7])
                );
                $customer_details['country'] = (!empty($row[8]))? trim($row[8]) : "AU";

                //echo "<pre>",print_r($customer_details),"</pre>";
                if(!empty($customer_id))
                {
                    $customer_details['customer_id'] = $customer_id;
                    $this->productioncustomer->editCustomer($customer_details);
                    //echo "<p>----------------------------------------------------------------------------------------------------</p>";
                    //echo "<p>Updated {$name}'s details</p>";
                    //echo "<p>----------------------------------------------------------------------------------------------------</p>";
                    ++$updated_customer_count;
                }
                else
                {
                    $customer_id = $this->productioncustomer->addCustomer($customer_details);
                    //echo "<p>----------------------------------------------------------------------------------------------------</p>";
                    //echo "<p>Added $name/p>";
                    //echo "<p>----------------------------------------------------------------------------------------------------</p>";
                    ++$added_customer_count;
                }
            }

            echo "<p>----------------------------------------------------------------------------------------------------</p>";
            echo "<p>Added $added_customer_count</p>";
            echo "<p>Updated $updated_customer_count</p>";
            echo "<p>----------------------------------------------------------------------------------------------------</p>";
        }
    }

    public function procJobfinisherUpdate()
    {
        //echo "<pre>DATA",print_r($this->request->data),"</pre>"; //die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        //echo "<pre>POST DATA",print_r($post_data),"</pre>"; die();
        //$date_ed_value = (!empty($date_ed_value))? $date_ed_value: 0;
        if(isset($finishers))
        {
            foreach($finishers as $ind => $finisher)
            {
                if(!$this->dataSubbed($finisher['name']))
                {
                    Form::setError('finishername_'.$ind, 'A Finisher Name is required');
                }
            }
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
            Session::set('jobfinisherdetailserrorfeedback', "<h3><i class='far fa-times-circle'></i>Errors found in the form</h3><p>Please correct where shown and resubmit</p>");
        }
        else
        {
            //echo "ALL GOOD<pre>",print_r($post_data),"</pre>";die();
            if(!isset($finishers))
            {
                //echo "<p>Will remove all finishers</p>";
                $this->productionjob-> removeFinishersFromJob($id);
                Session::set('jobfinisherdetailsfeedback',"<h3><i class='far fa-check-circle'></i>All Finisher have Removed</h3><p>They should <strong>NOT</strong> be showing below</p>");
            }
            else
            {
                //echo "Will update JOB: $id to<pre>",print_r($finishers),"</pre>";die();
                if($this->productionjob-> removeFinishersFromJob($id))
                {
                    foreach($finishers as $f => $finisher)
                    {
                        $this->productionjob->addFinisherToJob($id, array(
                            'ed_date_value'     => $finisher['ed_date_value'],
                            'contact_id'        => $finisher['contact_id'],
                            'purchase_order'    => $finisher['purchase_order'],
                            'finisher_order'    => $f,
                            'finisher_id'       => $finisher['finisher_id']
                        ));
                    }
                }
                else
                {
                    die('database error');
                }
                Session::set('jobfinisherdetailsfeedback',"<h3><i class='far fa-check-circle'></i>The Job's Finisher Details Have Been Updated</h3><p>The changes should be showing below</p>");
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."jobs/update-job/job={$id}#finisherdetails");
    }

    public function procJobCustomerUpdate()
    {
        //echo "<pre>DATA",print_r($this->request->data),"</pre>"; //die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        //echo "<pre>POST DATA",print_r($post_data),"</pre>"; die();
        if(!$this->dataSubbed($customer_name))
        {
            Form::setError('customer_name', 'A Customer Name is required');
        }
        //Might be required, or need to fulfill requirements
        if($this->dataSubbed($customer_email))
        {
            if(!$this->emailValid($customer_email))
            {
                Form::setError('customer_email', 'The email is not valid');
            }
        }
        if($this->dataSubbed($customer_contact_email))
        {
            if(!$this->emailValid($customer_contact_email))
            {
                Form::setError('customer_contact_email', 'The email is not valid');
            }
        }
        if(!empty($customer_address) || !empty($customer_suburb) || !empty($customer_state) || !empty($customer_postcode) || !empty($customer_country))
        {
            //$this->validateAddress($address, $suburb, $state, $postcode, $country, isset($ignore_address_error));
            $customer_country = strtoupper($customer_country);
            if( !$this->dataSubbed($customer_address) )
            {
                Form::setError('customer_address', 'An address is required');
            }
            elseif( !isset($ignore_customer_address_error) )
            {
                if( (!preg_match("/(?:[A-Za-z].*?\d|\d.*?[A-Za-z])/i", $customer_address)) && (!preg_match("/(?:care of)|(c\/o)|( co )/i", $customer_address)) )
                {
                    Form::setError('customer_address', 'The address must include both letters and numbers');
                }
            }
            if(!$this->dataSubbed($customer_postcode))
            {
                Form::setError('customer_postcode', "A postcode is required");
            }
            if(!$this->dataSubbed($customer_country))
            {
                Form::setError('customer_country', "A country is required");
            }
            elseif(strlen($customer_country) > 2)
            {
                Form::setError('customer_country', "Please use the two letter ISO code");
            }
            elseif($customer_country == "AU")
            {
                if(!$this->dataSubbed($customer_suburb))
        		{
        		    Form::setError('customer_suburb', "A delivery suburb is required for Australian addresses");
        		}
        		if(!$this->dataSubbed($customer_state))
        		{
        		    Form::setError('customer_state', "A delivery state is required for Australian addresses");
        		}
                $aResponse = $this->Eparcel->ValidateSuburb($customer_suburb, $customer_state, str_pad($customer_postcode,4,'0',STR_PAD_LEFT));
                $error_string = "";
                if(isset($aResponse['errors']))
                {
                    foreach($aResponse['errors'] as $e)
                    {
                        $error_string .= $e['message']." ";
                    }
                }
                elseif($aResponse['found'] === false)
                {
                    $error_string .= "Postcode does not match suburb or state";
                }
                if(strlen($error_string))
                {
                    Form::setError('customer_postcode', $error_string);
                }
            }
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
            Session::set('jobcustomerdetailserrorfeedback', "<h3><i class='far fa-times-circle'></i>Errors found in the form</h3><p>Please correct where shown and resubmit</p>");
        }
        else
        {
            //echo "<pre>",print_r($post_data),"</pre>"; die();
            //$this->productionjob->updateJobDetails($post_data);
            $customer_data = array(
                'name'  => $customer_name
            );
            if($this->dataSubbed($customer_phone)) $customer_data['phone'] = $customer_phone;
            //if($this->dataSubbed($customer_contact)) $customer_data['contact'] = $customer_contact;
            if($this->dataSubbed($customer_email)) $customer_data['email'] = $customer_email;
            if($this->dataSubbed($customer_address)) $customer_data['address'] = $customer_address;
            if($this->dataSubbed($customer_address2)) $customer_data['address2'] = $customer_address2;
            if($this->dataSubbed($customer_suburb)) $customer_data['suburb'] = $customer_suburb;
            if($this->dataSubbed($customer_state)) $customer_data['state'] = $customer_state;
            if($this->dataSubbed($customer_postcode)) $customer_data['postcode'] = $customer_postcode;
            if($this->dataSubbed($customer_country)) $customer_data['country'] = $customer_country;
            if($this->dataSubbed($customer_contact_id) && $customer_contact_id > 0)
            {
                $post_data['customer_contact_id'] = $customer_contact_id;
            }
            else
            {
                $post_data['customer_contact_id'] = 0;
            }
            //Need to add the customer?
            if($customer_id == 0)
            {
                if($this->dataSubbed($customer_contact_name)) $customer_data['contacts'][0]['name'] = $customer_contact_name;
                if($this->dataSubbed($customer_contact_role)) $customer_data['contacts'][0]['role'] = $customer_contact_role;
                if($this->dataSubbed($customer_contact_email)) $customer_data['contacts'][0]['email'] = $customer_contact_email;
                if($this->dataSubbed($customer_contact_phone)) $customer_data['contacts'][0]['phone'] = $customer_contact_phone;
                $customer_id = $this->productioncustomer->addCustomer($customer_data);
                $customer_data['customer_id'] = $customer_id;
                $post_data['customer_id'] = $customer_id;
                //this new customer will only have one contact
                $pcont = new Productioncontact();
                $post_data['customer_contact_id'] = $pcont->getCustomerContactIDs($customer_id, true);
            }
            else
            {
                $customer_data['customer_id'] = $customer_id;
                //$this->productioncustomer->editCustomer($customer_data);
                //echo "Will edit customer data<pre>",print_r($customer_data),"</pre>";
            }
            $this->productionjob->updateJobCustomerId($id, $customer_id, $post_data['customer_contact_id']);
            Session::set('jobcustomerdetailsfeedback',"<h3><i class='far fa-check-circle'></i>The Customer Details Have Been Updated</h3><p>The changes should be showing below</p>");
        }
        return $this->redirector->to(PUBLIC_ROOT."jobs/update-job/job={$id}#customerdetails");
    }

    public function procJobDetailsUpdate()
    {
        //echo "<pre>DATA",print_r($this->request->data),"</pre>"; //die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        //echo "<pre>POST DATA",print_r($post_data),"</pre>"; die();
        if(!$this->dataSubbed($job_id))
        {
            Form::setError('job_id', 'The job id is required');
        }
        if($status_id == 0)
        {
            Form::setError('status_id', 'Please choose a status');
        }
        if(!$this->dataSubbed($date_entered_value))
        {
            Form::setError('date_entered', 'Please supply the date the job was entered');
        }
        if( !$this->dataSubbed($date_due) && isset($strict_dd) )
        {
            Form::setError('date_due', 'Please indicate when this should be dispatched');
        }
        if(!$this->dataSubbed($description))
        {
            Form::setError('description', 'A job description is required');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
            Session::set('jobdetailserrorfeedback', "<h3><i class='far fa-times-circle'></i>Errors found in the form</h3><p>Please correct where shown and resubmit</p>");
        }
        else
        {
            //echo "<pre>",print_r($post_data),"</pre>"; die();
            $this->productionjob->updateJobDetails($post_data);
            Session::set('jobdetailsfeedback',"<h3><i class='far fa-check-circle'></i>The Job Details Have Been Updated</h3><p>The changes should be showing below</p>");
        }
        return $this->redirector->to(PUBLIC_ROOT."jobs/update-job/job={$id}#jobdetails");
    }

    public function procBulkProductionJobAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if($_FILES['csv_file']["size"] > 0)
        {
            if ($_FILES['csv_file']['error']  === UPLOAD_ERR_OK)
            {
                $tmp_name = $_FILES['csv_file']['tmp_name'];
                $csv_array = array_map('str_getcsv', file($tmp_name));
                //echo "<pre>",print_r($csv_array),"</pre>"; //die();
            }
            else
            {
            	$error_message = $this->file_upload_error_message($_FILES[$field]['error']);
                Form::setError('csv_file', $error_message);
            }
        }
        else
        {
            Form::setError('csv_file', 'please select a file to upload');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            //Session::set('value_array', $_POST);
            //Session::set('error_array', Form::getErrorArray());
            echo "<pre>",print_r(Form::getErrorArray()),"</pre>";
        }
        else
        {
            //echo "<pre>",print_r($csv_array),"</pre>";
            /*
            [0] => ?Job ID
            [1] => duplicate check
            [2] => Job ID
            [3] => Previous
            [4] => Customer
            [5] => Description
            [6] => Entered
            [7] => Due date
            [8] => Sales Rep
            [9] => Designer
            [10] => Finisher 1
            [11] => Finisher 2
            [12] => Finisher 3
            [13] => Notes & Comments
            [14] => E.T.D.
            [15] => Status
            [16] => Date
            */
            $imported_job_count = 0;
            $skip_first = isset($header_row);
            $line = 1;
            $data_error_string = "<ul>";
            $import_jobs = true;
            $jobs = array();
            foreach($csv_array as $row)
            {
                if($skip_first)
                {
                    $skip_first = false;
                    ++$line;
                    continue;
                }
                $job_id = trim($row[2]);
                $status_id = $this->jobstatus->getStatusId(trim($row[15]));
                if(!$status_id)
                {
                    echo "<p>----------------------------------------------------------------------------------------------------</p>";
                    echo "<p>Need to add {$row[15]} as a status</p>";
                    echo "<p>----------------------------------------------------------------------------------------------------</p>";
                }
                $rep_id = $this->salesrep->geRepIdByName(trim($row[8]));
                if(empty($rep_id))
                {
                    echo "<p>----------------------------------------------------------------------------------------------------</p>";
                    echo "<p>Need to add {$row[8]} as a sales rep</p>";
                    echo "<p>----------------------------------------------------------------------------------------------------</p>";
                }
                $created_date = str_replace('/', '-', trim($row[6]));
                $due_date = str_replace('/', '-', trim($row[7]));
                $etd = str_replace('/', '-', trim($row[14]));
                $job = array(
                    'job_id'                => $job_id,
                    'previous_job_id'       => trim($row[3]),
                    'description'           => trim($row[5]),
                    'date_entered_value'    => strtotime($created_date),
                    'date_due_value'        => strtotime($due_date),
                    'designer'              => trim($row[9]),
                    'notes'                 => trim($row[13]),
                    'date_ed_value'         => strtotime($etd),
                    'status_id'             => $status_id,
                    'salesrep_id'           => $rep_id,
                    'date'                  => time()
                );
                $customer_id = $this->productioncustomer->geCustomerIdByName(trim($row[4]));
                if(empty($customer_id))
                {
                    $customer_data = array(
                        'name'  => trim($row[2])
                    );
                    $customer_id = $this->productioncustomer->addCustomer($customer_data);
                }
                $job['customer_id'] = $customer_id;
                if(!empty(trim($row[10])))
                {
                    $finisher_id = $this->productionfinisher->getFinisherIdByName(trim($row[10]));
                    if(empty($finisher_id))
                    {
                        $finisher_data = array(
                            'name'  => trim($row[10])
                        );
                        $finisher_id = $this->productionsupplier->addSupplier($finisher_data);
                    }
                    $job['finisher_id'] = $finisher_id;
                }
                if(!empty(trim($row[11])))
                {
                    $finisher2_id = $this->productionfinisher->getFinisherIdByName(trim($row[11]));
                    if(empty($finisher2_id))
                    {
                        $finisher2_data = array(
                            'name'  => trim($row[11])
                        );
                        $finisher2_id = $this->productionsupplier->addSupplier($finisher2_data);
                    }
                    $job['finisher2_id'] = $finisher2_id;
                }
                if(!empty(trim($row[12])))
                {
                    $finisher3_id = $this->productionfinisher->getFinisherIdByName(trim($row[12]));
                    if(empty($finisher3_id))
                    {
                        $finisher3_data = array(
                            'name'  => trim($row[12])
                        );
                        $finisher3_id = $this->productionsupplier->addSupplier($finisher3_data);
                    }
                    $job['finisher3_id'] = $finisher3_id;
                }
                $jobs[] = $job;
                ++$line;
            }
            //echo "<pre>",print_r($jobs),"</pre>";die();
            foreach($jobs as $j)
            {
                //if($updater)
                $id= $this->productionjob->addJob($j);
                echo "<p>Job with id $id has been added</p>";
            }
        }
    }

    public function procAddProductionJob()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        //echo "FINISHERS<pre>",print_r($finishers),"</pre>"; die();
        //Required Fields
        if(!$this->dataSubbed($job_id))
        {
            Form::setError('job_id', 'The job id is required');
        }
        elseif($this->productionjob->jobNumberExists($job_id))
        {
            Form::setError('sku', 'This job id is already in use');
        }
        if($status_id == 0)
        {
            Form::setError('status_id', 'Please choose a status');
        }
        if($salesrep_id == 0)
        {
            Form::setError('salesrep_id', 'Please choose an FSG contact');
        }
        if(!$this->dataSubbed($date_entered_value))
        {
            Form::setError('date_entered', 'Please supply the date the job was entered');
        }
        if( !$this->dataSubbed($date_due) && isset($strict_dd) )
        {
            Form::setError('date_due', 'Please indicate when this should be dispatched');
        }
        if(!$this->dataSubbed($description))
        {
            Form::setError('description', 'A job description is required');
        }
        if(!$this->dataSubbed($customer_name))
        {
            Form::setError('customer_name', 'A Customer Name is required');
        }
        //Might be required, or need to fulfill requirements
        if($this->dataSubbed($customer_email))
        {
            if(!$this->emailValid($customer_email))
            {
                Form::setError('customer_email', 'The email is not valid');
            }
        }
        if($this->dataSubbed($customer_contact_email))
        {
            if(!$this->emailValid($customer_contact_email))
            {
                Form::setError('customer_contact_email', 'The email is not valid');
            }
        }
        //customer address checking
        if(!isset($country)) $country = "AU";
        if(!empty($customer_address) || !empty($customer_suburb) || !empty($customer_state) || !empty($customer_postcode) || !empty($customer_country) )
        {
            $this->validateAddress($customer_address, $customer_suburb, $customer_state, $customer_postcode, $customer_country, isset($ignore_customer_address_error), "customer_", "show_customer_address");
        }
        if( !isset($held_in_store) && !isset($hold_in_store) )
            $this->validateAddress($address, $suburb, $state, $postcode, $country, isset($ignore_address_error));
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //echo "ALL GOOD<pre>",print_r($post_data),"</pre>"; die();
            //customer details
            $customer_data = array(
                'name'  => $customer_name
            );
            if($this->dataSubbed($customer_phone)) $customer_data['phone'] = $customer_phone;
            if($this->dataSubbed($customer_website)) $customer_data['website'] = $customer_website;
            if($this->dataSubbed($customer_email)) $customer_data['email'] = $customer_email;
            if($this->dataSubbed($customer_address)) $customer_data['address'] = $customer_address;
            if($this->dataSubbed($customer_address2)) $customer_data['address2'] = $customer_address2;
            if($this->dataSubbed($customer_suburb)) $customer_data['suburb'] = $customer_suburb;
            if($this->dataSubbed($customer_state)) $customer_data['state'] = $customer_state;
            if($this->dataSubbed($customer_postcode)) $customer_data['postcode'] = $customer_postcode;
            if($this->dataSubbed($customer_country)) $customer_data['country'] = $customer_country;
            if($this->dataSubbed($customer_contact_id) && $customer_contact_id > 0) $post_data['customer_contact_id'] = $customer_contact_id;
            //Need to add the customer?
            if($customer_id == 0)
            {
                if($this->dataSubbed($customer_contact_name)) $customer_data['contacts'][0]['name'] = $customer_contact_name;
                if($this->dataSubbed($customer_contact_role)) $customer_data['contacts'][0]['role'] = $customer_contact_role;
                if($this->dataSubbed($customer_contact_email)) $customer_data['contacts'][0]['email'] = $customer_contact_email;
                if($this->dataSubbed($customer_contact_phone)) $customer_data['contacts'][0]['phone'] = $customer_contact_phone;
                $customer_id = $this->productioncustomer->addCustomer($customer_data);
                //echo "Will add customer data<pre>",print_r($customer_data),"</pre>";
                $customer_data['customer_id'] = $customer_id;
                $post_data['customer_id'] = $customer_id;
                //this new customer will only have one contact
                $pcont = new Productioncontact();
                $post_data['customer_contact_id'] = $pcont->getCustomerContactIDs($customer_id, true);
            }
            else
            {
                $customer_data['customer_id'] = $customer_id;
                //$this->productioncustomer->editCustomer($customer_data);
            }
            //echo "ALL GOOD<pre>",print_r($post_data),"</pre>"; die();
            $id = $this->productionjob->addJob($post_data);
            Session::set('feedback', "That job has been added to the system.<br/>The details can be edited <a href='/jobs/update-job/job=".$id."'>HERE</a>");
        }
        return $this->redirector->to(PUBLIC_ROOT."jobs/add-job");
    }

    public function procEditProductionCustomer()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                }
            }
        }
        if(!$this->dataSubbed($name))
        {
            Form::setError('name', 'The customers name is required');
        }
        if($this->dataSubbed($email))
        {
            if(!$this->emailValid($email))
            {
                Form::setError('email', 'The email is not valid');
            }
        }
        foreach($post_data['contacts'] as $ind => $cd)
        {
            if(!$this->dataSubbed($cd['name']))
            {
                Form::setError('contactname_'.$ind, 'A contact name is required');
            }
            if($this->dataSubbed($cd['email']))
            {
                if(!$this->emailValid($cd['email']))
                {
                    Form::setError('contactemail_'.$ind, 'The email is not valid');
                }
            }
        }
        if(!empty($address) || !empty($suburb) || !empty($state) || !empty($postcode) || !empty($country))
        {
            $this->validateAddress($address, $suburb, $state, $postcode, $country, isset($ignore_address_error));
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //echo "<pre>",print_r($post_data),"</pre>"; die();
            $this->productioncustomer->editCustomer($post_data);
            Session::set('feedback', "That customers's details have been updated");
        }
        return $this->redirector->to(PUBLIC_ROOT."customers/edit-customer/customer=$customer_id");
    }

    public function procAddProductionCustomer()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    ${$field[$key]} = $avalue;
                    $post_data[$field][$key] = $avalue;
                }
            }
        }
        if(!$this->dataSubbed($name))
        {
            Form::setError('name', 'The customer name is required');
        }
        if($this->dataSubbed($email))
        {
            if(!$this->emailValid($email))
            {
                Form::setError('email', 'The email is not valid');
            }
        }
        foreach($post_data['contacts'] as $ind => $cd)
        {
            if(!$this->dataSubbed($cd['name']))
            {
                Form::setError('contactname_'.$ind, 'A contact name is required');
            }
            if($this->dataSubbed($cd['email']))
            {
                if(!$this->emailValid($cd['email']))
                {
                    Form::setError('contactemail_'.$ind, 'The email is not valid');
                }
            }
        }
        if(!empty($address) || !empty($suburb) || !empty($state) || !empty($postcode) || !empty($country))
        {
            $this->validateAddress($address, $suburb, $state, $postcode, $country, isset($ignore_address_error));
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $id = $this->productioncustomer->addCustomer($post_data);
            Session::set('feedback', "That customer has been added to the system.<br/>The details can be edited <a href='/customers/edit-customer/customer=".$id."'>HERE</a>");
        }
        return $this->redirector->to(PUBLIC_ROOT."customers/add-customer");
    }

    public function procEditProductionFinisher()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                }
            }
        }
        //echo "<pre>",print_r($post_data),"</pre>"; die();
        if(!$this->dataSubbed($name))
        {
            Form::setError('name', 'The Finisher\'s name is required');
        }
        foreach($post_data['contacts'] as $ind => $cd)
        {
            if(!$this->dataSubbed($cd['name']))
            {
                Form::setError('contactname_'.$ind, 'A contact name is required');
            }
            if($this->dataSubbed($cd['email']))
            {
                if(!$this->emailValid($cd['email']))
                {
                    Form::setError('contactemail_'.$ind, 'The email is not valid');
                }
            }
        }
        if(!empty($address) || !empty($suburb) || !empty($state) || !empty($postcode) || !empty($country))
        {
            $this->validateAddress($address, $suburb, $state, $postcode, $country, isset($ignore_address_error));
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //echo "<pre>",print_r($post_data),"</pre>"; die();
            $this->productionfinisher->editFinisher($post_data);
            Session::set('feedback', "That Finisher's details have been updated");
        }
        return $this->redirector->to(PUBLIC_ROOT."finishers/edit-finisher/finisher=$finisher_id");
    }

    public function procAddProductionFinisher()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    ${$field[$key]} = $avalue;
                    $post_data[$field][$key] = $avalue;
                }
            }
        }
        //echo "<pre>",print_r($post_data),"</pre>"; die();
        if(!$this->dataSubbed($name))
        {
            Form::setError('name', 'The Finishers name is required');
        }
        foreach($post_data['contacts'] as $ind => $cd)
        {
            if(!$this->dataSubbed($cd['name']))
            {
                Form::setError('contactname_'.$ind, 'A contact name is required');
            }
            if($this->dataSubbed($cd['email']))
            {
                if(!$this->emailValid($cd['email']))
                {
                    Form::setError('contactemail_'.$ind, 'The email is not valid');
                }
            }
        }
        if($this->dataSubbed($email))
        {
            if(!$this->emailValid($email))
            {
                Form::setError('email', 'The email is not valid');
            }
        }
        if(!empty($address) || !empty($suburb) || !empty($state) || !empty($postcode) || !empty($country))
        {
            $this->validateAddress($address, $suburb, $state, $postcode, $country, isset($ignore_address_error));
        }

        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            //echo "ERRORS<pre>",print_r(Form::getErrorArray()),"</pre>"; die();
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //echo "ALL GOOD<pre>",print_r($post_data),"</pre>"; die();
            $id = $this->productionfinisher->addFinisher($post_data);
            Session::set('feedback', "That Finisher has been added to the system.<br/>The details can be edited <a href='/finishers/edit-finisher/finisher=".$id."'>HERE</a>");
        }
        return $this->redirector->to(PUBLIC_ROOT."finishers/add-finisher");
    }

    public function procAddProductionNote()
    {
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    ${$field[$key]} = $avalue;
                    $post_data[$field][$key] = $avalue;
                }
            }
        }
        if($this->productionjob->updateJobFieldValue($job_id, 'notes', $notes))
        {
            Session::set('notefeedback_'.$job_id, "That note has been updated.");
            $email_note = nl2br($notes);
            Email::notifyProdAdminOfNoteChange($job_no, $email_note, Session::getUsersName());
        }
        else
        {
            Session::set('noteerrorfeedback_'.$job_id, "There has been a database error.<br>That note has <strong>NOT</strong> been updated.");
        }
        return $this->redirector->to(PUBLIC_ROOT."jobs/view-jobs#tr_".$job_id);
    }

    public function procAddProductionDeliveryNote()
    {
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    ${$field[$key]} = $avalue;
                    $post_data[$field][$key] = $avalue;
                }
            }
        }
        if($this->productionjob->updateJobFieldValue($job_id, 'delivery_notes', $delivery_notes))
        {
            Session::set('deliveryfeedback_'.$job_id, "That note has been updated.");
            $email_note = nl2br($delivery_notes);
            Email::notifyProdOfDeliveryNoteChange($job_no, $email_note, Session::getUsersName());
        }
        else
        {
            Session::set('deliveryerrorfeedback_'.$job_id, "There has been a database error.<br>That note has <strong>NOT</strong> been updated.");
        }
        return $this->redirector->to(PUBLIC_ROOT."jobs/view-jobs#tr_".$job_id);
    }

    public function procAddPackages()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if(!$this->dataSubbed($width) || !$this->dataSubbed($height) || !$this->dataSubbed($depth) || !$this->dataSubbed($weight) || !$this->dataSubbed($count))
        {
            Session::set('errorfeedback', 'All fields must have a value<br/>Package has NOT been added');
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        elseif( (filter_var($width, FILTER_VALIDATE_FLOAT) === false || $width <= 0) || (filter_var($height, FILTER_VALIDATE_FLOAT) === false || $height <= 0) || (filter_var($depth, FILTER_VALIDATE_FLOAT) === false || $depth <= 0) || (filter_var($weight, FILTER_VALIDATE_FLOAT) === false || $weight <= 0) || (filter_var($count, FILTER_VALIDATE_INT) === false || $count <= 0) )
        {
            Session::set('errorfeedback', 'All values must have a positive number<br/>Package has NOT been added');
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $package = (isset($pallet))? "pallet" : "package";
            foreach($this->request->data['order_ids'] as $oid)
            {
                $post_data['order_id'] = $oid;
                $this->order->addPackage($post_data);
            }
            if($count > 1)
            {
                Session::set('feedback', "Those ".$package."s have been successfully added.");

            }
            else
            {
                Session::set('feedback', "That $package has been successfully added.");
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."orders/view-orders/client=".$client_id);
    }

    public function procJobStatusEdit()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        $id = $this->request->data['line_id'];
        $post_data = array('id' => $id);
        foreach($this->request->data as $field => $value)
        {
            $field = strtok($field, "_");
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }

        if( !$this->dataSubbed($name) )
        {
            Form::setError('name_'.$id, 'A status name is required');
        }
        elseif($this->jobstatus->getStatusId($name) && strtolower($name) != $currentname )
        {
            Form::setError('name_'.$id, 'This status is already in use. Status names need to be unique');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
            // set the default?
            if(isset($default))
            {
                $this->jobstatus->makeDefault($id);
            }
            if($this->jobstatus->editStatus($post_data))
            {
                Session::set('feedback', "Those details have been updated");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."production-settings/edit-job-status");
    }

    public function procJobStatusAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        $response = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A status name is required');
        }
        elseif($this->jobstatus->getStatusId($name) )
        {
            Form::setError('name', 'This status is already in use. Status names need to be unique');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //add the status
            if($this->jobstatus->addStatus($post_data))
            {
                Session::set('feedback', "That status has been added");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."production-settings/edit-job-status");
    }

    public function procDFCollection()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        $response = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !($this->dataSubbed($carton_count) || $this->dataSubbed($pallet_count)))
        {
            Form::setError('counter', "At least one of these cartons or pallets is required");
        }
        else
        {
            if($this->dataSubbed($carton_count))
            {
                if( filter_var( $carton_count, FILTER_VALIDATE_INT ) === false || $carton_count <= 0)
                {
                    Form::setError('carton_count', "Only positive whole numbers can be used for quantities");
                }
                if(!$this->dataSubbed($carton_width))
                {
                    Form::setError('carton_width', "A carton width is required if cartons are submitted");
                }
                elseif( filter_var( $carton_width, FILTER_VALIDATE_INT ) === false || $carton_width <= 0 )
                {
                    Form::setError('carton_width', "Only positive whole numbers can be used for sizes");
                }
                if(!$this->dataSubbed($carton_length))
                {
                    Form::setError('carton_length', "A carton length is required if cartons are submitted");
                }
                elseif( filter_var( $carton_length, FILTER_VALIDATE_INT ) === false || $carton_length <= 0 )
                {
                    Form::setError('carton_length', "Only positive whole numbers can be used for sizes");
                }
                if(!$this->dataSubbed($carton_height))
                {
                    Form::setError('carton_height', "A carton height is required if cartons are submitted");
                }
                elseif( filter_var( $carton_height, FILTER_VALIDATE_INT ) === false || $carton_height <= 0 )
                {
                    Form::setError('carton_height', "Only positive whole numbers can be used for sizes");
                }
            }
            else
            {
                $carton_count = 0;
                $carton_width = 0;
                $carton_length = 0;
                $carton_height = 0;
            }
            if($this->dataSubbed($pallet_count))
            {
                if( filter_var( $pallet_count, FILTER_VALIDATE_INT ) === false || $pallet_count <= 0)
                {
                    Form::setError('pallet_count', "Only positive whole numbers can be used for quantities");
                }
                if(!$this->dataSubbed($pallet_width))
                {
                    Form::setError('pallet_width', "A pallet width is required if pallets are submitted");
                }
                elseif( filter_var( $pallet_width, FILTER_VALIDATE_INT ) === false || $pallet_width <= 0 )
                {
                    Form::setError('pallet_width', "Only positive whole numbers can be used for sizes");
                }
                if(!$this->dataSubbed($pallet_length))
                {
                    Form::setError('pallet_length', "A pallet length is required if pallets are submitted");
                }
                elseif( filter_var( $pallet_length, FILTER_VALIDATE_INT ) === false || $pallet_length <= 0 )
                {
                    Form::setError('pallet_length', "Only positive whole numbers can be used for sizes");
                }
                if(!$this->dataSubbed($pallet_height))
                {
                    Form::setError('pallet_height', "A pallet height is required if pallets are submitted");
                }
                elseif( filter_var( $pallet_height, FILTER_VALIDATE_INT ) === false || $pallet_height <= 0 )
                {
                    Form::setError('pallet_height', "Only positive whole numbers can be used for sizes");
                }
            }
            else
            {
                $pallet_count = 0;
                $pallet_width = 0;
                $pallet_length = 0;
                $pallet_height = 0;
            }
        }
        if(!$this->dataSubbed($weight))
        {
            Form::setError('weight', "A weight is required");
        }
        elseif( filter_var( $weight, FILTER_VALIDATE_INT ) === false || $weight <= 0 )
        {
            Form::setError('weight', "Only positive whole numbers can be used for sizes");
        }

        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $cubic = round(($carton_width * $carton_length * $carton_height)/1000000 + ($pallet_width * $pallet_length * $pallet_height * $pallet_count)/1000000, 3);
            //create the API request
            $request = array(
                'AuthorisedContactName'     => 'Mike Bond',
                'AuthorisedContactPhone'    => '0386777418',
                'CloseTime'                 => '3:00pm',
                'EstimatedTotalKgs'         => $weight,
                'EstimatedTotalCubic'       => $cubic,
                'EstimatedTotalCartons'     => $carton_count,
                'LargestCartonsLength'      => $carton_length,
                'LargestCartonsWidth'       => $carton_width,
                'LargestCartonsHeight'      => $carton_height,
                'EstimatedTotalPallets'     => $pallet_count,
                'LargestPalletsLength'      => $pallet_length,
                'LargestPalletsWidth'       => $pallet_width,
                'LargestPalletsHeight'      => $pallet_height
            );
            //send the booking
            $response = $this->directfreight->bookCollection($request);
            Session::set('dfresponse', $response);
        }
        return $this->redirector->to(PUBLIC_ROOT."orders/book-direct-freight-collection");
    }

    public function procReeceUserCheck()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if($_FILES['reece_user_csv_file']["size"] > 0)
        {
            if ($_FILES['reece_user_csv_file']['error']  === UPLOAD_ERR_OK)
            {
                $tmp_name = $_FILES['reece_user_csv_file']['tmp_name'];
                $csv_array = array_map('str_getcsv', file($tmp_name));
                //echo "<pre>",print_r($csv_array),"</pre>"; die();
            }
            else
            {
            	$error_message = $this->file_upload_error_message($_FILES[$field]['error']);
                Form::setError('reece_user_csv_file', $error_message);
            }
        }
        else
        {
            Form::setError('reece_user_csv_file', 'please select a file to upload');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            /*
            [0] Employee ID
            [1] Employee Name
            [2] Job Title
            [3] Email
            [4] Department Name - includes Reece Department ID
            [5] Street Address
            [6] Suburb/City
            [7] State - includes NZ (did'nt know it was an Australian State!)
            [8] Postcode
            [9] Mobile number - needs to be formatted
            [10] telephone  - needs to be formatted
            [11] fax - needs to be formatted
            */
            $skip_first = isset($reece_user_header_row);
            $line = 1;
            $users = array();
            $feedback_string = "<ul>";
            //Set up csv file
            $cols = array(
                "Current Full Nmae",
                "Current First Name",
                "Current Last Name",
                "Current Department Name",
                "Current Reece Department Id",
                "Current Email",
                "Current Job Title",
                "Current Mobile Number",
                "Current Phone Number",
                "Current Fax Number",
                "",
                "New Full Nmae",
                "New First Name",
                "New Last Name",
                "New Department Name",
                "New Reece Department Id",
                "New Email",
                "New Job Title",
                "New Mobile Number",
                "New Phone Number",
                "New Fax Number",
            );

            $rows = array();
            foreach($csv_array as $row)
            {
                $reece_department_id = 0;
                if($skip_first)
                {
                    $skip_first = false;
                    ++$line;
                    continue;
                }
                $phone = $fax = $mobile = $address = "";
                //get Reece Department ID
                $array = explode(" ",$row[4], 2);
                $reece_department_id =(int)$array[0];
                $reece_department_name = $array[1];
                $words = explode( " ", trim($row[1]) );
                array_splice( $words, -1 );
                $name = implode(" ",$words);
                $email = strtolower(trim($row[3]));
                $job_title = strtolower(trim($row[2]));
                list($firstname, $lastname) = explode(" ", $name, 2);
                $phone = (empty($row[10]))? "removed": Utility::formatPhoneString($row[10], $row[7] == "NZ");
                $fax = (empty($row[11]))? "removed": Utility::formatPhoneString($row[11], $row[7] == "NZ");
                $mobile = (empty($row[9]))? "removed": Utility::formatMobileString($row[9], $row[7] == "NZ");
                //get any stored user data
                $stored_data = $this->reeceuser->getUserByEmail(trim($row[3])) ;
                if($stored_data)
                {
                    //User is already stored - check for data update
                    $department_details = $this->reecedepartment->getDepartmentById($stored_data['department_id']);
                    $fb_row = array(
                        $stored_data['full_name'],
                        $stored_data['first_name'],
                        $stored_data['last_name'],
                        $department_details['name'],
                        $department_details['reece_id'],
                        strtolower($stored_data['email']),
                        $stored_data['job_title'],
                        $stored_data['mobile_number'],
                        $stored_data['phone'],
                        $stored_data['fax'],
                        ""
                    );
                    //Name Details
                    $fb_row[] = ($stored_data['full_name'] != $name)? $name : "";
                    $fb_row[] = ($stored_data['first_name'] != $firstname)? $firstname : "";
                    $fb_row[] = ($stored_data['last_name'] != $lastname)? $lastname : "";
                    //Department Details
                    $fb_row[] = ($department_details['name'] != $reece_department_name)? $reece_department_name : "";
                    $fb_row[] = ($department_details['reece_id'] != $reece_department_id)? $reece_department_id : "";
                    //Email
                    $fb_row[] = (strtolower($stored_data['email']) != $email)? $email : "";
                    //Job Title
                    $fb_row[] = (strtolower($stored_data['job_title']) != $job_title)? trim($row[2]) : "";
                    //Phone. Mobile and Fax
                    $fb_row[] = ($stored_data['mobile_number'] != $mobile)? $mobile : "";
                    $fb_row[] = ($stored_data['phone'] != $phone)? $phone : "";
                    $fb_row[] = ($stored_data['fax'] != $fax) ? $fax : "";
                }
                else
                {
                    //Need to add new user
                    //echo "<p>Need to add {$row[1]} to the system at line $line</p>";
                    $fb_row = array(
                        "This",
                        "is",
                        "new",
                        "-",
                        "It",
                        "Will",
                        "need",
                        "to",
                        "be",
                        "added",
                        "",
                        $name,
                        $firstname,
                        $lastname,
                        $reece_department_name,
                        $reece_department_id,
                        $email,
                        ucwords($job_title),
                        $mobile,
                        $phone,
                        $fax
                    );
                }
                ++$line;
                $rows[] = $fb_row;
            }
            //echo "Rows<pre>",print_r($rows),"</pre>";
            $expire=time()+60;
            setcookie("fileDownload", "true", $expire, "/");
            $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "reece_users_feedback_csv".date("Ymd")]);
        }
        //return $this->redirector->to(PUBLIC_ROOT."admin-only/reece-data-tidy");
    }

    public function procReeceUserUpload()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $db = Database::openConnection();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if($_FILES['csv_user_file']["size"] > 0)
        {
            if ($_FILES['csv_user_file']['error']  === UPLOAD_ERR_OK)
            {
                $tmp_name = $_FILES['csv_user_file']['tmp_name'];
                $csv_array = array_map('str_getcsv', file($tmp_name));
                //echo "<pre>",print_r($csv_array),"</pre>"; die();
            }
            else
            {
            	$error_message = $this->file_upload_error_message($_FILES[$field]['error']);
                Form::setError('csv_user_file', $error_message);
            }
        }
        else
        {
            Form::setError('csv_user_file', 'please select a file to upload');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            /*
            [0] Contact Name
            [1] First Name
            [2] Last Name
            [3] Job Title
            [4] Contact Email
            [5] Mobile
            [6] Phone
            [7] Fax
            [8] Department name - includes Reece Department ID
            [9] Full address
            [10] Full Address Repeat
            */
            //die('all good');
            $imported_dept_count = 0;
            $skip_first = isset($user_header_row);
            $line = 1;
            $data_error_string = "<ul>";
            $import_users = true;
            $users = array();
            foreach($csv_array as $row)
            {
                $reece_department_id = 0;
                $user_array = array();
                $data_errors = false;
                if($skip_first)
                {
                    $skip_first = false;
                    ++$line;
                    continue;
                }
                if(!$this->dataSubbed($row[8]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>A Department Name is required on line: $line</li>";
                }
                else
                {
                    //Get the Department Name and ID
                    $array = explode(" ",$row[8], 2);
                    if(count($array) < 2)
                    {
                        $data_errors = true;
                        $data_error_string .= "<li>A Reece Department ID could not be determined from the name, {$row[8]}: $line</li>";
                        echo "<ul>$data_error_string</ul><pre>",print_r($array),"</pre>";echo count($array);die();
                    }
                    else
                    {
                        $reece_department_id =(int)$array[0];
                        $reece_department_name = $array[1];
                        if($reece_department_id === 0)
                        {
                            $data_errors = true;
                            $data_error_string .= "<li>A Reece Department ID could not be determined from the name: $line</li>";
                        }
                        else
                        {
                            if(!$stored_department_data = $this->reecedepartment->getDepartmentByReeceId($reece_department_id))
                            {
                                $data_errors = true;
                                $data_error_string .= "<li>An FSG Department Id could not be found based on $reece_department_name on line: $line</li>";
                            }
                            else
                            {
                                $user_array['department_id'] = $stored_department_data['id'];
                            }
                        }
                    }
                }
                if(!$this->dataSubbed($row[0]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>A Users Full name is required on line: $line</li>";
                }
                else
                {
                    //clean and trim the department
                    $user_array['full_name'] = Utility::deepTrim($row[0]);
                }
                if(!$this->dataSubbed($row[1]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>A Users First Name is required on line: $line</li>";
                }
                else
                {
                    //clean and trim the department
                    $user_array['first_name'] = Utility::deepTrim($row[1]);
                }
                if(!$this->dataSubbed($row[2]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>A Users Last name is required on line: $line</li>";
                }
                else
                {
                    //clean and trim the department
                    $user_array['last_name'] = Utility::deepTrim($row[2]);
                }
                /*  IS JOB TITLE REQUIRED???????????????????????????????????????????????????????????????????????????????????????????????????????????????
                if(!$this->dataSubbed($row[3]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>A Users Job title is required on line: $line</li>";
                }
                else
                {
                    //clean and trim the department
                    $user_array['job_title'] = Utility::deepTrim($row[3]);
                }
                *******************************************************************************************************************************************/
                $user_array['job_title'] = Utility::deepTrim($row[3]);
                if(!$this->dataSubbed($row[4]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>A Users email is required on line: $line</li>";
                }
                elseif(!$this->emailValid($row[4]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>An invalid email ws found on line: $line</li>";
                }
                else
                {
                    //clean and trim the department
                    $user_array['email'] = Utility::deepTrim($row[4]);
                }
                //Tidy up the phone formatting - no tidying required for importing
                $address_array = explode(' ', $row[9]);
                $country = strtolower(array_pop($address_array));
                //echo "<p>$country</p>";
                $user_array['mobile_number'] = $row[5];
                $user_array['phone'] = $row[6];
                $user_array['fax'] = $row[7];
                //$user_array['mobile_number']    = Utility::formatMobileString(ltrim(str_replace(' ', '', $row[5]), "+"), $country == "zealand");
                //$user_array['phone']            = Utility::formatPhoneString(ltrim(str_replace(' ', '', $row[6]), "+"), $country == "zealand");
                //$user_array['fax']              = Utility::formatPhoneString(ltrim(str_replace(' ', '', $row[7]), "+"), $country == "zealand");

                //$user_array['mobile_number']    = ($user_array['mobile_number'])? $user_array['mobile_number']: "";
                //$user_array['phone']            = ($user_array['phone'])? $user_array['phone']: "";
                //$user_array['fax']              = ($user_array['fax'])? $user_array['fax']: "";
                if($data_errors)
                {
                    $import_users = false;
                }
                ++$line;
                $users[] = $user_array;
            }
            if($import_users)
            {
                $this->reeceuser->addUpdateUsers($users);
                Session::set('feedback', "<h2><i class='far fa-check-circle'></i>User Import is Complete</h2><p>All Values have been inserted");
                //echo "<pre>",print_r($users),"</pre";die();
            }
            else
            {
                Session::set('errorfeedback',"<h2><i class='far fa-times-circle'></i>These Users Could Not Be Imported</h2><p>Reasons are listed below</p>$data_error_string");
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."admin-only/reece-data-tidy");
    }

    public function procReeceDepartmentCheck()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if($_FILES['reece_csv_file']["size"] > 0)
        {
            if ($_FILES['reece_csv_file']['error']  === UPLOAD_ERR_OK)
            {
                $tmp_name = $_FILES['reece_csv_file']['tmp_name'];
                $csv_array = array_map('str_getcsv', file($tmp_name));
                //echo "<pre>",print_r($csv_array),"</pre>"; die();
            }
            else
            {
            	$error_message = $this->file_upload_error_message($_FILES[$field]['error']);
                Form::setError('reece_csv_file', $error_message);
            }
        }
        else
        {
            Form::setError('reece_csv_file', 'please select a file to upload');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            /*
            [0] Employee ID
            [1] Employee Name
            [2] Job Title
            [3] Email
            [4] Department Name - includes Reece Department ID
            [5] Street Address
            [6] Suburb/City
            [7] State - includes NZ (did'nt know it was an Australian State!)
            [8] Postcode
            [9] Mobile number - needs to be formatted
            [10] telephone  - needs to be formatted
            [11] fax - needs to be formatted
            */
            $skip_first = isset($reece_header_row);
            $line = 1;
            $departments = array();
            $feedback_string = "<ul>";
            //Set up csv file
            $cols = array(
                "Current Reece Id",
                "Current Department Name",
                "Current Department Address",
                "Current Phone",
                "Current Fax",
                "",
                "New Reece Id",
                "New Department Name",
                "New Department Address",
                "New Phone",
                "New Fax",
            );

            $rows = array();
            foreach($csv_array as $row)
            {
                $reece_department_id = 0;
                if($skip_first)
                {
                    $skip_first = false;
                    ++$line;
                    continue;
                }
                $phone = $fax = $address = "";
                //get Department ID
                $array = explode(" ",$row[4], 2);
                $reece_department_id =(int)$array[0];
                $reece_department_name = $array[1];
                $stored_data = $this->reecedepartment->getDepartmentByReeceId($reece_department_id);
                if($stored_data)
                {
                    //Department is already stored - check for data update
                    $fb_row = array(
                        $reece_department_id,
                        $stored_data['name'],
                        $stored_data['stored_address'],
                        $stored_data['phone'],
                        $stored_data['fax'],
                        "",
                        ""
                    );
                    //Department Name
                    $fb_row[] = ($stored_data['name'] != $reece_department_name)? $reece_department_name : "";
                    //Phone and Fax
                    $phone = Utility::formatPhoneString($row[10], $row[7] == "NZ");
                    $fax = Utility::formatPhoneString($row[11], $row[7] == "NZ");
                    //Address
                    if($row[7] == "NZ")
                    {
                        $address = Utility::streetAbbreviations($row[5])." ".$row[6]." ".str_pad($row[8], 4, '0', STR_PAD_LEFT)." New Zealand";
                    }
                    else
                    {
                        $address = Utility::streetAbbreviations($row[5])." ".$row[6]." ".$row[7]." ".str_pad($row[8], 4, '0', STR_PAD_LEFT)." Australia";
                    }
                    $fb_row[] = (trim(strtolower($stored_data['stored_address'])) != trim(strtolower($address)))? $address : "";
                    $fb_row[] = ($stored_data['phone'] != $phone)? $phone : "";
                    $fb_row[] = ($stored_data['fax'] != $fax) ? $fax : "";
                }
                else
                {
                    //Need to add new department
                    $phone = Utility::formatPhoneString($row[10], $row[7] == "NZ");
                    $fax = Utility::formatPhoneString($row[11], $row[7] == "NZ");
                    if($row[7] == "NZ")
                    {
                        $address = Utility::streetAbbreviations($row[5])." ".$row[6]." ".str_pad($row[8], 4, '0', STR_PAD_LEFT)." New Zealand";
                    }
                    else
                    {
                        $address = Utility::streetAbbreviations($row[5])." ".$row[6]." ".$row[7]." ".str_pad($row[8], 4, '0', STR_PAD_LEFT)." Australia";
                    }
                    $fb_row = array(
                        "This is",
                        "new -",
                        "Will need",
                        "to be",
                        "added",
                        "",
                        $reece_department_id,
                        $reece_department_name,
                        $address,
                        $phone,
                        $fax
                    );
                }
                ++$line;
                $rows[] = $fb_row;
            }
            $expire=time()+60;
            setcookie("fileDownload", "true", $expire, "/");
            $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "reece_departments_feedback_csv".date("Ymd")]);
        }
        //return $this->redirector->to(PUBLIC_ROOT."admin-only/reece-data-tidy");
    }

    public function procReeceDepartmentUpload()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if($_FILES['csv_file']["size"] > 0)
        {
            if ($_FILES['csv_file']['error']  === UPLOAD_ERR_OK)
            {
                $tmp_name = $_FILES['csv_file']['tmp_name'];
                $csv_array = array_map('str_getcsv', file($tmp_name));
                //echo "<pre>",print_r($csv_array),"</pre>"; //die();
            }
            else
            {
            	$error_message = $this->file_upload_error_message($_FILES[$field]['error']);
                Form::setError('csv_file', $error_message);
            }
        }
        else
        {
            Form::setError('csv_file', 'please select a file to upload');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            /*
            [0] Contact Name
            [1] First Name
            [2] Last Name
            [3] Job Title
            [4] Contact Email
            [5] Mobile
            [6] Phone
            [7] Fax
            [8] Department name - includes Reece Department ID
            [9] Full address
            [10] Full Address Repeat
            */
            //die('all good');
            $imported_dept_count = 0;
            $skip_first = isset($header_row);
            $line = 1;
            $data_error_string = "<ul>";
            $import_departments = true;
            $departments = array();
            foreach($csv_array as $row)
            {
                $reece_department_id = 0;
                $department_array = array();
                $data_errors = false;
                if($skip_first)
                {
                    $skip_first = false;
                    ++$line;
                    continue;
                }
                if(!$this->dataSubbed($row[8]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>A Department Name is required on line: $line</li>";
                }
                else
                {
                    //Get the Department Name and ID
                    try{
                        $array = explode(" ",$row[8], 2);
                        $reece_department_id =(int)$array[0];
                        $reece_department_name = $array[1];
                        if($reece_department_id === 0)
                        {
                            $data_errors = true;
                            $data_error_string .= "<li>A Department ID could not be determined from the name: $line</li>";
                        }
                        else
                        {
                            $department_array['reece_id']   = $reece_department_id;
                            $department_array['name']       = $reece_department_name;
                        }
                    }
                    catch(exception $e)
                    {
                        $data_errors = true;
                        $data_error_string .= "<li>Could not explode: $line</li>";
                    }

                }
                if(!$this->dataSubbed($row[9]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>A Department Address is required on line: $line</li>";
                }
                else
                {
                    //clean and trim the department
                    $department_array['stored_address'] = Utility::deepTrim($row[9]);
                }
                $department_array['phone'] = $row[6];
                $department_array['fax'] = $row[7];
                if($data_errors)
                {
                    $import_departments = false;
                }
                ++$line;
                $departments[] = $department_array;
            }
            if($import_departments)
            {
                $this->reecedepartment->addUpdateDepartments($departments);
                Session::set('feedback', "<h2><i class='far fa-check-circle'></i>Department Import is Complete</h2><p>All Values have been inserted");
            }
            else
            {
                Session::set('errorfeedback',"<h2><i class='far fa-times-circle'></i>These Departments Could Not Be Imported</h2><p>Reasons are listed below</p>$data_error_string");
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."admin-only/reece-data-tidy");
    }

    public function procEncryptSomeShit()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>";die();
        $db = Database::openConnection();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if(!$this->dataSubbed($string))
        {
            Form::setError('string', 'A string to encrypt is required');
        }

        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $encrypted_value = Encryption::encryptStringBase64($string);
            $unenc_value = Encryption::decryptStringBase64($encrypted_value);
            Session::set('feedback', "<h2><i class='far fa-check-circle'></i>Encryption is complete</h2><p>The results are</p><ul><li>$string : $encrypted_value</li></ul>");
        }

        return $this->redirector->to(PUBLIC_ROOT."/admin-only/encrypt-some-shit");
    }

     public function procMoveAllClientStock()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>";die();
        $db = Database::openConnection();
        if(!isset($this->request->data['move_to_location']) || $this->request->data['move_to_location'] == 0)
        {
            Form::setError('move_to_location', 'Please select a location to move to');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $client_id = $this->request->data['client_id'];
            $move_to_id = $this->request->data['move_to_location'];
            //find all locations and details for client
            $locations = $this->location->getAllLocationsForClient($client_id);
            //echo "<pre>",print_r($locations),"</pre>";//die();
            foreach($locations as $l)
            {
                //echo "location<pre>",print_r($l),"</pre>";
                $move_from_data = array(
                    'add_product_id'    => $l['item_id'],
                    'add_to_location'   => $move_to_id,
                    'reason_id'         => 0,
                    'reference'         => 'move all stock to receiving'
                );
                if($l['qc_count'] > 0)
                {
                    $move_from_data['qc_stock'] = 1;
                    $move_from_data['qty_add'] = $l['qc_count'];
                    //echo "qc move<pre>",print_r($move_from_data),"</pre>";
                    $this->location->addToLocation($move_from_data);
                }
                if($l['qty'] - $l['qc_count'] > 0)
                {
                    $move_from_data['qty_add'] = $l['qty'] - $l['qc_count'];
                    if(isset($move_from_data['qc_stock'])) unset($move_from_data['qc_stock']);
                    //echo "No qc move<pre>",print_r($move_from_data),"</pre>";
                    $this->location->addToLocation($move_from_data);
                }

                $remove_from_data = array(
                    'subtract_product_id'       => $l['item_id'],
                    'subtract_from_location'    => $l['location_id'],
                    'reason_id'                 => 0,
                    'reference'                 => 'move all stock to receiving'
                );
                if($l['qc_count'] > 0)
                {
                    $remove_from_data['qc_stock'] = 1;
                    $remove_from_data['qty_subtract'] = $l['qc_count'];
                    //echo "qc remove<pre>",print_r($remove_from_data),"</pre>";
                    $this->location->subtractFromLocation($remove_from_data);
                }
                if($l['qty'] - $l['qc_count'] > 0)
                {
                    $remove_from_data['qty_subtract'] = $l['qty'] - $l['qc_count'];
                    if(isset($remove_from_data['qc_stock'])) unset($remove_from_data['qc_stock']);
                    //echo "no qc remove<pre>",print_r($remove_from_data),"</pre>";
                    $this->location->subtractFromLocation($remove_from_data);
                }
            }
            Session::set('feedback', "Those items have all been moved");
        }
        //die();
        return $this->redirector->to(PUBLIC_ROOT."inventory/move-all-client-stock/client=".$client_id);

    }

    public function procAddSerials()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>";die();
        $db = Database::openConnection();
        $post_data = array();
        $entered_serials = explode(",", $this->request->data['entered_serials']);
        Session::set('feedback',"<h2><i class='far fa-check-circle'></i>Serials Have Been Recorded</h2>");
        Session::set('errorfeedback',"<h2><i class='far fa-times-circle'></i>Serials Cannot Be Recorded</h2><p>Reasons are listed below</p>");
        Session::set('showfeedback', true);
        Session::set('showerrorfeedback', false);
        foreach($this->request->data['serial'] as $c =>$array)
        {
            foreach($array as $item_id => $details)
            {
                if(!$this->dataSubbed($details['number']))
                {
                    $_SESSION['showerrorfeedback'] = true;
                    $_SESSION['showfeedback'] = false;
                    $_SESSION['errorfeedback'] .= "<li>A serial Number is required for all items</li>";

                }
                elseif($db->fieldValueTaken('order_item_serials', $details['number'], 'serial_number') && in_array($details['number'], $entered_serials) === false)
                {
                   Form::setError('general', 'Serial Numbers must be unique');
                   $_SESSION['showerrorfeedback'] = true;
                   $_SESSION['showfeedback'] = false;
                   $_SESSION['errorfeedback'] .= "<li>Serial Numbers must be unique</li>";
                }
                $post_data[] = array(
                    'item_id'       => $item_id,
                    'order_id'      => $this->request->data['order_id'],
                    'serial_number' => $details['number'],
                    'serial_id'     => $details['line_id']
                );
            }
        }
        if(Session::getAndDestroy('showfeedback') == false)
        {
            Session::destroy('feedback');
        }
        else
        {
            //echo "<pre>",print_r($post_data),"</pre>";die();
            $this->Orderitemserials->insertData($post_data);
        }
        if(Session::getAndDestroy('showerrorfeedback') == false)
        {
            Session::destroy('errorfeedback');
        }
        return $this->redirector->to(PUBLIC_ROOT."orders/add-serials/order=".$this->request->data['order_id']);
    }

    public function procBulkOrderAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        //echo "Files<pre>",print_r($_FILES),"</pre>";die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $states = array(
            "NSW"   => "new south wales",
            "VIC"   => "victoria",
            "QLD"   => "queensland",
            "TAS"   => "tasmania",
            "SA"    => "south australia",
            "WA"    => "western australia",
            "NT"    => "northern territory",
            "ACT"   => "australian capital territory"
        );
        //$the_states = array_keys($states);
        if($_FILES['csv_file']["size"] > 0)
        {
            if ($_FILES['csv_file']['error']  === UPLOAD_ERR_OK)
            {
                $tmp_name = $_FILES['csv_file']['tmp_name'];
                $csv_array = array_map('str_getcsv', file($tmp_name));
                //echo "<pre>",print_r($csv_array),"</pre>"; die();
                Session::set('feedback',"<h2><i class='far fa-check-circle'></i>Orders Added to the system</h2>");
                $requests = array();
                $skip_first = isset($header_row);
                foreach($csv_array as $r)
                {
                    if($skip_first)
                    {
                        $skip_first = false;
                        continue;
                    }
                    $request = array(
                        'deliver_to'    => ucwords( trim($r[0]) ),
                        'client_id'     => $client_id,
                        'tracking_email'=> "",
                        'company_name'  => trim($r[1]),
                        'address'       => trim($r[2]),
                        'address_2'     => trim($r[3]),
                        'suburb'        => trim($r[4]),
                        'state'         => trim($r[5]),
                        'postcode'      => trim($r[6]),
                        'contact_phone' => trim($r[7]),
                        'date'          => time(),
                        'country'       => 'AU',
                        'errors'        => 0,
                        'error_string'  => '',
                        'weight'        => 14
                    );

                    $orders_items = array();
                    if( strlen($request['address']) > 40 )
                    {
                        $request['errors'] = 1;
                        $request['error_string'] .= "<p>Addresses cannot have more than 40 characters</p>";
                    }
                    $aResponse = $this->Eparcel->ValidateSuburb($request['suburb'], $request['state'], str_pad($request['postcode'],4,'0',STR_PAD_LEFT));

                    //echo "<pre>",print_r($aResponse),"</pre>";
                    if(isset($aResponse['errors']))
                    {
                        $request['errors'] = 1;
                        foreach($aResponse['errors'] as $e)
                        {
                            $request['error_string'] .= "<p>{$e['message']}</p>";
                        }
                    }
                    elseif($aResponse['found'] === false)
                    {
                        $request['errors'] = 1;
                        $request['error_string'] .= "<p>Postcode does not match suburb or state</p>";
                    }
                    if(!preg_match("/(?:[A-Za-z].*?\d|\d.*?[A-Za-z])/i", $request['address']) && (!preg_match("/(?:care of)|(c\/o)|( co )/i", $request['address'])))
                    {
                        $request['errors'] = 1;
                        $request['error_string'] .= "<p>The address is missing either a number or a word</p>";
                    }
                    if( $this->dataSubbed($request['tracking_email']) && !filter_var($request['tracking_email'], FILTER_VALIDATE_EMAIL) )
                    {
                        $request['errors'] = 1;
                        $request['error_string'] .= "<p>The customer email is not valid</p>";
                    }
                    /*
                    $location = array(
                                    'location_id'   => 2901, //Bayswater Receiving
                                    'qty'           => $r[12]
                    );
                    */
                    $location = array(
                                    'location_id'   => 1138, //packing
                                    'qty'           => 1
                    );
                    /*
                    $locations = array();
                    $locations[] = $location;
                    $request['items'][] = array(
                        'item_id'  => $r[11],
                        'locations' => $locations
                    );
                    */
                    $locations = array();
                    $locations[] = $location;
                    $request['items'][] = array(
                        'item_id'   => 13790,
                        'locations' => $locations
                    );
                    $requests[] = $request;
                }

            }
            else
            {
                $error_message = $this->file_upload_error_message($_FILES['csv_file']['error']);
                Form::setError('csv_file', $error_message);
            }
        }
        else
        {
            Form::setError('csv_file', 'Please upload a file');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //echo "<pre>",print_r($requests),"</pre>"; die();
            //create the order
            foreach($requests as $r)
            {
                $this->order->addOrder($r, $r['items']);
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."/orders/add-bulk-orders");
    }

    public function procSwatchCsvUpload()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        //echo "Files<pre>",print_r($_FILES),"</pre>";die();
        $swatch_id = 12521;
        $office_id = 1336;
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $states = array(
            "NSW"   => "new south wales",
            "VIC"   => "victoria",
            "QLD"   => "queensland",
            "TAS"   => "tasmania",
            "SA"    => "south australia",
            "WA"    => "western australia",
            "NT"    => "northern territory",
            "ACT"   => "australian capital territory"
        );
        //$the_states = array_keys($states);
        if($_FILES['csv_file']["size"] > 0)
        {
            if ($_FILES['csv_file']['error']  === UPLOAD_ERR_OK)
            {
                if($this->item->getAvailableStock($swatch_id, 4) <= 0)
                {
                    $_SESSION['errorfeedback'] = "<h2><i class='far fa-times-circle'></i>Swatches cannot be uploaded</h2><p>There are not enough swatches left</p>";
                    return $this->redirector->to(PUBLIC_ROOT."orders/manage-swatches");
                }
                $tmp_name = $_FILES['csv_file']['tmp_name'];
                $csv_array = array_map('str_getcsv', file($tmp_name));
                //echo "<pre>",print_r($csv_array),"</pre>"; die();
                Session::set('feedback',"<h2><i class='far fa-check-circle'></i>Swatches have been uploaded</h2><p>You should be able to see them below</p>");
                $requests = array();
                $skip_first = true;
                foreach($csv_array as $r)
                {
                    if($skip_first)
                    {
                        $skip_first = false;
                        continue;
                    }
                    if(!empty($r[7]))
                        continue;

                    if(array_search(strtolower(trim($r[3])), $states) === false)
                    {
                        $state = trim($r[3]);
                    }
                    else
                    {
                        $state = array_search(strtolower(trim($r[3])), $states);
                    }
                    $request = array(
                        'name'          => trim($r[0]),
                        'client_id'     => $client_id,
                        'email'         => trim($r[6]),
                        'address'       => trim($r[1]),
                        'suburb'        => trim($r[2]),
                        'state'         => $state,
                        'postcode'      => trim($r[5]),
                        'date'          => time(),
                        'errors'        => 0,
                        'error_string'  => ''
                    );
                    $orders_items = array();
                    if( strlen($request['address']) > 40 )
                    {
                        $request['errors'] = 1;
                        $request['error_string'] .= "<p>Addresses cannot have more than 40 characters</p>";
                    }
                    $aResponse = $this->Eparcel->ValidateSuburb($request['suburb'], $request['state'], str_pad($request['postcode'],4,'0',STR_PAD_LEFT));

                    //echo "<pre>",print_r($aResponse),"</pre>";
                    if(isset($aResponse['errors']))
                    {
                        $request['errors'] = 1;
                        foreach($aResponse['errors'] as $e)
                        {
                            $request['error_string'] .= "<p>{$e['message']}</p>";
                        }
                    }
                    elseif($aResponse['found'] === false)
                    {
                        $request['errors'] = 1;
                        $request['error_string'] .= "<p>Postcode does not match suburb or state</p>";
                    }
                    if(!preg_match("/(?:[A-Za-z].*?\d|\d.*?[A-Za-z])/i", $request['address']) && (!preg_match("/(?:care of)|(c\/o)|( co )/i", $request['address'])))
                    {
                        $request['errors'] = 1;
                        $request['error_string'] .= "<p>The address is missing either a number or a word</p>";
                    }
                    if( $this->dataSubbed($request['email']) && !filter_var($request['email'], FILTER_VALIDATE_EMAIL) )
                    {
                        $request['errors'] = 1;
                        $request['error_string'] .= "<p>The customer email is not valid</p>";
                    }
                    $request['items'] = array(
                        'id'       => $swatch_id,
                        'qty'      => 1,
                        'location' => $office_id
                    );
                    $requests[] = $request;
                }

            }
            else
            {
                $error_message = $this->file_upload_error_message($_FILES['csv_file']['error']);
                Form::setError('csv_file', $error_message);
            }
        }
        else
        {
            Form::setError('csv_file', 'Please upload a file');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //echo "<pre>",print_r($requests),"</pre>"; die();
            //create the request
            foreach($requests as $r)
            {
                $this->swatch->addSwatch($r);
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."orders/manage-swatches");
    }

    public function procEditServiceJob()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if($team_id == "0")
        {
            Form::setError('team_id', "A team must be chosen");
        }
        if(!$this->dataSubbed($work_order))
        {
            Form::setError('work_order', 'A work order number is required');
        }
        $this->validateAddress($address, $suburb, $state, $postcode, $country, isset($ignore_address_error));
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
            //required and defaults
            $vals = array(
                "client_id"           => $client_id,
                'type_id'      => $type_id,
                'team_id'    => $team_id,
                'work_order'     => $work_order,
                'job_date'      => $date_value,
                'battery'      => 0,
                'customer_name'     => NULL,
                'address'   => $address,
                'address_2'   => NULL,
                'suburb'    => $suburb,
                'state'     => $state,
                'postcode'  => $postcode,
                'country'   => $country,
                'entered_by'    =>  Session::getUserId()
            );
            if(isset($battery))
                $vals['battery'] = 1;
            if($this->dataSubbed($customer_name))
                $vals['customer_name'] = $customer_name;
            if($this->dataSubbed($address2))
                $vals['address_2'] = $address2;
            $this->solarservicejob->updateJobValues($vals, $job_id) ;
            Session::set('feedback', "That job has been updated in the system");
        }
        return $this->redirector->to(PUBLIC_ROOT."solar-jobs/update-service-details/id=".$job_id);
    }

    public function procSolarReturn()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if(!$this->dataSubbed($item_name))
        {
            Form::setError('item_name', 'The item name is required');
        }
        if(!$this->dataSubbed($serial_number))
        {
            Form::setError('name', 'The item serial number is required');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $this->solarreturn->addReturn($post_data);
            Session::set('feedback', "That return has been recorded");
        }
        return $this->redirector->to(PUBLIC_ROOT."inventory/solar-returns");
    }

    public function procMovementReasonAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if(!$this->dataSubbed($name))
        {
            Form::setError('name', 'The reason name is required');
        }
        elseif($this->stockmovementlabels->getLabelId($name) )
        {
            Form::setError('name', 'This reason is already in use. Reason names need to be unique');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $this->stockmovementlabels->addLabel($name);
            Session::set('feedback', "That reason has been added");
        }
        return $this->redirector->to(PUBLIC_ROOT."site-settings/stock-movement-reasons");
    }

    public function procUrgencyAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if(!$this->dataSubbed($name))
        {
            Form::setError('name', 'The name is required');
        }
        elseif($this->deliveryurgency->getUrgencyId($name) )
        {
            Form::setError('name', 'This name is already in use. Names need to be unique');
        }
        if(!$this->dataSubbed($cut_off))
        {
            Form::setError('cut_off', 'The Cut Off Time is required');
        }
        elseif((filter_var($cut_off, FILTER_VALIDATE_FLOAT) === false || $cut_off < 0 || $cut_off > 23))
        {
            Form::setError('cut_off', 'The Cut Off Time needs to a whole number between 0 and 23 (inclusive)');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $this->deliveryurgency->addUrgency($name, $cut_off, $charge_level);
            Session::set('feedback', "That Urgency has been added");
        }
        return $this->redirector->to(PUBLIC_ROOT."site-settings/delivery-urgencies");
    }

    public function procOrderCsvUpload()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        //echo "<pre>",print_r($_FILES),"</pre>";die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if($_FILES['csv_file']["size"] > 0)
        {
            if ($_FILES['csv_file']['error']  === UPLOAD_ERR_OK)
            {
                $tmp_name = $_FILES['csv_file']['tmp_name'];
                $csv_array = array_map('str_getcsv', file($tmp_name));
                //echo "<pre>",print_r($csv_array),"</pre>"; die();
                Session::set('feedback',"<h2><i class='far fa-check-circle'></i>Orders have been fulfilled</h2>");
                foreach($csv_array as $r)
                {
                    $d_array = explode("|", $r[24]);
                    //echo "<pre>",print_r($d_array),"</pre>";
                    $order_number = trim($d_array[2]);
                    $od = $this->order->getOrderByOrderNumber($order_number);
                    //echo "<pre>",print_r($od),"</pre>";
                    $this->request->data['consignment_id'] = $r[18];
                    $this->request->data['local_charge'] = ($r[29] + 6.77);  //includes fuel levy
                    $this->request->data['order_ids'] = $od['id'];
                    $this->orderfulfiller->fulfillDirectFreightOrder();
                }
            }
            else
            {
                $error_message = $this->file_upload_error_message($_FILES['csv_file']['error']);
                Form::setError('csv_file', $error_message);
            }
        }
        else
        {
            Form::setError('csv_file', 'Please upload a file');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        return $this->redirector->to(PUBLIC_ROOT."orders/order-csv-update");
    }

    public function procRegisterNewStock()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A product name is required');
        }
        if( !($this->dataSubbed($barcode) || $this->dataSubbed($client_product_id)))
        {
            Form::setError('counter', "At least one of these is required");
        }
        elseif( $this->dataSubbed($barcode) )
        {
            if($this->item->barcodeTaken($barcode))
            {
                Form::setError('barcode', 'This barcode is already in use');
            }
        }
        if( !$this->dataSubbed($sku) )
        {
            Form::setError('sku', 'An SKU is required');
        }
        elseif($this->item->skuTaken($sku))
        {
            Form::setError('sku', 'This SKU is already in use');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $array = array(
                'name'      => $name,
                'sku'       => $sku,
                'client_id' => $client_id
            );
            if(isset($is_pod))
                $array['is_pod'] = 1;
            if($this->dataSubbed($client_product_id))
                $array['client_product_id'] = $client_product_id;
            if($this->dataSubbed($barcode))
                $array['barcode'] = $barcode;
            if($this->dataSubbed($image))
                $array['image'] = $image;
            $item_id = $this->item->recordData($array);
            Session::set("feedback", "<h2><i class='far fa-check-circle'></i>{$name}'s Details Recorded</h2><p>Thankyou</p>");
        }
        return $this->redirector->to(PUBLIC_ROOT."inventory/record-new-product");
    }

    public function procRecordPickup()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();$post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !($this->dataSubbed($cartons) || $this->dataSubbed($pallets)))
        {
            Form::setError('counter', "At least one of these values is required");
        }
        else
        {
            if($this->dataSubbed($pallets))
            {
                if( filter_var( $pallets, FILTER_VALIDATE_INT ) === false || $pallets <= 0)
                {
                    Form::setError('counter', "Only positive whole numbers can be used for quantities");
                }
            }
            if($this->dataSubbed($cartons))
            {
                if( filter_var( $cartons, FILTER_VALIDATE_INT ) === false || $cartons <= 0)
                {
                    Form::setError('counter', "Only positive whole numbers can be used for quantities");
                }
            }
        }
        $this->validateAddress($address, $suburb, $state, $postcode, $country, isset($ignore_address_error));
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $vals = array(
                'client_id'     => $client_id,
                'courier_name'  => $courier_name,
                'con_id'        => $con_id,
                'date_recorded' => time(),
                'recorded_by'   => Session::getUserId(),
                'address'       => $address,
                'suburb'        => $suburb,
                'postcode'      => $postcode
            );
            if($this->dataSubbed($cartons))
                $vals['cartons'] = $cartons;
            if($this->dataSubbed($pallets))
                $vals['pallets'] = $pallets;
            if($this->dataSubbed($courier_charge))
                $vals['courier_charge'] = $courier_charge;
            if(isset($address2))
                $vals['address_2'] = $address2;
            $this->recordedpickup->recordData($vals);
            Session::set("feedback", "<h2><i class='far fa-check-circle'></i>Pickup Successfully Recorded</h2>");
        }
        return $this->redirector->to(PUBLIC_ROOT."orders/record-pickup/client=$client_id");
    }

    public function procPickOrder()
    {
        echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }

    }

    public function procPackOrder()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $items = $this->order->getItemsForOrderNoLocations($order_id);
        //echo "<pre>",print_r($items),"</pre>"; //die();
        Session::set('feedback',"<h2><i class='far fa-check-circle'></i>The Order Has Successfully Been Packed</h2>");
        Session::set('errorfeedback',"<h2><i class='far fa-times-circle'></i>There Has Been an Error Packing This Order</h2><p>Errors are listed below</p><ul>");
        Session::set('showfeedback', true);
        Session::set('showerrorfeedback', false);
        foreach($items as $i)
        {
            if(!array_key_exists($i['item_id'], $this->request->data['packed'] ))
            {
                $_SESSION['showerrorfeedback'] = true;
                $_SESSION['showfeedback'] = false;
                $_SESSION['errorfeedback'] .= "<li>No items listed for {$i['name']}</li>";
            }
            elseif( $i['qty'] !=$this->request->data['packed'][$i['item_id']] )
            {
                $_SESSION['showerrorfeedback'] = true;
                $_SESSION['showfeedback'] = false;
                $_SESSION['errorfeedback'] .= "<li>Wrong number packed for {$i['name']}</li>";
            }
        }
        $_SESSION['errorfeedback'] .= "</ul>";
        if(Session::getAndDestroy('showfeedback') == false)
        {
            Session::destroy('feedback');
        }
        else
        {
            $this->orderpacking->recordData(array(
                'order_id'  => $order_id,
                'packed_by' => Session::getUserId(),
                'date'      => time()
            ));
            $this->order->updateOrderValue('status_id', $this->order->packed_id, $order_id);
        }
        if(Session::getAndDestroy('showerrorfeedback') == false)
        {
            Session::destroy('errorfeedback');
        }
        return $this->redirector->to(PUBLIC_ROOT."orders/order-packing");
    }

    public function procOrderUpload()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if($_FILES['csv_file']["size"] > 0)
        {
            if ($_FILES['csv_file']['error']  === UPLOAD_ERR_OK)
            {
                $tmp_name = $_FILES['csv_file']['tmp_name'];
                $csv_array = array_map('str_getcsv', file($tmp_name));
                //echo "<pre>",print_r($csv_array),"</pre>"; die();
            }
            else
            {
            	$error_message = $this->file_upload_error_message($_FILES[$field]['error']);
                Form::setError('csv_file', $error_message);
            }
        }
        else
        {
            Form::setError('csv_file', 'please select a file to upload');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            /*
            [0] => Order_Number
            [1] => Shipment_Address_Company
            [2] => Shipment_Address_Name
            [3] => Shipment_Address1
            [4] => Shipment_Address2
            [5] => Shipment_Address_City
            [6] => Shipment_Address_State
            [7] => Shipment_AddressZIP_Code
            [8] => Shipment_Address_Country_Code
            [9] => Shipment_Address_Phone
            [10] => tracking_email
            [11] => ATL
            [12] => Delivery Instructions
            [13] => Express_Post
            [14] => Client Entry
            [15] => Item_1_sku
            [16] => Item_1_qty
            [17] => Item_1_whole_pallet
            [18] => item_2_sku
            [19] => item_2_qty
            [20] => Item_2_whole_pallet
            etc...
            */
            $imported_order_count = 0;
            $imported_orders = array();
            $skip_first = isset($header_row);
            $line = 1;
            $data_error_string = $item_error_string = "<ul>";
            $import_orders = true;
            foreach($csv_array as $row)
            {
                $data_errors = false;
                if($skip_first)
                {
                    $skip_first = false;
                    continue;
                }
                if(!empty($row[10]))
                {
                    if(!$this->emailValid($row[10]))
                    {
                        $data_errors = true;
                        $data_error_string .= "<li>Invalid tracking email on line: $line</li>";
                    }
                }
                if(!$this->dataSubbed($row[2]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>A Ship To Name is required on line: $line</li>";
                }
                if(!$this->dataSubbed($row[3]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>A Ship To Address is required on line: $line</li>";
                }
                if(!$this->dataSubbed($row[5]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>A Ship To Suburb/City is required on line: $line</li>";
                }
                if(!$this->dataSubbed($row[6]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>A Ship To State is required on line: $line</li>";
                }
                if(!$this->dataSubbed($row[7]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>A Ship To Postcode is required on line: $line</li>";
                }
                if(!$this->dataSubbed($row[8]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>A Ship To Country is required on line: $line</li>";
                }
                elseif(strlen($row[8]) > 2)
                {
                    $data_errors = true;
                    $data_error_string .= "<li>Please use the two letter ISO code for countries on line: $line</li>";
                }
                if(!$data_errors)
                {
                    $sig = ($row[11] == 1)? 0 : 1;
                    $order = array(
                        'error_string'          => '',
                        'items'                 => array(),
                        'ref2'                  => '',
                        'client_order_id'       => $row[0],
                        'errors'                => 0,
                        'tracking_email'        => $row[10],
                        'ship_to'               => $row[2],
                        'company_name'          => $row[1],
                        'date_ordered'          => time(),
                        'status_id'             => $this->controller->order->ordered_id,
                        'eparcel_express'       => 0,
                        'signature_req'         => $sig,
                        'contact_phone'         => $row[9],
                        'import_error'          => false,
                        'import_error_string'   => '',
                        'weight'                => 0,
                        'instructions'          => $row[12],
                        '3pl_comments'          => $row[14]
                    );
                    //the items
                    $items = array();
                    $item_error = false;
                    $i = 15;
                    do
                    {
                        $sku = $row[$i];
                        ++$i;
                        $qty = $row[$i];
                        ++$i;
                        $whole_pallet = false;
                        if(Session::getUserClientId() != 72)   //SELECTRONIC think everything is a whole pallet
                            $whole_pallet = ($row[$i] == 1);
                        $item = $this->item->getItemBySku($sku);
                        if(empty($item))
                        {
                            $item_error = true;
                            $import_orders = false;
                            $data_error_string .= "<li>$sku could not be matched to any items in cell $i on row $line</li>";
                        }
                        else
                        {
                            $items[] = array(
                                'qty'           =>  $qty,
                                'id'            =>  $item['id'],
                                'whole_pallet'  => $whole_pallet
                            );
                        }
                        ++$i;
                    }
                    while(!empty($row[$i]));
                    //$orders_items = array();
                    if(!$item_error)
                    {
                        $order['items'] = $items;
                        $orders_items[$imported_order_count] = $items;
                        //validate address
                        $ad = array(
                            'address'   => $row[3],
                            'address_2' => $row[4],
                            'suburb'    => $row[5],
                            'state'     => $row[6],
                            'postcode'  => $row[7],
                            'country'   => $row[8]
                        );
                        if($ad['country'] == "AU")
                        {
                            if(strlen($ad['address']) > 40 || strlen($ad['address_2']) > 40 || strlen($order['company_name'])  > 40)
                            {
                                $order['errors'] = 1;
                                $order['error_string'] .= "<p>Addresses cannot have more than 40 characters</p>";
                            }
                            $aResponse = $this->Eparcel->ValidateSuburb($ad['suburb'], $ad['state'], str_pad($ad['postcode'],4,'0',STR_PAD_LEFT));
                            //echo "<pre>",print_r($aResponse),"</pre>";
                            if(isset($aResponse['errors']))
                            {
                                $order['errors'] = 1;
                                foreach($aResponse['errors'] as $e)
                                {
                                    $order['error_string'] .= "<p>{$e['message']}</p>";
                                }
                            }
                            elseif($aResponse['found'] === false)
                            {
                                $order['errors'] = 1;
                                $order['error_string'] .= "<p>Postcode does not match suburb or state</p>";
                            }
                        }
                        else
                        {
                            if( strlen( $ad['address'] ) > 50 || strlen( $ad['address_2'] ) > 50 )
                            {
                                $order['errors'] = 1;
                                $order['error_string'] .= "<p>International addresses cannot have more than 50 characters</p>";
                            }
                            if( strlen($order['ship_to']) > 30 || strlen($order['company_name']) > 30 )
                            {
                                $order['errors'] = 1;
                                $order['error_string'] .= "<p>International names and company names cannot have more than 30 characters</p>";
                            }
                        }
                        if(!preg_match("/(?:[A-Za-z].*?\d|\d.*?[A-Za-z])/i", $ad['address']) && (!preg_match("/(?:care of)|(c\/o)|( co )/i", $ad['address'])))
                        {
                            $order['errors'] = 1;
                            $order['error_string'] .= "<p>The address is missing either a number or a word</p>";
                        }
                        $order = array_merge($order, $ad);
                        $imported_orders[$imported_order_count] = $order;
                        ++$imported_order_count;
                    }
                    else
                    {
                        $import_orders = false;
                    }
                }
                else
                {
                    $import_orders = false;
                }
                ++$line;
            }
            if($import_orders)
            {
                $all_items = $this->allocations->createOrderItemsArray($orders_items);
                //echo "<pre>",print_r($orders_items),"</pre>";die();
                $item_error = false;
                $error_string = "";
                foreach($all_items as $oind => $order_items)
                {
                    foreach($order_items as $item)
                    {
                        if($item['item_error'])
                        {
                            $import_orders = false;
                            $data_error_string .= "<li>".$item['item_error_string']." for order {$imported_orders[$oind]['client_order_id']}</li>";
                        }
                    }
                }
                $data_error_string .= "</ul>";

                if($import_orders)
                {
                    Session::set('feedback', "<h2><i class='far fa-check-circle'></i>$imported_order_count Orders Have Been Successfully Imported</h2>");
                    foreach($imported_orders as $oind => $o)
                    {
                        $vals = array(
                            'client_order_id'       => $o['client_order_id'],
                            //'customer_order_id'     => $o['customer_order_id'],
                            'client_id'             => Session::getUserClientId(),
                            'deliver_to'            => $o['ship_to'],
                            'company_name'          => $o['company_name'],
                            'date_ordered'          => $o['date_ordered'],
                            'tracking_email'        => $o['tracking_email'],
                            'weight'                => $o['weight'],
                            'delivery_instructions' => $o['instructions'],
                            '3pl_comments'          => $o['3pl_comments'],
                            'errors'                => $o['errors'],
                            'error_string'          => $o['error_string'],
                            'address'               => $o['address'],
                            'address2'              => $o['address_2'],
                            'state'                 => $o['state'],
                            'suburb'                => $o['suburb'],
                            'postcode'              => $o['postcode'],
                            'country'               => $o['country'],
                            'contact_phone'         => $o['contact_phone']
                        );
                        if($o['signature_req'] == 1) $vals['signature_req'] = 1;
                        if($o['eparcel_express'] == 1) $vals['eparcel_express'] = 1;
                        //echo "<pre>",print_r($all_items),"</pre>";die();
                        $itp = array($all_items[$oind]);
                        $order_number = $this->controller->order->addOrder($vals, $itp);
                        $_SESSION['feedback'] .= "<p>$order_number has been created</p>";
                    }
                }
                else
                {
                    Session::set('errorfeedback',"<h2><i class='far fa-times-circle'></i>These Orders Could Not Be Imported</h2><p>Reasons are listed below</p>$data_error_string");
                }
            }
            else
            {
                Session::set('errorfeedback',"<h2><i class='far fa-times-circle'></i>These Orders Could Not Be Imported</h2><p>Reasons are listed below</p>$data_error_string");
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."orders/bulk-upload-orders");
    }

    public function procPickupUpdate()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $pickup = $this->pickup->getPickup($pickup_id);
        if(isset($partof_order))
        {
            if(!$this->dataSubbed($order_number))
            {
                Form::setError('order_number', 'An order number is required if this is included in an order');
            }
            else
            {
                $od = $this->order->getOrderByOrderNumber($order_number);
                if(empty($od))
                {
                    Form::setError('order_number', 'No order was found with that order number');
                }
                elseif($od['client_id'] != $pickup['client_id'])
                {
                    Form::setError('order_number', 'That order number does not belong to this client');
                }
            }
        }
        else
        {
            if(!$this->dataSubbed($truck_charge))
            {
                Form::setError('charge', 'A charge is required');
            }
            elseif(filter_var($truck_charge, FILTER_VALIDATE_FLOAT, array('options' => array('min_range' => 0))) === false)
            {
                Form::setError("truck_charge", "Please enter a valid dollar and cents amount");
            }
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            if(isset($partof_order))
            {
                $vals = array(
                    'order_id'          => $od['id'],
                    'date_completed'    => time(),
                    'completed_by'      => Session::getUserId()
                );
                $this->pickup->updatePickupValues($vals, $pickup_id);
                Session::set('feedback', "<h2><i class='far fa-check-circle'></i>Pickup Details Updated</h2><p>That pickup is now associated with Order: $order_number</p>");
            }
            else
            {
                //update pickup table
                $vals = array(
                    'charge'          => $truck_charge,
                    'date_completed'  => time(),
                    'completed_by'    => Session::getUserId()
                );
                $this->pickup->updatePickupValues($vals, $pickup_id);
                //insert truck usage vals
                $t_vals = array(
                    'client_id'     => $pickup['client_id'],
                    'pickup_id'     => $pickup_id,
                    'address'       => $pickup['puaddress'],
                    'postcode'      => $pickup['pupostcode'],
                    'suburb'        => $pickup['pusuburb'],
                    'doaddress'     => $pickup['address'],
                    'dosuburb'      => $pickup['suburb'],
                    'dopostcode'    => $pickup['postcode'],
                    'pallets'       => $pickup['pallets'],
                    'charge'        => $truck_charge,
                    'date'          => time(),
                    'entered_by'    => Session::getUserId()
                );
                if(!empty($pickup['puaddress_2']))
                    $t_vals['address_2'] = $pickup['puaddress_2'];
                if(!empty($pickup['address_2']))
                    $t_vals['doaddress_2'] = $pickup['address_2'];

                $this->truckusage->recordData($t_vals);
                Session::set('feedback', "<h2><i class='far fa-check-circle'></i>Pickup Details Updated</h2><p>That pickup and associated truck usage has been recorded</p>");
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."orders/view-pickups");
    }

    public function procOrderEdit()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !$this->dataSubbed($deliver_to) )
        {
            Form::setError('deliver_to', 'A name is required');
        }
        if($this->dataSubbed($tracking_email))
        {
            if(!$this->emailValid($tracking_email))
            {
                Form::setError('tracking_email', 'The tracking email is not valid');
            }
        }
        //file uploads
        if($_FILES['invoice']["size"][0] > 0)
        {
            //echo "<pre>",print_r($_FILES),"</pre>";
            $pdfs = array();
            $file_error = false;
            for($i=0; $i<count($_FILES['invoice']['name']); $i++)
            {
                if ($_FILES['invoice']['error'][$i]  === UPLOAD_ERR_OK)
    			{
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $_FILES['invoice']['tmp_name'][$i]);
                    if($mime != "application/pdf")
                    {
                        Form::setError('invoice', 'Only upload pdf files');
                        $file_error = true;
                    }
                    else
                    {
                        $tmp_name = $_FILES['invoice']["tmp_name"][$i];
                        $pdfs[] = array(
                        	'file'		    =>	$tmp_name,
                            'orientation'	=>	'P'
                        );
                    }
    			}
    			else
    			{
                	$error_message = $this->file_upload_error_message($_FILES['invoice']['error'][$i]);
                    Form::setError('invoice', $error_message);
                    $file_error = true;
    			}
            }
            if(!$file_error)
            {
                $upcount = 1;
                $filename = "invoice";
                $name = "invoice.pdf";
                $upload_dir = "/client_uploads/$client_id/";
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

        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //echo "<pre>",print_r($this->request->data),"</pre>";
            //required and defaults
            $vals = array(
                "ship_to"           => $deliver_to,
                'company_name'      => NULL,
                'tracking_email'    => NULL,
                'contact_phone'     => NULL,
                'instructions'      => NULL,
                '3pl_comments'      => NULL,
                'uploaded_file'     => NULL,
                'eparcel_express'   => 0,
                'signature_req'     => 0,
                'store_order'       => 0,
                'client_order_id'   => NULL
            );
            if($this->dataSubbed($company_name))
                $vals['company_name'] = $company_name;
            if($this->dataSubbed($client_order_id))
                $vals['client_order_id'] = $client_order_id;
            if($this->dataSubbed($contact_phone))
                $vals['contact_phone'] = $contact_phone;
            if($this->dataSubbed($tracking_email))
                $vals['tracking_email'] = $tracking_email;
            if($this->dataSubbed($delivery_instructions))
                $vals['instructions'] = $delivery_instructions;
            if($this->dataSubbed($tpl_comments))
                $vals['3pl_comments'] = $tpl_comments;
            if(isset($express_post))
                $vals['eparcel_express'] = 1;
            if(isset($signature_req))
                $vals['signature_req'] = 1;
            if(isset($store_order))
                $vals['store_order'] = 1;
            if(isset($uploaded_file) || isset($delete_file))
            {
                if(isset($uploaded_file))
                    $vals['uploaded_file'] = $uploaded_file;
                $od = $this->order->getOrderDetail($order_id);
                if(!empty($od['uploaded_file']) && file_exists(UPLOADS.$client_id."/".$od['uploaded_file']))
                {
                    unlink(UPLOADS.$client_id."/".$od['uploaded_file']);
                }
            }
            $this->order->updateOrderValues($vals, $order_id);

            Session::set("feedback", "<h2><i class='far fa-check-circle'></i>Order Details Updated</h2>");
        }
        return $this->redirector->to(PUBLIC_ROOT."orders/order-edit/order=$order_id");
    }

    public function procBookPickup()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();

        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !($this->dataSubbed($cartons) || $this->dataSubbed($pallets)))
        {
            Form::setError('counter', "At least one of these values is required");
        }
        else
        {
            if($this->dataSubbed($pallets))
            {
                if( filter_var( $pallets, FILTER_VALIDATE_INT ) === false || $pallets <= 0)
                {
                    Form::setError('counter', "Only positive whole numbers can be used for quantities");
                }
            }
            if($this->dataSubbed($cartons))
            {
                if( filter_var( $cartons, FILTER_VALIDATE_INT ) === false || $cartons <= 0)
                {
                    Form::setError('counter', "Only positive whole numbers can be used for quantities");
                }
            }
        }
        if(!$this->dataSubbed($puaddress))
        {
            Form::setError('puaddress', "A pickup address is required");
        }
        if(!$this->dataSubbed($pusuburb))
        {
            Form::setError('pusuburb', "A pickup suburb is required");
        }
        if(!$this->dataSubbed($pupostcode))
        {
            Form::setError('pupostcode', "A pickup postcode is required");
        }
        if(!$this->dataSubbed($address))
        {
            Form::setError('address', "A delivery address is required");
        }
        if(!$this->dataSubbed($suburb))
        {
            Form::setError('suburb', "A delivery suburb is required");
        }
        if(!$this->dataSubbed($postcode))
        {
            Form::setError('postcode', "A delivery postcode is required");
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $vals = array(
                'pickup_number' => $this->pickup->getPickupNumber(),
                'client_id'     => Session::getUserClientId(),
                'date'          => time(),
                'puaddress'     => $puaddress,
                'pusuburb'      => $pusuburb,
                'pupostcode'    => $pupostcode,
                'entered_by'    => Session::getUserId(),
                'address'       => $address,
                'suburb'        => $suburb,
                'postcode'      => $postcode
            );
            if($this->dataSubbed($cartons))
                $vals['cartons'] = $cartons;
            if($this->dataSubbed($pallets))
                $vals['pallets'] = $pallets;
            if(isset($puaddress_2))
                $vals['puaddress_2'] = $puaddress_2;
            if(isset($address_2))
                $vals['address_2'] = $address_2;
            $this->pickup->recordData($vals);
            if(Email::sendClientSubmittedPickupNotifcations($post_data, $this->client->getClientName(Session::getUserClientId()), $this->user->getUserName(Session::getUserId()), $this->user->getUserEmail(Session::getUserId())))
            {
                Session::set("feedback", "<h2><i class='far fa-check-circle'></i>Pickup Successfully Booked</h2><p>A confirmation email has been sent to your address</p>");
            }
            else
            {
                Session::set("feedback", "<h2><i class='far fa-times-circle'></i>Pickup Successfully Booked</h2><p>However, there was an error sending notifications</p><p>Please call the office on 03 8512 1444 and let 3PL+ know about the pickup</p>");
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."orders/book-pickup");
    }

    public function procTruckUsage()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if($client_id == "0")
        {
            Form::setError('client_id', 'A client is required');
        }
        if(!$this->dataSubbed($suburb))
        {
            Form::setError('suburb', 'A suburb is required');
        }
        if(!$this->dataSubbed($postcode))
        {
            Form::setError('postcode', 'A postcode is required');
        }
        if(!$this->dataSubbed($truck_pallets))
        {
            Form::setError('truck_pallets', 'A pallet count is required');
        }
        elseif(filter_var($truck_pallets, FILTER_VALIDATE_INT, array('options' => array('min_range' => 0))) === false)
        {
            Form::setError("truck_pallets", "Please enter positive whole numbers only");
        }
        if(!$this->dataSubbed($charge))
        {
            Form::setError('charge', 'A charge is required');
        }
        elseif(filter_var($charge, FILTER_VALIDATE_FLOAT, array('options' => array('min_range' => 0))) === false)
        {
            Form::setError("pallet_count", "Please enter a valid dollar and cents amount");
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $vals = array(
                'client_id'     =>  $client_id,
                'address'       =>  $address,
                'suburb'        =>  $suburb,
                'postcode'	    =>  $postcode,
                'entered_by'    =>  Session::getUserId(),
                'date'          =>  $date_value,
                'pallets'       =>  $truck_pallets,
                'charge'        =>  $charge
            );
            if(isset($address_2))
                $vals['address_2'] = $address_2;
            $this->truckusage->recordData($vals);
            Session::set('feedback',"<h2><i class='far fa-check-circle'></i>Those details have been entered into the system</h2>");
        }
        return $this->redirector->to(PUBLIC_ROOT."orders/truck-usage");
    }

    public function procGoodsOut()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if(!$this->dataSubbed($pallet_count) && !$this->dataSubbed($carton_count))
        {
            Form::setError('counter', 'At least one of the pallet/carton counts is required');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $pallet_count = ($this->dataSubbed($pallet_count))? $pallet_count: 0;
            $carton_count = ($this->dataSubbed($carton_count))? $carton_count: 0;
            $this->outwardsgoods->recordData(array(
                'client_id'     =>  $client_id,
                'pallets'       =>  $pallet_count,
                'cartons'       =>  $carton_count,
                'date'          =>  time(),
                'entered_by'    =>  Session::getUserId()
            ));
            Session::set('feedback',"<h2><i class='far fa-check-circle'></i>Those details have been entered into the system</h2>");

        }
        return $this->redirector->to(PUBLIC_ROOT."inventory/goods-out/client=$client_id");
    }

    public function procGoodsIn()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if(!$this->dataSubbed($pallet_count) && !$this->dataSubbed($carton_count))
        {
            Form::setError('counter', 'At least one of the pallet/carton counts is required');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $pallet_count = ($this->dataSubbed($pallet_count))? $pallet_count: 0;
            $carton_count = ($this->dataSubbed($carton_count))? $carton_count: 0;
            $this->inwardsgoods->recordData(array(
                'client_id'     =>  $client_id,
                'pallets'       =>  $pallet_count,
                'cartons'       =>  $carton_count,
                'date'          =>  time(),
                'entered_by'    =>  Session::getUserId()
            ));
            Session::set('feedback',"<h2><i class='far fa-check-circle'></i>Those details have been entered into the system</h2>");
            if($this->dataSubbed($consignment_id) && isset($this->request->data['item_returns']))
            {
                $reason_id = $this->stockmovementlabels->getlabelId('Returns - RTS');
                $location_id = $this->location->getLocationId('Returns') ;
                foreach($this->request->data['item_returns'] as $item_id => $details)
                {
                    if(($details['qty']) > 0)
                    {
                        $this->orderreturn->recordData(array(
                            'reason'        =>  'Return To Sender',
                            'item_id'       =>  $item_id,
                            'qty_returned'  => $details['qty'],
                            'order_id'      =>  $order_id,
                            'client_id'     =>  $client_id,
                            'entered_by'    =>  Session::getUserId(),
                            'date'          =>  time()
                        ));

                        $this->itemmovement->recordData(array(
                            'item_id'       =>  $item_id,
                            'qty_in'        =>  $details['qty'],
                            'reason_id'     =>  $reason_id,
                            'order_id'      =>  $order_id,
                            'location_id'   =>  $location_id,
                            'entered_by'    =>  Session::getUserId(),
                            'date'          =>  time()
                        ));
                        $this->item->addStockToLocation($item_id, $location_id, $details['qty']);
                    }

                }
                //$mailer->sendRTSNotification($_POST['order_id'], $_POST['return_items']);
                $_SESSION['feedback'] .= "<p>The order has also been marked with a return and the items have been place in the 'returns' location</p>";
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."inventory/goods-in/client=$client_id");
    }

    public function procAddMiscToOrder()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $handling_charge = (empty($handling_charge))? 0 : $handling_charge;
        $postage_charge = (empty($postage_charge))? 0 : $postage_charge;
        $pallets = ($this->dataSubbed($pallets))? $pallets: 0;
        $satchels = ($this->dataSubbed($satchels))? $satchels: 0;
        if(isset($inc_gst))
            $gst = ($handling_charge + $postage_charge) * 0.1;
        else
            $gst = $handling_charge + 0.1;
        $total_cost = $handling_charge + $postage_charge + $gst;
        $vals = array(
            'pallets'           => $pallets,
            'satchels'          => $satchels,
            'total_cost'        => $total_cost,
            'gst'               => $gst,
            'postage_charge'    => $postage_charge,
            'handling_charge'   => $handling_charge,
            'shrink_wrap'       => 0,
            'bubble_wrap'       => 0
        );
        if(isset($shrink_wrap))
            $vals['shrink_wrap'] = 1;
        if(isset($bubble_wrap))
            $vals['bubble_wrap'] = 1;
        //echo "<pre>",print_r($vals),"</pre>"; die();
        $this->order->updateOrderValues($vals, $order_id);
        Session::set('miscfeedback',"Order Has Been Updated");
        return $this->redirector->to(PUBLIC_ROOT."orders/order-update/order=$order_id#misc");
    }

    public function procOrderCourierUpdate()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        Session::set('showcouriererrorfeedback', false);
        Session::set('showcourierfeedback', false);
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if($courier_id == "0")
        {
            Form::setError('courier_id', 'A courier must be selected');
        }
        elseif($courier_id == $this->courier->localId && !$this->dataSubbed($courier_name))
        {
            Form::setError('courier_name', 'A name must be entered for local couriers');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
            Session::set('showcouriererrorfeedback', true);
            Session::set('couriererrorfeedback', "<h3><i class='far fa-times-circle'></i>Errors found in the form</h3><p>Please correct where shown and resubmit</p>");
        }
        else
        {
            //$ip = (isset($ignore_pc))? 1 : 0;    deprecated functionality
            $courier_name = !$this->dataSubbed($courier_name)? "":$courier_name;
            Session::set('showcourierfeedback', true);
            Session::set('courierfeedback',"<h3><i class='far fa-check-circle'></i>Courier has been assigned</h3>");
            Session::set('couriererrorfeedback', "");
            $this->courierselector->assignCourier($order_id, $courier_id, $courier_name, 1);
        }
        if(Session::getAndDestroy('showcouriererrorfeedback') == false)
        {
            Session::destroy('couriererrorfeedback');
        }
        if(Session::getAndDestroy('showcourierfeedback') == false)
        {
            Session::destroy('courierfeedback');
        }
        return $this->redirector->to(PUBLIC_ROOT."orders/order-update/order={$order_id}#courier");
    }

    public function procStockMovement()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if(filter_var($qty_move, FILTER_VALIDATE_INT) === false && $qty_move <= 0)
        {
            Form::setError('qty_move', 'Please enter only positive whole numbers');
        }
        if($move_to_location == "0")
        {
            Form::setError('move_to_location', 'Please select a location');
        }
        if($move_from_location == "0")
        {
            Form::setError('move_from_location', 'Please select a location');
        }
        $l_details = $this->item->getLocationForItem($move_product_id, $move_from_location);
        //echo "<pre>",print_r($l_details),"</pre>"; //die();
        if(isset($qc_stock))
        {
            $post_data['sub_qc_stock'] = "On";
            if($l_details['qc_count'] < $qty_move)
            {
                Form::setError('qty_move', 'You cannot move more quality control stock than there is available');
            }
        }
        else
        {
            if( ($l_details['qty'] - $l_details['qc_count'] - $l_details['allocated']) < $qty_move )
            {
                Form::setError('qty_move', 'You cannot move more stock than there is available');
            }
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
            Session::set('adderrorfeedback', 'Errors were found in the form. Please correct where shown and resubmit');
        }
        else
        {
            $this->item->moveStock($post_data, $this->stockmovementlabels->getLabelId('Internal Stock Movement'));
            $this->clientsbays->stockRemoved($client_id, $move_from_location, $move_product_id);
            $this->clientsbays->stockAdded($client_id, $move_to_location);

            if( $this->client->isDeliveryClient($client_id) )
            {
                $this->deliveryclientsbay->stockAdded([
                    'client_id'     => $client_id,
                    'location_id'   => $move_to_location,
                    'size'          => $this->deliveryclientsbay->getBaySize($move_from_location, $client_id, $move_product_id),
                    'item_id'       => $move_product_id
                ]);
                $this->deliveryclientsbay->stockRemoved($client_id, $move_from_location, $move_product_id);
            }
            Session::set('feedback', $move_product_name.' has had its stock adjusted');
        }
        return $this->redirector->to(PUBLIC_ROOT."inventory/move-stock/product=$move_product_id");
    }

    public function procContainerUnload()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        if($container_size == "0")
        {
            Form::setError('container_size', 'Please select a container size');
        }
        if($client_id == "0")
        {
            Form::setError('client_id', 'Please select a client');
        }
        if($load_type == "0")
        {
            Form::setError('load_type', 'Please select a load type');
        }
        elseif($load_type == "Loose")
        {
            if( !isset($item_count) )
            {
                Form::setError('item_count', 'Item counts are required for loose unloads');
            }
            elseif( filter_var( $item_count, FILTER_VALIDATE_INT ) === false || $item_count <= 0)
            {
                Form::setError('item_count', 'Only enter whole positive numbers here');
            }
        }
        if(!$this->dataSubbed($date_value))
        {
            Form::setError('date', "Please enter a date");
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $this->unloadedcontainer->recordData($post_data);
            Session::set('feedback', 'That data has been recorded');
        }
        return $this->redirector->to(PUBLIC_ROOT."data-entry/container-unloading");
    }

    public function procAddClientLocation()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if($location == "0")
        {
            Form::setError('location', 'Please select a location');
        }
        if($client_id == "0")
        {
            Form::setError('client_id', 'Please select a client');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $this->clientslocation->addLocation($post_data);
            Session::set('feedback', 'That location has been allocated to that client');
        }
        return $this->redirector->to(PUBLIC_ROOT."inventory/client-locations");
    }

    public function procBasicProductAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A product name is required');
        }
        if( !$this->dataSubbed($sku) )
        {
            Form::setError('sku', 'An SKU is required');
        }
        elseif($this->item->skuTaken($sku))
        {
            Form::setError('sku', 'This SKU is already in use');
        }

        if($this->dataSubbed($barcode))
        {
            if($this->item->barcodeTaken($barcode))
            {
                Form::setError('barcode', 'This barcode is already in use');
            }
        }
        if( $this->dataSubbed($weight) )
        {
            if(filter_var($weight, FILTER_VALIDATE_FLOAT) === false && $weight <= 0)
            {
                Form::setError('weight', 'Please enter a valid positive number');
            }
        }
        else
        {
            $post_data['weight'] = 0;
        }
        $package_types = array();
        $palletizedd = (isset($palletized))? 1:0;
        $post_data['palletized'] = $palletizedd;
        $post_data['width'] = 0;
        $post_data['depth'] = 0;
        $post_data['height'] = 0;
        $post_data['boxed_item'] = 0;
        $post_data['low_stock_warning'] = null;
        $post_data['trigger_point'] = 0;
        $product_id = $this->item->addItem($post_data);
        $post_data = array(
            'reference'         =>  'New Stock',
            'reason_id'         =>  $this->stockmovementlabels->getLabelId("New Stock"),
            'qty_add'           =>  $qty,
            'add_to_location'   =>  $add_to_location,
            'add_product_id'    =>  $product_id
        );
        $this->location->addToLocation($post_data);
        //$this->item->addPackingTypesForItem($package_types, $product_id);
        Session::set('feedback', "{$name}'s details have been added to the system and $qty have been imported");
        return $this->redirector->to(PUBLIC_ROOT."inventory/scan-to-inventory/client=$client_id");
    }

    public function procScanToInventory()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $pallet_multiplier = 1;
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }

        $post_data = array(
            'reference'         =>  'New Stock',
            'reason_id'         =>  $this->stockmovementlabels->getLabelId("New Stock"),
            'qty_add'           =>  $qty,
            'add_product_id'    =>  $item_id
        );
        if(isset($to_receiving))
        {
            $add_to_location = 0;
            $post_data['add_to_location'] = $this->location->receiving_id;
        }
        else
        {
            $to_receiving = 0;
            $post_data['add_to_location'] = $add_to_location;
        }

        if($lid = $this->newstock->isRegistered($item_id))
        {
            $this->newstock->updateRecorded($lid);
            $this->newstock->updateInCount($lid, $qty);
        }

        $this->location->addToLocation($post_data);
        $this->clientsbays->stockAdded($client_id, $add_to_location, $to_receiving, $pallet_multiplier);
        Session::set('feedback', $qty.' of '.$name.' have been added to the system');
        return $this->redirector->to(PUBLIC_ROOT."inventory/scan-to-inventory/client=$client_id");
    }

    public function procBreakPacks()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if(!$this->dataSubbed($break_count))
        {
            Form::setError('break_count', 'Please enter a value');
        }
        elseif(filter_var($break_count, FILTER_VALIDATE_INT) === false && $break_count <= 0)
        {
            Form::setError('break_count', 'Please enter only whole positive numbers');
        }
        elseif(!isset($this->request->data['location']))
        {
            Form::setError('breakgeneral', 'Please select locations to return items to');
        }
        else
        {
            $pack_items = $this->item->getPackItemDetails($break_product_id);
            $returns = array();
            foreach($pack_items as $pi)
            {
                $return = $break_count * $pi['number'];
                if($this->request->data['location'][$pi['linked_item_id']] == "0")
                {
                    Form::setError('breakgeneral', 'Please select locations to return items to');
                }
                else
                {
                    $returns[] = array(
                        'item_id'       =>  $pi['linked_item_id'],
                        'location_id'   =>  $this->request->data['location'][$pi['linked_item_id']],
                        'qty'           =>  $return
                    );
                }
            }
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
            Session::set('breakerrorfeedback', "Errors have been found<p>Please fix where shown and resubmit</p>");
        }
        else
        {
            //$this->item->makePacks($post_data, $pack_items);
            //echo "<pre>",print_r($this->request->data),"</pre>";
            //echo "<pre>",print_r($returns),"</pre>"; die();
            $this->item->breakPacks($post_data, $returns);
            Session::set('breakfeedback', 'Those packs have been broken up, and the individual items have been returned to inventory');
        }
        return $this->redirector->to(PUBLIC_ROOT."inventory/pack-items-manage/product=$break_product_id");
    }

    public function procMakePacks()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        $pallet_multiplier = 1;
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if(!$this->dataSubbed($make_count))
        {
            Form::setError('make_count', 'Please enter a value');
        }
        elseif(filter_var($make_count, FILTER_VALIDATE_INT) === false && $make_count <= 0)
        {
            Form::setError('make_count', 'Please enter only whole positive numbers');
        }
        elseif(!isset($this->request->data['location']))
        {
            Form::setError('makegeneral', 'Please select locations to pick items from');
        }
        else
        {
            $pack_items = $this->item->getPackItemDetails($add_product_id);
            foreach($pack_items as $pi)
            {
                $need = $make_count * $pi['number'];
                if($this->request->data['location'][$pi['linked_item_id']] == "0")
                {
                    Form::setError('makegeneral', 'Please select locations to pick items from');
                }
                elseif($this->item->getAvailableInLocation($pi['linked_item_id'], $this->request->data['location'][$pi['linked_item_id']]) < $need)
                {
                    Form::setError('makegeneral', 'There are insufficient '.$pi['name'].' to make theses packs');
                }
                else
                {
                    $items[] = array(
                        'item_id'       =>  $pi['linked_item_id'],
                        'location_id'   =>  $this->request->data['location'][$pi['linked_item_id']],
                        'qty'           =>  $need
                    );
                }
            }
        }
        if(isset($to_receiving))
        {
            if(filter_var($pallet_multiplier, FILTER_VALIDATE_INT) === false && $pallet_multiplier <= 0)
            {
                Form::setError('pallet_multiplier', 'Please enter only positive whole numbers');
            }
            $add_to_location = 0;
            $post_data['add_to_location'] = $this->location->receiving_id;
        }
        elseif($add_to_location == "0")
        {
            Form::setError('add_to_location', 'Please select a location');
        }
        else
        {
            $to_receiving = 0;
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
            Session::set('makeerrorfeedback', "<p>Errors have been found</p><p>Please fix where shown and resubmit</p>");
        }
        else
        {
            //echo "<pre>",print_r($this->request->data),"</pre>";
            //echo "<pre>",print_r($items),"</pre>"; die();
            $this->item->makePacks($post_data, $items);
            $this->clientsbays->stockAdded($client_id, $add_to_location, $to_receiving, $pallet_multiplier);
            Session::set('makefeedback', 'Those packs have been created');
        }
        return $this->redirector->to(PUBLIC_ROOT."inventory/pack-items-manage/product=$add_product_id");
    }

    public function procPackItemEdit()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $item_error = false;
        if(!isset($this->request->data['items']))
        {
            Form::setError('items', 'At least one item must be selected');
        }
        else
        {
            foreach($this->request->data['items'] as $id => $array)
            {
                $number = $array['qty'];
                if(filter_var($number, FILTER_VALIDATE_INT) === false && $number <= 0)
                {
                    $item_error = true;
                }
            }
        }
        if($item_error)
        {
            Form::setError('items', 'Only positive whole numbers should be used');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $this->item->updatePackItem($this->request->data['items'], $item_id);
            Session::set('feedback', 'Those details have been updated');
        }
        return $this->redirector->to(PUBLIC_ROOT."products/pack-items-edit/product=$item_id");
    }

    public function procCollectionEdit()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $item_error = false;
        if(!isset($this->request->data['items']))
        {
            Form::setError('items', 'At least one item must be selected');
        }
        else
        {
            foreach($this->request->data['items'] as $id => $array)
            {
                $number = $array['qty'];
                if(filter_var($number, FILTER_VALIDATE_INT) === false && $number <= 0)
                {
                    $item_error = true;
                }
            }
        }
        if($item_error)
        {
            Form::setError('items', 'Only positive whole numbers should be used');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $this->item->updateCollection($this->request->data['items'], $item_id);
            Session::set('feedback', 'Those details have been updated');
        }
        return $this->redirector->to(PUBLIC_ROOT."products/collections-edit/client=$client_id/product=$item_id");
    }

    public function procAddSite()
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
        if(!$this->dataSubbed($name))
        {
            Form::setError('name', 'The site name is required');
        }
        elseif($this->site->getSiteId($name))
        {
            Form::setError('name', 'This name is already in use. Site names need to be unique');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $this->site->addSite($post_data);
            Session::set('feedback', "That site has been added");
        }
        return $this->redirector->to(PUBLIC_ROOT."site-settings/warehouse-locations");
    }

    public function procAddLocation()
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
        if(!$this->dataSubbed($location))
        {
            Form::setError('location', 'The location name is required');
        }
        elseif($this->location->getLocationId($location))
        {
            Form::setError('location', 'This name is already in use. location names need to be unique');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $this->location->addLocation($post_data);
            Session::set('feedback', "That location has been added");
        }
        return $this->redirector->to(PUBLIC_ROOT."site-settings/locations");
    }

    public function procQualityControl()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $error = true;
        $check = true;
        if(!isset($qty_subtract))
            $qty_subtract = 0;
        if(!isset($subtract_from_location))
            $subtract_from_location = 0;
        if($this->dataSubbed($qty_add))
        {
            $error = false;
            if(filter_var($qty_add, FILTER_VALIDATE_INT) === false && $qty_add <= 0)
            {
                Form::setError('qty_add', 'Please enter only positive whole numbers');
                $check = false;
            }
            if($add_to_location == "0")
            {
                Form::setError('add_to_location', 'Please select a location');
                $check = false;
            }
        }
        if($this->dataSubbed($qty_subtract))
        {
            $error = false;
            if(filter_var($qty_subtract, FILTER_VALIDATE_INT) === false && $qty_subtract <= 0)
            {
                Form::setError('qty_subtract', 'Please enter only positive whole numbers');
                $check = false;
            }
            if($subtract_from_location == "0")
            {
                Form::setError('subtract_from_location', 'Please select a location');
                $check = false;
            }
        }
        if($error)
        {
            Form::setError('qty_add', 'Please put a number into one of these fields');
            Form::setError('qty_subtract', 'Please put a number into one of these fields');
        }
        elseif($check)
        {
            if($this->dataSubbed($qty_subtract))
            {
                $location = $this->item->getLocationForItem($product_id, $subtract_from_location);
                if($qty_subtract > $location['qc_count'] && $this->dataSubbed($qty_subtract))
                {
                    Form::setError('qty_subtract', 'You cannot remove more quality control stock than there is');
                }
            }
            if($this->dataSubbed($qty_add))
            {
                $location = $this->item->getLocationForItem($product_id, $add_to_location);
                if($qty_add > ($location['qty'] - $location['qc_count'] - $location['allocated']) && $this->dataSubbed($qty_add))
                {
                    Form::setError('qty_add', 'You cannot add more quality control stock than there is unallocated non quality control items');
                }
            }
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $this->location->updateQualityControlStatus($post_data);
            Session::set('feedback', $product_name.' has had its quality control status updated');
        }
        return $this->redirector->to(PUBLIC_ROOT."inventory/quality-control/product=".$product_id);
    }

    public function procSubtractFromStock()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $check = true;
        if(filter_var($qty_subtract, FILTER_VALIDATE_INT) === false && $qty_subtract <= 0)
        {
            Form::setError('qty_subtract', 'Please enter only positive whole numbers');
            $check = false;
        }
        if($subtract_from_location == "0")
        {
            Form::setError('subtract_from_location', 'Please select a location');
            $check = false;
        }
        if($check)
        {
            $location = $this->item->getLocationForItem($subtract_product_id, $subtract_from_location);
            if(isset($sub_qc_stock))
            {
                if($qty_subtract > $location['qc_count'])
                {
                    Form::setError('qty_subtract', 'You cannot remove more quality control stock than there is');
                }
            }
            else
            {
                if($qty_subtract > ($location['qty'] - $location['allocated']))
                {
                    Form::setError('qty_subtract', 'You cannot remove more stock than is unallocated');
                }
                if($qty_subtract > ($location['qty'] - $location['qc_count']))
                {
                    Form::setError('qty_subtract', 'You cannot remove more stock than there is');
                }
            }
        }
        if($reason_id == "0")
        {
            Form::setError('reason_id', 'Please select a reason for subtracting stock');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
            Session::set('subtractitemerrorfeedback', 'Errors were found in the form. Please correct where shown and resubmit');
        }
        else
        {
            $this->location->subtractFromLocation($post_data);
            $this->clientsbays->stockRemoved($client_id, $subtract_from_location, $subtract_product_id, isset($remove_oversize));
            //record removal from delivery client bays
            if( $this->client->isDeliveryClient($client_id) )
                $this->deliveryclientsbay->stockRemoved($client_id, $subtract_from_location, $subtract_product_id);
            Session::set('subtractitemfeedback', $subtract_product_name.' has had '.$qty_subtract.' removed fom its count');
        }
        return $this->redirector->to(PUBLIC_ROOT."inventory/add-subtract-stock/product=".$subtract_product_id."#subtract");
    }

    public function procAddToStock()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $pallet_multiplier = 1;
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        //die('oversize'.$oversize);
        if(filter_var($qty_add, FILTER_VALIDATE_INT) === false && $qty_add <= 0)
        {
            Form::setError('qty_add', 'Please enter only positive whole numbers');
        }
        if(isset($to_receiving))
        {
            if(filter_var($pallet_multiplier, FILTER_VALIDATE_INT) === false && $pallet_multiplier <= 0)
            {
                Form::setError('pallet_multiplier', 'Please enter only positive whole numbers');
            }
            $add_to_location = 0;
            $post_data['add_to_location'] = $this->location->receiving_id;
        }
        elseif($add_to_location == "0")
        {
            Form::setError('add_to_location', 'Please select a location');
        }
        else
        {
            $to_receiving = 0;
        }
        if($reason_id == "0")
        {
            Form::setError('reason_id', 'Please select a reason for adding stock');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
            Session::set('adderrorfeedback', 'Errors were found in the form. Please correct where shown and resubmit');
        }
        else
        {
            //echo "<pre>",print_r($post_data),"</pre>"; die();
            $this->location->addToLocation($post_data);
            $this->clientsbays->stockAdded($client_id, $add_to_location, $to_receiving, $pallet_multiplier, isset($oversize));
            if($this->client->isDeliveryClient($client_id))
                $this->deliveryclientsbay->stockAdded([
                    'client_id'     => $client_id,
                    'location_id'   => $add_to_location,
                    'size'          => $pallet_size,
                    'item_id'       => $add_product_id
                ]);
            Session::set('addfeedback', $add_product_name.' has had '.$qty_add.' added to its count');
        }
        return $this->redirector->to(PUBLIC_ROOT."inventory/add-subtract-stock/product=".$add_product_id."#add");
    }

    public function procAddShipmentPackage()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if(!$this->dataSubbed($width) || !$this->dataSubbed($height) || !$this->dataSubbed($depth) || !$this->dataSubbed($weight) || !$this->dataSubbed($count))
        {
            Session::set('packageerrorfeedback', 'All fields must have a value<br/>Package has NOT been added');
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        elseif( (filter_var($width, FILTER_VALIDATE_FLOAT) === false || $width <= 0) || (filter_var($height, FILTER_VALIDATE_FLOAT) === false || $height <= 0) || (filter_var($depth, FILTER_VALIDATE_FLOAT) === false || $depth <= 0) || (filter_var($weight, FILTER_VALIDATE_FLOAT) === false || $weight <= 0) || (filter_var($count, FILTER_VALIDATE_INT) === false || $count <= 0) )
        {
            Session::set('packageerrorfeedback', 'All values must have a positive number<br/>Package has NOT been added');
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $package = (isset($pallet))? "pallet" : "package";
            if($id = $this->productionjobsshipment->addPackage($post_data))
            {
                if($count > 1)
                {
                    Session::set('packagefeedback', "Those ".$package."s have been added. They should be showing below");

                }
                else
                {
                    Session::set('packagefeedback', "That $package has been added. It should be showing below");
                }
            }
            else
            {
                Session::set('packageerrorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."jobs/create-shipment/job=".$job_id."#packages");
    }

    public function procAddPackage()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if(!$this->dataSubbed($width) || !$this->dataSubbed($height) || !$this->dataSubbed($depth) || !$this->dataSubbed($weight) || !$this->dataSubbed($count))
        {
            Session::set('packageerrorfeedback', 'All fields must have a value<br/>Package has NOT been added');
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        elseif( (filter_var($width, FILTER_VALIDATE_FLOAT) === false || $width <= 0) || (filter_var($height, FILTER_VALIDATE_FLOAT) === false || $height <= 0) || (filter_var($depth, FILTER_VALIDATE_FLOAT) === false || $depth <= 0) || (filter_var($weight, FILTER_VALIDATE_FLOAT) === false || $weight <= 0) || (filter_var($count, FILTER_VALIDATE_INT) === false || $count <= 0) )
        {
            Session::set('packageerrorfeedback', 'All values must have a positive number<br/>Package has NOT been added');
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $package = (isset($pallet))? "pallet" : "package";
            if($id = $this->order->addPackage($post_data))
            {
                if($count > 1)
                {
                    Session::set('packagefeedback', "Those ".$package."s have been added. They should be showing below");

                }
                else
                {
                    Session::set('packagefeedback', "That $package has been added. It should be showing below");
                }
            }
            else
            {
                Session::set('packageerrorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."orders/order-update/order=".$order_id."#package");
    }

    public function procItemsUpdate()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if(!isset($this->request->data['items']))
        {
            Form::setError('items', 'At least one item must be selected');
        }
        else
        {
            $orders_items = array();
            $error = false;
            foreach($this->request->data['items'] as $itid => $details)
            {
                if( !isset($details['qty']) || $details['qty'] == 0 )
                {
                    $error = true;
                    Form::setError('items', 'Please ensure all items have a quantity');
                    break;
                }
                if(!isset($details['id']))
                {
                    $error = true;
                    Form::setError('items', 'There has been an error recognising an item');
                    break;
                }
                if(!$error)
                {
                    $array = array(
                        'qty'   => $details['qty'],
                        'id'    => $details['id']
                    );
                    $array['whole_pallet'] = isset($details['whole_pallet']);
                    $orders_items[] = $array ;
                }

            }
            //echo "<pre>orders_items",print_r($orders_items),"</pre>"; //die();
            $item_array = array(
                $order_id => $orders_items
            );
            //echo "<pre>item_array",print_r($item_array),"</pre>"; //die();
            $oitems = $this->allocations->createOrderItemsArray($item_array, $order_id);
            //echo "<pre>oitems",print_r($oitems),"</pre>"; die();

            foreach($oitems[$order_id] as $item)
            {
                //echo "<pre>",print_r($items),"</pre>"; die();
                //foreach($items as $item)
                //{
                    if($item['item_error'])
                    {
                        Form::setError('items', $item['item_error_string']);
                        Session::set('errorfeedback', '<ul>'.$item['item_error_string'].'</ul>');
                        Session::set('value_array', $_POST);
                        Session::set('error_array', Form::getErrorArray());
                        return $this->redirector->to(PUBLIC_ROOT."orders/items-update/order=".$order_id);
                    }
                //}
            }
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //echo "<pre>",print_r($oitems['values']),"</pre>"; die();
            if($this->order->updateItemsForOrder($oitems[$order_id], $order_id))
            {
                Session::set('feedback', "Those items have been updated");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."orders/items-update/order=".$order_id);
    }

    public function procAddressUpdate()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !$this->dataSubbed($ship_to) )
        {
            Form::setError('ship_to', 'A ship to name is required');
        }
        $this->validateAddress($address, $suburb, $state, $postcode, $country, isset($ignore_address_error));
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            $this->order->updateOrderAddress($post_data);
            $this->order->removeError($order_id);
            Session::set('feedback', "That address has been updated");
        }
        return $this->redirector->to(PUBLIC_ROOT."orders/address-update/order=".$order_id);
    }

    public function procUserAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }

        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A name is required');
        }
        if(!$this->dataSubbed($email))
        {
            Form::setError('email', 'An email is required');
        }
        elseif( !$this->emailValid($email))
        {
            Form::setError('email', 'Please enter a valid email');
        }
        elseif( $this->user->emailTaken($email))
        {
            Form::setError('email', 'This email is already registered');
        }
        if($role_id == 0)
        {
            Form::setError('role_id', 'Please select a role');
        }
        elseif($role_id == $client_role_id)
        {
            if( $client_id == 0 )
            {
                Form::setError('client_id', 'Please select a client');
            }
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //insert the user
            $this->user->addUser($post_data);
            Session::set('feedback', "<p>That user has been added to the system</p>");
            if(!isset($test_user))
            {
                //send the email
                Email::sendNewUserEmail($name, $email);
                $_SESSION['feedback'] .= "<p>password setup instructions have been emailed to $email</p>";
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."user/add-user");
    }

    public function procProfileUpdate()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'Your name is required');
        }
        //image uploads
        $field = "image";
        if($_FILES[$field]["size"] > 0)
        {
            if(getimagesize($this->request->data[$field]['tmp_name']) !== false)
            {
                $filename = pathinfo($this->request->data[$field]['name'], PATHINFO_FILENAME);
                $image_name = preg_replace("/[^A-Za-z0-9 ]/", '', $filename);//strip out non alphanumeric characters
                $image_name = strtolower(str_replace(' ','_',$image_name));
                //main image
                $image_name = $this->uploadImage($field, 200, 200, $image_name, 'jpg', false, 'profile_pictures/');
                //thumbnail image
                //$this->uploadImage($field, 100, false, "tn_".$image_name, 'jpg', false, 'products/');
                $post_data['image_name'] = $image_name;
            }
            else
            {
                Form::setError($field, 'Only upload images here');
            }
        }
        elseif($_FILES[$field]['error']  !== UPLOAD_ERR_NO_FILE)
        {
            $error_message = $this->file_upload_error_message($_FILES[$field]['error']);
            Form::setError($field, $error_message);
        }
        if($this->dataSubbed($new_password))
        {
            if(!$this->dataSubbed($conf_new_password))
            {
                Form::setError('conf_new_password', 'Please retype new password for confirmation');
            }
            elseif($conf_new_password !== $new_password)
            {
                Form::setError('conf_new_password', 'Passwords do not match');
            }
            else
            {
                $post_data['hashed_password'] = password_hash($new_password, PASSWORD_DEFAULT, array('cost' => Config::get('HASH_COST_FACTOR')));
            }
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
            return $this->redirector->to(PUBLIC_ROOT . "login/resetPassword", ['id' => $this->request->data("id"), 'token' => $this->request->data("token")]);
        }
        else
        {
            $this->user->updateProfileInfo($post_data, Session::getUserId());
            //reset some session data
            Session::reset([
                "user_id"       => Session::getUserId(),
                "role"          => $this->user->getUserRoleName($role_id),
                "ip"            => $this->request->clientIp(),
                "user_agent"    => $this->request->userAgent(),
                "users_name"    => $name,
                "client_id"     => $client_id,
                "is_admin_user" => $this->user->isAdminUser(),
                "is_production_user"    => $this->user->isProductionUser(),
                "is_warehouse_user"     => $this->user->isWarehouseUser()
            ]);
            //set the cookie to remember the user
            Cookie::reset(Session::getUserId());
        }
        return $this->redirector->to(PUBLIC_ROOT."user/profile");
    }

    public function procUserRoleEdit()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        $id = $this->request->data['line_id'];
        $post_data = array('id' => $id);
        foreach($this->request->data as $field => $value)
        {
            $field = strtok($field, "_");
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        //echo "<pre>",print_r($post_data),"</pre>"; die();
        $name = strtolower($name);
        $currentname = strtolower($currentname);
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name_'.$id, 'A name is required');
        }
        elseif($this->user->getUserRoleId($name) && $name != $currentname)
        {
            Form::setError('name_'.$id, 'User roles need a unique name');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
            if($this->user->editUserRole($post_data))
            {
                Session::set('feedback', "Those details have been updated");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."site-settings/user-roles");
    }

    public function procUserRoleAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $name = strtolower($name);
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A name is required');
        }
        elseif($this->user->getUserRoleId($name))
        {
            Form::setError('name', 'User roles need a unique name');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
            if($role_id = $this->user->addUserRole($post_data))
            {
                Session::set('feedback', "That role has been added to the system");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."site-settings/user-roles");
    }

    public function procStoreEdit()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if($chain_id == "0")
        {
            Form::setError('chain_id', "A chain must be chosen");
        }
        if( !$this->dataSubbed($store_number) && $store_number != "0" )
        {
            Form::setError('name', 'A store number is required');
        }
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A name is required');
        }
        if( $this->dataSubbed($contact_email) )
        {
            if(!$this->emailValid($contact_email))
            {
                Form::setError('contact_email', 'The email is not valid');
            }
        }
        $this->validateAddress($address, $suburb, $state, $postcode, $country, isset($ignore_address_error));
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
            if($store_id = $this->store->editStore($post_data))
            {
                Session::set('feedback', "That store has been updated");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."stores/edit-store/store=$store_id");
    }

    public function procStoreAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if($chain_id == "0")
        {
            Form::setError('chain_id', "A chain must be chosen");
        }
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A name is required');
        }
        if( !$this->dataSubbed($store_number) && $store_number != "0" )
        {
            Form::setError('name', 'A store number is required');
        }
        if( $this->dataSubbed($contact_email) )
        {
            if(!$this->emailValid($contact_email))
            {
                Form::setError('contact_email', 'The email is not valid');
            }
        }
        $this->validateAddress($address, $suburb, $state, $postcode, $country, isset($ignore_address_error));
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
            if($store_id = $this->store->addStore($post_data))
            {
                Session::set('feedback', "That store has been added to the system");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."stores/edit-store/store=$store_id");
    }

    public function procStoreChainEdit()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        $id = $this->request->data['line_id'];
        $post_data = array('id' => $id);
        foreach($this->request->data as $field => $value)
        {
            $field = strtok($field, "_");
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }

        if( !$this->dataSubbed($name) )
        {
            Form::setError('name_'.$id, 'A name is required');
        }
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A name is required');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
            if($this->storechain->editChain($post_data))
            {
                Session::set('feedback', "Those details have been updated");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."site-settings/store-chains");
    }

    public function procSolarTypeEdit()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        $id = $this->request->data['line_id'];
        $post_data = array('id' => $id);
        foreach($this->request->data as $field => $value)
        {
            $field = strtok($field, "_");
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }

        if( !$this->dataSubbed($name) )
        {
            Form::setError('name_'.$id, 'A name is required');
        }
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A name is required');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
            if($this->solarordertype->editType($post_data))
            {
                Session::set('feedback', "Those details have been updated");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."site-settings/solar-order-types");
    }

    public function procCourierEdit()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        $id = $this->request->data['line_id'];
        $post_data = array('id' => $id);
        foreach($this->request->data as $field => $value)
        {
            $field = strtok($field, "_");
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }

        if( !$this->dataSubbed($name) )
        {
            Form::setError('name_'.$id, 'A name is required');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
            if($this->courier->editCourier($post_data))
            {
                Session::set('feedback', "Those details have been updated");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."site-settings/couriers");
    }

    public function procStoreChainAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A name is required');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
            if($chain_id = $this->storechain->addChain($post_data))
            {
                Session::set('feedback', "That chain has been added to the system");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."site-settings/store-chains");
    }

    public function procSolarTypeAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A name is required');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
            if($type_id = $this->solarordertype->addType($post_data))
            {
                Session::set('feedback', "That solar type has been added to the system");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."site-settings/solar-order-types");
    }

    public function procCourierAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A name is required');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
            if($courier_id = $this->courier->addCourier($post_data))
            {
                Session::set('feedback', "That chain has been added to the system");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."site-settings/couriers");
    }

    public function procConfigAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A name is required');
        }
        if( !$this->dataSubbed($value) )
        {
            Form::setError('value', 'A value is required');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add/edit details
            $db = Database::openConnection();
            $post_data['value'] = Encryption::encryptStringBase64($rawvalue);
            if($updater = $db->queryValue('configuration', array('name' => $name)))
            {
                $post_data['id'] = $updater;
                $this->configuration->editConfiguration($post_data);
                Session::set('feedback', "That data has been updated to the system");
            }
            else
            {
                $this->configuration->addConfiguration($post_data) ;
                Session::set('feedback', "That data has been added to the system");
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."admin-only/update-configuration");
    }

    public function procRepAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A name is required');
        }
        if( !$this->dataSubbed($email) )
        {
            Form::setError('email', 'An email is required');
        }
        elseif(!$this->emailValid($email))
        {
            Form::setError('email', 'The email is not valid');
        }
        if( !$this->dataSubbed($phone) )
        {
            Form::setError('phone', 'A phone number is required');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
            if($rep_id = $this->salesrep->addRep($post_data))
            {
                Session::set('feedback', "That rep has been added to the system");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."sales-reps/edit-rep/rep=$rep_id");
    }

    public function procSolarTeamEdit()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A name is required');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
            Session::set('feedback', "Those details have been updated");
            $this->solarteam->editTeam($post_data);
        }
        return $this->redirector->to(PUBLIC_ROOT."solar-teams/edit-team/team=$team_id");
    }

    public function procRepEdit()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; //die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A name is required');
        }
        if( !$this->dataSubbed($email) )
        {
            Form::setError('email', 'An email is required');
        }
        elseif(!$this->emailValid($email))
        {
            Form::setError('email', 'The email is not valid');
        }
        if( !$this->dataSubbed($phone) )
        {
            Form::setError('phone', 'A phone number is required');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
            if($this->salesrep->editRep($post_data))
            {
                Session::set('feedback', "Those details have been updated");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."fsg-contacts/edit-contact/contact=$rep_id");
    }

    public function procOrderAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        /*Session::set('value_array', $_POST);
        Session::set('error_array', Form::getErrorArray());
        return $this->redirector->to(PUBLIC_ROOT."orders/add-order");*/
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $store_order = isset($b2b);
        if($client_id == "0")
        {
            Form::setError('client_id', "A client must be chosen");
        }
        if( !$this->dataSubbed($deliver_to) )
        {
            Form::setError('deliver_to', 'A name is required');
        }
        if($this->dataSubbed($tracking_email))
        {
            if(!$this->emailValid($tracking_email))
            {
                Form::setError('tracking_email', 'The tracking email is not valid');
            }
        }
        //file uploads
        if($_FILES['invoice']["size"][0] > 0)
        {
            //echo "<pre>",print_r($_FILES),"</pre>";
            $pdfs = array();
            $file_error = false;
            for($i=0; $i<count($_FILES['invoice']['name']); $i++)
            {
                if ($_FILES['invoice']['error'][$i]  === UPLOAD_ERR_OK)
    			{
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $_FILES['invoice']['tmp_name'][$i]);
                    if($mime != "application/pdf")
                    {
                        Form::setError('invoice', 'Only upload pdf files');
                        $file_error = true;
                    }
                    else
                    {
                        $tmp_name = $_FILES['invoice']["tmp_name"][$i];
                        $pdfs[] = array(
                        	'file'		    =>	$tmp_name,
                            'orientation'	=>	'P'
                        );
                    }
    			}
    			else
    			{
                	$error_message = $this->file_upload_error_message($_FILES['invoice']['error'][$i]);
                    Form::setError('invoice', $error_message);
                    $file_error = true;
    			}
            }
            if(!$file_error)
            {
                $upcount = 1;
                $filename = "invoice";
                $name = "invoice.pdf";
                $upload_dir = "/client_uploads/$client_id/";
                if ( ! is_dir(DOC_ROOT.$upload_dir))
                            mkdir(DOC_ROOT.$upload_dir);
    			while(file_exists(DOC_ROOT.$upload_dir.$name))
                {
                    $name = $filename."_".$upcount.".pdf";
                    ++$upcount;
                }
                $pdf = new Mympdf();
                $pdf->mergePDFFilesToServer($pdfs, $name, DOC_ROOT.$upload_dir);
                $post_data['uploaded_file'] = $name;
            }

        }
        $this->validateAddress($address, $suburb, $state, $postcode, $country, isset($ignore_address_error));
        if(!isset($this->request->data['items']))
        {
            Form::setError('items', 'At least one item must be selected');
        }
        else
        {
            $orders_items = array();
            $error = false;
            foreach($this->request->data['items'] as $itid => $details)
            {
				if( !isset($details['qty']) || $details['qty'] == 0 )
				{
                    $error = true;
                    Form::setError('items', 'Please ensure all items have a quantity');
                    break;
				}
				if(!isset($details['id']))
				{
					$error = true;
                    Form::setError('items', 'There has been an error recognising an item');
                    break;
				}
				if(!$error)
				{
					$array = array(
						'qty'   => $details['qty'],
						'id'    => $details['id']
					);
                    $array['whole_pallet'] = isset($details['whole_pallet']);
					$orders_items[] = $array ;
				}

            }
            //echo "<pre>",print_r($orders_items),"</pre>"; die();
            if(count($orders_items) == 0)
            {
                Form::setError('items', 'At least one item must be selected');
            }
            elseif(!$error)
            {
                $the_items = array(
                    0 => $orders_items
                );
                $oitems = $this->allocations->createOrderItemsArray($the_items, 0, $store_order);
                foreach($oitems[0] as $item)//there is only one order
                {
                    if($item['import_error'])
                    {
                        Form::setError('items', $item['import_error_string']);
                    }
                }
            }
			/*
            else
            {
                Form::setError('items', 'Please check your items');
            }
			*/
        }

        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
            //deal with the customer
            if($customer_id == 0)
            {
                //add the customer
                $post_data['customer_id'] = $this->customer->addCustomer($post_data);
            }
            else
            {
                //edit the customer
                $this->customer->editCustomer($post_data);
            }
            //add the order
            $order_number = $this->order->addOrder($post_data, $oitems);
            Session::set('feedback', "An order with number: <strong>$order_number</strong> has been created");
        }
        return $this->redirector->to(PUBLIC_ROOT."orders/add-order");
    }

    public function procPackTypeAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A name is required');
        }
        if($this->dataSubbed($width))
        {
            if(filter_var($width, FILTER_VALIDATE_FLOAT) === false && $width <= 0)
            {
            	Form::setError('width', 'Please enter a valid positive number');
            }
        }
        else
        {
            $post_data['width'] = 0;
        }
        if($this->dataSubbed($depth))
        {
            if(filter_var($depth, FILTER_VALIDATE_FLOAT) === false && $depth <= 0)
            {
            	Form::setError('depth', 'Please enter a valid positive number');
            }
        }
        else
        {
            $post_data['depth'] = 0;
        }
        if($this->dataSubbed($height))
        {
            if(filter_var($height, FILTER_VALIDATE_FLOAT) === false && $height <= 0)
            {
            	Form::setError('height', 'Please enter a valid positive number');
            }
        }
        else
        {
            $post_data['height'] = 0;
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
            if($this->packingtype->addType($post_data))
            {
                Session::set('feedback', "That has been added to the system");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }

        }
        return $this->redirector->to(PUBLIC_ROOT."site-settings/packing-types");

    }

    public function procPackTypeEdit()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $id = $this->request->data['line_id'];
        $post_data = array('id' => $id);
        foreach($this->request->data as $field => $value)
        {
            $field = strtok($field, "_");
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }

        if( !$this->dataSubbed($name) )
        {
            Form::setError('name_'.$id, 'A name is required');
        }
        if($this->dataSubbed($width))
        {
            if(filter_var($width, FILTER_VALIDATE_FLOAT) === false && $width <= 0)
            {
            	Form::setError('width_'.$id, 'Please enter a valid positive number');
            }
        }
        else
        {
            $post_data['width'] = 0;
        }
        if($this->dataSubbed($depth))
        {
            if(filter_var($depth, FILTER_VALIDATE_FLOAT) === false && $depth <= 0)
            {
            	Form::setError('depth', 'Please enter a valid positive number');
            }
        }
        else
        {
            $post_data['depth'] = 0;
        }
        if($this->dataSubbed($height))
        {
            if(filter_var($height, FILTER_VALIDATE_FLOAT) === false && $height <= 0)
            {
            	Form::setError('height_'.$id, 'Please enter a valid positive number');
            }
        }
        else
        {
            $post_data['height'] = 0;
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
           $this->packingtype->editType($post_data) ;
           Session::set('feedback', "The details for {$name} have been updated");
        }
        return $this->redirector->to(PUBLIC_ROOT."site-settings/packing-types");

    }

    public function procClientAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !$this->dataSubbed($client_name) )
        {
            Form::setError('client_name', 'A client name is required');
        }
        if( !$this->dataSubbed($ref_1) )
        {
            Form::setError('ref_1', 'A courier Reference is required');
        }
        if(!$this->emailValid($billing_email))
        {
            Form::setError('billing_email', 'Please enter a valid email address');
        }
        if(!$this->emailValid($sales_email))
        {
            Form::setError('sales_email', 'Please enter a valid email address');
        }
        if(!$this->emailValid($inventory_email))
        {
            Form::setError('inventory_email', 'Please enter a valid email address');
        }
        if(!$this->emailValid($deliveries_email))
        {
            Form::setError('deliveries_email', 'Please enter a valid email address');
        }
        $this->validateAddress($address, $suburb, $state, $postcode, $country, isset($ignore_address_error));
        //image uploads
        $field = "client_logo";
        if($this->request->data[$field]["size"] > 0)
        {
            if(getimagesize($this->request->data[$field]['tmp_name']) !== false)
            {
                $filename = pathinfo($this->request->data[$field]['name'], PATHINFO_FILENAME);
                $image_name = preg_replace("/[^A-Za-z0-9 ]/", '', $filename);//strip out non alphanumeric characters
                $image_name = strtolower(str_replace(' ','_',$image_name));
                //main image
                $image_name = $this->uploadImage($field, 180, 100, $image_name, 'jpg', false, 'client_logos/');
                //thumbnail image
                $this->uploadImage($field, 100, false, "tn_".$image_name, 'jpg', false, 'client_logos/');
                $post_data['image_name'] = $image_name;
            }
            else
            {
                Form::setError($field, 'Only upload images here');
            }
        }
        elseif($_FILES[$field]['error']  !== UPLOAD_ERR_NO_FILE)
        {
            $error_message = $this->file_upload_error_message($_FILES[$field]['error']);
            Form::setError($field, $error_message);
        }


        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //Session::set('feedback', "$client_name has been added to the system");
            /*  */
            //all good, add details
            if($client_id = $this->client->addClient($post_data))
            {
                Session::set('feedback', "$client_name has been added to the system");
                return $this->redirector->to(PUBLIC_ROOT."clients/edit-client/client=".$client_id);
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }

        }
        return $this->redirector->to(PUBLIC_ROOT."clients/add-client/");
    }

    public function procClientEdit()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
       //echo "<pre>",print_r($post_data),"</pre>"; //die();
       //echo "Client Name: ".$client_name;die();
       /*
        if($this->dataSubbed($pallet_charge))
        {
            if(!preg_match("/\b\d{1,3}(?:,?\d{3})*(?:\.\d{2})?\b/", $pallet_charge))
            {
                Form::setError('pallet_charge', 'Please enter a valid dollar amount');
            }
        }
        if($this->dataSubbed($carton_charge))
        {
            if(!preg_match("/\b\d{1,3}(?:,?\d{3})*(?:\.\d{2})?\b/", $carton_charge))
            {
                Form::setError('carton_charge', 'Please enter a valid dollar amount');
            }
        }
        if($this->dataSubbed($truck_charge))
        {
            if(!preg_match("/\b\d{1,3}(?:,?\d{3})*(?:\.\d{2})?\b/", $truck_charge))
            {
                Form::setError('truck_charge', 'Please enter a valid dollar amount');
            }
        }
        if($this->dataSubbed($ute_charge))
        {
            if(!preg_match("/\b\d{1,3}(?:,?\d{3})*(?:\.\d{2})?\b/", $ute_charge))
            {
                Form::setError('ute_charge', 'Please enter a valid dollar amount');
            }
        }
        */
        if( !$this->dataSubbed($client_name) )
        {
            Form::setError('client_name', 'A client name is required');
        }
        if( !$this->dataSubbed($ref_1) )
        {
            Form::setError('ref_1', 'A courier Reference is required');
        }
        if(!$this->emailValid($billing_email))
        {
            Form::setError('billing_email', 'Please enter a valid email address');
        }
        if(!$this->emailValid($sales_email))
        {
            Form::setError('sales_email', 'Please enter a valid email address');
        }
        if(!$this->emailValid($inventory_email))
        {
            Form::setError('inventory_email', 'Please enter a valid email address');
        }
        if(!$this->emailValid($deliveries_email))
        {
            Form::setError('deliveries_email', 'Please enter a valid email address');
        }
        $this->validateAddress($address, $suburb, $state, $postcode, $country, isset($ignore_address_error));
        //image uploads
        $field = "client_logo";
        if($this->request->data[$field]["size"] > 0)
        {
            if(getimagesize($this->request->data[$field]['tmp_name']) !== false)
            {
                $filename = pathinfo($this->request->data[$field]['name'], PATHINFO_FILENAME);
                $image_name = preg_replace("/[^A-Za-z0-9 ]/", '', $filename);//strip out non alphanumeric characters
                $image_name = strtolower(str_replace(' ','_',$image_name));
                //main image
                $image_name = $this->uploadImage($field, 180, 100, $image_name, 'jpg', false, 'client_logos/');
                //thumbnail image
                $this->uploadImage($field, 100, false, "tn_".$image_name, 'jpg', false, 'client_logos/');
                $post_data['image_name'] = $image_name;
            }
            else
            {
                Form::setError($field, 'Only upload images here');
            }
        }
        elseif($_FILES[$field]['error']  !== UPLOAD_ERR_NO_FILE)
        {
            $error_message = $this->file_upload_error_message($_FILES[$field]['error']);
            Form::setError($field, $error_message);
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, update details
            if($this->client->updateClientInfo($post_data))
            {
                Session::set('feedback', "Those details have been updated");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }

        }
        return $this->redirector->to(PUBLIC_ROOT."clients/edit-client/client=".$client_id);
    }

    public function procForgotPassword()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        $email      = $this->request->data('email');
        $userIp     = $this->request->clientIp();
        $userAgent  = $this->request->userAgent();
        Session::set('display-form', 'forgot-password');
        $db = Database::openConnection();
        if(!$this->dataSubbed($email))
        {
            Form::setError('email', 'Please enter your email address');
        }
        elseif(!$this->emailValid($email))
        {
            Form::setError('email', 'Please enter a valid email address');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
		{
		    Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
		}
        else
        {
            if($db->fieldValueTaken('users', $email, 'email'))
            {
                //die('email found');
                //only do stuf if the email exists in the system
                $user     = $db->queryRow("SELECT * FROM users WHERE email = :email", array('email' => $email));
                $forgottenPassword = $db->queryRow("SELECT * FROM forgotten_passwords WHERE user_id = ".$user['id']);
                $last_time = isset($forgottenPassword["password_last_reset"])? $forgottenPassword["password_last_reset"]: null;
                $count     = isset($forgottenPassword["forgotten_password_attempts"])? $forgottenPassword["forgotten_password_attempts"]: null;
                $block_time = (10 * 60);
                $time_elapsed = time() - $last_time;
                if ($count >= 5 && $time_elapsed < $block_time)
                {
                    Form::setError('toomanytimes', "You exceeded number of possible attempts, please try again later after " .date("i", $block_time - $time_elapsed) . " minutes");
                    Session::set('value_array', $_POST);
                    Session::set('error_array', Form::getErrorArray());
                    return $this->redirector->login();
                }
                $newPasswordToken = $this->login->generateForgottenPasswordToken($user["id"], $forgottenPassword);
                if(!Email::sendPasswordReset($user['id'], $user['name'], $email, $newPasswordToken))
                {
                    die('mail error');
                }
            }
            Session::set('feedback', "<p>An email has been sent with a reset password link. This link will remain valid for 24 hours</p>");
        }
        return $this->redirector->login();
    }

    public function procLogin()
    {
        //echo "<pre>",print_r($this->request),"</pre>";die();
        $email      = $this->request->data('email');
        $password   = $this->request->data('password');
        $userIp     = $this->request->clientIp();
        $redirect   = $this->request->data("redirect");
        $userAgent  = $this->request->userAgent();
        if($this->login->isIpBlocked($userIp))
        {
            Form::setError("general","Your IP Address has been blocked");
        }
        if(!$this->dataSubbed($email))
        {
            Form::setError('email', 'Please enter your email address');
        }
        elseif(!$this->emailValid($email))
        {
            Form::setError('email', 'Please enter a valid email address');
        }
        elseif( !$this->user->isUserActive($email) )
        {
            Form::setError('general', 'Sorry, either your email address is not registered in our system, or your account has been deactivated');
        }
        elseif(!$this->login->isLoginAttemptAllowed($email))
        {
            Form::setError('general', "You exceeded number of possible attempts, please try again later after " .$this->login->getMinutesBeforeLogin($email) . " minutes");
        }
        else
        {
            $user = $this->user->getUserByEmail($email);
            $userId = isset($user["id"])? $user["id"]: null;
        }
        if(!$this->dataSubbed($password))
        {
            Form::setError('password', 'Please enter your password');
        }
        if(Form::$num_errors == 0):		/* No entry errors */
            if(password_verify($password, $user["hashed_password"]) === false)
            {
                Form::setError("general","Email and Password combination was not found");
                $this->login->handleIpFailedLogin($userIp, $email);
            }
        endif;
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
		{
		    Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
			return $this->redirector->login($redirect);
		}
        else
        {
            //echo "<pre>",print_r($this->request),"</pre>"; die();
            // reset session
            Session::reset([
                "user_id"       => $userId,
                "role"          => $this->user->getUserRoleName($user["role_id"]),
                "ip"            => $userIp,
                "user_agent"    => $userAgent,
                "users_name"    => $user['name'],
                "client_id"     => $user['client_id'],
                "is_admin_user" => $this->user->isAdminUser($userId),
                "is_production_user"    => $this->user->isProductionUser($userId),
                "is_warehouse_user"     => $this->user->isWarehouseUser($userId)
            ]);
            //set the cookie to remember the user
            Cookie::reset($userId);

            $this->login->resetFailedLogins($email);
            $this->login->resetPasswordToken($userId);
            $redirect = ltrim($redirect, "/");
            return $this->redirector->root($redirect);
        }
    }

    public function procProductAdd()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A product name is required');
        }
        if( !$this->dataSubbed($sku) )
        {
            Form::setError('sku', 'An SKU is required');
        }
        elseif($this->item->skuTaken($sku))
        {
            Form::setError('sku', 'This SKU is already in use');
        }

        if($this->dataSubbed($barcode))
        {
            if($this->item->barcodeTaken($barcode))
            {
                Form::setError('barcode', 'This barcode is already in use');
            }
        }
        if($this->dataSubbed($box_barcode))
        {
            if($this->item->boxBarcodeTaken($box_barcode))
            {
                Form::setError('box_barcode', 'This barcode is already in use');
            }
            if(!$this->dataSubbed($per_box))
            {
                Form::setError('per_box', 'A number per box is required if a Box barcode is given');
            }
            elseif(filter_var($per_box, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1))) === false)
            {
                Form::setError('per_box', 'Only positive whole numbers for count per box');
            }
        }
        if( $this->dataSubbed($weight) )
        {
            if(filter_var($weight, FILTER_VALIDATE_FLOAT) === false && $weight <= 0)
            {
                Form::setError('weight', 'Please enter a valid positive number');
            }
        }
        else
        {
            $post_data['weight'] = 0;
        }
        if($this->dataSubbed($width))
        {
            if(filter_var($width, FILTER_VALIDATE_FLOAT) === false && $width <= 0)
            {
            	Form::setError('width', 'Please enter a valid positive number');
            }
        }
        else
        {
            $post_data['width'] = 0;
        }
        if($this->dataSubbed($depth))
        {
            if(filter_var($depth, FILTER_VALIDATE_FLOAT) === false && $depth <= 0)
            {
            	Form::setError('depth', 'Please enter a valid positive number');
            }
        }
        else
        {
            $post_data['depth'] = 0;
        }
        if($this->dataSubbed($height))
        {
            if(filter_var($height, FILTER_VALIDATE_FLOAT) === false && $height <= 0)
            {
            	Form::setError('height', 'Please enter a valid positive number');
            }
        }
        else
        {
            $post_data['height'] = 0;
        }
        if($this->dataSubbed($low_stock_warning))
        {
            if(filter_var($low_stock_warning, FILTER_VALIDATE_INT) === false && $low_stock_warning <= 0)
            {
            	Form::setError('low_stock_warning', 'Please enter a positive whole number');
            }
        }
        else
        {
            $post_data['low_stock_warning'] = null;
        }
        if($this->dataSubbed($trigger_point))
        {
            if(filter_var($trigger_point, FILTER_VALIDATE_INT) === false && $trigger_point <= 0)
            {
            	Form::setError('trigger_point', 'Please enter a positive whole number');
            }
        }
        else
        {
            $post_data['trigger_point'] = 0;
        }
        $package_types = array();

        if(!$this->dataSubbed($client_id) || $client_id == "0")
        {
        	Form::setError('client_id', 'A client must be selected');
        }
        if($this->dataSubbed($price))
        {
        	if(filter_var($price, FILTER_VALIDATE_FLOAT) === false && $price <= 0)
            {
            	Form::setError('price', 'Please enter a valid dollar amount');
            }
        }
        $palletizedd = (isset($palletized))? 1:0;
        $post_data['palletized'] = $palletizedd;
        $boxed_itemd = (isset($boxed_item))? 1:0;
        $post_data['boxed_item'] = $boxed_itemd;
        //image uploads
        $field = "image";
        if($_FILES[$field]["size"] > 0)
        {
            if(getimagesize($this->request->data[$field]['tmp_name']) !== false)
            {
                $filename = pathinfo($this->request->data[$field]['name'], PATHINFO_FILENAME);
                $image_name = preg_replace("/[^A-Za-z0-9 ]/", '', $filename);//strip out non alphanumeric characters
                $image_name = strtolower(str_replace(' ','_',$image_name));
                //main image
                $image_name = $this->uploadImage($field, 180, false, $image_name, 'jpg', false, 'products/');
                //thumbnail image
                $this->uploadImage($field, 100, false, "tn_".$image_name, 'jpg', false, 'products/');
                $post_data['image_name'] = $image_name;
            }
            else
            {
                Form::setError($field, 'Only upload images here');
            }
        }
        elseif($_FILES[$field]['error']  !== UPLOAD_ERR_NO_FILE)
        {
            $error_message = $this->file_upload_error_message($_FILES[$field]['error']);
            Form::setError($field, $error_message);
        }

        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {

            /* */
            //all good, add details
            if($product_id = $this->item->addItem($post_data))
            {
                Session::set('feedback', "{$name}'s details have been added to the system");
                //$this->item->addPackingTypesForItem($package_types, $product_id);
                return $this->redirector->to(PUBLIC_ROOT."products/edit-product/product=".$product_id);
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."products/add-product");
    }

    public function procProductEdit()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if( !$this->dataSubbed($name) )
        {
            Form::setError('name', 'A product name is required');
        }
        if( !$this->dataSubbed($sku) )
        {
            Form::setError('sku', 'An SKU is required');
        }
        elseif($this->item->skuTaken($sku) && $sku != $current_sku)
        {
            Form::setError('sku', 'This SKU is already in use');
        }

        if($this->dataSubbed($barcode))
        {
            if($this->item->barcodeTaken($barcode) && $barcode != $current_barcode)
            {
                Form::setError('barcode', 'This barcode is already in use');
            }
        }
        if($this->dataSubbed($box_barcode))
        {
            if($this->item->boxBarcodeTaken($box_barcode) && $box_barcode != $current_box_barcode)
            {
                Form::setError('box_barcode', 'This barcode is already in use');
            }
            if(!$this->dataSubbed($per_box))
            {
                Form::setError('per_box', 'A number per box is required if a Box barcode is given');
            }
            elseif(filter_var($per_box, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1))) === false)
            {
                Form::setError('per_box', 'Only positive whole numbers for count per box');
            }
        }
        if($this->dataSubbed($weight))
        {
            if(filter_var($weight, FILTER_VALIDATE_FLOAT) === false && $weight <= 0)
            {
            	Form::setError('width', 'Please enter a valid positive number');
            }
        }
        else
        {
            $post_data['weight'] = 0;
        }
        if($this->dataSubbed($width))
        {
            if(filter_var($width, FILTER_VALIDATE_FLOAT) === false && $width <= 0)
            {
            	Form::setError('width', 'Please enter a valid positive number');
            }
        }
        else
        {
            $post_data['width'] = 0;
        }
        if($this->dataSubbed($depth))
        {
            if(filter_var($depth, FILTER_VALIDATE_FLOAT) === false && $depth <= 0)
            {
            	Form::setError('depth', 'Please enter a valid positive number');
            }
        }
        else
        {
            $post_data['depth'] = 0;
        }
        if($this->dataSubbed($height))
        {
            if(filter_var($height, FILTER_VALIDATE_FLOAT) === false && $height <= 0)
            {
            	Form::setError('height', 'Please enter a valid positive number');
            }
        }
        else
        {
            $post_data['height'] = 0;
        }
        if($this->dataSubbed($low_stock_warning))
        {
            if(filter_var($low_stock_warning, FILTER_VALIDATE_INT) === false && $low_stock_warning <= 0)
            {
            	Form::setError('low_stock_warning', 'Please enter a positive whole number');
            }
        }
        else
        {
            $post_data['low_stock_warning'] = null;
        }
        if($this->dataSubbed($trigger_point))
        {
            if(filter_var($trigger_point, FILTER_VALIDATE_INT) === false && $trigger_point <= 0)
            {
            	Form::setError('trigger_point', 'Please enter a positive whole number');
            }
        }
        else
        {
            $post_data['trigger_point'] = 0;
        }
        $package_types = array();
        if(isset($this->request->data['package_type']) && count($this->request->data['package_type']))
        {
            foreach($this->request->data['package_type'] as $key => $type_id)
            {
                $package = array(
                    'id'        =>  $type_id,
                    'multiple'  =>  false,
                    'number'    =>  1
                );
                if($this->packingtype->isMultiple($type_id))
                {
                    if( (filter_var(${'number_in_' . $type_id}, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1))) === false) )
                    {
                        Form::setError('package_type', 'Only whole positive numbers');
                    }
                    else
                    {
                        $package['multiple'] = true;
                        $package['number'] = 1 / ${'number_in_' . $type_id};
                    }
                }
                $package_types[] = $package;
            }
        }
        if( (isset($double_bay) && !$this->item->isDoubleBayItem($item_id)) )
        {
            $locations = $this->item->getLocationsForItem($item_id);
            //echo "<pre>",print_r($locations),"</pre>"; die();
            foreach($locations as $l)
            {
                $chosen_location = $this->location->getLocationName($l['location_id']);
                if( !preg_match("/\d{1,2}\.\d{1,2}\.\w{1}\.a/i", $chosen_location) )
                {
                    Form::setError('double_bay', 'Double Bay items can only go into \'a\' locations<br/>You will need to move it first');
                }
                else
                {
                    $next_location = substr($chosen_location, 0, -1)."b";
                    $next_location_id = $this->location->getLocationId($next_location);
                    if(!$next_location_id)
                    {
                        Form::setError('double_bay', 'Error locating the next location for double bay item');
                    }
                    elseif( !$this->location->isEmptyLocation($next_location_id) )
                    {
                        Form::setError('double_bay', 'Double bay items can only go where the next bay is empty<br/>You will need to move something from '. $next_location .' first');
                    }
                }

            }
        }
        if($this->dataSubbed($price))
        {
        	if(filter_var($price, FILTER_VALIDATE_FLOAT) === false && $price <= 0)
            {
            	Form::setError('price', 'Please enter a valid dollar amount');
            }
        }
        $palletizedd = (isset($palletized))? 1:0;
        $post_data['palletized'] = $palletizedd;
        $boxed_itemd = (isset($boxed_item))? 1:0;
        $post_data['boxed_item'] = $boxed_itemd;
        //image uploads
        $field = "image";
        if($_FILES[$field]["size"] > 0)
        {
            if(getimagesize($this->request->data[$field]['tmp_name']) !== false)
            {
                $filename = pathinfo($this->request->data[$field]['name'], PATHINFO_FILENAME);
                $image_name = preg_replace("/[^A-Za-z0-9 ]/", '', $filename);//strip out non alphanumeric characters
                $image_name = strtolower(str_replace(' ','_',$image_name));
                //main image
                $image_name = $this->uploadImage($field, 180, false, $image_name, 'jpg', false, 'products/');
                //thumbnail image
                $this->uploadImage($field, 100, false, "tn_".$image_name, 'jpg', false, 'products/');
                $post_data['image_name'] = $image_name;
            }
            else
            {
                Form::setError($field, 'Only upload images here');
            }
        }
        elseif($_FILES[$field]['error']  !== UPLOAD_ERR_NO_FILE)
        {
            $error_message = $this->file_upload_error_message($_FILES[$field]['error']);
            Form::setError($field, $error_message);
        }

        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, update details
            if($this->item->editItem($post_data))
            {
                $this->item->addPackingTypesForItem($package_types, $item_id);
                Session::set('feedback', "{$name}'s details have been updated in the system");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }


        }
        return $this->redirector->to(PUBLIC_ROOT."products/edit-product/product=$item_id");
    }

    public function procUpdatePassword()
    {
        //echo "<pre>",print_r($this->request),"</pre>";
        $password        = $this->request->data("password");
        $confirmPassword = $this->request->data("confirm_password");
        $userId          = Session::get("user_id_reset_password");

        if(!$this->dataSubbed($password))
        {
            Form::setError('password', 'A new password is required');
        }
        if(!$this->dataSubbed($confirmPassword))
        {
            Form::setError('confirm_password', 'Please retype you password');
        }
        elseif($password !== $confirmPassword)
        {
            Form::setError('confirm_password', 'Passwords do not match');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
		{
		    Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
			return $this->redirector->to(PUBLIC_ROOT . "login/resetPassword", ['id' => $this->request->data("id"), 'token' => $this->request->data("token")]);
		}
        else
        {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT, array('cost' => Config::get('HASH_COST_FACTOR')));
            $this->login->updatePassword($hashedPassword, $userId);
            $this->login->resetPasswordToken($userId);
            // logout, and clear any existing session and cookies
            Session::remove();
            Cookie::remove($userId);
            //return $this->redirector->to(PUBLIC_ROOT."login/passwordUpdated");
            $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/login/", Config::get('LOGIN_PATH') . 'passwordUpdated.php');
        }
    }



    /*********************************************************************************************************************************************************
    *   Helper functions below this
    **********************************************************************************************************************************************************/
    /*******************************************************************
    ** validates addresses
    ********************************************************************/
    public function validateAddress($address, $suburb, $state, $postcode, $country, $ignore_address_error, $prefix = "", $session_var = false)
    {
        if( !$this->dataSubbed($address) )
        {
            if($session_var)
            {
                Session::set($session_var, true);
            }
            Form::setError($prefix.'address', 'An address is required');
        }
        elseif( !$ignore_address_error )
        {
            if( (!preg_match("/(?:[A-Za-z].*?\d|\d.*?[A-Za-z])/i", $address)) && (!preg_match("/(?:care of)|(c\/o)|( co )/i", $address)) )
            {
                if($session_var)
                {
                    Session::set($session_var, true);
                }
                Form::setError($prefix.'address', 'The address must include both letters and numbers');
            }
        }
        if(!$this->dataSubbed($postcode))
        {
            if($session_var)
            {
                Session::set($session_var, true);
            }
            Form::setError($prefix.'postcode', "A delivery postcode is required");
        }
        if(!$this->dataSubbed($country))
        {
            if($session_var)
            {
                Session::set($session_var, true);
            }
            Form::setError($prefix.'country', "A delivery country is required");
        }
        elseif(strlen($country) > 2)
        {
            if($session_var)
            {
                Session::set($session_var, true);
            }
            Form::setError($prefix.'country', "Please use the two letter ISO code");
        }
        elseif($country == "AU")
        {
            if(!$this->dataSubbed($suburb))
    		{
    		    if($session_var)
                {
                    Session::set($session_var, true);
                }
    			Form::setError($prefix.'suburb', "A delivery suburb is required for Australian addresses");
    		}
    		if(!$this->dataSubbed($state))
    		{
    		    if($session_var)
                {
                    Session::set($session_var, true);
                }
    			Form::setError($prefix.'state', "A delivery state is required for Australian addresses");
    		}
            $aResponse = $this->Eparcel->ValidateSuburb($suburb, $state, str_pad($postcode,4,'0',STR_PAD_LEFT));
            $error_string = "";
            if(isset($aResponse['errors']))
            {
                foreach($aResponse['errors'] as $e)
                {
                    $error_string .= $e['message']." ";
                }
            }
            elseif($aResponse['found'] === false)
            {
                $error_string .= "Postcode does not match suburb or state";
            }
            if(strlen($error_string))
            {
                if($session_var)
                {
                    Session::set($session_var, true);
                }
                Form::setError($prefix.'postcode', $error_string);
            }
        }
    }

    /*******************************************************************
    ** validates empty data fields
    ********************************************************************/
	public function dataSubbed($data)
	{
		if(!$data || strlen($data = trim($data)) == 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}//end dataSubbed()

    /*******************************************************************
   ** validates email addresses
   ********************************************************************/
	public function emailValid($email)
	{
		if(!$email || strlen($email = trim($email)) == 0)
		{
         	return false;
      	}
      	else
		{
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        	 /* Check if valid email address
         	$regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i";
         	if(!preg_match($regex,$email))
			{
            	return false;
         	}
         	else
			{
				return true;
			}
            */
      	}
	}//end emailValid()

    /*******************************************************************
   ** Returns human readable errors for file uploads
   ********************************************************************/
	private function file_upload_error_message($error_code) {
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the maximum upload size allowed by the server';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the maximum upload size allowed by the server';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was selected for uploading';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension';
            default:
                return 'Unknown upload error';
        	}
	}



    private function uploadImage($field, $width, $height = false, $picturename = "image", $format = 'jpg', $overwrite = false, $dir = '/images/uploads/')
    {
        //namespace Verot\Upload;
        if ($_FILES[$field]['error']  === UPLOAD_ERR_OK)
        {//////////////////////////////////////////////////////////////////////only if entered?
                //$handle = new Upload($_FILES[$field]);
                $handle = new \Verot\Upload\Upload($_FILES[$field]);
                if($handle->uploaded)
                {
                    //file uploaded.
                    //die($field);
                        //Image settings
                        $handle->image_resize = true;
                        $handle->image_ratio = true;
                        $handle->file_auto_rename = !$overwrite;
                        $handle->file_overwrite = $overwrite;
                        $handle->image_x = $width;
                        if($height)
                        {
                            $handle->image_y = $height;
                            $handle->image_ratio = true;
                        }
                        else
                        {
                            $handle->image_ratio_y = true;
                        }
                        $handle->file_new_name_body = $picturename;
                        $handle->image_convert = $format;
                        $handle->Process(IMAGES.$dir);
                        if(!$handle->processed)
                        {
                            Form::setError($field, $handle->error);
                        }
                        return $handle->file_dst_name_body;
                }
                else
                {
                    //error uploading file
                    Form::setError($field, $handle->error);
                }
        }///end if picture uploaded
        else
        {
            //error uploading file
            $error_message = $this->file_upload_error_message($_FILES[$field]['error']);
            Form::setError($field, $error_message);
        }
    }//end function
}
?>