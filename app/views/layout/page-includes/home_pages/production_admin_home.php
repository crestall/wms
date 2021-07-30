<div class="col-md-12 text-center">
    <h2>Quick Links</h2>
</div>
<div class="card-deck homepagedeck">
    <?php
    include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/quick_links/view-jobs.php");
    include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/quick_links/add-job.php");
    include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/quick_links/add-customer.php");
    include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/quick_links/add-finisher.php");
    ?>
</div>
<?php if(count($production_orders)):?>
    <div class="col-md-12 text-center">
        <h2>Latest Unfulfilled Production Order Counts</h2>
    </div>
    <div class="card-deck homepagedeck">
        <?php foreach($production_orders as $o):
            $s = ($o['order_count'] > 1)? "s" : ""; ?>
            <div class="card homepagecard">
                <div class="card-header">
                    <h4><?php echo $o['client_name'];?></h4>
                </div>
                <div class="card-body">
                	<i class="fad fa-truck fa-3x fa-flip-horizontal" style="vertical-align: middle;"></i>&nbsp;<span style="font-size:larger"><?php echo $o['order_count'];?> New Order<?php echo $s;?></span>
                </div>
                <div class="card-footer text-right">
                    <a class="btn btn-lg btn-outline-fsg" href="/warehouse-orders/view-orders/client=<?php echo $o['client_id'];?>">Manage Orders</a>
                </div>
            </div>
        <?php ++$c; endforeach;?>
    </div>
<?php else:?>
    <div class="col-md-12">
        <div class="errorbox">
            <h2><i class="fas fa-exclamation-triangle"></i> No Orders Listed</h2>
            <p>There are no unfulfilled production orders listed in the system</p>
        </div>
    </div>
<?php endif;?>
<?php include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/activity_charts.php"); ?> 