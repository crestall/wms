<?php

?>
<input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if(isset($_SESSION['deliveryfeedback'])) :?>
            <div class="row">
                <div class="col-12">
                    <div class='feedbackbox'><?php echo Session::getAndDestroy('deliveryfeedback');?></div>
                </div>
            </div>
        <?php endif; ?>
        <?php if(count($deliveries)):?>
            <div class="row">
                <div class="col text-center">
                    <span class="inst">These are Deliveries yet to be completed.<br>Complete deliveries can be found in the <a href="/reports/">Reports section</a></span>
                </div>
            </div>
            <div id="waiting" class="row">
                <div class="col-lg-12 text-center">
                    <h2>Drawing Table..</h2>
                    <p>May take a few moments</p>
                    <img class='loading' src='/images/preloader.gif' alt='loading...' />
                </div>
            </div>
            <div class="row" id="table_holder" style="display:none">
                <div class="col-12">
                    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/deliveries_table.php");?>
                </div>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2><i class="fas fa-exclamation-triangle"></i> No Open Deliveries Found</h2>
                        <p>You can use the Report Links above to view completed deliveries</p>
                        <p>You can search for deliveries <a href="/deliveries/delivery-search">here</a></p>
                        <p>You can book a new delivery <a href="/deliveries/book-delivery">here</a></p>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>