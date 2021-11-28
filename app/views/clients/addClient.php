<?php
$address = Form::value('address');
$address2 = Form::value('address2');
$suburb = Form::value('suburb');
$state = Form::value('state');
$postcode = Form::value('postcode');
$country = Form::value('country');
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col">
                <p><a href="/clients/view-clients/" class="btn btn-outline-fsg">Return to Client List</a></p>
            </div>
        </div>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php echo Form::displayError('general');?>
        <div class="row">
            <div class="col">
                <form id="client_add" method="post" enctype="multipart/form-data" action="/form/procClientAdd">
                     <div class="p-3 pb-0 mb-2 rounded mid-grey">
                        <div class="form-group row">
                            <h4 class="col-md-8">Client Details</h4>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Client Name</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control required" name="client_name" id="client_name" value="<?php echo Form::value('client_name');?>" />
                                <?php echo Form::displayError('client_name');?>
                            </div>
                        </div>
                        <div class="form-group row custom-control custom-checkbox custom-control-right">
                            <input class="custom-control-input" type="checkbox" id="can_adjust" name="can_adjust" checked />
                            <label class="custom-control-label col-md-3" for="can_adjust">Can Edit Order Items/Allocations</label>
                        </div>
                        <div class="form-group row custom-control custom-checkbox custom-control-right">
                            <input class="custom-control-input" type="checkbox" id="production_client" name="production_client" />
                            <label class="custom-control-label col-md-3" for="production_client">Production Client</label>
                        </div>
                        <div class="form-group row custom-control custom-checkbox custom-control-right">
                            <input class="custom-control-input" type="checkbox" id="delivery_client" name="delivery_client" />
                            <label class="custom-control-label col-md-3" for="delivery_client">Delivery Client</label>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Courier Reference</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control required" id="ref_1" name="ref_1" value="<?php echo Form::value('ref_1');?>" />
                                <?php echo Form::displayError('ref_1');?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3">Products Description</label>
                            <div class="col-md-4">
                                <input type="text" placeholder="Used by couriers for labels" class="form-control" name="products_description" id="products_description" value="<?php echo Form::value('products_description');?>" />
                            </div>
                            <label class="col-md-3 col-form-label">&nbsp;</label>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3">Logo</label>
                            <div class="col-md-4">
                                <input type="file" name="client_logo" id="client_logo" />
                                <?php echo Form::displayError('client_logo');?>
                            </div>
                        </div>
                     </div>
                    <!--div class="form-group row">
                        <label class="col-md-3">In/Out Charge per Pallet</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                </div>
                                <input type="text" class="form-control" data-rule-number="true" name="pallet_charge" id="pallet_charge" value="<?php echo Form::value('pallet_charge');?>" />
                            </div>
                            <?php echo Form::displayError('pallet_charge');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3">In/Out Charge per Carton</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                </div>
                                <input type="text" class="form-control" data-rule-number="true" name="carton_charge" id="carton_charge" value="<?php echo Form::value('carton_charge');?>" />
                            </div>
                            <?php echo Form::displayError('carton_charge');?>
                        </div>
                    </div-->
                    <div class="p-3 pb-0 mb-2 rounded mid-grey">
                        <div class="form-group row">
                            <h4 class="col-md-8">Contact Details</h4>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3">Contact Name</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="contact_name" id="contact_name" value="<?php echo Form::value('contact_name');?>" />
                            </div>
                        </div>
                        <!--p><label class="col-md-3 col-form-label">Phone:</label><div class="col-md-4"><input type="text" class="form-control required" name="phone" id="phone" value="<?php echo Form::value('phone');?>" /></p-->
                        <div class="form-group row">
                            <label class="col-md-3">
                            <sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Billing Email</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control required email" name="billing_email" id="billing_email" value="<?php echo Form::value('billing_email');?>" />
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
                                <input type="text" class="form-control" name="sales_contact" id="sales_contact" value="<?php echo Form::value('sales_contact');?>" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Sales Reports Email</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control required email" name="sales_email" id="sales_email" value="<?php echo Form::value('sales_email');?>" />
                                <?php echo Form::displayError('sales_email');?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3">Inventory Contact Name</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="inventory_contact" id="inventory_contact" value="<?php echo Form::value('inventory_contact');?>" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Inventory Reports Email</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control required email" name="inventory_email" id="inventory_email" value="<?php echo Form::value('inventory_email');?>" />
                                <?php echo Form::displayError('inventory_email');?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3">Deliveries Contact Name</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="deliveries_contact" id="deliveries_contact" value="<?php echo Form::value('deliveries_contact');?>" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Deliveries Email</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control required email" name="deliveries_email" id="deliveries_email" value="<?php echo Form::value('deliveries_email');?>" />
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
                                            <input type="text" class="form-control" data-rule-number="true" name="truck_standard_charge" id="truck_standard_charge" value="<?php echo Form::value('truck_standard_charge');?>" />
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
                                            <input type="text" class="form-control" data-rule-number="true" name="truck_urgent_charge" id="truck_urgent_charge" value="<?php echo Form::value('truck_urgent_charge');?>" />
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
                                            <input type="text" class="form-control" data-rule-number="true" name="ute_standard_charge" id="ute_standard_charge" value="<?php echo Form::value('ute_standard_charge');?>" />
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
                                            <input type="text" class="form-control" data-rule-number="true" name="ute_urgent_charge" id="ute_urgent_charge" value="<?php echo Form::value('ute_urgent_charge');?>" />
                                        </div>
                                        <?php echo Form::displayError('ute_urgent_charge');?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 pb-0 mb-2 rounded mid-grey">
                        <div class="form-group row">
                            <h4 class="col-md-8">Weekly Storage Charges</h4>
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
                                            <input type="text" class="form-control" data-rule-number="true" name="standard_storage_charge" id="standard_storage_charge" value="<?php echo Form::value('standard_storage_charge');?>" />
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
                                            <input type="text" class="form-control" data-rule-number="true" name="oversize_storage_charge" id="oversize_storage_charge" value="<?php echo Form::value('oversize_storage_charge');?>" />
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
                                            <input type="text" class="form-control" data-rule-number="true" name="pickface_storage_charge" id="pickface_storage_charge" value="<?php echo Form::value('pickface_storage_charge');?>" />
                                        </div>
                                        <?php echo Form::displayError('pickface_storage_charge');?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                    <div class="form-group row">
                        <label class="col-md-3">&nbsp;</label>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-outline-secondary">Add Client</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>