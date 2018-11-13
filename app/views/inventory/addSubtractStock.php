<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-3">
            <p><a class="btn btn-primary" href="/inventory/view-inventory/client=<?php echo $product_info['client_id'];?>">Return to Clients Inventory</a></p>
        </div>
        <div class="col-lg-3">
            <p><a class="btn btn-primary" href="/inventory/move-stock/product=<?php echo $product_id;?>">Move Stock for This Item</a></p>
        </div>
    </div>
    <?php echo $addform;?>
    <?php if($onhand > 0):?>
        <?php echo $subtractform;?>
    <?php endif;?>
</div>