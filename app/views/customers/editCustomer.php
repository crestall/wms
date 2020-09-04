<?php
$name       = empty(Form::value('name'))?       $customer['name']         : Form::value('name');
$contact    = empty(Form::value('contact'))?    $customer['contact']      : Form::value('contact');
$email      = empty(Form::value('email'))?      $customer['email']        : Form::value('email');
$phone      = empty(Form::value('phone'))?      $customer['phone']        : Form::value('phone');
$address    = empty(Form::value('address'))?    $customer['address']      : Form::value('address');
$address2   = empty(Form::value('address2'))?   $customer['address_2']    : Form::value('address2');
$suburb     = empty(Form::value('suburb'))?     $customer['suburb']       : Form::value('suburb');
$state      = empty(Form::value('state'))?      $customer['state']        : Form::value('state');
$postcode   = empty(Form::value('postcode'))?   $customer['postcode']     : Form::value('postcode');
$country    = empty(Form::value('country'))?    $customer['country']      : Form::value('country');
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <form id="edit_production_customer" method="post" action="/form/procEditProductionCustomer">
            <div class="form-group row">
                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="name" id="name" value="<?php echo $name;?>" />
                    <?php echo Form::displayError('name');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Contact</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="contact" id="contact" value="<?php echo $contact;?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Phone</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="phone" id="phone" value="<?php echo $phone;?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Email</label>
                <div class="col-md-4">
                    <input type="text" class="form-control email" name="email" id="email" value="<?php echo $email;?>" />
                    <?php echo Form::displayError('email');?>
                </div>
            </div>
            <?php include(Config::get('VIEWS_PATH')."forms/address_nr.php");?>
            <div class="form-group row">
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <input type="hidden" name="customer_id" value="<?php echo $customer_id;?>" />
                <div class="col-md-4 offset-md-3">
                    <button type="submit" class="btn btn-outline-secondary">Update Details</button>
                </div>
            </div>
        </form>
    </div>
</div>