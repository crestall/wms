<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php echo $form;?>
        <div class="row">
            <label class="col">&nbsp;</label>
            <div class="col m-3 offset-6">
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
            <div class="row">
                <div class="col-12">
                    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/production_jobs_table.php");?>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>