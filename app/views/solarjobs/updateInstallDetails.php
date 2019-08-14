<?php
$address = (empty(Form::value('address')))? $details['address'] : Form::value('address');
$address2 = (empty(Form::value('address2')))? $details['address_2'] : Form::value('address2');
$suburb = (empty(Form::value('suburb')))? $details['suburb'] : Form::value('suburb');
$state =  (empty(Form::value('state')))? $details['state'] : Form::value('state');
$postcode = (empty(Form::value('postcode')))? $details['postcode'] : Form::value('postcode');
$country = (empty(Form::value('country')))? $details['country'] : Form::value('country');
$date_filter = "Install Date";
$date = (empty(Form::value('date_value')))? $details['install_date'] : Form::value('date_value');
$team_id = (empty(Form::value('team_id')))? $details['team_id'] : Form::value('team_id');
$work_order = (empty(Form::value('work_order')))? $details['work_order'] : Form::value('work_order');
$customer_name = (empty(Form::value('customer_name')))? $details['customer_name'] : Form::value('customer_name');
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-md-4">
            <p><a class="btn btn-primary" href="/solar-jobs/view-installs/type=<?php echo $details['type_id'];?>">View Installs For <?php echo $order_type;?></a></p>
        </div>
        <div class="col-md-4">
            <p><a class="btn btn-primary" href="/solar-jobs/edit-install/id=<?php echo $details['id'];?>">View This Install Details</a></p>
        </div>
    </div>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <div class="row">
        <div class="col-lg-12">
            <form id="edit-solar-install" method="post" action="/form/procEditInstall" autocomplete="off">
                <div class="row form-group">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Select an Install Type</label>
                    <div class="col-md-4">
                        <p><select id="type_id" name="type_id" class="form-control selectpicker" disabled><option value="0">--Choose One--</option><?php echo $this->controller->solarordertype->getSelectSolarOrderTypes($details['type_id']);?></select></p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Team</label>
                    <div class="col-md-4">
                        <select id="team_id" name="team_id" class="form-control selectpicker"><option value="0">--Select One--</option><?php echo $this->controller->solarteam->getSelectTeam($team_id);?></select>
                        <?php echo Form::displayError('team_id');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Work Order</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required" name="work_order" id="work_order" value="<?php echo $work_order;?>" />
                        <?php echo Form::displayError('work_order');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Customer Name</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="customer_name" id="customer_name" value="<?php echo $customer_name;?>" />
                        <?php echo Form::displayError('customer_name');?>
                    </div>
                </div>
                <?php include(Config::get('VIEWS_PATH')."layout/page-includes/select_date.php");?>
                <?php include(Config::get('VIEWS_PATH')."forms/address.php");?>
                <input type="hidden" name="order_id" id="order_id" value="<?php echo $id;?>" />
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <input type="hidden" name="client_id" id="client_id" value="67" />
                <input type="hidden" name="type_id" id="type_id" value="<?php echo $details['type_id'];?>" />
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