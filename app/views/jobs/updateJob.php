<?php
$job_id = (!empty(Form::value('job_id')))? Form::value('job_id'):$job['job_id'];
$previous_job_id = (!empty(Form::value('previous_job_id')))? Form::value('previous_job_id'):$job['previous_job_id'];
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
                            <label class="col-md-4">Job Id</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control required number" name="job_id" id="job_id" value="<?php echo $job_id;?>" />
                            </div>
                        </div>
                        <div class="form-group row">Related Job Id</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control number" name="previous_job_id" id="previous_job_id" value="<?php echo $previous_job_id;?>" />
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