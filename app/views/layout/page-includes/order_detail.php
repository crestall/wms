<div class="row">
    <div class="col-xs-12">
        <h2>Details For Order Number: <?php echo $order['order_number'];?></h2>
    </div>
</div>
<div class="row ">
    <div class="col-xs-6">
        <div class="bs-callout bs-callout-primary bs-callout-more">
            <dl class="dl-horizontal order-details">
                <dt>Deliver To</dt>
                <dd><?php echo $order['ship_to'];?></dd>
                <dt>Address</dt>
                <dd><?php echo $order['address'];?></dd>
                <?php if(!empty($order['address_2'])):?>
                    <dt>&nbsp;</dt>
                    <dd><?php echo $order['address_2'];?></dd>
                <?php endif;?>
                <dt>&nbsp;</dt>
                <dd><?php echo $order['suburb'];?></dd>
                <dt>&nbsp;</dt>
                <dd><?php echo $order['state'];?></dd>
                <dt>&nbsp;</dt>
                <dd><?php echo $order['postcode'];?></dd>
                <dt>&nbsp;</dt>
                <dd><?php echo $order['country'];?></dd>
            </dl>
        </div>
    </div>
    <div class="col-xs-6">
        <div class="bs-callout bs-callout-primary bs-callout-more">
            <dl class="dl-horizontal order-details">
                <dt>Order Status</dt>
                <dd><?php echo $order_status;?></dd>
                <dt>Date Ordered</dt>
                <dd><?php echo date('d-m-Y', $order['date_ordered']);?></dd>
                <dt>Date Fulfilled</dt>
                <dd><?php if($order['date_fulfilled'] > 0)echo date('d-m-Y', $order['date_fulfilled']);?></dd>
                <dt>Consignment ID</dt>
                <dd><?php if($order['date_fulfilled'] > 0)echo $order['consignment_id'];?></dd>
                <dt>Courier Service</dt>
                <dd><?php echo $courier;?></dd>
                <dt>Client Order Number</dt>
                <dd><?php echo $order['client_order_id'];?></dd>
            </dl>
        </div>
    </div>
</div>