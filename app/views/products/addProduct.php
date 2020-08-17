<?php
$client_id = Form::value('client_id');
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php echo Form::displayError('general');?>
        <div class="row">
            <div class="col">
                <form id="add_product"  method="post" enctype="multipart/form-data" action="/form/procProductAdd">
                    <div class="row">
                        <div class="col-lg-12">
                            <h3>Details</h3>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control required" name="name" id="name" value="<?php echo Form::value('name');?>" />
                            <?php echo Form::displayError('name');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Image</label>
                        <div class="col-md-4">
                            <input type="file" name="image" id="image" />
                            <?php echo Form::displayError('image');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Client</label>
                        <div class="col-md-4">
                            <select id="client_id" name="client_id" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->client->getSelectClients($client_id);?></select>
                            <?php echo Form::displayError('client_id');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" id="type_label">Supplier</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="supplier" id="supplier" value="<?php echo Form::value('supplier');?>" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> SKU</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control required" name="sku" id="sku" value="<?php echo Form::value('sku');?>" />
                            <?php echo Form::displayError('sku');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Barcode</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="barcode" id="barcode" value="<?php echo Form::value('barcode');?>" />
                            <?php echo Form::displayError('barcode');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Box Barcode</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="box_barcode" id="box_barcode" value="<?php echo Form::value('box_barcode');?>" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Number in Box</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="per_box" id="per_box" value="<?php echo Form::value('per_box');?>" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3">Weight</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control number" name="weight" id="weight" value="<?php echo Form::value('weight');?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">Kg</span>
                                </div>
                            </div>
                            <?php echo Form::displayError('weight');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3">Width</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control number" name="width" id="width" value="<?php echo Form::value('width');?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                            <?php echo Form::displayError('width');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3">Depth</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control number" name="depth" id="depth" value="<?php echo Form::value('depth');?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                            <?php echo Form::displayError('depth');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3">Height</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control number" name="height" id="height" value="<?php echo Form::value('height');?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                            <?php echo Form::displayError('height');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3">Price</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="text" class="form-control" name="price" id="price" value="<?php echo Form::value('price');?>" />
                            </div>
                            <?php echo Form::displayError('price');?>
                        </div>
                    </div>
                    <div class="form-group row custom-control custom-checkbox custom-control-right">
                        <input class="custom-control-input" type="checkbox" id="requires_bubblewrap" name="requires_bubblewrap" <?php if(!empty(Form::value('requires_bubblewrap'))) echo "checked";?> />
                        <label class="custom-control-label col-md-3" for="requires_bubblewrap">Requires Bubblewrap</label>
                    </div>
                    <div class="form-group row custom-control custom-checkbox custom-control-right">
                        <input class="custom-control-input" type="checkbox" id="collection" name="collection" <?php if(!empty(Form::value('collection'))) echo "checked";?> />
                        <label class="custom-control-label col-md-3" for="collection">Collection</label>
                    </div>
                    <div class="form-group row custom-control custom-checkbox custom-control-right">
                        <input class="custom-control-input" type="checkbox" id="palletized" name="palletized" <?php if(!empty(Form::value('palletized'))) echo "palletized";?> />
                        <label class="custom-control-label col-md-3" for="palletized">Dispatch as Whole Pallets</label>
                    </div>
                    <div id="type_holder"></div>
                    <div class="row">
                        <div class="col-lg-12">
                            <h3>Inventory</h3>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3">Low Stock Trigger</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control number" name="trigger_point" id="trigger_point" value="<?php echo Form::value('trigger_point');?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text"><em>for warehouse</em></span>
                                </div>
                            </div>
                            <?php echo Form::displayError('trigger_point');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3">Low Stock Warning</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control number" name="low_stock_warning" id="low_stock_warning" value="<?php echo Form::value('low_stock_warning');?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text"><em>for client</em></span>
                                </div>
                            </div>
                            <?php echo Form::displayError('low_stock_warning');?>
                        </div>
                    </div>
                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">&nbsp;</label>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-outline-secondary">Add Product</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>