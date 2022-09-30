<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <?php //echo "<pre>",print_r($bookings),"</pre>";?>
            <div class="col-md-12">
                <p class="inst font-weight-bold">The displayed prices are GST exclusive.</p>
                <p class="inst font-weight-bold">No margin has been added.</p>
            </div>
        </div>
        <?php if(count($bookings)):?>
            <div id="waiting" class="row">
                <div class="col-lg-12 text-center">
                    <h2>Drawing Table..</h2>
                    <p>May take a few moments</p>
                    <img class='loading' src='/images/preloader.gif' alt='loading...' />
                </div>
            </div>
            <div class="row" id="table_holder" style="display:none">
                <div class="col-md-12">
                    <table class="table-striped table-hover" id="view_bookings_table" width="100%">
                        <thead>
                            <tr>
                                <th data-priority="1">Date Sent</th>
                                <th data-priority="1">Sent To</th>
                                <th data-priority="1">Consignment Id</th>
                                <th>Freight Charge</th>
                                <th>Other Charges</th>
                                <th>Fuel Levy</th>
                                <th data-priority="1">Total Charge</th>
                                <th data-priority="1">Delivery Tracking</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2><i class="fas fa-exclamation-triangle"></i> No Bookings Listed</h2>
                        <p>You may need to remove some filters</p>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>