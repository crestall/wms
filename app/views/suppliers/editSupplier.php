<?php
$name       = empty(Form::value('name'))?       $supplier['name']         : Form::value('name');
$contact    = empty(Form::value('contact'))?    $supplier['contact']      : Form::value('contact');
$email      = empty(Form::value('email'))?      $supplier['email']        : Form::value('email');
$phone      = empty(Form::value('phone'))?      $supplier['phone']        : Form::value('phone');
$address    = empty(Form::value('address'))?    $supplier['address']      : Form::value('address');
$address2   = empty(Form::value('address2'))?   $supplier['address_2']    : Form::value('address2');
$suburb     = empty(Form::value('suburb'))?     $supplier['suburb']       : Form::value('suburb');
$state      = empty(Form::value('state'))?      $supplier['state']        : Form::value('state');
$postcode   = empty(Form::value('postcode'))?   $supplier['postcode']     : Form::value('postcode');
$country    = empty(Form::value('country'))?    $supplier['country']      : Form::value('country');
$website    = empty(Form::value('website'))?    $supplier['website']      : Form::value('website');
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <form id="edit_production_supplier" method="post" action="/form/procEditProductionSupplier">
            <div class="form-group row">
                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="name" id="name" value="<?php echo $name;?>" />
                    <?php echo Form::displayError('name');?>
                </div>
            </div>
            <div class="form-group row custom-control custom-checkbox custom-control-right">
                        <input class="custom-control-input" type="checkbox" id="active" name="active" <?php if($supplier['active'] > 0) echo "checked";?> />
                        <label class="custom-control-label col-md-3" for="active">Active</label>
                    </div>
            <div class="form-group row">
                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Contact</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="contact" id="contact" value="<?php echo $contact;?>" />
                    <?php echo Form::displayError('contact');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Phone</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="phone" id="phone" value="<?php echo $phone;?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Email</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required email" name="email" id="email" value="<?php echo $email;?>" />
                    <?php echo Form::displayError('email');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Website</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="website" id="website" value="<?php echo $website;?>" />
                </div>
            </div>
            <?php include(Config::get('VIEWS_PATH')."forms/address.php");?>
            <div class="form-group row">
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <input type="hidden" name="supplier_id" value="<?php echo $supplier_id;?>" />
                <div class="col-md-4 offset-md-3">
                    <button type="submit" class="btn btn-outline-secondary">Update Details</button>
                </div>
            </div>
        </form>
    </div>
</div>