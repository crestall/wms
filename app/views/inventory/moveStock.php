<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-3">
            <p><a class="btn btn-primary" href="/inventory/view-inventory/client=<?php echo $product_info['client_id'];?>">Return to Clients Inventory</a></p>
        </div>
        <div class="col-lg-3">
            <p><a class="btn btn-primary" href="/inventory/add-subtract-stock/product=<?php echo $product_id;?>">Add/Subtract Stock for This Item</a></p>
        </div>
    </div>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <div class="row">
        <div class="col-md-7">
            <form id="move_stock" method="post" action="/form/procStockMovement">
                <div class="form-group row">
                    <label class="col-md-5 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Number To Move</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control required number" name="qty_move" id="qty_move" value="<?php echo Form::value('qty_move');?>" />
                        <?php echo Form::displayError('qty_move');?>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="form-check">
                        <label class="form-check-label col-md-5" for="qc_stock">Move Quality Control Stock</label>
                        <div class="col-md-7 checkbox checkbox-default">
                            <input class="form-check-input styled" type="checkbox" id="qc_stock" name="qc_stock" <?php if(!empty(Form::value('qc_stock'))) echo 'checked';?> />
                            <label for="qc_stock"></label>
                        </div>
                    </div>
                </div>
                <!--div class="form-group row">
                    <div class="form-check">
                        <label class="form-check-label col-md-5" for="allocated_stock">Move Allocated Stock</label>
                        <div class="col-md-7 checkbox checkbox-default">
                            <input class="form-check-input styled" type="checkbox" id="allocated_stock" name="allocated_stock" <?php if(!empty(Form::value('allocated_stock'))) echo 'checked';?> />
                            <label for="allocated_stock"></label>
                        </div>
                    </div>
                </div-->
                <div class="form-group row">
                    <label class="col-md-5 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Move From Location</label>
                    <div class="col-md-7">
                        <select id="move_from_location" name="move_from_location" class="form-control selectpicker" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectItemInLocations($product_id, Form::value('move_from_location'));?></select>
                        <?php echo Form::displayError('move_from_location');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-5 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Move To Location</label>
                    <div class="col-md-7">
                        <?php if($product_info['double_bay'] > 0):?>
                            <select id="move_to_location" name="move_to_location" class="form-control selectpicker" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectDBLocations(Form::value('move_to_location'), $product_id);?></select>
                            <input type="hidden" name="double_bay" value="1"/>
                        <?php else:?>
                            <select id="move_to_location" name="move_to_location" class="form-control selectpicker" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectLocations(Form::value('move_to_location'), $product_id);?></select>
                            <input type="hidden" name="double_bay" value="0"/> 
                        <?php endif;?>
                        <?php echo Form::displayError('move_to_location');?>
                    </div>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <input type="hidden" name="move_product_id" value="<?php echo $product_id; ?>" />
                <input type="hidden" name="client_id" value="<?php echo $product_info['client_id']; ?>" />
                <input type="hidden" name="move_product_name" value="<?php echo $product_info['name']; ?>" />
                <div class="form-group row">
                    <label class="col-md-5 col-form-label">&nbsp;</label>
                    <div class="col-md-7">
                        <button type="submit" class="btn btn-primary">Move Stock</button>
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