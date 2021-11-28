<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php echo Form::displayError('general');?>
        <form id="add_misc_task_to_runsheet" method="post" action="/form/procAddMiscTask">
            <div class="form-group row">
                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Deliver To</label>
                <div class="col-md-4">
                    <input type="text" class="required form-control" name="deliver_to" id="deliver_to" value="<?php echo Form::value('deliver_to');?>">
                    <?php echo Form::displayError('deliver_to');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Attention</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="attention" id="attention" value="<?php echo Form::value('attention');?>">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Delivery Instructions</label>
                <div class="col-md-4">
                    <textarea class="form-control" name="delivery_instructions" id="delivery_instructions"><?php echo Form::value('delivery_instructions');?></textarea>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Driver</label>
                <div class="col-md-4">
                    <select id="driver_id" name="driver_id" class="form-control selectpicker" data-style="btn-outline-secondary"><option value="0">--Select One--</option><?php echo $this->controller->driver->getSelectDrivers( Form::value('driver_id') );?></select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Units</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="units" id="units" value="<?php echo Form::value('units');?>">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Address Line 1</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="address" id="address" value="<?php echo Form::value('address');?>" />
                    <?php echo Form::displayError('address');?>
                </div>
                <div class="custom-control custom-checkbox col-md-3">
                    <input type="checkbox" class="custom-control-input" id="ignore_address_error" name="ignore_address_error" <?php if(!empty(Form::value('ignore_address_error'))) echo 'checked';?> />
                    <label class="custom-control-label" for="ignore_address_error">No need for a number</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Address Line 2</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="address_2" id="address_2" value="<?php echo Form::value('address_2');?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Suburb/Town</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="suburb" id="suburb" value="<?php echo Form::value('suburb');?>" />
                    <?php echo Form::displayError('suburb');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Postcode</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="postcode" id="postcode" value="<?php echo Form::value('postcode');?>" />
                    <?php echo Form::displayError('postcode');?>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" >
            <input type="hidden" name="runsheet_id" id="runsheet_id" value="<?php echo $runsheet_id;?>" >
            <div class="form-group row">
                <div class="col-md-5 offset-md-3">
                    <button type="submit" class="btn btn-outline-secondary" id="submitter">Add Task To Runsheet</button>
                </div>
            </div>
        </form>
    </div>
</div>