<?php
$address = Form::value('address');
$address2 = Form::value('address2');
$suburb = Form::value('suburb');
$state = Form::value('state');
$postcode = Form::value('postcode');
$country = Form::value('country');
$contact_name = (empty(Form::value('contact_name')))? "Storeman" : Form::value('contact_name')
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <div class="row">
        <form id="add-store" method="post" action="/form/procStoreAdd">
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Chain</label>
                <div class="col-md-4">
                    <select id="chain_id" name="chain_id" class="form-control selectpicker"><option value="0">--Select One--</option><?php echo $this->controller->storechain->getSelectStoreChains(Form::value('chain_id'));?></select>
                    <?php echo Form::displayError('chain_id');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="name" id="name" value="<?php echo Form::value('name');?>" />
                    <?php echo Form::displayError('name');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Store Number</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="store_number" id="store_number" value="<?php echo Form::value('store_number');?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Contact Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="contact_name" id="contact_name" value="<?php echo $contact_name;?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Contact Email</label>
                <div class="col-md-4">
                    <input type="text" class="form-control email" name="contact_email" id="contact_email" value="<?php echo Form::value('contact_email');?>" />
                    <?php echo Form::displayError('contact_email');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Contact Phone</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="phone" id="phone" value="<?php echo Form::value('phone');?>" />
                </div>
            </div>
            <?php include(Config::get('VIEWS_PATH')."forms/address.php");?>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Add Store</button>
                </div>
            </div>
        </form>
    </div>
</div>