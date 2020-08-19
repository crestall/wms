<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col">
                <p><a class="btn btn-outline-fsg" href="/inventory/view-inventory/client=<?php echo $product_info['client_id'];?>">Client Inventory</a></p>
            </div>
            <div class="col text-right">
                <p><a class="btn btn-outline-fsg" href="/inventory/add-subtract-stock/product=<?php echo $product_id;?>">Add/Subtract Stock</a></p>
            </div>
        </div>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <div class="row">
            <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4 mb-3">
                <div class="card h-100 border-secondary">
                    <div class="card-header bg-secondary text-white">
                        Current Locations
                    </div>
                    <div class="card-body">
                        <?php echo $location_string;?>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-md-8 col-lg-8 col-xl-8 mb-3">
                <div class="card h-100 border-secondary">
                    <div class="card-header bg-secondary text-white">
                        Move Stock
                    </div>
                    <div class="card-body">
                        put the form here
                    </div>
                    <div class="card-footer">

                    </div>
                </div>
            </div>
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
                    <div class="form-group row">
                        <div class="form-check">
                            <label class="form-check-label col-md-5" for="os_location">Oversize Location</label>
                            <div class="col-md-7 checkbox checkbox-default">
                                <input class="form-check-input styled" type="checkbox" id="os_location" name="os_location" <?php if(!empty(Form::value('os_location'))) echo 'checked';?> />
                                <label for="os_location"></label>
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
        </div>
    </div>
</div>