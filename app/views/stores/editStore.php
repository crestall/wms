<?php
$address = empty(Form::value('address'))? $store['address'] : Form::value('address');
$address2 = empty(Form::value('address2'))? $store['address_2'] : Form::value('address2');
$suburb = empty(Form::value('suburb'))? $store['suburb'] : Form::value('suburb');
$state = empty(Form::value('state'))? $store['state'] : Form::value('state');
$postcode = empty(Form::value('postcode'))? $store['postcode'] : Form::value('postcode');
$country = empty(Form::value('country'))? $store['country'] : Form::value('country');
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <div class="row">
        <form id="edit-store" method="post" action="/form/procStoreEdit">
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Chain</label>
                <div class="col-md-4">
                    <select id="chain_id" name="chain_id" class="form-control selectpicker"><option value="0">--Select One--</option><?php echo $this->controller->storechain->getSelectStoreChains($store['chain_id']);?></select>
                    <?php echo Form::displayError('chain_id');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="name" id="name" value="<?php echo $store['name'];?>" />
                    <?php echo Form::displayError('name');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Store Number</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="store_number" id="store_number" value="<?php echo $store['store_number'];?>" />
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="active">Active</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="active" name="active" <?php if($store['active'] > 0) echo "checked";?> />
                        <label for="active"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Contact Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="contact_name" id="contact_name" value="<?php echo $store['contact_name'];?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Contact Email</label>
                <div class="col-md-4">
                    <input type="text" class="form-control email" name="contact_email" id="contact_email" value="<?php echo $store['contact_email'];?>" />
                    <?php echo Form::displayError('contact_email');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Contact Phone</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="phone" id="phone" value="<?php echo $store['phone'];?>" />
                </div>
            </div>
            <?php include(Config::get('VIEWS_PATH')."forms/address.php");?>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <input type="hidden" name="store_id" value="<?php echo $store['id']; ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Edit</button>
                </div>
            </div>
        </form>
    </div>
</div>