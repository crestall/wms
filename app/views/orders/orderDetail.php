<?php

?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php if($order_id == 0):?>
        <div class="row">
            <div class="col-lg-12">
                <div class="errorbox">
                    <h2>No Order ID Supplied</h2>
                    <p>No order id was supplied, so an order could not be found</p>
                    <p>Please <a href="/orders/client-orders">click here</a> to go back to the list of orders to select one to track.</p>
                </div>
            </div>
        </div>
    <?php elseif(empty($order)):?>
        <div class="row">
            <div class="col-lg-12">
                <div class="errorbox">
                    <h2>No Order Found</h2>
                    <p>No order was found with the supplied ID</p>
                    <p>Please <a href="/orders/client-orders">click here</a> to go back to the list of orders to select one to track.</p>
                </div>
            </div>
        </div>
    <?php else:?>
        <div id="print_this">
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/order_detail.php");?>
            <div class="row">
                <div class="col-xs-12">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>SKU</th>
                                <th>Qty Ordered</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($products as $p):?>
                                <tr>
                                    <td><?php echo $p['name'];?></td>
                                    <td><?php echo $p['sku'];?></td>
                                    <td><?php echo $p['qty'];?></td>
                                </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <p class="text-right"><button class="btn btn-primary" id="print">Print These Details</button></p>
            </div>
        </div>
    <?php endif;?>
</div>