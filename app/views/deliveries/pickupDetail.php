<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div id="print_this">
            <div class="row">
                <div class="col">
                    <h2>Details For Pickup Number: <?php echo $pickup['pickup_number'];?></h2>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6 col-xl-4 mb-3">
                    <div class="card border-secondary h-100 order-card">
                        <div class="card-header bg-secondary text-white">
                            Pickup Details
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <label class="col-5">Client Name</label>
                                <div class="col-7"><?php echo $pickup['client_name'];?></div>
                            </div>
                            <div class="row">
                                <label class="col-5">Requested By</label>
                                <div class="col-7"><?php echo $pickup['requested_by_name'];?></div>
                            </div>
                            <div class="row">
                                    <label class="col-5">Address</label>
                                    <div class="col-7"><?php echo $pickup['address'];?></div>
                                </div>
                                <?php if(!empty($pickup['address_2'])):?>
                                    <div class="row">
                                        <label class="col-5">&nbsp;</label>
                                        <div class="col-7"><?php echo $pickup['address_2'];?></div>
                                    </div>
                                <?php endif;?>
                                <div class="row">
                                    <label class="col-5">&nbsp;</label>
                                    <div class="col-7"><?php echo $pickup['suburb'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">&nbsp;</label>
                                    <div class="col-7"><?php echo $pickup['state'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">&nbsp;</label>
                                    <div class="col-7"><?php echo $pickup['postcode'];?></div>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 col-xl-4 mb-3">
                    <div class="card border-secondary h-100 order-card">
                        <div class="card-header bg-secondary text-white">
                            Pickup Status
                        </div>
                        <div class="card-body">

                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 col-xl-4 mb-3">
                    <div class="card border-secondary h-100 order-card">
                        <div class="card-header bg-secondary text-white">
                            Pickup Items
                        </div>
                        <div class="card-body">

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