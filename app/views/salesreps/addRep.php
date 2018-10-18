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
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <div class="row">
        <form id="add-sales-rep" method="post" action="/form/procRepAdd">
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Client</label>
                <div class="col-md-4">
                    <select id="client_id" name="client_id" class="form-control selectpicker"><option value="0">--Select One--</option><?php echo $this->controller->client->getSelectSalesRepClients(Form::value('client_id'));?></select>
                    <?php echo Form::displayError('client_id');?>
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
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Email</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required email" name="email" id="email" value="<?php echo Form::value('email');?>" />
                    <?php echo Form::displayError('email');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Phone</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="phone" id="phone" value="<?php echo Form::value('phone');?>" />
                    <?php echo Form::displayError('phone');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Tax File Number</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="tfn" id="tfn" value="<?php echo Form::value('tfn');?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">ABN</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="abn" id="abn" value="<?php echo Form::value('abn');?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Comments</label>
                <div class="col-md-4">
                    <textarea class="form-control" name="comments" id="comments"><?php echo Form::value('comments');?></textarea>
                </div>
            </div>
            <?php include(Config::get('VIEWS_PATH')."forms/address_nr.php");?>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Add Rep</button>
                </div>
            </div>
        </form>
    </div>
</div>