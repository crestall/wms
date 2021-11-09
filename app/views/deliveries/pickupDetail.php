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
        </div>
        <div class="row">
            <div class="col text-right">
                <button class="btn btn-outline-fsg" id="print">Print These Details</button>
            </div>
        </div>
    </div>
</div>