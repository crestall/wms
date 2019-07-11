<?php
$address = Form::value('address');
$address2 = Form::value('address2');
$suburb = Form::value('suburb');
$state =  (empty(Form::value('state')))? "VIC" : Form::value('state');
$postcode = Form::value('postcode');
$country = (empty(Form::value('country')))? "AU" : Form::value('country');
$date_filter = "Job Date";
$date = (empty(Form::value('date_value')))? time() : Form::value('date_value');
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <div class="row">
        <div class="col-lg-12">
            <form id="add-service-job" method="post" action="/form/procAddServiceJob" autocomplete="off">
                <div class="row form-group">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Select a Job Type</label>
                    <div class="col-md-4">
                        <p><select id="job_type" name="job_type" class="form-control selectpicker"><option value="0">--Choose One--</option><?php echo $this->controller->solarordertype->getSelectSolarOrderTypes(Form::value('job_type'));?></select></p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Team</label>
                    <div class="col-md-4">
                        <select id="team_id" name="team_id" class="form-control selectpicker"><option value="0">--Select One--</option><?php echo $this->controller->solarteam->getSelectTeam(Form::value('team_id'));?></select>
                        <?php echo Form::displayError('team_id');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Work Order</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required" name="work_order" id="work_order" value="<?php echo Form::value('work_order');?>" />
                        <?php echo Form::displayError('work_order');?>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="form-check">
                        <label class="form-check-label col-md-3" for="battery">Battery Install</label>
                        <div class="col-md-4 checkbox checkbox-default">
                            <input class="form-check-input styled" type="checkbox" id="battery" name="battery" <?php if(!empty(Form::value('battery'))) echo 'checked';?> />
                            <label for="battery"></label>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Customer Name</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="customer_name" id="customer_name" value="<?php echo Form::value('customer_name');?>" />
                        <?php echo Form::displayError('customer_name');?>
                    </div>
                </div>
                <?php include(Config::get('VIEWS_PATH')."layout/page-includes/select_date.php");?>
                <?php include(Config::get('VIEWS_PATH')."forms/address.php");?>
                <?php include(Config::get('VIEWS_PATH')."forms/item_adder.php");?>
                <input type="hidden" name="selected_items" id="selected_items" />
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id; ?>" />
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">&nbsp;</label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary" id="add_service_job_submitter">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>