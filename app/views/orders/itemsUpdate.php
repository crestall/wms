<?php
$si_string = "";
foreach($order_items as $oi)
{
    $si_string .= $oi['id'].",";
}
$si_string = rtrim($si_string, ",");
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php if($error):?>
        <div class="row">
            <div class="col-lg-12">
                <div class="errorbox">
                    <div class="row">
                        <div class="col-lg-2" style="font-size:96px">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="col-lg-6">
                            <h2>No Order ID Supplied</h2>
                            <p>No order was supplied to update</p>
                            <p><a href="/orders/view-orders">Please click here to view all orders to choose from</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif(!$order || !count($order)):?>
        <div class="row">
            <div class="col-lg-12">
                <div class="errorbox">
                    <div class="row">
                        <div class="col-lg-2" style="font-size:96px">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="col-lg-6">
                            <h2>No Order Found</h2>
                            <p>No order was found with that ID</p>
                            <p><a href="/orders/view-orders">Please click here to view all orders to choose from</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else:?>
        <div class="row">
            <div class="col-lg-4">
                <a class="btn btn-primary" href="/orders/order-update/order=<?php echo $order_id;?>">Return to Order</a>
            </div>
            <div class="col-lg-4">
                <a class="btn btn-primary" href="/orders/view-orders/client=<?php echo $order['client_id'];?>">View Orders For Client</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <h2>Updating Items For Order Number <?php echo $order['order_number'];?></h2>
            </div>
        </div>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php echo Form::displayError('general');?>
        <div class="row">
            <form id="items-update" method="post" action="/form/procItemsUpdate">
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/item_selector.php");?>








                <input type="hidden" name="order_id" value="<?php echo $order['id'];?>" />
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <input type="hidden" name="client_id" id="client_id" value="<?php echo $order['client_id'];?>" />
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
</div>