<?php

/**
 * Form controller
 *
 * processes all forms on thee site
 * @author     Mark Solly <mark.solly@3plplus.com.au>
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


        $this->loadEparcelLocations([
            'Freedom',
            'Nuchev',
            'TTAU'
        ]);

    }

    public function beforeAction(){

        parent::beforeAction();
        $action = $this->request->param('action');
        $actions = [
            'printSwatchLabels',
            'procAddClientLocation',
            'procAddLocation',
            'procAddMiscToOrder',
            'procAddPackage',
            'procAddressUpdate',
            'procAddServiceJob',
            'procAddSerials',
            'procAddSolarInstall',
            'procAddTljOrder',
            'procAddToStock',
            'procBasicProductAdd',
            'procBookPickup',
            'procBreakPacks',
            'procBulkOrderAdd',
            'procClientAdd',
            'procClientDailyReports',
            'procClientEdit',
            'procContainerUnload',
            'procCourierAdd',
            'procCourierEdit',
            'procEditServiceJob',
            'procEditInstall',
            'procEncryptSomeShit',
            'procForgotPassword',
            'procGoodsIn',
            'procGoodsOut',
            'procItemsUpdate',
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
            'procPickupUpdate',
            'procProductAdd',
            'procProductEdit',
            'procProfileUpdate',
            'procQualityControl',
            'procRecordPickup',
            'procReeceDepartmentUpload',
            'procRegisterNewStock',
            'procRepAdd',
            'procRepEdit',
            'procScanToInventory',
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
            'procTruckUsage',
            'procUpdatePassword',
            'procUserAdd',
            'procUserRoleAdd',
            'procUserRoleEdit'
        ];
        $this->Security->config("form", [ 'fields' => ['csrf_token']]);
        $this->Security->requirePost($actions);
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
                if(!$this->dataSubbed($row[9]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>A Department Address is required on line: $line</li>";
                }
                else
                {

                    $department_array['stored_address'] = $row[9];
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
                echo "<pre>",print_r($departments),"</pre>";
                die('will import departments');
                //Do
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

    public function procEditInstall()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>";die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $post_data['install_date'] = $post_data['date'];
        unset($post_data['date']);
        if($team_id == 0)
        {
            Form::setError('team_id', 'Please select a team');
        }
        if(!$this->dataSubbed($work_order))
        {
            Form::setError('work_order', 'A work order number is required');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //all good, add details
            //echo "<pre>Good",print_r($post_data),"</pre>"; die();
            $vals = array(
                "team_id"       => $team_id,
                'work_order'    => $work_order,
                'customer_name' => NULL,
                'install_date'  => $date_value,
                'address'       => $address,
                'address_2'     => NULL,
                'suburb'        => $suburb,
                'state'         => $state,
                'country'       => 'AU',
                'client_id'     => $client_id,
                'type_id'       => $type_id
            );
            if($this->dataSubbed($customer_name))
                $vals['customer_name'] = $customer_name;
            if($this->dataSubbed($address2))
                $vals['address_2'] = $address2;
            Session::set('feedback', "Those details have been updated");
            $this->solarorder->updateOrderValues($vals, $order_id);
        }
        return $this->redirector->to(PUBLIC_ROOT."solar-jobs/update-details/id=".$order_id);
    }

    public function procAddSolarInstall()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>";//die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $items = array();
        if($panel_id > 0)
        {
            $items[] = array(
                'id'    => $panel_id,
                'qty'   => $panel_qty
            );
        }
        if($inverter_id > 0)
        {
            $items[] = array(
                'id'    => $inverter_id,
                'qty'   => $inverter_qty
            );
        }
        if(isset($this->request->data['consumables']))
            $items = array_merge($items, $this->request->data['consumables']);
        if( isset($this->request->data['items'][0]['qty']) )
        {
            $items = array_merge($items, $this->request->data['items']);
        }
        //echo "<pre>",print_r($items),"</pre>"; die();
        $orders_items = array();
        foreach($items as $item)
        {
            if($item['qty'] == 0)
            {
                continue;
            }
            $array = array(
                'qty'           => $item['qty'],
                'id'            => $item['id'],
                'whole_pallet'  => false
            );
            $orders_items[] = $array;
        }
        $the_items = array(
            0 => $orders_items
        );
        $oitems = $this->allocations->createSolarOrderItemsArray($the_items, 0, false);
        foreach($oitems[0] as $item)//there is only one order
        {
            if($item['import_error'])
            {
                Form::setError('items', $item['import_error_string']);
            }
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //echo "<pre>",print_r($oitems),"</pre>"; die();
            //all good, add details
            //echo "<pre>oitems",print_r($oitems),"</pre>";die();
            //echo "<pre>",print_r($post_data),"</pre>"; die();
            $order_id = $this->solarorder->addOrder($post_data, $oitems);
            Session::set('feedback', "An order with id: <strong>$order_id</strong> has been created");
        }
        return $this->redirector->to(PUBLIC_ROOT."solar-jobs/add-solar-install");
    }

    public function printSwatchLabels()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>";die();
        $labels = new AddressLabels;
        $config['layout'] = "name<br />address_1<br />address_2<br />suburb<br />state<br />postcode";
        //$config['format'] = 'html';
        $labels->initialize($config);
        $addresses = array();
        foreach($this->request->data['orders'] as $id)
        {
            $od = $this->swatch->getSwatchDetail($id);
            $addresses[] = array(
                "name"      => ucwords($od['name']),
                "address_1" => ucwords($od['address']),
                "address_2" => ucwords($od['address_2']),
                "suburb"    => strtoupper($od['suburb']),
                "state"     => strtoupper($od['state']),
                "postcode"  => $od['postcode']
            );
        }
        $labels->output($addresses);
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
                /*
                if($this->item->getAvailableStock($swatch_id, 4) <= 0)
                {
                    $_SESSION['errorfeedback'] = "<h2><i class='far fa-times-circle'></i>Swatches cannot be uploaded</h2><p>There are not enough swatches left</p>";
                    return $this->redirector->to(PUBLIC_ROOT."orders/manage-swatches");
                }
                */
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

                    /*
                    $request = array(
                        'deliver_to'    => trim($r[3]),
                        'client_id'     => $client_id,
                        'tracking_email'=> trim($r[2]),
                        'company_name'  => trim($r[4]),
                        'address'       => trim($r[6]),
                        'address_2'     => trim($r[7]),
                        'suburb'        => trim($r[8]),
                        'state'         => trim($r[9]),
                        'postcode'      => trim($r[10]),
                        'contact_phone' => trim($r[5]),
                        'date'          => time(),
                        'country'       => 'AU',
                        'signature_req' => 0,
                        'errors'        => 0,
                        'error_string'  => '',
                        'weight'        => 0.46
                    );
                    */
                    $request = array(
                        'deliver_to'    => trim($r[3]),
                        'client_id'     => $client_id,
                        'tracking_email'=> "",
                        //'company_name'  => trim($r[4]),
                        'address'       => trim($r[4]),
                        'address_2'     => trim($r[5]),
                        'suburb'        => trim($r[6]),
                        'state'         => trim($r[7]),
                        'postcode'      => trim($r[8]),
                        'contact_phone' => trim($r[11]),
                        'date'          => time(),
                        'country'       => 'AU',
                        'errors'        => 0,
                        'error_string'  => '',
                        'weight'        => 1.87
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
                        'item_id'  => 13301,
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

    public function procAddServiceJob()
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
        if($job_type == "0")
        {
            Form::setError('job_type', "A job type must be chosen");
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
                if(!isset($details['qty']))
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
                    if(!empty($details['pallet_qty']))
                    {
                        $array['qty'] = $details['pallet_qty'];
                        $array['whole_pallet'] = true;
                    }
                    else
                    {
                        $array['qty'] = $details['qty'];
                        $array['whole_pallet'] = false;
                    }
                    if(empty($array['qty']))
                    {
                        $error = true;
                        Form::setError('items', 'Please ensure all items have a quantity');
                    }
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
                $oitems = $this->allocations->createOrderItemsArray($the_items, 0, false);
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
            //echo "<pre>oitems",print_r($oitems),"</pre>";die();
            //echo "<pre>",print_r($post_data),"</pre>"; die();
            $job_id = $this->solarservicejob->addJob($post_data, $oitems);
            Session::set('feedback', "That job has been created and entered in the system");
        }
        //return $this->redirector->to(PUBLIC_ROOT."orders/add-order");
        return $this->redirector->to(PUBLIC_ROOT."solar-jobs/add-service-job");
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

    public function procOriginOrderAdd()
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
        $items = array();
        $items[] = array(
            'id'    => $panel_id,
            'qty'   => $panel_qty
        );
        $items[] = array(
            'id'    => $inverter_id,
            'qty'   => $inverter_qty
        );
        /*
        foreach($this->request->data['items'] as $i)
        {
            $items[] = array(
                'id'    => $i['id'],
                'qty'   => $i['qty']
            );
        }
        */
        $items = array_merge($items, $this->request->data['consumables']);
        if( isset($this->request->data['items'][0]['qty']) )
        {
            $items = array_merge($items, $this->request->data['items']);
        }
        //echo "<pre>",print_r($items),"</pre>"; //die();
        $orders_items = array();
        foreach($items as $item)
        {
            if($item['qty'] == 0)
            {
                continue;
            }
            $array = array(
                'qty'           => $item['qty'],
                'id'            => $item['id'],
                'whole_pallet'  => false
            );
            $orders_items[] = $array;
        }
        $the_items = array(
            0 => $orders_items
        );
        //echo "<pre>",print_r($the_items),"</pre>"; die();
        $oitems = $this->allocations->createSolarOrderItemsArray($the_items, 0, false);
        foreach($oitems[0] as $item)//there is only one order
        {
            if($item['import_error'])
            {
                Form::setError('items', $item['import_error_string']);
            }
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //echo "<pre>",print_r($oitems),"</pre>"; die();
            //all good, add details
            //echo "<pre>oitems",print_r($oitems),"</pre>";die();
            //echo "<pre>",print_r($post_data),"</pre>"; die();
            $order_id = $this->solarorder->addOrder($post_data, $oitems);
            Session::set('feedback', "An order with id: <strong>$order_id</strong> has been created");
        }
        return $this->redirector->to(PUBLIC_ROOT."solar-jobs/add-origin-install");
    }

    public function procAddTljOrder()
    {
        //echo "POST<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $items = array();
        $items[] = array(
            'id'    => $panel_id,
            'qty'   => $panel_qty
        );
        $items[] = array(
            'id'    => $inverter_id,
            'qty'   => $inverter_qty
        );
        /* */
        foreach($this->request->data['items'] as $i)
        {
            if(empty($i['id']))
                continue;
            $items[] = array(
                'id'    => $i['id'],
                'qty'   => $i['qty']
            );
        }

        //echo "Items<pre>",print_r($items),"</pre>"; die();
        $orders_items = array();
        foreach($items as $item)
        {
            if($item['qty'] == 0)
            {
                continue;
            }
            $array = array(
                'qty'           => $item['qty'],
                'id'            => $item['id'],
                'whole_pallet'  => false
            );
            $orders_items[] = $array;
        }
        $the_items = array(
            0 => $orders_items
        );
        //echo "The items<pre>",print_r($the_items),"</pre>"; //die();
        $oitems = $this->allocations->createSolarOrderItemsArray($the_items, 0, false);
        foreach($oitems[0] as $item)//there is only one order
        {
            if($item['import_error'])
            {
                Form::setError('items', $item['import_error_string']);
            }
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            //echo "oitems<pre>",print_r($oitems),"</pre>"; die();
            //all good, add details
            //echo "<pre>oitems",print_r($oitems),"</pre>";die();
            //echo "<pre>",print_r($post_data),"</pre>"; die();
            $order_id = $this->solarorder->addOrder($post_data, $oitems);
            Session::set('feedback', "An order with id: <strong>$order_id</strong> has been created");
        }
        return $this->redirector->to(PUBLIC_ROOT."solar-jobs/add-tlj-install");
    }

    public function procAddSolargainOrder()
    {
        //echo "POST<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        $post_data['panel_qty'] = 0;
        $items = array();
        if(!isset($this->request->data['items']) || empty($this->request->data['items'][0]['name']))
        {
            Form::setError('items', 'At least one item must be selected');
        }
        else
        {
            foreach($this->request->data['items'] as $i)
            {
                $items[] = array(
                    'id'    => $i['id'],
                    'qty'   => $i['qty']
                );
                //echo "Items<pre>",print_r($items),"</pre>"; die();
                $orders_items = array();
                foreach($items as $item)
                {
                    if($item['qty'] == 0)
                    {
                        continue;
                    }
                    $array = array(
                        'qty'           => $item['qty'],
                        'id'            => $item['id'],
                        'whole_pallet'  => false
                    );
                    $orders_items[] = $array;
                }
                $the_items = array(
                    0 => $orders_items
                );
                //echo "The items<pre>",print_r($the_items),"</pre>"; //die();
                $oitems = $this->allocations->createSolarOrderItemsArray($the_items, 0, false);
                foreach($oitems[0] as $item)//there is only one order
                {
                    if($item['import_error'])
                    {
                        Form::setError('items', $item['import_error_string']);
                    }
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
            //echo "oitems<pre>",print_r($oitems),"</pre>"; die();
            //all good, add details
            //echo "<pre>oitems",print_r($oitems),"</pre>";die();
            //echo "<pre>",print_r($post_data),"</pre>"; die();
            $order_id = $this->solarorder->addOrder($post_data, $oitems);
            Session::set('feedback', "An order with id: <strong>$order_id</strong> has been created");
        }
        return $this->redirector->to(PUBLIC_ROOT."solar-jobs/add-solargain-install");
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
        if(filter_var($qty, FILTER_VALIDATE_INT) === false && $qty <= 0)
        {
            Form::setError('qty', 'Please enter only positive whole numbers');
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
            if($this->dataSubbed($supplier))
                $array['supplier'] = $supplier;
            $item_id = $this->item->recordData($array);
            $this->newstock->recordData(
                array(
                    'client_id'     => $client_id,
                    'item_id'       => $item_id,
                    'qty'           => $qty,
                    'entered'       => time(),
                    'entered_by'    => Session::getUserId()
                )
            );
            Session::set("feedback", "<h2><i class='far fa-check-circle'></i>New Item Recorded</h2><p>An email will be sent when the item arrives and is scanned into the system</p>");
        }
        return $this->redirector->to(PUBLIC_ROOT."inventory/register-new-stock");
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
            [0] => Client Order Number
            [1] => Customer Order Number
            [2] => Company
            [3] => Addressee
            [4] => Shipping Address Line 1
            [5] => Shipping Address Line 2
            [6] => Shipping City
            [7] => Shipping State
            [8] => Shipping Zip/Postcode
            [9] => Shipping Country
            [10] => Shipping Address Phone
            [11] => Tracking Email
            [11] => Customer Email gone for now - where? dunno
            [12] => ATL
            [13] => Instructions
            [14] => Use Express Post
            [15] => Client Spot
            [16] => Product Name_1
            [17] => Qty_1
            [18] => Whole Pallet 1
            [19] => Product Name_2
            [20] => Qty_2
            [21] => Whole Pallet 2
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
                if(!empty($row[11]))
                {
                    if(!$this->emailValid($row[11]))
                    {
                        $data_errors = true;
                        $data_error_string .= "<li>Invalid tracking email on line: $line</li>";
                    }
                }
                if(!$this->dataSubbed($row[3]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>A Ship To Name is required on line: $line</li>";
                }
                if(!$this->dataSubbed($row[4]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>A Ship To Address is required on line: $line</li>";
                }
                if(!$this->dataSubbed($row[6]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>A Ship To Suburb/City is required on line: $line</li>";
                }
                if(!$this->dataSubbed($row[7]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>A Ship To State is required on line: $line</li>";
                }
                if(!$this->dataSubbed($row[8]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>A Ship To Postcode is required on line: $line</li>";
                }
                if(!$this->dataSubbed($row[9]))
                {
                    $data_errors = true;
                    $data_error_string .= "<li>A Ship To Country is required on line: $line</li>";
                }
                elseif(strlen($row[9]) > 2)
                {
                    $data_errors = true;
                    $data_error_string .= "<li>Please use the two letter ISO code for countries on line: $line</li>";
                }
                if(!$data_errors)
                {
                    $order = array(
                        'error_string'          => '',
                        'items'                 => array(),
                        'ref2'                  => '',
                        'client_order_id'       => $row[0],
                        'customer_order_id'     => $row[1],
                        'errors'                => 0,
                        'tracking_email'        => $row[11],
                        'ship_to'               => $row[3],
                        'company_name'          => $row[2],
                        'date_ordered'          => time(),
                        'status_id'             => $this->controller->order->ordered_id,
                        'eparcel_express'       => 0,
                        'signature_req'         => 0,
                        'contact_phone'         => $row[10],
                        'import_error'          => false,
                        'import_error_string'   => '',
                        'weight'                => 0,
                        'instructions'          => $row[13],
                        '3pl_comments'          => $row[15]
                    );
                    //the items
                    $items = array();
                    $item_error = false;
                    $i = 16;
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
                            'address'   => $row[4],
                            'address_2' => $row[5],
                            'suburb'    => $row[6],
                            'state'     => $row[7],
                            'postcode'  => $row[8],
                            'country'   => $row[9]
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
                            'customer_order_id'     => $o['customer_order_id'],
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
            $satchel_count = ($this->dataSubbed($satchel_count))? $satchel_count: 0;
            $this->outwardsgoods->recordData(array(
                'client_id'     =>  $client_id,
                'pallets'       =>  $pallet_count,
                'cartons'       =>  $carton_count,
                'satchels'      =>  $satchel_count,
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
            if($this->dataSubbed($consignment_id) && count($this->request->data['item_returns']))
            {
                $reason_id = $this->stockmovementlabels->getlabelId('Returns - RTS');
                $location_id = $this->location->getLocationId('Returns') ;
                foreach($this->request->data['item_returns'] as $item_id => $details)
                {
                    if(isset($details['id']))
                    {
                        $this->orderreturn->recordData(array(
                            'reason'    =>  'Return To Sender',
                            'item_id'   =>  $item_id,
                            'order_id'  =>  $order_id,
                            'client_id' =>  $client_id,
                            'entered_by'=>  Session::getUserId(),
                            'date'      =>  time()
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
        $pallets = ($this->dataSubbed($pallets))? $pallets: 0;
        $satchels = ($this->dataSubbed($satchels))? $satchels: 0;
        $vals = array(
            'pallets'       => $pallets,
            'satchels'      => $satchels,
            'total_cost'    => $charge,
            'shrink_wrap'   => 0,
            'bubble_wrap'   => 0
        );
        if(isset($shrink_wrap))
            $vals['shrink_wrap'] = 1;
        if(isset($bubble_wrap))
            $vals['bubble_wrap'] = 1;
        //echo "<pre>",print_r($vals),"</pre>"; die();
        $this->order->updateOrderValues($vals, $order_id);
        Session::set('feedback',"<h2><i class='far fa-check-circle'></i>Order Has Been Updated</h2>");
        return $this->redirector->to(PUBLIC_ROOT."orders/order-update/order=$order_id");
    }

    public function procOrderCourierUpdate()
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
            Session::set('errorfeedback', "<h2><i class='far fa-times-circle'></i>Errors found in the form</h2><p>Please correct where shown and resubmit</p>");
        }
        else
        {
            $ip = (isset($ignore_pc))? 1 : 0;
            Session::set('errorfeedback',"<h2><i class='far fa-times-circle'></i>This Order Could Not Be Assigned</h2><p>Reasons are listed below</p>");
            Session::set('showerrorfeedback', false);
            $courier_name = !$this->dataSubbed($courier_name)? "":$courier_name;
            Session::set('feedback',"<h2><i class='far fa-check-circle'></i>Courier has been assigned</h2>");
            $this->courierselector->assignCourier($order_id, $courier_id, $courier_name, $ip);
        }
        if(Session::getAndDestroy('showerrorfeedback') == false)
        {
            Session::destroy('errorfeedback');
        }
        return $this->redirector->to(PUBLIC_ROOT."orders/order-update/order=$order_id");
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
            if($l_details['qc_count'] < $qty_move)
            {
                Form::setError('qty_move', 'You cannot move more quality control stock than there is available');
            }
        }
        elseif(isset($allocated_stock))
        {
            if($l_details['allocated'] < $qty_move)
            {
                Form::setError('qty_move', 'You cannot move more allocated stock than there is');
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
            if($double_bay == 1)
            {
                //deal with double bays
                if($this->location->isEmptyOfItem($move_from_location, $move_product_id) )
                {
                    //die('remove double bay');
                    $move_from_location_name = $this->location->getLocationName($move_from_location);
                    $next_location_name = substr($move_from_location_name, 0, -1)."b";
                    $next_location_id = $this->location->getLocationId($next_location_name);
                    $this->clientslocation->deleteAllocationByClientLocation($client_id, $next_location_id);
                }
                //die('not removing double bay');
                $move_to_location_name = $this->location->getLocationName($move_to_location);
                $next_location_name = substr($move_to_location_name, 0, -1)."b";
                $next_location_id = $this->location->getLocationId($next_location_name);
                $this->clientslocation->addLocation(array(
                    'location'   => $next_location_id,
                    'client_id'  => $client_id,
                    'notes'      => "Double Bay Item"
                ));
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
        if(!$this->dataSubbed($date))
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
        $palletizedd = (isset($palletized))? 1:0;
        if($palletizedd > 0)
        {
            if(!$this->dataSubbed($per_pallet))
            {
                Form::setError('per_pallet', 'A number is required for palletized goods');
            }
            elseif(filter_var($per_pallet, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1))) === false)
            {
                Form::setError('per_pallet', 'Only enter positive whole numbers for amount per pallet');
            }
        }
        $post_data['palletized'] = $palletizedd;
        $post_data['width'] = 0;
        $post_data['depth'] = 0;
        $post_data['height'] = 0;
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
        $this->item->addPackingTypesForItem($package_types, $product_id);
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
        return $this->redirector->to(PUBLIC_ROOT."products/collections-edit/product=$item_id");
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
            $location = $this->item->getLocationForItem($product_id, $subtract_from_location);
            if($qty_subtract > $location['qc_count'] && $this->dataSubbed($qty_subtract))
            {
                Form::setError('qty_subtract', 'You cannot remove more quality control stock than there is');
            }
            $location = $this->item->getLocationForItem($product_id, $add_to_location);
            if($qty_add > ($location['qty'] - $location['qc_count'] - $location['allocated']) && $this->dataSubbed($qty_add))
            {
                Form::setError('qty_add', 'You cannot add more quality control stock than there is unallocated non quality control items');
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
            if(isset($qc_stock))
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
            Session::set('subtracterrorfeedback', 'Errors were found in the form. Please correct where shown and resubmit');
        }
        else
        {
            $this->location->subtractFromLocation($post_data);
            $this->clientsbays->stockRemoved($client_id, $subtract_from_location, $subtract_product_id, isset($remove_oversize));
            Session::set('subtractfeedback', $subtract_product_name.' has had '.$qty_subtract.' removed fom its count');
        }
        return $this->redirector->to(PUBLIC_ROOT."inventory/add-subtract-stock/product=".$subtract_product_id);
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
            Session::set('addfeedback', $add_product_name.' has had '.$qty_add.' added to its count');
        }
        return $this->redirector->to(PUBLIC_ROOT."inventory/add-subtract-stock/product=".$add_product_id);
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
        if(!$this->dataSubbed($width) || !$this->dataSubbed($height) || !$this->dataSubbed($depth) || !$this->dataSubbed($weight))
        {
            Session::set('packageerrorfeedback', 'All fields must have a value<br/>Package has NOT been added');
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        elseif( (filter_var($width, FILTER_VALIDATE_FLOAT) === false && $width <= 0) || (filter_var($height, FILTER_VALIDATE_FLOAT) === false && $height <= 0) || (filter_var($depth, FILTER_VALIDATE_FLOAT) === false && $depth <= 0) || (filter_var($weight, FILTER_VALIDATE_FLOAT) === false && $weight <= 0) )
        {
            Session::set('packageerrorfeedback', 'All values must have a positive number<br/>Package has NOT been added');
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {
            if($id = $this->order->addPackage($post_data))
            {
                Session::set('packagefeedback', "That package has been added. It should be showing below");
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
            foreach($this->request->data['items'] as $itid => $details)
            {
                $array = array(
                    'qty'   => $details['qty'],
                    'id'    => $details['id']
                );
                if(!empty($details['pallet_qty']))
                {
                    $array['qty'] = $details['pallet_qty'];
                    $array['whole_pallet'] = true;
                }
                else
                {
                    $array['qty'] = $details['qty'];
                    $array['whole_pallet'] = false;
                }
                $orders_items[] = $array ;
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

    public function procSolarItemsUpdate()
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
        if(!isset($this->request->data['items']))
        {
            Form::setError('items', 'At least one item must be selected');
        }
        else
        {
            $orders_items = array();
            foreach($this->request->data['items'] as $itid => $details)
            {
                $array = array(
                    'qty'   => $details['qty'],
                    'id'    => $details['id']
                );
                if(!empty($details['pallet_qty']))
                {
                    $array['qty'] = $details['pallet_qty'];
                    $array['whole_pallet'] = true;
                }
                else
                {
                    $array['qty'] = $details['qty'];
                    $array['whole_pallet'] = false;
                }
                $orders_items[] = $array ;
            }
            //echo "<pre>orders_items",print_r($orders_items),"</pre>"; //die();
            $item_array = array(
                $order_id => $orders_items
            );
            //echo "<pre>item_array",print_r($item_array),"</pre>"; //die();
            //$oitems = $this->allocations->createOrderItemsArray($item_array, $order_id);
            $oitems = $this->allocations->createSolarOrderItemsArray($item_array, $order_id, false);
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
                        return $this->redirector->to(PUBLIC_ROOT."solar-jobs/items-update/job=".$order_id);
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
            if($this->solarorder->updateSolarItemsForOrder($oitems[$order_id], $order_id))
            {
                Session::set('feedback', "Those items have been updated");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."solar-jobs/items-update/job=".$order_id);
    }

    public function procServiceItemsUpdate()
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
        if(!isset($this->request->data['items']))
        {
            Form::setError('items', 'At least one item must be selected');
        }
        else
        {
            $orders_items = array();
            foreach($this->request->data['items'] as $itid => $details)
            {
                $array = array(
                    'qty'   => $details['qty'],
                    'id'    => $details['id']
                );
                if(!empty($details['pallet_qty']))
                {
                    $array['qty'] = $details['pallet_qty'];
                    $array['whole_pallet'] = true;
                }
                else
                {
                    $array['qty'] = $details['qty'];
                    $array['whole_pallet'] = false;
                }
                $orders_items[] = $array ;
            }
            //echo "<pre>orders_items",print_r($orders_items),"</pre>"; //die();
            $item_array = array(
                $order_id => $orders_items
            );
            //echo "<pre>item_array",print_r($item_array),"</pre>"; //die();
            //$oitems = $this->allocations->createOrderItemsArray($item_array, $order_id);
            $oitems = $this->allocations->createOrderItemsArray($item_array, $order_id, false);
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
                        return $this->redirector->to(PUBLIC_ROOT."solar-jobs/service-items-update/job=".$order_id);
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
            if($this->solarservicejob->updateItemsForJob($oitems[$order_id], $order_id))
            {
                Session::set('feedback', "Those items have been updated");
            }
            else
            {
                Session::set('errorfeedback', 'A database error has occurred. Please try again');
            }
        }
        return $this->redirector->to(PUBLIC_ROOT."solar-jobs/service-items-update/job=".$order_id);
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
            if($table == "orders")
            {
                $this->order->updateOrderAddress($post_data);
                $this->order->removeError($order_id);
            }
            else
            {
                $this->swatch->updateSwatchAddress($post_data);
                $this->swatch->removeError($order_id);
            }

            Session::set('feedback', "That address has been updated");
        }
        if($table == "orders")
        {
            return $this->redirector->to(PUBLIC_ROOT."orders/address-update/order=".$order_id);
        }
        else
        {
           return $this->redirector->to(PUBLIC_ROOT."orders/address-update/swatch=".$order_id);
        }

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
                "is_admin_user" => $this->user->isAdminUser()
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
        if($client_id == "0")
        {
            Form::setError('client_id', "A client must be chosen");
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
        return $this->redirector->to(PUBLIC_ROOT."sales-reps/edit-sales-rep/rep=$rep_id");
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
        if($client_id == "0")
        {
            Form::setError('client_id', "A client must be chosen");
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
        return $this->redirector->to(PUBLIC_ROOT."sales-reps/edit-sales-rep/rep=$rep_id");
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
				if(!isset($details['qty']))
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
					if(!empty($details['pallet_qty']))
					{
						$array['qty'] = $details['pallet_qty'];
						$array['whole_pallet'] = true;
					}
					else
					{
						$array['qty'] = $details['qty'];
						$array['whole_pallet'] = false;
					}
					if(empty($array['qty']))
					{
						$error = true;
						Form::setError('items', 'Please ensure all items have a quantity');
					}
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
            //echo "<pre>oitems",print_r($oitems),"</pre>";die();
            //echo "<pre>",print_r($post_data),"</pre>"; die();
            $order_number = $this->order->addOrder($post_data, $oitems);
            Session::set('feedback', "An order with number: <strong>$order_number</strong> has been created");
        }
        //return $this->redirector->to(PUBLIC_ROOT."orders/add-order");
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
       // echo "<pre>",print_r($post_data),"</pre>"; die();
       //echo "Client Name: ".$client_name;die();
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
            Form::setError('general', 'Sorry, your account has been deactivated');
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
            //echo "<pre>",print_r($this->request),"</pre>";
            // reset session
            Session::reset([
                "user_id"       => $userId,
                "role"          => $this->user->getUserRoleName($user["role_id"]),
                "ip"            => $userIp,
                "user_agent"    => $userAgent,
                "users_name"    => $user['name'],
                "client_id"     => $user['client_id'],
                "is_admin_user" => $this->user->isAdminUser($userId)
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
        if(Session::getUserRole() == "solar admin")
        {
            $client_id = $this->client->solar_client_id;
            $post_data['client_id'] = $client_id;
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

/*
        if($hunters_goods_type == "20")
        {
            $satchel_error = true;
            if($this->dataSubbed($small_satchel))
            {
                $satchel_error = false;
                if( (filter_var($small_satchel, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1))) === false) )
    			{
                	Form::setError('small_satchel', 'Only whole positive numbers');
    			}
            }
            if($this->dataSubbed($large_satchel))
            {
                $satchel_error = false;
                if( (filter_var($large_satchel, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1))) === false) )
    			{
                	Form::setError('large_satchel', 'Only whole positive numbers');
    			}
            }
            if($satchel_error)
            {
                Form::setError('large_satchel', 'A value is required for at least one satchel size');
            }
        }
*/
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
        if($palletizedd > 0)
        {
            if(!$this->dataSubbed($per_pallet))
            {
                Form::setError('per_pallet', 'A number is required for palletized goods');
            }
            elseif(filter_var($per_pallet, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1))) === false)
            {
                Form::setError('per_pallet', 'Only enter positive whole numbers for amount per pallet');
            }
        }
        $post_data['palletized'] = $palletizedd;
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
                $this->item->addPackingTypesForItem($package_types, $product_id);
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
        if($palletizedd > 0)
        {
            if(!$this->dataSubbed($per_pallet))
            {
                Form::setError('per_pallet', 'A number is required for palletized goods');
            }
            elseif(filter_var($per_pallet, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1))) === false)
            {
                Form::setError('per_pallet', 'Only enter positive whole numbers for amount per pallet');
            }
        }
        $post_data['palletized'] = $palletizedd;
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

        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
        }
        else
        {

            /* */
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
    private function validateAddress($address, $suburb, $state, $postcode, $country, $ignore_address_error)
    {
        if( !$this->dataSubbed($address) )
        {
            Form::setError('address', 'An address is required');
        }
        elseif( !$ignore_address_error )
        {
            if( (!preg_match("/(?:[A-Za-z].*?\d|\d.*?[A-Za-z])/i", $address)) && (!preg_match("/(?:care of)|(c\/o)|( co )/i", $address)) )
            {
                Form::setError('address', 'The address must include both letters and numbers');
            }
        }
        if(!$this->dataSubbed($postcode))
        {
            Form::setError('postcode', "A delivery postcode is required");
        }
        if(!$this->dataSubbed($country))
        {
            Form::setError('country', "A delivery country is required");
        }
        elseif(strlen($country) > 2)
        {
            Form::setError('country', "Please use the two letter ISO code");
        }
        elseif($country == "AU")
        {
            if(!$this->dataSubbed($suburb))
    		{
    			Form::setError('suburb', "A delivery suburb is required for Australian addresses");
    		}
    		if(!$this->dataSubbed($state))
    		{
    			Form::setError('state', "A delivery state is required for Australian addresses");
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
                Form::setError('postcode', $error_string);
            }
        }
    }

    /*******************************************************************
    ** validates empty data fields
    ********************************************************************/
	protected function dataSubbed($data)
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
	private function emailValid($email)
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