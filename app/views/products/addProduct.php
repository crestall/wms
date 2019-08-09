<?php
$per_pallet_display = (empty(Form::value('palletized')))? "none" : "block";
//die('form val: '.print_r((array)Form::value('package_type')));
$client_id = (Session::getUserRole() == "solar admin")? $this->controller->solarordertype->TLJSolarId : Form::value('client_id');
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <div class="row">
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
                    <select id="client_id" name="client_id" class="form-control selectpicker" <?php if(Session::getUserRole() == "solar admin") echo "disabled";?>><option value="0">--Select One--</option><?php echo $this->controller->client->getSelectClients($client_id);?></select>
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
                <label class="col-md-3 col-form-label">Solar Supplier</label>
                <div class="col-md-4">
                    <select id="solar_type_id" name="solar_type_id" class="form-control selectpicker"><option value="0">--Select One--</option><?php echo $this->controller->solarordertype->getSelectSolarOrderTypes(Form::value('solar_type_id'));?></select>
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
            <!--div class="form-group row">
                <label class="col-md-3 col-form-label">Barcode Type</label>
                <div class="col-md-4">
                    <select id="barcode_type" name="barcode_type" class="form-control selectpicker"><?php echo $this->controller->barcodetype->getSelectBarcodeType(Form::value('barcode_type'));?></select>
                </div>
            </div-->
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
                <label class="col-md-3 col-form-label">Weight</label>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control number" name="weight" id="weight" value="<?php echo Form::value('weight');?>" />
                        <span class="input-group-addon">Kg</span>
                    </div>
                    <?php echo Form::displayError('weight');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Width</label>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control number" name="width" id="width" value="<?php echo Form::value('width');?>" />
                        <span class="input-group-addon">cm</span>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Depth</label>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control number" name="depth" id="depth" value="<?php echo Form::value('depth');?>" />
                        <span class="input-group-addon">cm</span>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Height</label>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control number" name="height" id="height" value="<?php echo Form::value('height');?>" />
                        <span class="input-group-addon">cm</span>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Price</label>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <input type="text" class="form-control" name="price" id="price" value="<?php echo Form::value('price');?>" />
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="requires_bubblewrap">Requires Bubblewrap</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="requires_bubblewrap" name="requires_bubblewrap" <?php if(!empty(Form::value('requires_bubblewrap'))) echo "checked";?> />
                        <label for="requires_bubblewrap"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="pack_item">Pack Item</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="pack_item" name="pack_item" <?php if(!empty(Form::value('pack_item'))) echo "checked";?> />
                        <label for="pack_item"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="collection">Collection</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="collection" name="collection" <?php if(!empty(Form::value('collection'))) echo "checked";?> />
                        <label for="collection"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="double_bay">Double Bay Item</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="double_bay" name="double_bay" <?php if(!empty(Form::value('double_bay'))) echo "checked";?> />
                        <label for="double_bay"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="palletized">Dispatch as Whole Pallets</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="palletized" name="palletized" <?php if(!empty(Form::value('palletized'))) echo "checked";?> />
                        <label for="palletized"></label>
                    </div>
                </div>
            </div>
            <div id="per_pallet_holder" style="display: <?php echo $per_pallet_display;?>">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Number per pallet</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required" name="per_pallet" id="per_pallet" value="<?php echo Form::value('per_pallet');?>" />
                        <?php echo Form::displayError('per_pallet');?>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Package Type(s)</label>
                <div class="col-md-4">
                    <select id="package_type" name="package_type[]" class="form-control selectpicker" multiple><?php echo $this->controller->packingtype->getSelectPackingTypesMultiple((array)Form::value('package_type'));?></select>
                    <span class="inst">Select all relevent types</span>
                </div>
            </div>
            <div id="type_holder"></div>
            <div class="row">
                <div class="col-lg-12">
                    <h3>Inventory</h3>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Preferred Pick Location</label>
                <div class="col-md-4">
                    <select id="preferred_pick_location_id" name="preferred_pick_location_id" class="form-control selectpicker" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectLocations(Form::value('preferred_pick_location_id'));?></select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Low Stock Trigger</label>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control number" name="trigger_point" id="trigger_point" value="<?php echo Form::value('trigger_point');?>" />
                        <span class="input-group-addon"><em>for warehouse</em></span>
                    </div>
                    <?php echo Form::displayError('trigger_point');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Low Stock Warning</label>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control number" name="low_stock_warning" id="low_stock_warning" value="<?php echo Form::value('low_stock_warning');?>" />
                        <span class="input-group-addon"><em>for client</em></span>
                    </div>
                    <?php echo Form::displayError('low_stock_warning');?>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </div>
        </form>
    </div>
</div>