<?php
$add_to_location = (empty(Form::value('add_to_location')))? $product_info['preferred_pick_location_id'] : Form::value('add_to_location');
$item_id = $product_id;
$div_class = "col-md-7";
$label_class = "col-md-5";

//echo "<pre>",print_r($product_info),"</pre>";
?>
<div class="row">
    <div class="col-md-12">
        <?php if(isset($_SESSION['addfeedback'])) :?>
           <div class='feedbackbox'><i class="far fa-check-circle"></i> <?php echo Session::getAndDestroy('addfeedback');?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['adderrorfeedback'])) :?>
           <div class='errorbox'><i class="far fa-times-circle"></i> <?php echo Session::getAndDestroy('adderrorfeedback');?></div>
        <?php endif; ?>
        <p class="text-info">fields marked <sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> are required</p>
    </div>
</div>
<div class="row">
    <div class="col-md-7">
        <form id="add_to_stock" method="post" action="/form/procAddToStock">
            <div class="row">
                <div class="col-md-12">
                    <h3>Add To Stock</h3>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-5 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Quantity</label>
                <div class="col-md-7">
                    <input type="text" class="form-control required number" name="qty_add" id="qty_add" value="<?php echo Form::value('qty_add');?>" />
                    <?php echo Form::displayError('qty_add');?>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-5" for="under_qc">Under Quality Control</label>
                    <div class="col-md-7 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="under_qc" name="under_qc" <?php if(!empty(Form::value('under_qc'))) echo 'checked';?> />
                        <label for="under_qc"></label>
                    </div>
                </div>
            </div>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/location_selector.php");?>
            <div class="form-group row">
                <label class="col-md-5 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Reason</label>
                <div class="col-md-7">
                    <select id="reason_id" name="reason_id" class="form-control selectpicker" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->stockmovementlabels->getSelectStockMovementLabels(Form::value('reason_id'));?></select>
                    <?php echo Form::displayError('reason_id');?>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <input type="hidden" name="add_product_id" value="<?php echo $product_id; ?>" />
            <input type="hidden" name="client_id" value="<?php echo $product_info['client_id']; ?>" />
            <input type="hidden" name="add_product_name" value="<?php echo $product_info['name']; ?>" />
            <div class="form-group row">
                <label class="col-md-5 col-form-label">&nbsp;</label>
                <div class="col-md-7">
                    <button type="submit" class="btn btn-primary">Add To Stock</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-5">
        <label>Current Locations</label>
        <textarea class="form-control disabled" rows="<?php echo $rows;?>" disabled><?php echo $location_string;?></textarea>
    </div>
</div>