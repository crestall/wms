<?php
if( !isset($path) )
    $path = "/production-reports/warehouse-orders";
?>
<?php if(count($production_orders)):?>
    <div class="col-md-12 text-center">
        <h2>Latest Unfulfilled Production Order Counts</h2>
    </div>
    <div class="card-deck indexpagedeck">
        <?php foreach($production_orders as $o):
            $s = ($o['order_count'] > 1)? "s" : ""; ?>
            <div class="card indexpagecard">
                <div class="card-header">
                    <h4><?php echo $o['client_name'];?></h4>
                </div>
                <div class="card-body">
                	<i class="fad fa-truck fa-3x fa-flip-horizontal" style="vertical-align: middle;"></i>&nbsp;<span style="font-size:larger"><?php echo $o['order_count'];?> New Order<?php echo $s;?></span>
                </div>
                <div class="card-footer text-right">
                    <a class="btn btn-lg btn-outline-fsg" href="<?php echo $path;?>/client=<?php echo $o['client_id'];?>">Manage Orders</a>
                </div>
            </div>
        <?php ++$c; endforeach;?>
    </div>
<?php endif;?>