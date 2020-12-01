<div class="col-md-12 text-center">
    <h2>Quick Links</h2>
</div>
<div class="card-deck homepagedeck">
    <?php
    include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/quick_links/view-jobs.php");
    include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/quick_links/add-job.php");
    ?>
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
</div>