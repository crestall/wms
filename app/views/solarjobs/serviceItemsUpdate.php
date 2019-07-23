<?php
$si_string = "";
foreach($job_items as $oi)
{
    $si_string .= $oi['id'].",";
}
$si_string = rtrim($si_string, ",");
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php if(!$job || !count($job)):?>
        <div class="row">
            <div class="col-md-12">
                <div class="errorbox">
                    <div class="row">
                        <div class="col-md-2" style="font-size:96px">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="col-md-6">
                            <h2>No Job Found</h2>
                            <p>No service job was found with that ID</p>
                            <p><a href="/solar-jobs/view-service-jobs">Please click here to view all service jobs to choose from</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else:?>
        <div class="row">
            <div class="col-md-4">
                <a class="btn btn-primary" href="/solar-jobs/view-service-jobs/type=<?php echo $job['type_id'];?>">View Service Jobs For <?php echo $job_type;?></a>
            </div>
            <div class="col-md-4">
                <a class="btn btn-primary" href="/solar-jobs/edit-servicejob/id=<?php echo $job['id'];?>">View This Job Details</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h2>Updating Items For Job Number <?php echo $job['work_order'];?></h2>
            </div>
        </div>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php echo Form::displayError('general');?>
        <div class="row">
            <form id="items-update" method="post" action="/form/procServiceItemsUpdate">
                <?php include(Config::get('VIEWS_PATH')."forms/item_updater.php");?>
                <input type="hidden" name="order_id" value="<?php echo $job['id'];?>" />
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <input type="hidden" name="client_id" id="client_id" value="<?php echo $job['client_id'];?>" />
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">&nbsp;</label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Update Items</button>
                    </div>
                </div>
            </form>
        </div>
    <?php endif;?>
</div>