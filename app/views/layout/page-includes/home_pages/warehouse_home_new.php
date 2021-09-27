<input type="hidden" id="admin_from_value" value="<?php echo strtotime('last friday', strtotime('-3 months'));?>" />
<input type="hidden" id="admin_to_value" value="<?php echo strtotime('last friday', strtotime('tomorrow'));?>" />
<div class="row">
    <div class="col-xl-3 col-md-6 col-12">
        <?php if(count($orders)):?>
            <div class="row text-center">
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
            <div class="errorbox">
                <h2><i class="fas fa-exclamation-triangle"></i> No Orders Listed</h2>
                <p>There are no unfulfilled orders listed in the system</p>
            </div>
        <?php endif;?>
    </div>
</div>
<div class="col-md-12 text-center">
    <h2>Client Activity Last 3 Months</h2>
</div>
<div id="order_activity_chart"></div>
<div class="col-md-12 text-right">
    <button class="btn btn-sm btn-outline-fsg" style="display:none" id="chart_button_1"></button>
</div>
<?php if(count($backorders)):?>
    <div class="col-md-12 text-center">
        <h2>Current Orders With Backorders</h2>
    </div>
    <div class="card-deck backorderdeck border border-secondary p-3 m-3 rounded bg-light ">
        <?php foreach($backorders as $o):
            $s = ($o['order_count'] > 1)? "s" : ""; ?>
            <div class="card backordercard">
                <div class="card-header">
                    <h4><?php echo $o['client_name'];?></h4>
                </div>
                <div class="card-body">
                	<i class="fad fa-truck fa-3x fa-flip-horizontal" style="vertical-align: middle;"></i>&nbsp;<span style="font-size:larger"><?php echo $o['order_count'];?> Backorder<?php echo $s;?></span>
                </div>
                <div class="card-footer text-right">
                    <a class="btn btn-lg btn-outline-fsglight" href="/orders/view-backorders/client=<?php echo $o['client_id'];?>">Manage Backorders</a>
                </div>
            </div>
        <?php ++$c; endforeach;?>
    </div>
<?php endif;?>