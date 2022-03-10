<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col">
                <p><a class="btn btn-outline-fsg" href="/inventory/view-inventory/client=<?php echo $product_info['client_id'];?>">Client Inventory</a></p>
            </div>
            <div class="col text-right">
                <p><a class="btn btn-outline-fsg" href="/inventory/move-stock/product=<?php echo $product_id;?>">Move Stock</a></p>
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
            <div id="add" class="col-sm-8 col-md-8 col-lg-8 col-xl-4 mb-3">
                <div class="card h-100 border-secondary">
                    <div class="card-header bg-secondary text-white">
                        Add To Stock
                    </div>
                    <div class="card-body">
                        <?php if(isset($_SESSION['addfeedback'])) :?>
                           <div class='feedbackbox'><i class="far fa-check-circle"></i> <?php echo Session::getAndDestroy('addfeedback');?></div>
                        <?php endif; ?>
                        <?php if(isset($_SESSION['adderrorfeedback'])) :?>
                           <div class='errorbox'><i class="far fa-times-circle"></i> <?php echo Session::getAndDestroy('adderrorfeedback');?></div>
                        <?php endif; ?>
                        <p class="text-info">fields marked <sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> are required</p>
                        <div class="container-fluid">
                            <form id="add_to_stock" method="post" action="/form/procAddToStock">
                                <div class="form-group row">
                                    <label class="col-5 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Quantity</label>
                                    <div class="col-7">
                                        <input type="text" class="form-control required number" name="qty_add" id="qty_add" value="<?php echo Form::value('qty_add');?>" />
                                        <?php echo Form::displayError('qty_add');?>
                                    </div>
                                </div>
                                <div class="form-group row custom-control custom-checkbox custom-control-right">
                                    <input class="custom-control-input" type="checkbox" id="qc_stock" name="qc_stock" <?php if(!empty(Form::value('qc_stock'))) echo 'checked';?> />
                                    <label class="custom-control-label col-sm-8 col-xl-10" for="qc_stock">Under Quality Control</label>
                                </div>
                                <div class="form-group row">
                                    <label class="col-5"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Location</label>
                                    <div class="col-7">
                                        <select id="add_to_location" name="add_to_location" class="form-control selectpicker" data-live-search="true" data-style="btn-outline-secondary"><option value="0">--Select One--</option>
                                            <?php echo $this->controller->location->getSelectLocations(Form::value('add_to_location'), $product_id);?>
                                        </select>
                                        <?php echo Form::displayError('add_to_location');?>
                                    </div>
                                </div>
                                <?php if($this->controller->client->isDeliveryClient($product_info['client_id'])):?>
                                    <div class="form-group row">
                                        <label class="col-5"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Pallet Size</label>
                                        <div class="col-7">
                                            <select id="pallet_size" name="pallet_size" class="form-control selectpicker pallet_size" data-live-search="true" data-style="btn-outline-secondary">
                                                <?php echo Utility::getPalletSizeSelect(Form::value("pallet_size"));?>
                                            </select>
                                            <?php echo Form::displayError('pallet_size');?>
                                        </div>
                                    </div>
                                <?php else:?>
                                    <div id="oversize_holder" class="form-group row custom-control custom-checkbox custom-control-right">
                                        <input class="custom-control-input" type="checkbox" id="oversize" name="oversize" <?php if(!empty(Form::value('oversize'))) echo 'checked';?> />
                                        <label class="custom-control-label col-sm-8 col-xl-10" for="oversize">Mark as Oversize</label>
                                    </div>
                                    <div class="form-group row custom-control custom-checkbox custom-control-right">
                                        <input class="custom-control-input" type="checkbox" id="to_receiving" name="to_receiving" <?php if(!empty(Form::value('to_receiving'))) echo 'checked';?> />
                                        <label class="custom-control-label col-sm-8 col-xl-10" for="to_receiving">Add To Receiving</label>
                                    </div>
                                <?php endif;?>
                                <div class="form-group row">
                                    <label class="col-5 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Reason</label>
                                    <div class="col-7">
                                        <select id="reason_id" name="reason_id" class="form-control selectpicker" data-live-search="true" data-style="btn-outline-secondary"><option value="0">--Select One--</option><?php echo $this->controller->stockmovementlabels->getSelectStockMovementLabels(Form::value('reason_id'));?></select>
                                        <?php echo Form::displayError('reason_id');?>
                                    </div>
                                </div>
                                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                                <input type="hidden" name="add_product_id" value="<?php echo $product_id; ?>" />
                                <input type="hidden" name="client_id" value="<?php echo $product_info['client_id']; ?>" />
                                <input type="hidden" name="add_product_name" value="<?php echo $product_info['name']; ?>" />
                            </form>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button id="add_stock_submitter" class="btn btn-outline-secondary">Add to Stock</button>
                    </div>
                </div>
            </div>
            <div id="subtract" class="col-sm-8 col-md-8 col-lg-8 col-xl-4 ml-auto mb-3">
                <div class="card h-100 border-secondary">
                    <div class="card-header bg-secondary text-white">
                        Subtract From Stock
                    </div>
                    <div class="card-body">
                        <?php if(isset($_SESSION['subtractitemfeedback'])) :?>
                            <div class='feedbackbox'><i class="far fa-check-circle"></i> <?php echo Session::getAndDestroy('subtractitemfeedback');?></div>
                        <?php endif; ?>
                        <?php if(isset($_SESSION['subtractitemerrorfeedback'])) :?>
                            <div class='errorbox'><i class="far fa-times-circle"></i> <?php echo Session::getAndDestroy('subtractitemerrorfeedback');?></div>
                        <?php endif; ?>
                        <p class="text-info">fields marked <sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> are required</p>
                        <div class="container-fluid">
                            <form id="subtract_from_stock" method="post" action="/form/procSubtractFromStock">
                                <div class="form-group row">
                                    <label class="col-5"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Quantity</label>
                                    <div class="col-7">
                                        <?php if($this->controller->client->isDeliveryClient($product_info['client_id'])):?>
                                            <input type="text" class="form-control delivery-client required" name="qty_subtract" id="qty_subtract" placeholder="Full Pallet" value="<?php echo Form::value('qty_subtract');?>" readonly>
                                        <?php else:?>
                                            <input type="text" class="form-control required" name="qty_subtract" id="qty_subtract" value="<?php echo Form::value('qty_subtract');?>" />
                                            <?php echo Form::displayError('qty_subtract');?>
                                        <?php endif;?>
                                    </div>
                                </div>
                                <div class="form-group row custom-control custom-checkbox custom-control-right">
                                    <input class="custom-control-input" type="checkbox" id="sub_qc_stock" name="sub_qc_stock" <?php if(!empty(Form::value('sub_qc_stock'))) echo 'checked';?> />
                                    <label class="custom-control-label col-sm-8 col-xl-10" for="sub_qc_stock">Quality Control Stock</label>
                                </div>
                                <div class="form-group row">
                                    <label class="col-5"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Location</label>
                                    <div class="col-7">
                                        <select id="subtract_from_location" name="subtract_from_location" class="form-control selectpicker" data-live-search="true" data-style="btn-outline-secondary"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectItemInLocations($product_id, Form::value('subtract_from_location'));?></select>
                                        <?php echo Form::displayError('subtract_from_location');?>
                                    </div>
                                </div>
                                <div id="remove_oversize_holder" class="form-group row custom-control custom-checkbox custom-control-right" style="display:none">
                                    <input class="custom-control-input" type="checkbox" id="remove_oversize" name="remove_oversize" <?php if(!empty(Form::value('remove_oversize'))) echo 'checked';?> />
                                    <label class="custom-control-label col-sm-8 col-xl-10" for="remove_oversize">Remove Oversize</label>
                                </div>
                                <div class="form-group row">
                                    <label class="col-5">Reference</label>
                                    <div class="col-7">
                                        <input type="text" class="form-control" name="reference" id="reference" value="<?php echo Form::value('reference');?>" >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-5"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Reason</label>
                                    <div class="col-7">
                                        <select id="reason_id" name="reason_id" class="form-control selectpicker" data-live-search="true" data-style="btn-outline-secondary"><option value="0">--Select One--</option><?php echo $this->controller->stockmovementlabels->getSelectStockMovementLabels(Form::value('reason_id'));?></select>
                                        <?php echo Form::displayError('reason_id');?>
                                    </div>
                                </div>
                                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                                <input type="hidden" name="subtract_product_id" value="<?php echo $product_id; ?>" />
                                <input type="hidden" name="client_id" value="<?php echo $product_info['client_id']; ?>" />
                                <input type="hidden" name="subtract_product_name" value="<?php echo $product_info['name']; ?>" />
                            </form>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button id="subtract_stock_submitter" class="btn btn-outline-secondary">Subtract from Stock</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>