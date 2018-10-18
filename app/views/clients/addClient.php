<?php
$address = Form::value('address');
$address2 = Form::value('address2');
$suburb = Form::value('suburb');
$state = Form::value('state');
$postcode = Form::value('postcode');
$country = Form::value('country');
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-12">
            <p><a href="/clients/view-clients/" class="btn btn-primary">Return to Client List</a></p>
        </div>
    </div>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <div class="row">
        <form id="client_add" method="post" enctype="multipart/form-data" action="/form/procClientAdd">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Client Details</h3>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Client Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="client_name" id="client_name" value="<?php echo Form::value('client_name');?>" />
                    <?php echo Form::displayError('client_name');?>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="has_reps">Manage Sales Reps</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="has_reps" name="has_reps" />
                        <label for="has_reps"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Courier Reference</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" id="ref_1" name="ref_1" value="<?php echo Form::value('ref_1');?>" />
                    <?php echo Form::displayError('ref_1');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Products Description</label>
                <div class="col-md-4">
                    <input type="text" placeholder="Used by couriers for labels" class="form-control" name="products_description" id="products_description" value="<?php echo Form::value('products_description');?>" />
                </div>
                <label class="col-md-3 col-form-label">&nbsp;</label>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">In/Out Charge per Pallet</label>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <input type="text" class="form-control" name="pallet_charge" id="pallet_charge" value="<?php echo Form::value('pallet_charge');?>" />
                    </div>
                    <?php echo Form::displayError('pallet_charge');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">In/Out Charge per Carton</label>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <input type="text" class="form-control" name="carton_charge" id="carton_charge" value="<?php echo Form::value('carton_charge');?>" />
                    </div>
                    <?php echo Form::displayError('carton_charge');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Contact Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="contact_name" id="contact_name" value="<?php echo Form::value('contact_name');?>" />
                </div>
            </div>
            <!--p><label class="col-md-3 col-form-label">Phone:</label><div class="col-md-4"><input type="text" class="form-control required" name="phone" id="phone" value="<?php echo Form::value('phone');?>" /></p-->
            <div class="form-group row">
                <label class="col-md-3 col-form-label">
                <sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Billing Email</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required email" name="billing_email" id="billing_email" value="<?php echo Form::value('billing_email');?>" />
                    <?php echo Form::displayError('billing_email');?>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="ufa">Use Billing Details for all</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="ufa" name="ufa" />
                        <label for="ufa"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Sales Contact Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="sales_contact" id="sales_contact" value="<?php echo Form::value('sales_contact');?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Sales Reports Email</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required email" name="sales_email" id="sales_email" value="<?php echo Form::value('sales_email');?>" />
                    <?php echo Form::displayError('sales_email');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Inventory Contact Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="inventory_contact" id="inventory_contact" value="<?php echo Form::value('inventory_contact');?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Inventory Reports Email</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required email" name="inventory_email" id="inventory_email" value="<?php echo Form::value('inventory_email');?>" />
                    <?php echo Form::displayError('inventory_email');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Deliveries Contact Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="deliveries_contact" id="deliveries_contact" value="<?php echo Form::value('deliveries_contact');?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Deliveries Email</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required email" name="deliveries_email" id="deliveries_email" value="<?php echo Form::value('deliveries_email');?>" />
                    <?php echo Form::displayError('deliveries_email');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Logo</label>
                <div class="col-md-4">
                    <input type="file" name="client_logo" id="client_logo" />
                    <?php echo Form::displayError('client_logo');?>
                </div>
            </div>
            <h3>Address</h3>
            <?php include(Config::get('VIEWS_PATH')."forms/address.php");?>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Add Client</button>
                </div>
            </div>
        </form>
    </div>
</div>