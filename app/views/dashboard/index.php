<?php
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
    <div id="page_container" class="container">
        <?php if($user_role == "admin" || $user_role == "warehouse"):?>
            <input type="hidden" id="admin_from_value" value="<?php echo strtotime('last friday', strtotime('-3 months'));?>" />
            <input type="hidden" id="admin_to_value" value="<?php echo strtotime('last friday', strtotime('tomorrow'));?>" />
            <?php if(count($orders)):?>
                <div class="col-md-12 text-center">
                    <h2>Latest Unfulfilled Order Counts</h2>
                </div>
                <div class="card-deck homepagedeck">
                    <?php foreach($orders as $o):
                        $s = ($o['order_count'] > 1)? "s" : ""; ?>
                        <div class="card homepagecard">
                            <div class="card-header">
                                <h4><?php echo $o['client_name'];?></h4>
                            </div>
                            <div class="card-body">
                            	<i class="fad fa-truck fa-3x fa-flip-horizontal" style="vertical-align: middle;"></i>&nbsp;<span style="font-size:larger"><?php echo $o['order_count'];?> New Order<?php echo $s;?></span>
                            </div>
                            <div class="card-footer text-right">
                                <a class="btn btn-lg btn-outline-fsg" href="/orders/view-orders/client=<?php echo $o['client_id'];?>">Manage Orders</a>
                            </div>
                        </div>
                    <?php ++$c; endforeach;?>
                </div>
            <?php else:?>
                <div class="col-md-12">
                    <div class="errorbox">
                        <h2><i class="fas fa-exclamation-triangle"></i> No Orders Listed</h2>
                        <p>There are no unfulfilled orders listed in the system</p>
                    </div>
                </div>
            <?php endif;?>
            <div class="col-md-12 text-center">
                <h2>Client Activity Last 3 Months</h2>
            </div>
            <div id="order_activity_chart"></div>
            <div class="col-md-12 text-right">
                <button class="btn btn-sm btn-outline-fsg" id="chart_button_1"></button>
            </div>
        <?php elseif($user_role == "client"):?>
            <input type="hidden" id="client_id" value="<?php echo $client_id; ?>" />
            <input type="hidden" id="from_value" value="<?php echo strtotime('last friday', strtotime('-3 months'));?>" />
            <input type="hidden" id="to_value" value="<?php echo strtotime('last saturday', strtotime('tomorrow'));?>" />
            <div class="col-md-12 text-center">
                <h2>Quick Links</h2>
            </div>
            <div class="card-deck homepagedeck">
                <div class="card homepagecard">
                    <div class="card-header">
                        <h4>Create An Order</h4>
                    </div>
                    <div class="card-body text-center">
                    	<a class="btn btn-lg btn-outline-fsg" href="/orders/add-order"><i class="fad fa-shipping-fast fa-3x fa-flip-horizontal" style="vertical-align: middle;"></i>&nbsp;<span style="font-size:larger">Go!</span></a>
                    </div>
                </div>
                <div class="card homepagecard">
                    <div class="card-header">
                        <h4>View Orders</h4>
                    </div>
                    <div class="card-body text-center">
                    	<a class="btn btn-lg btn-outline-fsg" href="/orders/client-orders"><i class="fad fa-th-list fa-3x" style="vertical-align: middle;"></i>&nbsp;<span style="font-size:larger">Look!</span></a>
                    </div>
                </div>
                <div class="card homepagecard">
                    <div class="card-header">
                        <h4>View Inventory</h4>
                    </div>
                    <div class="card-body text-center">
                    	<a class="btn btn-lg btn-outline-fsg" href="/inventory/client-inventory"><i class="fad fa-inventory fa-3x" style="vertical-align: middle;"></i>&nbsp;<span style="font-size:larger">Check!</span></a>
                    </div>
                </div>
                <div class="card homepagecard">
                    <div class="card-header">
                        <h4>Dispatch Reports</h4>
                    </div>
                    <div class="card-body text-center">
                    	<a class="btn btn-lg btn-outline-fsg" href="/reports/dispatch-report"><i class="fad fa-file-spreadsheet fa-3x" style="vertical-align: middle;"></i>&nbsp;<span style="font-size:larger">Read!</span></a>
                    </div>
                </div>
            </div>
            <div id="orders_chart"></div>
            <div class="col-md-12 text-right">
                <button class="btn btn-sm btn-outline-fsg" id="chart_button_2"></button>
            </div>
            <div id="products_chart"></div>
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