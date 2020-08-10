<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col-lg-3">
                <p><a href="/orders/view-orders/client=<?php echo $client_id;?>" class="btn btn-outline-secondary">View Orders For Client</a></p>
            </div>
            <?php if($single_order):?>
                <div class="col-lg-3">
                    <p><a href="/orders/order-update/order=<?php echo $order_id;?>" class="btn btn-outline-secondary">View This Order</a></p>
                </div>
            <?php endif;?>
        </div>
        <?php if($error):?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2><i class="far fa-times-circle"></i>There Was An Error Generating Labels</h2>
                        <?php echo $error_message;?>
                    </div>
                </div>
            </div>
        <?php else:?>
            <?php if(count($bad_orders)):?>
            <div class="row">
                <div class="errorbox">
                    <div class="col-12">
                        <h3><i class="far fa-times-circle"></i>There Was An Error With Some Orders</h3>
                    </div>
                    <?php foreach($bad_orders as $bo):?>
                        <div class="col-lg-3 col-md-4 col-sm-6"><?php echo $bo['order_number'];?></div>
                        <div class="col-lg-9 col-md-8 col-sm-6"><?php echo $bo['response_message'];?></div>
                    <?php endforeach;?>
                </div>
            </div>
            <?php endif;?>
            <div class="row">
                <div class="feedbackbox">
                    <div class="col-12">
                        <h3><i class="far fa-check-circle"></i>The following orders labels have been generated</h3>
                    </div>
                    <div class="col-md-6">
                        <?php echo implode(", ", $good_orders);?>
                    </div>
                    <div class="col-md-6">
                        <a class="btn tn-outline-secondary" href="<?php echo $url;?>">Click to Download Them</a>
                    </div>
            </div>
        <?php endif;?>
    </div>
</div>