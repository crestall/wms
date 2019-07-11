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
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Select an Order Type</label>
                    <div class="col-md-4">
                        <p><select id="job_type" name="job_type" class="form-control selectpicker"><option value="0">--Choose One--</option><?php echo $this->controller->solarordertype->getSelectSolarOrderTypes(Form::value('job_type'));?></select></p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>