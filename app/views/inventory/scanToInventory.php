<div id="page-wrapper">
    <input type="hidden" id="client_id" value="<?php echo $client_id;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="form-group">
            <label class="col-md-3">Select a Client</label>
            <div class="col-md-4">
                <p><select id="client_selector" class="form-control selectpicker"><option value="0">Select</option><?php echo $this->controller->client->getSelectClients($client_id);?></select></p>
            </div>
        </div>
    </div>
    <?php if($client_id > 0):?>
        <div class="row">
            <div class="col-lg-12">
                <?php if(isset($_SESSION['feedback'])) :?>
                   <div class='feedbackbox'><i class="far fa-check-circle"></i> <?php echo Session::getAndDestroy('feedback');?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['errorfeedback'])) :?>
                   <div class='errorbox'><i class="far fa-times-circle"></i> <?php echo Session::getAndDestroy('errorfeedback');?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">Item Barcode</label>
            <div class="col-md-4">
                <input type="text" class="form-control" name="item_barcode" id="item_barcode" placeholder="scan the barcode or manually type it in" />
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary btn-sm" id="get_item">Locate Item</button>
            </div>
        </div>
        <div id="item_details"></div>
    <?php endif;?>
</div>