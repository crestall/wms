<?php
$job_id = (!empty(Form::value('job_id')))? Form::value('job_id'):$job['job_id'];
$previous_job_id = (!empty(Form::value('previous_job_id')))? Form::value('previous_job_id'):$job['previous_job_id'];
$status_id = (!empty(Form::value('status_id')))? Form::value('status_id'):$job['status_id'];
$salesrep_id = (!empty(Form::value('salesrep_id')))? Form::value('salesrep_id'):$job['salesrep_id'];
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php //echo "JOB<pre>",print_r($job),"</pre>";?>
        <?php //echo "CUSTOMER<pre>",print_r($customer),"</pre>";?>
        <?php //echo "SUPPLIER<pre>",print_r($supplier),"</pre>";?>
        <div class="row">
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card h-100 border-secondary order-card">
                    <div class="card-header bg-secondary text-white">
                        Job Details
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Job Id</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control required number" name="job_id" id="job_id" value="<?php echo $job_id;?>" />
                                <?php echo Form::displayError('job_id');?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4">Related Job Id</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control number" name="previous_job_id" id="previous_job_id" value="<?php echo $previous_job_id;?>" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Status</label>
                            <div class="col-md-8">
                                <select id="status_id" class="form-control selectpicker" name="status_id" data-style="btn-outline-secondary"><option value="0">-- Select One --</option><?php echo $this->controller->jobstatus->getSelectJobStatus(Form::value('status_id'));?></select>
                                <?php echo Form::displayError('status_id');?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4">Sales Rep</label>
                            <div class="col-md-8">
                                <select id="salesrep_id" class="form-control selectpicker" name="salesrep_id" data-style="btn-outline-secondary"><option value="0">-- Select One --</option><?php echo $this->controller->salesrep->getSelectSalesReps(Form::value('salesrep_id'));?></select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">

                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card h-100 border-secondary order-card">
                    <div class="card-header bg-secondary text-white">
                        Customer Details
                    </div>
                    <div class="card-body">

                    </div>
                    <div class="card-footer text-right">

                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card h-100 border-secondary order-card">
                    <div class="card-header bg-secondary text-white">
                        Supplier Details
                    </div>
                    <div class="card-body">

                    </div>
                    <div class="card-footer text-right">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>