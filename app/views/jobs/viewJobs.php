<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if(count($jobs)):?>
            <div id="waiting" class="row">
                <div class="col-lg-12 text-center">
                    <h2>Drawing Table..</h2>
                    <p>May take a few moments</p>
                    <img class='loading' src='/images/preloader.gif' alt='loading...' />
                </div>
            </div>
            <div class="row mt-4" id="table_holder" style="display:none">
                <?php //echo "User Role $user_role";?>
                <div class="col-md-4 mb-3 text-center"><a class="btn btn-outline-fsg" href="/jobs/view-jobs/completed=1">View Only Completed Jobs</a></div>
                <div class="col-md-4 mb-3 text-center"><a class="btn btn-outline-fsg" href="/jobs/view-jobs">View Only Incompleted Jobs</a></div>
                <div class="col-md-4 mb-3 text-center"><a class="btn btn-outline-fsg" href="/jobs/view-jobs/cancelled=1">View Only Cancelled Jobs</a></div>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3"><button class="btn btn-sm btn-block btn-outline-primary p-3 driver-runsheet"><i class="fas fa-truck"></i> Add Selected to Chosen Driver's Runsheet</button></div>
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