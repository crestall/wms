<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if($order_id == 0):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/no_order_id.php");?>
        <?php elseif(empty($order)):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/no_order_found.php");?>
        <?php else:?>
            <?php if($user_role != "client"):?>
                <div class="row">
                    <div class="col">
                        <a class="btn btn-outline-secondary" href="/orders/order-update/order=<?php echo $order_id;?>">Return to Order</a>
                    </div>
                    <div class="col">
                        <a class="btn btn-outline-secondary" href="/orders/view-orders/client=<?php echo $order['client_id'];?>">View Orders For Client</a>
                    </div>
                </div>
            <?php endif;?>
            <div id="print_this" class="container-fluid">
                <?php include(Config::get('VIEWS_PATH')."layout/page-includes/order_detail_for_printing.php");?>
            </div>
            <div class="row">
                <div class="col text-right">
                    <button class="btn btn-outline-fsg" id="print">Print These Details</button>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>