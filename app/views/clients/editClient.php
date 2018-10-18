<?php
$address    = empty(Form::value('address'))?    $client['address']      : Form::value('address');
$address2   = empty(Form::value('address2'))?   $client['address_2']    : Form::value('address2');
$suburb     = empty(Form::value('suburb'))?     $client['suburb']       : Form::value('suburb');
$state      = empty(Form::value('state'))?      $client['state']        : Form::value('state');
$postcode   = empty(Form::value('postcode'))?   $client['postcode']     : Form::value('postcode');
$country    = empty(Form::value('country'))?    $client['country']      : Form::value('country');
?>
        <div id="page-wrapper">
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
            <div class="row">
                <div class="col-lg-12">
                    <p><a href="/clients/view-clients/" class="btn btn-primary">Return to Client List</a></p>
                </div>
                <div class=col-lg-12>
                    <h2>Editing <?php echo $client['client_name'];?></h2>
                    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
                    <?php echo Form::displayError('general');?>
                    <form id="client_edit" method="post" enctype="multipart/form-data" action="/form/procClientEdit">
                        <h3>Client Details</h3>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Client Name</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control required" name="client_name" id="client_name" value="<?php echo $client['client_name'];?>" />
                                <?php echo Form::displayError('client_name');?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="form-check">
                                <label class="form-check-label col-md-3" for="active">Active</label>
                                <div class="col-md-4 checkbox checkbox-default">
                                    <input class="form-check-input styled" type="checkbox" id="active" name="active" <?php if($client['active'] > 0) echo "checked";?> />
                                    <label for="active"></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="form-check">
                                <label class="form-check-label col-md-3" for="has_reps">Manage Sales Reps</label>
                                <div class="col-md-4 checkbox checkbox-default">
                                    <input class="form-check-input styled" type="checkbox" id="has_reps" name="has_reps" <?php if($client['has_reps'] > 0) echo "checked";?> />
                                    <label for="has_reps"></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="form-check">
                                <label class="form-check-label col-md-3" for="use_bubblewrap">Add Bubblewrap To Packing</label>
                                <div class="col-md-4 checkbox checkbox-default">
                                    <input class="form-check-input styled" type="checkbox" id="use_bubblewrap" name="use_bubblewrap" <?php if($client['use_bubblewrap'] > 0) echo "checked";?> />
                                    <label for="use_bubblewrap"></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Courier Reference</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control required" id="ref_1" name="ref_1" value="<?php echo$client['ref_1'];?>" />
                                <?php echo Form::displayError('ref_1');?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Products Description</label>
                            <div class="col-md-4">
                                <input type="text" placeholder="Used by courier for labels" class="form-control" name="products_description" id="products_description" value="<?php echo$client['products_description'];?>" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">In/Out Charge per Pallet</label>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="text" class="form-control" name="pallet_charge" id="pallet_charge" value="<?php echo $client['pallet_charge']?>" />
                                </div>
                                <?php echo Form::displayError('pallet_charge');?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">In/Out Charge per Carton</label>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="text" class="form-control" name="carton_charge" id="carton_charge" value="<?php echo $client['carton_charge']?>" />
                                </div>
                                <?php echo Form::displayError('carton_charge');?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Contact Name</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="contact_name" id="contact_name" value="<?php echo $client['contact_name'];?>" />
                            </div>
                        </div>
                        <!--p><label class="col-md-3 col-form-label">Phone:</label><div class="col-md-4"><input type="text" class="form-control required" name="phone" id="phone" value="<?php echo Form::value('phone');?>" /></p-->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">
                            <sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Billing Email</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control required email" name="billing_email" id="billing_email" value="<?php echo $client['billing_email'];?>" />
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
                                <input type="text" class="form-control" name="sales_contact" id="sales_contact" value="<?php echo $client['sales_contact'];?>" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Sales Reports Email</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control required email" name="sales_email" id="sales_email" value="<?php echo $client['sales_email'];?>" />
                                <?php echo Form::displayError('sales_email');?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Inventory Contact Name</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="inventory_contact" id="inventory_contact" value="<?php echo $client['inventory_contact'];?>" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Inventory Reports Email</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control required email" name="inventory_email" id="inventory_email" value="<?php echo $client['inventory_email'];?>" />
                                <?php echo Form::displayError('inventory_email');?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Deliveries Contact Name</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="deliveries_contact" id="deliveries_contact" value="<?php echo $client['deliveries_contact'];?>" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Deliveries Email</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control required email" name="deliveries_email" id="deliveries_email" value="<?php echo $client['deliveries_email'];?>" />
                                <?php echo Form::displayError('deliveries_email');?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Logo</label>
                            <div class="col-md-4">
                                <input type="file" name="client_logo" id="client_logo" />
                                <?php echo Form::displayError('client_logo');?>
                            </div>
                            <?php if( !is_null($client['logo']) && !empty($client['logo']) ) :?>
                                </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label">Current Logo</label>
                                        <div class="col-md-4">
                                            <img src="/images/client_logos/tn_<?php echo $client['logo'];?>" />
                                        </div>
                                    </div>
                                    <div class="form-check form-group row">
                                        <label class="col-md-3 col-form-label" for="delete_logo">Delete Current Logo</label>
                                        <div class="col-md-6 checkbox checkbox-default">
                                            <input class="form-check-input styled" type="checkbox" id="delete_logo" name="delete_logo" />
                                            <label for="delete_logo"></label>
                                        </div>
                            <?php endif;?>
                        </div>
                        <h3>Address</h3>
                        <?php include(Config::get('VIEWS_PATH')."forms/address.php");?>
                        <input type="hidden" name="client_id" value="<?php echo $client['id'];?>" />
                        <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">&nbsp;</label>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Edit Client</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>