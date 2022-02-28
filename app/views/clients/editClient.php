<?php
//echo "<pre>",print_r($client),"</pre>";//die();
$address    = empty(Form::value('address'))?    $client['address']      : Form::value('address');
$address2   = empty(Form::value('address2'))?   $client['address_2']    : Form::value('address2');
$suburb     = empty(Form::value('suburb'))?     $client['suburb']       : Form::value('suburb');
$state      = empty(Form::value('state'))?      $client['state']        : Form::value('state');
$postcode   = empty(Form::value('postcode'))?   $client['postcode']     : Form::value('postcode');
$country    = empty(Form::value('country'))?    $client['country']      : Form::value('country');

$standard_truck = empty(Form::value('standard_truck'))? $client['standard_truck'] : Form::value('standard_truck');
$urgent_truck = empty(Form::value('urgent_truck'))? $client['urgent_truck'] : Form::value('urgent_truck');
$standard_ute = (!empty(Form::value('standard_ute')))? Form::value('standard_ute') : $client['standard_ute'];
$urgent_ute = (!empty(Form::value('urgent_ute')))? Form::value('urgent_ute') : $client['urgent_ute'];
$standard_bay = (!empty(Form::value('standard_bay')))? Form::value('standard_bay') : $client['standard_bay'];
$oversize_bay = (!empty(Form::value('oversize_bay')))? Form::value('oversize_bay') : $client['oversize_bay'];
$loose_20GP = (!empty(Form::value('20GP_loose')))? Form::value('20GP_loose') : $client['20GP_loose'];
$loose_40GP = (!empty(Form::value('40GP_loose')))? Form::value('40GP_loose') : $client['40GP_loose'];
$palletised_20GP = (!empty(Form::value('20GP_palletised')))? Form::value('20GP_palletised') : $client['20GP_palletised'];
$palletised_40GP = (!empty(Form::value('40GP_palletised')))? Form::value('40GP_palletised') : $client['40GP_palletised'];
$max_loose_20GP = (!empty(Form::value('max_loose_20GP')))? Form::value('max_loose_20GP') : $client['max_loose_20GP'];
$max_loose_40GP = (!empty(Form::value('max_loose_40GP')))? Form::value('max_loose_40GP') : $client['max_loose_40GP'];
$additional_loose = (!empty(Form::value('additional_loose')))? Form::value('max_loose_40GP') : $client['additional_loose'];
$repalletising = (!empty(Form::value('repalletising')))? Form::value('repalletising') : $client['repalletising'];
$shrinkwrap = (!empty(Form::value('shrinkwrap')))? Form::value('shrinkwrap') : $client['shrinkwrap'];
$service_fee = (!empty(Form::value('service_fee')))? Form::value('service_fee') : $client['service_fee'];
$manual_order_entry = (!empty(Form::value('manual_order_entry')))? Form::value('manual_order_entry') : $client['manual_order_entry'];
$pallet_in = (!empty(Form::value('pallet_in')))? Form::value('pallet_in') : $client['pallet_in'];
$pallet_out = (!empty(Form::value('pallet_out')))? Form::value('pallet_out') : $client['pallet_out'];
$carton_in = (!empty(Form::value('carton_in')))? Form::value('carton_in') : $client['carton_in'];
$carton_out = (!empty(Form::value('carton_out')))? Form::value('carton_out') : $client['carton_out'];
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
                            <input class="custom-control-input one_of" type="checkbox" id="delivery_client" name="delivery_client" <?php if($client['delivery_client'] > 0) echo "checked";?> />
                            <label class="custom-control-label col-md-3" for="delivery_client">Delivery Client</label>
                        </div>
                        <div class="form-group row custom-control custom-checkbox custom-control-right">
                            <input class="custom-control-input one_of" type="checkbox" id="pick_pack" name="pick_pack" <?php if($client['delivery_client'] > 0) echo "checked";?> />
                            <label class="custom-control-label col-md-3" for="pick_pack">Pick Pack Client</label>
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
                            <h4 class="col-md-8">Goods In/Out</h4>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-5">Pallets In</label>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="pallet_in" id="pallet_in" value="<?php echo $pallet_in;?>" />
                                        </div>
                                        <?php echo Form::displayError('pallet_in');?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-5">Cartons In</label>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="carton_in" id="carton_in" value="<?php echo $carton_in;?>" />
                                        </div>
                                        <?php echo Form::displayError('carton_in');?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-5">Pallets Out</label>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="pallet_out" id="pallet_out" value="<?php echo $pallet_out;?>" />
                                        </div>
                                        <?php echo Form::displayError('pallet_out');?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-5">Cartons Out</label>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="carton_out" id="carton_out" value="<?php echo $carton_out;?>" />
                                        </div>
                                        <?php echo Form::displayError('carton_out');?>
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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-5">Max Loose Items In 40&rsquo; Container</label>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" data-rule-digits="true" name="max_loose_40GP" id="max_loose_40GP" value="<?php echo $max_loose_40GP;?>">
                                        <?php echo Form::displayError('max_loose_40GP');?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-5">Max Loose Items In 20&rsquo; Container</label>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" data-rule-digits="true" name="max_loose_20GP" id="max_loose_20GP" value="<?php echo $max_loose_20GP;?>">
                                        <?php echo Form::displayError('max_loose_20GP');?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-5">Additional Loose Items</label>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="additional_loose" id="additonal_loose" value="<?php echo $additional_loose;?>" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">per item</span>
                                            </div>
                                        </div>
                                        <?php echo Form::displayError('additional_loose');?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 pb-0 mb-2 rounded mid-grey">
                        <div class="form-group row">
                            <h4 class="col-md-8">Miscellaneous Charges</h4>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-5">Repalletising</label>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="repalletising" id="repalletising" value="<?php echo $repalletising;?>" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">per pallet</span>
                                            </div>
                                        </div>
                                        <?php echo Form::displayError('repalletising');?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-5">Shrinkwrapping</label>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="shrinkwrap" id="shrinkwrap" value="<?php echo $shrinkwrap;?>" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">per pallet</span>
                                            </div>
                                        </div>
                                        <?php echo Form::displayError('shrinkwrap');?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-5">Service Fee</label>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="service_fee" id="service_fee" value="<?php echo $service_fee;?>" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">per month</span>
                                            </div>
                                        </div>
                                        <?php echo Form::displayError('service_fee');?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-md-5">Manual Order Entry</label>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-dollar-sign"></i></span>
                                            </div>
                                            <input type="text" class="form-control" data-rule-number="true" name="manual_order_entry" id="manual_order_entry" value="<?php echo $manual_order_entry;?>" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">per order</span>
                                            </div>
                                        </div>
                                        <?php echo Form::displayError('manual_order_entry');?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="client_id" value="<?php echo $client['client_id'];?>" />
                    <input type="hidden" name="charges_id" value="<?php echo $client['id'];?>" />
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