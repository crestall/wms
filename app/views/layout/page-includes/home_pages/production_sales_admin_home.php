<div class="col-md-12 text-center">
    <h2>Quick Links</h2>
</div>
<div class="card-deck homepagedeck">
    <?php
    include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/quick_links/view-jobs.php"); 
    include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/quick_links/view-customers.php");
    include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/quick_links/add-customer.php");
    include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/quick_links/view-finishers.php");
    include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/quick_links/add-finisher.php");
    ?>
</div>
<?php include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/quick_links/production-orders.php"); ?>
<?php include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/activity_charts.php"); ?> 