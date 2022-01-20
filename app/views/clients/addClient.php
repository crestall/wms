<?php
$address = Form::value('address');
$address2 = Form::value('address2');
$suburb = Form::value('suburb');
$state = Form::value('state');
$postcode = Form::value('postcode');
$country = Form::value('country');
$standard_truck = (!empty(Form::value('standard_truck')))? Form::value('standard_truck') : STANDARD_TRUCK;
$urgent_truck = (!empty(Form::value('urgent_truck')))? Form::value('urgent_truck') : URGENT_TRUCK;
$standard_ute = (!empty(Form::value('standard_ute')))? Form::value('standard_ute') : STANDARD_UTE;
$urgent_ute = (!empty(Form::value('urgent_ute')))? Form::value('urgent_ute') : URGENT_UTE;
$standard_bay = (!empty(Form::value('standard_bay')))? Form::value('standard_bay') : STANDARD_BAY;
$oversize_bay = (!empty(Form::value('oversize_bay')))? Form::value('oversize_bay') : OVERSIZE_BAY;
$loose_20GP = (!empty(Form::value('20GP_loose')))? Form::value('20GP_loose') : LOOSE_20GP;
$loose_40GP = (!empty(Form::value('40GP_loose')))? Form::value('40GP_loose') : LOOSE_40GP;
$palletised_20GP = (!empty(Form::value('20GP_palletised')))? Form::value('20GP_palletised') : PALLETISED_20GP;
$palletised_40GP = (!empty(Form::value('40GP_palletised')))? Form::value('40GP_palletise') : PALLETISED_40GP;

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
                                            <input type="text" class="form-control" data-rule-number="true" name="standard_truck" id="standard_truck" value="<?php echo $standard_truck;?>" />
                                        </div>
                                        <?php echo Form::displayError('standard_truck');?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-5">Urgent Charge</label>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="urgent_truck" id="urgent_truck" value="<?php echo $urgent_truck;?>" />
                                        </div>
                                        <?php echo Form::displayError('urgent_truck');?>
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
                                            <input type="text" class="form-control" data-rule-number="true" name="standard_ute" id="standard_ute" value="<?php echo $standard_ute;?>" />
                                        </div>
                                        <?php echo Form::displayError('standard_ute');?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-5">Urgent Charge</label>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="urgent_ute" id="urgent_ute" value="<?php echo $urgent_ute;?>" />
                                        </div>
                                        <?php echo Form::displayError('urgent_ute');?>
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
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-5">Standard Charge</label>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="standard_bay" id="standard_bay" value="<?php echo $standard_bay;?>" />
                                        </div>
                                        <?php echo Form::displayError('standard_bay');?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-5">Oversize Charge</label>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="oversize_bay" id="oversize_bay" value="<?php echo $oversize_bay;?>" />
                                        </div>
                                        <?php echo Form::displayError('oversize_bay');?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 pb-0 mb-2 rounded mid-grey">
                        <div class="form-group row">
                            <h4 class="col-md-8">Container Unloading Charges</h4>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-5">40&rsquo; Loose Container</label>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="40GP_loose" id="40GP_loose" value="<?php echo $loose_40GP;?>" />
                                        </div>
                                        <?php echo Form::displayError('40GP_loose');?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-5">20&rsquo; Loose Container</label>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="20GP_loose" id="20GP_loose" value="<?php echo $loose_20GP;?>" />
                                        </div>
                                        <?php echo Form::displayError('20GP_loose');?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-5">40&rsquo; Palletised Container</label>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="40GP_palletised" id="40GP_palletised" value="<?php echo $palletised_40GP;?>" />
                                        </div>
                                        <?php echo Form::displayError('40GP_loose');?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-5">20&rsquo; Palletised Container</label>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="20GP_palletised" id="20GP_palletised" value="<?php echo $palletised_20GP;?>" />
                                        </div>
                                        <?php echo Form::displayError('20GP_palletised');?>
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