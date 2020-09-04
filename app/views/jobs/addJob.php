<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <form id="add_production_job" method="post" action="/form/procAddProductionJob">
            <div class="form-group row">
                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Job Id</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="job_id" id="job_id" value="<?php echo Form::value('job_id');?>" />
                    <?php echo Form::displayError('job_id');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3">Related Job Id</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="previous_job_id" id="previous_job_id" value="<?php echo Form::value('previous_job_id');?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Customer Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="customer_name" id="customer_name" value="<?php echo Form::value('customer_name');?>" />
                    <input type="hidden" name="customer_id" id="customer_id" value="0" />
                    <?php echo Form::displayError('customer_name');?>
                </div>
            </div>
            <div class="form-group row">
                <div class="border border-secondary p-3 rounded">
                    <label class="col-md-1">Contact</label>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="customer_contact" id="customer_contact" value="<?php echo Form::value('customer_contact');?>" />
                    </div>
                    <label class="col-md-1">Email</label>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="customer_email" id="customer_email" value="<?php echo Form::value('customer_email');?>" />
                    </div>
                    <label class="col-md-1">Phone</label>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="customer_phone" id="customer_phone" value="<?php echo Form::value('customer_phone');?>" />
                    </div>
                </div>
        </form>
    </div>
</div>