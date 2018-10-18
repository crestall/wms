<?php

?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="form-group row">
        <label class="col-md-3 col-form-label">Order Number</label>
        <div class="col-md-4">
            <input type="text" class="form-control" id="order_number" placeholder="Scan order barcode" />
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php if(isset($_SESSION['feedback'])) :?>
               <div class='feedbackbox'><?php echo Session::getAndDestroy('feedback');?></div>
            <?php endif; ?>
            <?php if(isset($_SESSION['errorfeedback'])) :?>
               <div class='errorbox'><?php echo Session::getAndDestroy('errorfeedback');?></div>
            <?php endif; ?>
        </div>
    </div>
    <div id="order_details"></div>
</div>