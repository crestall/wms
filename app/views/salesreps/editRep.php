<?php
$address = empty(Form::value('address'))? $rep['address'] : Form::value('address');
$address2 = empty(Form::value('address2'))? $rep['address_2'] : Form::value('address2');
$suburb = empty(Form::value('suburb'))? $rep['suburb'] : Form::value('suburb');
$state = empty(Form::value('state'))? $rep['state'] : Form::value('state');
$postcode = empty(Form::value('postcode'))? $rep['postcode'] : Form::value('postcode');
$country = empty(Form::value('country'))? $rep['country'] : Form::value('country');
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-md-12">
            <p><a class="btn btn-primary" href="/sales-reps/view-reps">View List of Reps</a></p>
        </div>
    </div>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <div class="row">
        <form id="edit-sales-rep" method="post" action="/form/procRepEdit">
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Client</label>
                <div class="col-md-4">
                    <select id="client_id" name="client_id" class="form-control selectpicker"><option value="0">--Select One--</option><?php echo $this->controller->client->getSelectSalesRepClients($rep['client_id']);?></select>
                    <?php echo Form::displayError('client_id');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="name" id="name" value="<?php echo $rep['name'];?>" />
                    <?php echo Form::displayError('name');?>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="active">Active</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="active" name="active" <?php if($rep['active'] > 0) echo "checked";?> />
                        <label for="active"></label>
                    </div>
                </div>
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
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Tax File Number</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="tfn" id="tfn" value="<?php echo $rep['tfn'];?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">ABN</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="abn" id="abn" value="<?php echo $rep['abn'];?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Comments</label>
                <div class="col-md-4">
                    <textarea class="form-control" name="comments" id="comments"><?php echo $rep['comments'];?></textarea>
                </div>
            </div>
            <?php include(Config::get('VIEWS_PATH')."forms/address_nr.php");?>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <input type="hidden" name="rep_id" value="<?php echo $rep['id']; ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Edit Rep</button>
                </div>
            </div>
        </form>
    </div>
</div>