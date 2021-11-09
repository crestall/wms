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