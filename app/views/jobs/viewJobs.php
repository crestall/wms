<?php
$can_do_runsheets  = Permission::canDoRunsheets($user_role);
$can_change_status = Permission::canChangeStatus($user_role);
$need_checkbox = ($can_do_runsheets || $can_change_status);
?>
<div id="page-wrapper">
    <input type="hidden" id="completed" value="<?php echo $completed;?>" >
    <input type="hidden" id="cancelled" value="<?php echo $cancelled;?>" >
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <?php if($completed == 0):?>
                <div class="col-md-4 mb-3 text-center"><a class="btn btn-outline-fsg" href="/jobs/view-jobs/completed=1">View All Completed Jobs</a></div>
            <?php endif;?>
            <?php if($cancelled == 0):?>
                <div class="col-md-4 mb-3 text-center"><a class="btn btn-outline-fsg" href="/jobs/view-jobs/cancelled=1">View All Cancelled Jobs</a></div>
            <?php endif;?>
            <?php if($cancelled == 1 || $completed == 1):?>
                <div class="col-md-4 mb-3 text-center"><a class="btn btn-outline-fsg" href="/jobs/view-jobs">View All Active Jobs</a></div>
            <?php endif;?>
            <div class="col-md-4 mb-3 text-center"><a class="btn btn-outline-fsg" href="/jobs/job-search">Search All Jobs</a></div>
        </div>
        <div class="border border-secondary p-3 m-3 rounded bg-light">
            <h3>Filter <?php echo $filter;?> Jobs</h3>
            <div class="form-group row">
                <label class="col-md-2 mb-3">Filter By Customer</label>
                <div class="col-md-4 mb-3">
                    <select id="customer_id" name="customer_ids[]" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true" data-actions-box="true" multiple title="Filter by any of the following..."><?php echo $this->controller->productioncustomer->getMultiSelectCustomers($customer_ids);?></select>
                    <?php echo Form::displayError('client_id');?>
                </div>
                <label class="col-md-2 mb-3">Filter By Finisher</label>
                <div class="col-md-4 mb-3">
                    <select id="finisher_id" name="finisher_ids[]" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true" data-actions-box="true" multiple title="Filter by any of the following..."><?php echo $this->controller->productionfinisher->getMultiSelectFinishers($finisher_ids);?></select>
                </div>
                <label class="col-md-2 mb-3">Filter By FSG Contact</label>
                <div class="col-md-4 mb-3">
                    <select id="salesrep_id" name="salesrep_ids[]" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true" data-actions-box="true" multiple title="Filter by any of the following..."><?php echo $this->controller->salesrep->getMultiSelectSalesReps($salesrep_ids);?></select>
                </div>
                <?php if(!($completed == 1 || $cancelled == 1)):?>
                    <label class="col-md-2 mb-3">Filter By Status</label>
                    <div class="col-md-4 mb-3">
                        <select id="status_id" name="status_ids[]" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true" data-actions-box="true" multiple title="Filter by any of the following..."><?php echo $this->controller->jobstatus->getMultiSelectJobStatus($status_ids, 1, true, [9,11]);?></select>
                    </div>
                <?php endif;?>
                <label class="col-md-2 mb-3">Live Filter Text</label>
                <div class="col-md-4 mb-3">
                    <input type="text" class="form-control" id="live-filter-text" >
                </div>
                <div class="col-md-2 offset-md-8 mb-3">
                    <button class="btn btn-outline-danger" id="unfilter_jobs">Remove Filters</button>
                </div>
                <div class="col-md-2 mb-3">
                    <button class="btn btn-outline-fsg" id="filter_jobs">Apply Filters</button>
                </div>
            </div>
        </div>
        <?php //echo "<pre>",print_r($status_ids),"</pre>";?>
        <?php if(count($jobs)):?>
            <div id="waiting" class="row">
                <div class="col-lg-12 text-center">
                    <h2>Drawing Table..</h2>
                    <p>May take a few moments</p>
                    <img class='loading' src='/images/preloader.gif' alt='loading...' />
                </div>
            </div>
            <div class="row mt-4" id="table_holder" style="display:none">
                <?php //echo "<pre>",print_r($jobs),"</pre>";?>
                <?php if($can_do_runsheets):?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3"><button class="btn btn-sm btn-block btn-outline-primary" id="runsheet"><i class="fas fa-truck"></i> Add Selected to Chosen Day's Runsheet</button></div>
                <?php endif;?>
                <?php if($can_change_status):?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3"><button class="btn btn-sm btn-block btn-outline-fsg" id="status"><i class="fal fa-file-check"></i> Update Status for Selected</button></div>
                <?php endif;?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3"><button class="btn btn-sm btn-block btn-outline-secondary" id="priority_change"><i class="fal fa-file-plus"></i> Update Priority for Selected</button></div>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3"><button class="btn btn-sm btn-block btn-outline-success" id="create_pdf"><i class="fal fa-file-pdf"></i> Create PDF for Selected</button></div> 
                <div class="col-12">
                    <?php if(isset($_SESSION['feedback'])) :?>
                       <div class='feedbackbox'><?php echo Session::getAndDestroy('feedback');?></div>
                    <?php endif; ?>
                    <?php if(isset($_SESSION['errorfeedback'])) :?>
                       <div class='errorbox'><?php echo Session::getAndDestroy('errorfeedback');?></div>
                    <?php endif; ?>
                </div>
                <div class="col-12">
                    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/production_jobs_table.php");?>
                </div>
            </div>
        <?php else:?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="errorbox">
                            <h2><i class="fas fa-exclamation-triangle"></i> No Jobs Listed</h2>
                        </div>
                    </div>
                </div>
        <?php endif;?>
    </div>
</div>