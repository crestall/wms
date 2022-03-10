<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col">
                <p><a class="btn btn-outline-fsg" href="/inventory/view-inventory/client=<?php echo $product_info['client_id'];?>">Client Inventory</a></p>
            </div>
            <div class="col text-right">
                <p><a class="btn btn-outline-fsg" href="/inventory/add-subtract-stock/product=<?php echo $product_id;?>">Add/Subtract Stock</a></p>
            </div>
        </div>
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
            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 mb-3">
                <div class="card h-100 border-secondary">
                    <div class="card-header bg-secondary text-white">
                        Move Stock
                    </div>
                    <div class="card-body">
                        <div class="container-fluid">
                            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
                            <form id="move_stock" method="post" action="/form/procStockMovement">
                                <div class="form-group row">
                                    <label class="col-md-5"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Number To Move</label>
                                    <div class="col-md-7">
                                    <?php if($this->controller->client->isDeliveryClient($product_info['client_id'])):?>
                                        <input type="text" class="form-control required" name="qty_move" id="qty_move" placeholder="Full Pallet" value="<?php echo Form::value('qty_move');?>" readonly>
                                    <?php else:?>
                                        <input type="text" class="form-control required" name="qty_move" id="qty_move" value="<?php echo Form::value('qty_move');?>" />
                                        <?php echo Form::displayError('qty_move');?>
                                    <?php endif;?>
                                    </div>
                                </div>
                                <div class="form-group row custom-control custom-checkbox custom-control-right">
                                    <input class="custom-control-input" type="checkbox" id="qc_stock" name="qc_stock" <?php if(!empty(Form::value('qc_stock'))) echo 'checked';?> />
                                    <label class="custom-control-label col-md-5" for="qc_stock">Move Quality Control Stock</label>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-5"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup>Move From Location</label>
                                    <div class="col-md-7">
                                        <select id="move_from_location" name="move_from_location" class="form-control selectpicker" data-live-search="true" data-style="btn-outline-secondary"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectItemInLocations($product_id, Form::value('move_from_location'));?></select>
                                        <?php echo Form::displayError('move_from_location');?>
                                    </div>
                                </div>
                                <div id="remove_oversize_holder" class="form-group row custom-control custom-checkbox custom-control-right" style="display:none">
                                    <input class="custom-control-input" type="checkbox" id="remove_oversize" name="remove_oversize" <?php if(!empty(Form::value('remove_oversize'))) echo 'checked';?> />
                                    <label class="custom-control-label col-sm-5" for="remove_oversize">Remove Oversize</label>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-5"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Move To Location</label>
                                    <div class="col-md-7">
                                        <select id="move_to_location" name="move_to_location" class="form-control selectpicker" data-live-search="true" data-style="btn-outline-secondary"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectLocations(Form::value('move_to_location'), $product_id);?></select>
                                        <?php echo Form::displayError('move_to_location');?>
                                    </div>
                                </div>
                                <div id="make_oversize_holder" class="form-group row custom-control custom-checkbox custom-control-right">
                                    <input class="custom-control-input" type="checkbox" id="make_oversize" name="make_oversize" <?php if(!empty(Form::value('make_oversize'))) echo 'checked';?> />
                                    <label class="custom-control-label col-md-5" for="make_oversize">Make Oversize</label>
                                </div>
                                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                                <input type="hidden" name="move_product_id" value="<?php echo $product_id; ?>" />
                                <input type="hidden" name="client_id" value="<?php echo $product_info['client_id']; ?>" />
                                <input type="hidden" name="move_product_name" value="<?php echo $product_info['name']; ?>" />
                            </form>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button id="move_stock_submitter" class="btn btn-outline-secondary">Move Stock</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>