<div class="col-sm-12 col-md-6 mb-3">
    <div class="card border-secondary h-100 order-card">
        <div class="card-header bg-secondary text-white">
            Order Details
        </div>
        <div class="card-body">
            <div class="row">
                    <label class="col-5">Client Order Number</label>
                    <div class="col-7"><?php echo $order['client_order_id'];?></div>
                </div>
                <div class="row">
                    <label class="col-5">Deliver To</label>
                    <div class="col-7"><?php echo $order['ship_to'];?></div>
                </div>
                <div class="row">
                    <label class="col-5">Company</label>
                    <div class="col-7"><?php echo $order['company_name'];?></div>
                </div>
                <div class="row">
                    <label class="col-5">Contact Phone</label>
                    <div class="col-7"><?php echo $order['contact_phone'];?></div>
                </div>
                <div class="row">
                    <label class="col-5">Tracking Email</label>
                    <div class="col-7"><?php echo $order['tracking_email'];?></div>
                </div>
                <div class="row">
                    <label class="col-5">Delivery Instructions</label>
                    <div class="col-7"><?php echo $order['instructions'];?></div>
                </div>
                <div class="row">
                    <label class="col-5">Use Express</label>
                    <div class="col-7"><?php if($order['eparcel_express'] > 0) echo "Yes"; else echo "No";?></div>
                </div>
                <div class="row">
                    <label class="col-5">Signature Required</label>
                    <div class="col-7"><?php if($order['signature_req'] > 0) echo "Yes"; else echo "No";?></div>
                </div>
                <div class="row">
                    <label class="col-5">Client Invoice</label>
                    <div class="col-7"><?php echo $order['uploaded_file'];?></div>
                </div>
                <div class="row">
                    <label class="col-5">Picking Instructions</label>
                    <div class="col-7"><?php echo $order['3pl_comments'];?></div>
                </div>
                <div class="row">
                    <label class="col-5">Entered By</label>
                    <div class="col-7"><?php echo $entered_by;?></div>
                </div>
        </div>
        <div class="card-footer text-right">
            <?php if($order['courier_id'] == 0 && ($user_role == "admin" || $user_role == "super admin")):?>
                <a class="btn btn-outline-secondary" href="/orders/order-edit/order=<?php echo $order_id;?>">Update Order Details</a>
            <?php endif;?>
        </div>
    </div>
</div>





    <div class="col-sm-12">
        <h2>Details For Order Number: <?php echo $order['order_number'];?></h2>
    </div>
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