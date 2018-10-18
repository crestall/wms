<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-3">
            <p><a class="btn btn-primary" href="/inventory/view-inventory/client=<?php echo $product_info['client_id'];?>">Return to Clients Inventory</a> </p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php if(isset($_SESSION['feedback'])) :?>
               <div class='feedbackbox'><i class="far fa-check-circle"></i> <?php echo Session::getAndDestroy('feedback');?></div>
            <?php endif; ?>
            <?php if(isset($_SESSION['errorfeedback'])) :?>
               <div class='errorbox'><i class="far fa-times-circle"></i> <?php echo Session::getAndDestroy('errorfeedback');?></div>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-7">
            <form id="quality_control" method="post" action="/form/procQualityControl">
                <div class="form-group row">
                    <label class="col-md-5 col-form-label">Add To Quality Control</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control number" name="qty_add" id="qty_add" value="<?php echo Form::value('qty_add');?>" />
                        <?php echo Form::displayError('qty_add');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-5 col-form-label">Location</label>
                    <div class="col-md-7">
                        <select id="add_to_location" name="add_to_location" class="form-control selectpicker" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectItemInLocations($product_id, Form::value('add_to_location'));?></select>
                        <?php echo Form::displayError('add_to_location');?>
                    </div>
                </div>
                <?php if($show_remove):?>
                    <div class="form-group row">
                        <label class="col-md-5 col-form-label">Remove From Quality Control</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control number" name="qty_subtract" id="qty_subtract" value="<?php echo Form::value('qty_subtract');?>" />
                            <?php echo Form::displayError('qty_subtract');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-5 col-form-label">Location</label>
                        <div class="col-md-7">
                            <select id="subtract_from_location" name="subtract_from_location" class="form-control selectpicker" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectQCItemInLocations($product_id, Form::value('subtract_from_location'));?></select>
                            <?php echo Form::displayError('subtract_from_location');?>
                        </div>
                    </div>
                <?php endif;?>
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
                <input type="hidden" name="product_name" value="<?php echo $product_info['name']; ?>" />
                <div class="form-group row">
                    <label class="col-md-5 col-form-label">&nbsp;</label>
                    <div class="col-md-7">
                        <button type="submit" class="btn btn-primary">Update Quality Control Stock</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-5">
            <label>Current Locations</label>
            <textarea class="form-control disabled" rows="<?php echo $rows;?>" disabled><?php echo $location_string;?></textarea>
        </div>
    </div>
</div>