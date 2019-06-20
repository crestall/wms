<?php

?>
<div class="row">
    <div class="col-lg-12">
        <?php if(isset($_SESSION['subtractfeedback'])) :?>
           <div class='feedbackbox'><i class="far fa-check-circle"></i> <?php echo Session::getAndDestroy('subtractfeedback');?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['subtracterrorfeedback'])) :?>
           <div class='errorbox'><i class="far fa-times-circle"></i> <?php echo Session::getAndDestroy('subtracterrorfeedback');?></div>
        <?php endif; ?>
        <p class="text-info">fields marked <sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> are required</p>
    </div>
</div>
<div class="row">
    <form id="subtract_from_stock" method="post" action="/form/procSubtractFromStock">
        <div class="row">
            <div class="col-lg-12">
                <h3>Subtract From Stock</h3>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Quantity</label>
            <div class="col-md-4">
                <input type="text" class="form-control required" name="qty_subtract" id="qty_subtract" value="<?php echo Form::value('qty_subtract');?>" />
                <?php echo Form::displayError('qty_subtract');?>
            </div>
        </div>
        <div class="form-group row">
            <div class="form-check">
                <label class="form-check-label col-md-3" for="qc_stock">Quality Control Stock</label>
                <div class="col-md-4 checkbox checkbox-default">
                    <input class="form-check-input styled" type="checkbox" id="qc_stock" name="qc_stock" <?php if(!empty(Form::value('qc_stock'))) echo 'checked';?> />
                    <label for="qc_stock"></label>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Location</label>
            <div class="col-md-4">
                <select id="subtract_from_location" name="subtract_from_location" class="form-control selectpicker" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectItemInLocations($product_id, Form::value('subtract_from_location'));?></select>
                <?php echo Form::displayError('subtract_from_location');?>
            </div>
        </div>
        <div class="form-group row">
            <div class="form-check">
                <label class="form-check-label col-md-3" for="remove_oversize">Remove Oversize</label>
                <div class="col-md-4 checkbox checkbox-default">
                    <input class="form-check-input styled" type="checkbox" id="remove_oversize" name="remove_oversize" <?php if(!empty(Form::value('remove_oversize'))) echo 'checked';?> />
                    <label for="remove_oversize"></label>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Reason</label>
            <div class="col-md-4">
                <select id="reason_id" name="reason_id" class="form-control selectpicker" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->stockmovementlabels->getSelectStockMovementLabels(Form::value('reason_id'));?></select>
                <?php echo Form::displayError('reason_id');?>
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
        <input type="hidden" name="subtract_product_id" value="<?php echo $product_id; ?>" />
        <input type="hidden" name="client_id" value="<?php echo $product_info['client_id']; ?>" />
        <input type="hidden" name="subtract_product_name" value="<?php echo $product_info['name']; ?>" />
        <div class="form-group row">
            <label class="col-md-3 col-form-label">&nbsp;</label>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Subtract from Stock</button>
            </div>
        </div>
    </form>
</div>