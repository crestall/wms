<?php
$can_do_runsheets  = false;
$can_change_status = false;
$need_checkbox = ($can_do_runsheets || $can_change_status);
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php echo $form;?>
        <div class="row">
            <div class="col offset-md-8">
                <a href="/jobs/job-search" class="btn btn-primary">Reset Form</a>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <h2>Search Results</h2>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <?php if($count > 0):?>
                    <div class="feedbackbox">
                        <h2>Found <?php echo $count;?> job<?php echo $s;?></h2>
                        <p><?php if($count == 1) echo "It is"; else echo "They are"?> listed below</p>
                    </div>
                <?php else:?>
                    <div class="errorbox">
                        <h2>No Jobs Found</h2>
                        <p>No Jobs were found when searching against "<strong><?php echo $term;?></strong>"</p>
                        <p>Maybe remove some filters?</p>
                    </div>
                <?php endif;?>
            </div>
        </div>
        <?php if($count > 0):
            $c = 0;?>
            <div id="waiting" class="row">
                <div class="col-lg-12 text-center">
                    <h2>Drawing Table..</h2>
                    <p>May take a few moments</p>
                    <img class='loading' src='/images/preloader.gif' alt='loading...' />
                </div>
            </div>
            <div class="mt-4" id="table_holder" style="display:none">
                <div class="row">
                    <label class="col-md-3 mb-3">Live Filter Text</label>
                    <div class="col-md-5 mb-3">
                        <input type="text" class="form-control" id="live-filter-text" >
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/production_jobs_table.php");?>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>