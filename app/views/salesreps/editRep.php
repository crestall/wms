<?php
$address = empty(Form::value('address'))? $rep['address'] : Form::value('address');
$address2 = empty(Form::value('address2'))? $rep['address_2'] : Form::value('address2');
$suburb = empty(Form::value('suburb'))? $rep['suburb'] : Form::value('suburb');
$state = empty(Form::value('state'))? $rep['state'] : Form::value('state');
$postcode = empty(Form::value('postcode'))? $rep['postcode'] : Form::value('postcode');
$country = empty(Form::value('country'))? $rep['country'] : Form::value('country');
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-md-12">
            <p><a class="btn btn-outline-fsg" href="/fsg-contacts/view-contacts">View List of Contacts</a></p>
        </div>
    </div>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
        <form id="edit-sales-rep" method="post" action="/form/procRepEdit">
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="name" id="name" value="<?php echo $rep['name'];?>" />
                    <?php echo Form::displayError('name');?>
                </div>
            </div>
            <div class="form-group row custom-control custom-checkbox custom-control-right">
                <input class="custom-control-input" type="checkbox" id="active" name="active" <?php if($rep['active'] > 0) echo "checked";?> />
                <label class="custom-control-label col-md-3" for="active">Active</label>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Email</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required email" name="email" id="email" value="<?php echo $rep['email'];?>" />
                    <?php echo Form::displayError('email');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Phone</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="phone" id="phone" value="<?php echo $rep['phone'];?>" />
                    <?php echo Form::displayError('phone');?>
                </div>
            </div>
            <!--div class="form-group row">
                <label class="col-md-3 col-form-label">Tax File Number</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="tfn" id="tfn" value="<?php //echo $rep['tfn'];?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">ABN</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="abn" id="abn" value="<?php //echo $rep['abn'];?>" />
                </div>
            </div-->
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Comments</label>
                <div class="col-md-4">
                    <textarea class="form-control" name="comments" id="comments"><?php echo $rep['comments'];?></textarea>
                </div>
            </div>
            <?php //include(Config::get('VIEWS_PATH')."forms/address_nr.php");?>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <input type="hidden" name="rep_id" value="<?php echo $rep['id']; ?>" />
            <div class="form-group row">
                <div class="col-md-4 offset-md-3">
                    <button type="submit" class="btn btn-outline-secondary">Edit Contact</button>
                </div>
            </div>
        </form>
    </div>
</div>