<?php
$panel_classes = array(
    'primary',
    'info',
    'success',
    'warning',
    'danger'
);
$c = 1;
?>
<div id="page-wrapper">
    <?php //echo $user_role;?>
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">3PL Plus Warehouse Management System</h1>
        </div>
    </div>
    <?php if($user_role == "admin" || $user_role == "warehouse"):?>
        <input type="hidden" id="admin_from_value" value="<?php echo strtotime('last friday', strtotime('-3 months'));?>" />
        <input type="hidden" id="admin_to_value" value="<?php echo strtotime('last friday', strtotime('tomorrow'));?>" />
        <?php //echo "<pre>",print_r($orders),"</pre>";die();?>
        <div class="row">
            <!-- unfulfilled orders -->
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-10 text-center">
                                <h2>Latest Unfulfilled Order Counts</h2>
                            </div>
                            <div class="col-xs-2 text-right">
                                 <a id="toggle_orders" data-toggle="collapse" href="#new_orders"><span class="fa arrow huge"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="collapse in" id="new_orders">
                            <div class="row">
                                <?php foreach($orders as $o):
                                    $s = ($o['order_count'] > 1)? "s" : ""; ?>
                                    <div class="col-lg-6">
                                        <div class="panel panel-<?php echo $panel_classes[$c % count($panel_classes)];?>">
                                            <div class="panel-heading order-panel">
                                                <h3 class="text-center"><?php echo $o['client_name'];?></h3>
                                            </div>
                                            <div class="panel-footer">
                                                <div class="row">
                                                    <div class="col-xs-8">
                                                        <div><span class="huge"><?php echo $o['order_count'];?></span> New Order<?php echo $s;?></div>
                                                        <div><a class="btn btn-<?php echo $panel_classes[$c % count($panel_classes)];?>" href="/orders/view-orders/client=<?php echo $o['client_id'];?>">Manage Orders</a></div>
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <i class="fas fa-truck fa-3x"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if($c % 2 == 0):?>
                                        </div><div class="row">
                                    <?php endif;++$c;?>
                                <?php endforeach;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- unfulfilled orders -->
            <!-- store orders -->
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-10 text-center">
                                <h2>Latest Unfulfilled Store Order Counts </h2>
                            </div>
                            <div class="col-xs-2 text-right">
                                 <a id="toggle_storeorders" data-toggle="collapse" href="#new_storeorders"><span class="fa arrow huge"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="collapse in" id="new_storeorders">
                            <div class="row">
                               <?php foreach($store_orders as $so):
                                    $s = ($so['order_count'] > 1)? "s" : ""; ?>
                                    <div class="col-lg-6">
                                        <div class="panel panel-<?php echo $panel_classes[$c % count($panel_classes)];?>">
                                            <div class="panel-heading order-panel">
                                                <h3 class="text-center"><?php echo $so['client_name'];?></h3>
                                            </div>
                                            <div class="panel-footer">
                                                <div class="row">
                                                    <div class="col-xs-8">
                                                        <div><span class="huge"><?php echo $so['order_count'];?></span> Store Order<?php echo $s;?></div>
                                                        <div><a class="btn btn-<?php echo $panel_classes[$c % count($panel_classes)];?>" href="/orders/view-storeorders/client=<?php echo $so['client_id'];?>">Manage Store Orders</a></div>
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <i class="fas fa-truck fa-3x"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if($c % 2 == 0):?>
                                        </div><div class="row">
                                    <?php endif;++$c;?>
                                <?php endforeach;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- store orders -->
        </div><!-- end 1st row -->
        <div class="row"><!-- second row -->
            <div class="col-lg-6">
                <?php include(Config::get('VIEWS_PATH')."layout/page-includes/dashboard_solar_installs.php");?>
            </div>
            <div class="col-lg-6">
                <?php include(Config::get('VIEWS_PATH')."layout/page-includes/dashboard_service_jobs.php");?>
            </div>
        </div> <!-- end 2nd row -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-10 text-center">
                                <h2>Client Activity Last 3 Months</h2>
                            </div>
                            <div class="col-xs-2 text-right">
                                 <a id="toggle_orders" data-toggle="collapse" href="#client_activity"><span class="fa arrow huge"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="collapse in" id="client_activity">
                            <div class="row">
                                <div class="col-md-6">
                                    <div id="error_activity_chart"></div>
                                </div>
                                <div class="col-md-6">
                                    <div id="order_activity_chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif($user_role == "client"):?>
        <input type="hidden" id="client_id" value="<?php echo $client_id; ?>" />
        <input type="hidden" id="from_value" value="<?php echo strtotime('last friday', strtotime('-3 months'));?>" />
        <input type="hidden" id="to_value" value="<?php echo strtotime('last saturday', strtotime('tomorrow'));?>" />
        <div class="bs-callout bs-callout-primary row bs-callout-more">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Quick Links</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <p><a class="btn btn-primary btn-lg" href="/orders/add-order"><i class="fa fas fa-truck fa-fw"></i> Add an Order</a></p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <p><a class="btn btn-primary btn-lg" href="/orders/client-orders"><i class="fa fas fa-truck fa-fw"></i> View Orders</a></p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <p><a class="btn btn-success btn-lg" href="/inventory/client-inventory"><i class="fa fas fa-tasks fa-fw"></i> View Inventory</a></p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <p><a class="btn btn-info btn-lg" href="/reports/dispatch-report"><i class="fa fas fa-chart-bar fa-fw"></i> Dispatch Report</a></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div id="orders_chart"></div>
            </div>
            <div class="col-lg-6">
                <div id="products_chart"></div>
            </div>
        </div>
    <?php elseif($user_role == "solar_admin"):?>
        <input type="hidden" id="solar_admin_from_value" value="<?php echo strtotime('last friday', strtotime('-3 months'));?>" />
        <input type="hidden" id="solar_admin_to_value" value="<?php echo strtotime('last saturday', strtotime('tomorrow'));?>" />
        <div class="row">
            <div class="col-lg-6">
                <?php include(Config::get('VIEWS_PATH')."layout/page-includes/dashboard_solar_installs.php");?>
            </div>
            <div class="col-lg-6">
                <?php include(Config::get('VIEWS_PATH')."layout/page-includes/dashboard_service_jobs.php");?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-10 text-center">
                                <h2>Job Activity Last 3 Months</h2>
                            </div>
                            <div class="col-xs-2 text-right">
                                 <a id="toggle_orders" data-toggle="collapse" href="#solar_activity"><span class="fa arrow huge"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="collapse in" id="solar_activity">
                            <div class="row">
                                <div class="col-md-6">
                                    <div id="solar_install_chart"></div>
                                </div>
                                <div class="col-md-6">
                                    <div id="solar_service_chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif($user_role == "solar"):?>
        <div class="bs-callout bs-callout-primary row bs-callout-more">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Quick Links</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <p><a class="btn btn-primary btn-lg" href="/solar-jobs/view-installs"><i class="fa fas fa-tools"></i> View Installs</a></p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <p><a class="btn btn-primary btn-lg" href="/solar-jobs/view-service-jobs"><i class="fa fas fa-tools"></i> View Service Jobs</a></p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <p><a class="btn btn-success btn-lg" href="/ordering/order-consumables"><i class="fa fas fa-cash-register"></i> Order Consumables</a></p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <p><a class="btn btn-info btn-lg" href="/scheduling/view-schedule"><i class="fa far fa-calendar-alt fa-fw"></i> Scheduling</a></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <hr/>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <h2>Some Job Info Will Be Here</h2>
            </div>
            <div class="col-lg-6">
                <h2>Some Scheduling Info Will Be Here</h2>
            </div>
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