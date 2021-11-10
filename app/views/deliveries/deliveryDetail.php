<?php
$time_windows = array(
    'Within Two Hours'  => '+2 hours',
    'Same Day'          => 'today 5pm',
    'Next Day'          => 'tomorrow 5pm'
);
$required_time = strtotime($time_windows[$delivery['delivery_window']], $delivery['date_entered']);
$items = explode("~",$delivery['items']);
$pallet_count = 0;
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div id="print_this">
            <div class="row">
                <div class="col">
                    <h2>Details For Delivery Number: <?php echo $delivery['delivery_number'];?></h2>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6 col-xl-4 mb-3">
                    <div class="card border-secondary h-100 order-card">
                        <div class="card-header bg-secondary text-white">
                            Delivery Details
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <label class="col-5">Client Name</label>
                                <div class="col-7"><?php echo $delivery['client_name'];?></div>
                            </div>
                            <div class="row">
                                <label class="col-5">Attention</label>
                                <div class="col-7"><?php echo $delivery['attention'];?></div>
                            </div>
                            <div class="row">
                                <label class="col-5">Address</label>
                                <div class="col-7"><?php echo $delivery['address'];?></div>
                            </div>
                            <?php if(!empty($delivery['address_2'])):?>
                                <div class="row">
                                    <label class="col-5">&nbsp;</label>
                                    <div class="col-7"><?php echo $delivery['address_2'];?></div>
                                </div>
                            <?php endif;?>
                            <div class="row">
                                <label class="col-5">&nbsp;</label>
                                <div class="col-7"><?php echo $delivery['suburb'];?></div>
                            </div>
                            <div class="row">
                                <label class="col-5">&nbsp;</label>
                                <div class="col-7"><?php echo $delivery['state'];?></div>
                            </div>
                            <div class="row">
                                <label class="col-5">&nbsp;</label>
                                <div class="col-7"><?php echo $delivery['postcode'];?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 col-xl-4 mb-3">
                    <div class="card border-secondary h-100 order-card">
                        <div class="card-header bg-secondary text-white">
                            Delivery Status
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <label class="col-5">Requested At</label>
                                <div class="col-7"><?php echo date('D d/m/Y - g:i A', $delivery['date_entered']);?></div>
                            </div>
                            <div class="row">
                                <label class="col-5">Urgency</label>
                                <div class="col-7"><?php echo $delivery['delivery_window'];?></div>
                            </div>
                            <div class="row">
                                <label class="col-5">Required By</label>
                                <div class="col-7"><?php echo date('D d/m/Y - g:i A', $required_time);?></div>
                            </div>
                            <?php if($delivery['date_fulfilled'] > 0):?>
                                <div class="row">
                                    <label class="col-5">Completed At</label>
                                    <div class="col-7"><?php echo date('D d/m/Y - g:i A', $delivery['Date Fulfilled']);?></div>
                                </div>
                            <?php else:?>
                                <div class="row">
                                    <label class="col-5">&nbsp;</label>
                                    <div class="col-7">Not Yet Completed</div>
                                </div>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 col-xl-4 mb-3">
                    <div class="card border-secondary h-100 order-card">
                        <div class="card-header bg-secondary text-white">
                            Delivery Items
                        </div>
                        <div class="card-body">
                            <div class="border-bottom border-secondary mb-3 ">
                            <?php foreach($items as $i):
                                ++$pallet_count;
                                list($item_id, $item_name, $item_sku, $item_qty, $location_id) = explode("|",$i);?>
                                <div class="border-bottom border-secondary border-bottom-dashed mb-2 ">
                                    <div class="row">
                                        <label class="col-5">Item Name</label>
                                        <div class="col-7"><?php echo $item_name;?></div>
                                    </div>
                                    <div class="row">
                                        <label class="col-5">Item SKU</label>
                                        <div class="col-7"><?php echo $item_sku;?></div>
                                    </div>
                                    <div class="row">
                                        <label class="col-5">Quantity</label>
                                        <div class="col-7">Pallet of <?php echo $item_qty;?></div>
                                    </div>
                                </div>
                            <?php endforeach;?>
                            </div>
                            <div class="item_total text-right">
                                Total Pallets: <?php echo $pallet_count;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col text-right offset-xl-4 col-xl-4">
                <button class="btn btn-outline-fsg" id="print">Print These Details</button>
            </div>
        </div>
    </div>
</div>