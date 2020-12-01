<?php
$page_title = ": Home Page";
$card_classes = array(
    'primary',
    'secondary',
    'info',
    'success',
    'warning',
    'danger'
);
$c = 1;
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php //echo $user_role;?>
<!----------------------------------------------------------------------------------------------------------------------------------------
---------------------------------------     Warehouse Users     --------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------------------>
        <?php if($user_role == "admin" || $user_role == "warehouse"):
            include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/warehouse_home.php");
        elseif($user_role == "client"):?>
<!----------------------------------------------------------------------------------------------------------------------------------------
---------------------------------------     Client Users     --------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------------------>
        <?php elseif(   $user_role == "production"
                        || $user_role == "production_admin"
                        || $user_role == "production_sales_admin"
                        || $user_role == "production_sales"):
            include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/client_home.php");?>
<!----------------------------------------------------------------------------------------------------------------------------------------
---------------------------------------     Production Users     --------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------------------>
            <div class="col-md-12 text-center">
                <h2>Quick Links</h2>
            </div>
            <div class="card-deck homepagedeck">
                <?php if($user_role == "production_admin"):?>
                <!----------------------------------------------------------------------------------------------------------------------------------------
                ---------------------------------------     Production Admin Users     --------------------------------------------------------------------------
                ------------------------------------------------------------------------------------------------------------------------------------------>
                    <div class="card homepagecard">
                        <div class="card-header">
                            <h4>View Jobs</h4>
                        </div>
                        <div class="card-body text-center">
                        	<a class="btn btn-lg btn-outline-fsg" href="/jobs/view-jobs"><span class="fa-layers fa-fw fa-3x align-middle"><i class="fad fa-tasks"></i><i class="fal fa-binoculars" data-fa-transform="shrink-6 up-2 right-6 rotate-30"></i></span>&nbsp;<span style="font-size:larger">Look!</span></a>
                        </div>
                    </div>
                    <div class="card homepagecard">
                        <div class="card-header">
                            <h4>Add A New Job</h4>
                        </div>
                        <div class="card-body text-center">
                        	<a class="btn btn-lg btn-outline-fsg" href="/jobs/add-job"><span class="fa-layers fa-fw fa-3x align-middle"><i class="fad fa-tasks"></i><i class="far fa-plus" data-fa-transform="shrink-4 up-3 right-4"></i></span>&nbsp;<span style="font-size:larger">Add!</span></a>
                        </div>
                    </div>
                    <div class="card homepagecard">
                        <div class="card-header">
                            <h4>Add A New Customer</h4>
                        </div>
                        <div class="card-body text-center">
                        	<a class="btn btn-lg btn-outline-fsg" href="/customers/add-customer"><span class="fa-layers fa-fw fa-3x align-middle"><i class="fad fa-user-tie"></i><i class="far fa-plus" data-fa-transform="shrink-4 up-3 right-4"></i></span>&nbsp;<span style="font-size:larger">Add!</span></a>
                        </div>
                    </div>
                    <div class="card homepagecard">
                        <div class="card-header">
                            <h4>Add A New Finisher</h4>
                        </div>
                        <div class="card-body text-center">
                        	<a class="btn btn-lg btn-outline-fsg" href="/finishers/add-finisher"><span class="fa-layers fa-fw fa-3x align-middle"><i class="fad fa-people-arrows"></i><i class="far fa-plus" data-fa-transform="shrink-4 up-3 right-4"></i></span>&nbsp;<span style="font-size:larger">Add!</span></a>
                        </div>
                    </div>
                <?php endif;?>
                <?php if($user_role == "production"):?>
                <!----------------------------------------------------------------------------------------------------------------------------------------
                ---------------------------------------     Production     --------------------------------------------------------------------------
                ------------------------------------------------------------------------------------------------------------------------------------------>
                    <div class="card homepagecard">
                        <div class="card-header">
                            <h4>View Jobs</h4>
                        </div>
                        <div class="card-body text-center">
                        	<a class="btn btn-lg btn-outline-fsg" href="/jobs/view-jobs"><span class="fa-layers fa-fw fa-3x align-middle"><i class="fad fa-tasks"></i><i class="fal fa-binoculars" data-fa-transform="shrink-6 up-2 right-6 rotate-30"></i></span>&nbsp;<span style="font-size:larger">Look!</span></a>
                        </div>
                    </div>
                    <div class="card homepagecard">
                        <div class="card-header">
                            <h4>Add A New Job</h4>
                        </div>
                        <div class="card-body text-center">
                        	<a class="btn btn-lg btn-outline-fsg" href="/jobs/add-job"><span class="fa-layers fa-fw fa-3x align-middle"><i class="fad fa-tasks"></i><i class="far fa-plus" data-fa-transform="shrink-4 up-3 right-4"></i></span>&nbsp;<span style="font-size:larger">Add!</span></a>
                        </div>
                    </div>
                <?php endif;?>
                <?php if($user_role == "production_sales_admin"):?>
                <!----------------------------------------------------------------------------------------------------------------------------------------
                ---------------------------------------     Production Sales Admin     --------------------------------------------------------------------------
                ------------------------------------------------------------------------------------------------------------------------------------------>
                    <div class="card homepagecard">
                        <div class="card-header">
                            <h4>View Finishers</h4>
                        </div>
                        <div class="card-body text-center">
                        	<a class="btn btn-lg btn-outline-fsg" href="/finishers/view-finishers"><span class="fa-layers fa-fw fa-3x align-middle"><i class="fad fa-people-arrows"></i><i class="fal fa-binoculars" data-fa-transform="shrink-6 up-2 right-6 rotate-30"></i></span>&nbsp;<span style="font-size:larger">Look!</span></a>
                        </div>
                    </div>

                <?php endif;?>
                <?php if($user_role == "production_sales"):?>
                <!----------------------------------------------------------------------------------------------------------------------------------------
                ---------------------------------------     Production Sales     --------------------------------------------------------------------------
                ------------------------------------------------------------------------------------------------------------------------------------------>
                <?php endif;?>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <div class="row">
                            <div class="col-lg-2" style="font-size:96px">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="col-lg-6">
                                <h2>User Classification Error</h2>
                                <p>Sorry, there has been an error determining your access priviledges</p>
                                <p><a href="/login/logout">Please click here to login again</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>