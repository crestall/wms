<?php
  $pallets = (empty(Form::value('truck_pallets')))? 1 : Form::value('truck_pallets');
  $date = (empty(Form::value('date_value')))? time() : Form::value('date_value');
$address    = Form::value('address');
$address2   = Form::value('address2');
$suburb     = Form::value('suburb');
$state      = Form::value('state');
$postcode   = Form::value('postcode');
$country    = Form::value('country');
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <div class="row">
        <form id="truck-usage" method="post" action="/form/procTruckUsage">
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Client</label>
                <div class="col-md-4">
                    <select id="client_id" name="client_id" class="form-control selectpicker"><option value="0">--Select One--</option><?php echo $this->controller->client->getSelectClients(Form::value('client_id'));?></select>
                    <?php echo Form::displayError('client_id');?>
                </div>
            </div>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/select_date.php");?>
            <?php include(Config::get('VIEWS_PATH')."forms/address.php");?>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Pallets</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required number" name="truck_pallets" id="truck_pallets" value="<?php echo $pallets;?>" />
                    <?php echo Form::displayError('truck_pallets');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Charge</label>
                <div class="col-md-2">
                    <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <input type="text" class="form-control required number" name="charge" id="charge" value="<?php echo Form::value('charge');?>" />
                    </div>
                </div>
                <div class="col-md-2">
                    <button id="truck_charge_calc" class="btn btn-sm btn-success">Calculate</button>
                </div>
                <?php echo Form::displayError('charge');?>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Add Record</button>
                </div>
            </div>
        </form>
    </div>
</div>