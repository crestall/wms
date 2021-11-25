<?php

?>
<input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if(isset($_SESSION['pickupfeedback'])) :?>
            <div class="row">
                <div class="col-12">
                    <div class='feedbackbox'><?php echo Session::getAndDestroy('pickupfeedback');?></div>
                </div>
            </div>
        <?php endif; ?>
        <?php if(count($pickups)):?>
            <div class="row">
                <div class="col text-center">
                    <span class="inst">These are Pickups yet to be completed.<br>Complete pickups can be found in the <a href="/reports/">Reports section</a></span>
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
                    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/pickups_table.php");?>
                </div>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2><i class="fas fa-exclamation-triangle"></i> No Open Pickups Found</h2>
                        <p>You can use the Report Links above to view completed prickups</p>
                        <p>You can search for pickups <a href="/deliveries/pickup-search">here</a></p>
                        <p>You can book a new pickup <a href="/deliveries/book-pickup">here</a></p>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>