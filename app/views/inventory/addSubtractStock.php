<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col">
                <p><a class="btn btn-outline-fsg" href="/inventory/view-inventory/client=<?php echo $product_info['client_id'];?>">Clieny Inventory</a></p>
            </div>
            <div class="col text-right">
                <p><a class="btn btn-outline-fsg" href="/inventory/move-stock/product=<?php echo $product_id;?>">Move Stock</a></p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3 col-md-3 col-lg-3 col-xl-4 mb-3">
                <div class="card h-100 border-secondary">
                    <div class="card-header bg-secondary text-white">
                        Current Locations
                    </div>
                    <div class="card_body">
                        <?php echo $location_string;?>
                    </div>
                </div>
            </div>
            <div class="col-sm-9 col-md-9 col-lg-9 col-xl-4 mb-3">
                <div class="card h-100 border-secondary">
                    <div class="card-header bg-secondary text-white">
                        Add To Stock
                    </div>
                    <div class="card_body">
                        <form id="add_to_stock" method="post" action="/form/procAddToStock">
                            <div class="row">
                                <label class="col-5 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Quantity</label>
                                <div class="col-7">
                                    <input type="text" class="form-control required number" name="qty_add" id="qty_add" value="<?php echo Form::value('qty_add');?>" />
                                    <?php echo Form::displayError('qty_add');?>
                                </div>
                            </div>
                            <div class="row custom-control custom-checkbox custom-control-right">
                                <input class="custom-control-input" type="checkbox" id="under_qc" name="under_qc" <?php if(!empty(Form::value('under_qc'))) echo 'checked';?> />
                                <label class="custom-control-label col-7" for="under_qc">Under Quality Control</label>
                            </div>
                            <div class="row">
                                <label class="col-5"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Location</label>
                                <div class="col-7">
                                    <select id="add_to_location" name="add_to_location" class="form-control selectpicker" data-live-search="true"><option value="0">--Select One--</option>
                                        <?php echo $this->controller->location->getSelectLocations(Form::value('add_to_location'), $product_id);?>
                                    </select>
                                    <?php echo Form::displayError('add_to_location');?>
                                </div>
                            </div>
                            <div class="row custom-control custom-checkbox custom-control-right">
                                <input class="custom-control-input" type="checkbox" id="to_receiving" name="to_receiving" <?php if(!empty(Form::value('to_receiving'))) echo 'checked';?> />
                                <label class="custom-control-label col-7" for="to_receiving">Add To Receiving</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-5 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Reason</label>
                                <div class="col-7">
                                    <select id="reason_id" name="reason_id" class="form-control selectpicker" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->stockmovementlabels->getSelectStockMovementLabels(Form::value('reason_id'));?></select>
                                    <?php echo Form::displayError('reason_id');?>
                                </div>
                            </div>
                            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                            <input type="hidden" name="add_product_id" value="<?php echo $product_id; ?>" />
                            <input type="hidden" name="client_id" value="<?php echo $product_info['client_id']; ?>" />
                            <input type="hidden" name="add_product_name" value="<?php echo $product_info['name']; ?>" />
                        </form>
                    </div>
                    <div class="card-footer text-right">
                        <button id="add_stock_submitter" class="btn btn-outline-secondary">Add to Stock</button>
                    </div>
                </div>
            </div>
            <div class="col-sm-9 col-md-9 col-lg-9 col-xl-4 ml-auto mb-3">
                <div class="card h-100 border-secondary">
                    <div class="card-header bg-secondary text-white">
                        Subtract From Stock
                    </div>
                    <div class="card_body">
                        The subtract from stock form goes in this box
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>