<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if($order_id == 0):?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2><i class="fad fa-times-circle"></i> No Order ID Supplied</h2>
                        <p>No order id was supplied, so an order could not be found</p>
                        <p>Please <a href="/orders/client-orders">click here</a> to go back to the list of orders to select one to track.</p>
                    </div>
                </div>
            </div>
        <?php elseif(empty($order)):?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2><i class="fad fa-times-circle"></i> No Order Found</h2>
                        <p>No order was found with the supplied ID</p>
                        <p>Please <a href="/orders/client-orders">click here</a> to go back to the list of orders to select one to track.</p>
                    </div>
                </div>
            </div>
        <?php else:?>
            <div id="print_this" class="container-fluid">
                <div class="col-sm-12">
                    <h2>Details For Order Number: <?php echo $order['order_number'];?></h2>
                </div>
                <div class="col-sm-12 col-md-6 mb-3">
                    <div class="card border-secondary h-100 order-card">
                        <div class="card-header bg-secondary text-white">
                            Delivery Details
                        </div>
                        <div class="card-body">
                                    <label class="col-5">Client Order Number</label>
                                    <div class="col-7"><?php echo $order['client_order_id'];?></div>
                                    <label class="col-5">Deliver To</label>
                                    <div class="col-7"><?php echo $order['ship_to'];?></div>
                                    <label class="col-5">Company</label>
                                    <div class="col-7"><?php echo $order['company_name'];?></div>
                                    <label class="col-5">Address</label>
                                    <div class="col-7"><?php echo $order['address'];?></div>
                                <?php if(!empty($order['address_2'])):?>

                                        <label class="col-5">&nbsp;</label>
                                        <div class="col-7"><?php echo $order['address_2'];?></div>

                                <?php endif;?>
                                    <label class="col-5">&nbsp;</label>
                                    <div class="col-7"><?php echo $order['suburb'];?></div>
                                    <label class="col-5">&nbsp;</label>
                                    <div class="col-7"><?php echo $order['state'];?></div>
                                    <label class="col-5">&nbsp;</label>
                                    <div class="col-7"><?php echo $order['postcode'];?></div>
                                    <label class="col-5">&nbsp;</label>
                                    <div class="col-7"><?php echo $order['country'];?></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 mb-3">
                    <div class="card border-secondary h-100 order-card">
                        <div class="card-header bg-secondary text-white">
                            Order Status
                        </div>
                        <div class="card-body">
                            <label class="col-5">Order Status</label>
                            <div class="col-7"><?php echo $order_status;?></div>
                            <label class="col-5">Date Ordered</label>
                            <div class="col-7"><?php echo date('d-m-Y', $order['date_ordered']);?></div>
                            <label class="col-5">Date Fulfilled</label>
                            <div class="col-7"><?php if($order['date_fulfilled'] > 0)echo date('d-m-Y', $order['date_fulfilled']);?></div>
                            <label class="col-5">Courier Service</label>
                            <div class="col-7"><?php echo $courier;?></div>
                            <label class="col-5">Use Express</label>
                            <div class="col-7"><?php if($order['eparcel_express'] > 0) echo "Yes"; else echo "No";?></div>
                            <label class="col-5">Signature Required</label>
                            <div class="col-7"><?php if($order['signature_req'] > 0) echo "Yes"; else echo "No";?></div>
                            <label class="col-5">Consignment ID</label>
                            <div class="col-7"><?php if($order['date_fulfilled'] > 0)echo $order['consignment_id'];?></div>
                        </div>
                    </div>
                </div>
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
</div>