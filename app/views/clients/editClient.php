<?php
//echo "<pre>",print_r($tc),"</pre>";die();
$address    = empty(Form::value('address'))?    $client['address']      : Form::value('address');
$address2   = empty(Form::value('address2'))?   $client['address_2']    : Form::value('address2');
$suburb     = empty(Form::value('suburb'))?     $client['suburb']       : Form::value('suburb');
$state      = empty(Form::value('state'))?      $client['state']        : Form::value('state');
$postcode   = empty(Form::value('postcode'))?   $client['postcode']     : Form::value('postcode');
$country    = empty(Form::value('country'))?    $client['country']      : Form::value('country');
$stc        = empty(Form::value('truck_standard_charge'))? ($tc['standard_charge'] > 0)? $tc['standard_charge'] : "" : Form::value('truck_standard_charge');
$utc        = empty(Form::value('truck_urgent_charge'))? ($tc['urgent_charge'] > 0)? $tc['urgent_charge'] : "" : Form::value('truck_urgent_charge');
$suc        = empty(Form::value('ute_standard_charge'))? ($uc['standard_charge'] > 0)? $uc['standard_charge'] : "" : Form::value('ute_standard_charge');
$uuc        = empty(Form::value('ute_urgent_charge'))? ($uc['urgent_charge'] > 0)? $uc['urgent_charge'] : "" : Form::value('ute_urgent_charge');
$ssc        = empty(Form::value('standard_storage_charge'))? ($sc['standard'] > 0)? $sc['standard'] : "" : Form::value('standard_storage_charge');
$osc        = empty(Form::value('oversize_storage_charge'))? ($sc['oversize'] > 0)? $sc['oversize'] : "" : Form::value('oversize_storage_charge');
$psc        = empty(Form::value('pickface_storage_charge'))? ($sc['pickface'] > 0)? $sc['pickface'] : "" : Form::value('pickface_storage_charge');
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col">
                <p><a href="/clients/view-clients/" class="btn btn-outline-fsg">Return to Client List</a></p>
            </div>
        </div>
        <div class="row">
            <div class=col>
                <h2>Editing <?php echo $client['client_name'];?></h2>
                <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
                <?php echo Form::displayError('general');?>
                <form id="client_edit" method="post" enctype="multipart/form-data" action="/form/procClientEdit">
                    <div class="p-3 pb-0 mb-2 rounded mid-grey">
                        <div class="form-group row">
                            <h4 class="col-md-8">Client Details</h4>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Client Name</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control required" name="client_name" id="client_name" value="<?php echo $client['client_name'];?>" />
                                <?php echo Form::displayError('client_name');?>
                            </div>
                        </div>
                        <div class="form-group row custom-control custom-checkbox custom-control-right">
                            <input class="custom-control-input" type="checkbox" id="active" name="active" <?php if($client['active'] > 0) echo "checked";?> />
                            <label class="custom-control-label col-md-3" for="active">Active</label>
                        </div>
                        <div class="form-group row custom-control custom-checkbox custom-control-right">
                            <input class="custom-control-input" type="checkbox" id="production_client" name="production_client" <?php if($client['production_client'] > 0) echo "checked";?> />
                            <label class="custom-control-label col-md-3" for="production_client">Production Client</label>
                        </div>
                        <div class="form-group row custom-control custom-checkbox custom-control-right">
                            <input class="custom-control-input" type="checkbox" id="delivery_client" name="delivery_client" <?php if($client['delivery_client'] > 0) echo "checked";?> />
                            <label class="custom-control-label col-md-3" for="delivery_client">Delivery Client</label>
                        </div>
                        <div class="form-group row custom-control custom-checkbox custom-control-right">
                            <input class="custom-control-input" type="checkbox" id="use_bubblewrap" name="use_bubblewrap" <?php if($client['use_bubblewrap'] > 0) echo "checked";?> />
                            <label class="custom-control-label col-md-3" for="use_bubblewrap">Add Bubblewrap To Packing</label>
                        </div>
                        <div class="form-group row custom-control custom-checkbox custom-control-right">
                            <input class="custom-control-input" type="checkbox" id="can_adjust" name="can_adjust" <?php if($client['can_adjust'] > 0) echo "checked";?> />
                            <label class="custom-control-label col-md-3" for="can_adjust">Can Edit Order Items/Allocations</label>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Courier Reference</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control required" id="ref_1" name="ref_1" value="<?php echo$client['ref_1'];?>" />
                                <?php echo Form::displayError('ref_1');?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3">Products Description</label>
                            <div class="col-md-4">
                                <input type="text" placeholder="Used by courier for labels" class="form-control" name="products_description" id="products_description" value="<?php echo$client['products_description'];?>" />
                            </div>
                        </div>
                        <?php if( !is_null($client['logo']) && !empty($client['logo']) ) :?>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Current Logo</label>
                                <div class="col-md-4">
                                    <img src="/images/client_logos/tn_<?php echo $client['logo'];?>" />
                                </div>
                            </div>
                            <div class="form-group row custom-control custom-checkbox custom-control-right">
                                <input class="custom-control-input" type="checkbox" id="delete_logo" name="delete_logo" />
                                <label class="custom-control-label col-md-3" for="delete_logo">Delete Current Logo</label>
                            </div>
                        <?php endif;?>
                        <div class="form-group row">
                            <label class="col-md-3">Logo</label>
                            <div class="col-md-4">
                                <input type="file" name="client_logo" id="client_logo" />
                                <?php echo Form::displayError('client_logo');?>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 pb-0 mb-2 rounded mid-grey">
                        <div class="form-group row">
                            <h4 class="col-md-8">Contact Details</h4>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Contact Name</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="contact_name" id="contact_name" value="<?php echo $client['contact_name'];?>" />
                            </div>
                        </div>
                        <!--p><label class="col-md-3 col-form-label">Phone:</label><div class="col-md-4"><input type="text" class="form-control required" name="phone" id="phone" value="<?php echo Form::value('phone');?>" /></p-->
                        <div class="form-group row">
                            <label class="col-md-3">
                            <sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Billing Email</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control required email" name="billing_email" id="billing_email" value="<?php echo $client['billing_email'];?>" />
                                <?php echo Form::displayError('billing_email');?>
                            </div>
                        </div>
                        <div class="form-group row custom-control custom-checkbox custom-control-right">
                            <input class="custom-control-input" type="checkbox" id="ufa" name="ufa" />
                            <label class="custom-control-label col-md-3" for="ufa">Use Billing Details for all</label>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3">Sales Contact Name</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="sales_contact" id="sales_contact" value="<?php echo $client['sales_contact'];?>" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Sales Reports Email</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control required email" name="sales_email" id="sales_email" value="<?php echo $client['sales_email'];?>" />
                                <?php echo Form::displayError('sales_email');?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3">Inventory Contact Name</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="inventory_contact" id="inventory_contact" value="<?php echo $client['inventory_contact'];?>" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Inventory Reports Email</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control required email" name="inventory_email" id="inventory_email" value="<?php echo $client['inventory_email'];?>" />
                                <?php echo Form::displayError('inventory_email');?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3">Deliveries Contact Name</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="deliveries_contact" id="deliveries_contact" value="<?php echo $client['deliveries_contact'];?>" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Deliveries Email</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control required email" name="deliveries_email" id="deliveries_email" value="<?php echo $client['deliveries_email'];?>" />
                                <?php echo Form::displayError('deliveries_email');?>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 pb-0 mb-2 rounded mid-grey">
                        <div class="form-group row">
                            <h4 class="col-md-8">Address Details</h4>
                        </div>
                        <?php include(Config::get('VIEWS_PATH')."forms/address.php");?>
                    </div>
                    <div class="p-3 pb-0 mb-2 rounded mid-grey">
                        <div class="form-group row">
                            <h4 class="col-md-8">Local Delivery Charges</h4>
                        </div>
                        <input type="hidden" name="tc_line_id" value="<?php echo $tc['id'];?>">
                        <input type="hidden" name="uc_line_id" value="<?php echo $uc['id'];?>">
                        <?php //echo "UTE<pre>",print_r($uc),"</pre>";?>
                        <?php //echo "TRUCK<pre>",print_r($tc),"</pre>";?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <h5 class="text-center">Truck Charges</h5>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-5">Standard Charge</label>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="truck_standard_charge" id="truck_standard_charge" value="<?php echo $stc;?>" />
                                        </div>
                                        <?php echo Form::displayError('truck_standard_charge');?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-5">Urgent Charge</label>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="truck_urgent_charge" id="truck_urgent_charge" value="<?php echo $utc;?>" />
                                        </div>
                                        <?php echo Form::displayError('truck_urgent_charge');?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <h5 class="text-center">Ute Charges</h5>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-5">Standard Charge</label>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="ute_standard_charge" id="ute_standard_charge" value="<?php echo $suc;?>" />
                                        </div>
                                        <?php echo Form::displayError('ute_standard_charge');?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-5">Urgent Charge</label>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="ute_urgent_charge" id="ute_urgent_charge" value="<?php echo $uuc;?>" />
                                        </div>
                                        <?php echo Form::displayError('ute_urgent_charge');?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 pb-0 mb-2 rounded mid-grey">
                        <div class="form-group row">
                            <h4 class="col-md-8">Storage Charges</h4>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <label class="col-md-5">Standard Charge</label>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="standard_storage_charge" id="standard_storage_charge" value="<?php echo $ssc;?>" />
                                        </div>
                                        <?php echo Form::displayError('standard_storage_charge');?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <label class="col-md-5">Oversize Charge</label>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="oversize_storage_charge" id="oversize_storage_charge" value="<?php echo $osc;?>" />
                                        </div>
                                        <?php echo Form::displayError('oversize_storage_charge');?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <label class="col-md-5">Pickface Charge</label>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="pickface_storage_charge" id="pickface_storage_charge" value="<?php echo $psc;?>" />
                                        </div>
                                        <?php echo Form::displayError('pickface_storage_charge');?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="client_id" value="<?php echo $client['id'];?>" />
                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">&nbsp;</label>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-outline-secondary">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>