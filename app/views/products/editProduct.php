<?php
$per_pallet_display = ($product['palletized'] == 0)? "none" : "block";
//die('form val: '.print_r((array)Form::value('package_type')));
$per_box = ($product['per_box'] > 0)? $product['per_box']: "";
$width = ($product['width'] > 0)? $product['width']: "";
$depth = ($product['depth'] > 0)? $product['depth']: "";
$height = ($product['height'] > 0)? $product['height']: "";
$price = ($product['price'] > 0)? $product['price']: "";
$weight = ($product['weight'] > 0)? $product['weight']: "";
$supplier = (!empty($product['supplier']))? $product['supplier']: "";
$client_id = $product['client_id'];

?>
<div id="page-wrapper">
    <?php //echo "<pre>",print_r($packing_types),"</pre>"; ?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <div class="row">
        <div class="col-lg-3">
            <a class="btn btn-primary" href="/inventory/view-inventory/client=<?php echo $client_id;?>">Return to Client Inventory</a>
        </div>
        <div class="col-lg-3">
            <a class="btn btn-primary" href="/products/view-products/client=<?php echo $client_id;?>">Return to Client Products</a>
        </div>
    </div>
    <div class="row">
        <form id="edit_product"  method="post" enctype="multipart/form-data" action="/form/procProductEdit">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Details</h3>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="name" id="name" value="<?php echo $product['name'];?>" />
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
            <?php if( !is_null($product['image']) && !empty($product['image']) ) :?>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Current Image</label>
                    <div class="col-md-4">
                        <div class="col-md-4">
                            <img src="/images/products/tn_<?php echo $product['image'];?>" class="thumbnail" />
                        </div>
                        <div class="col-md-6 checkbox checkbox-default">
                            <input class="form-check-input styled" type="checkbox" id="delete_image" name="delete_image" />
                            <label for="delete_image"><small><em>delete image</em></small></label>
                        </div>
                    </div>
                </div>
            <?php endif;?>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Client</label>
                <div class="col-md-4">
                    <select id="client_id" name="client_id" class="form-control selectpicker disabled" disabled><option value="0">--Select One--</option><?php echo $this->controller->client->getSelectClients($product['client_id']);?></select>
                    <?php echo Form::displayError('client_id');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" id="type_label">Supplier</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="supplier" id="supplier" value="<?php echo $supplier;?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Solar Supplier</label>
                <div class="col-md-4">
                    <select id="solar_type_id" name="solar_type_id" class="form-control selectpicker" <?php if($product['client_id'] != 67) echo "disabled"?>><option value="0">--Select One--</option><?php echo $this->controller->solarordertype->getSelectSolarOrderTypes($product['solar_type_id']);?></select>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="active">Active</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="active" name="active" <?php if($product['active'] > 0) echo "checked";?> />
                        <label for="active"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> SKU</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="sku" id="sku" value="<?php echo $product['sku']?>" />
                    <?php echo Form::displayError('sku');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Barcode</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="barcode" id="barcode" value="<?php echo $product['barcode'];?>" />
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
                    <input type="text" class="form-control" name="box_barcode" id="box_barcode" value="<?php echo $product['box_barcode'];?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Number in Box</label>
                <div class="col-md-4">
                    <input type="text" class="form-control number" name="per_box" id="per_box" value="<?php echo $per_box;?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Weight</label>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control number" name="weight" id="weight" value="<?php echo $weight;?>" />
                        <span class="input-group-addon">Kg</span>
                    </div>
                    <?php echo Form::displayError('weight');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Width</label>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control number" name="width" id="width" value="<?php echo $width;?>" />
                        <span class="input-group-addon">cm</span>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Depth</label>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control number" name="depth" id="depth" value="<?php echo $depth;?>" />
                        <span class="input-group-addon">cm</span>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Height</label>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control number" name="height" id="height" value="<?php echo $height;?>" />
                        <span class="input-group-addon">cm</span>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Price</label>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <input type="text" class="form-control" name="price" id="price" value="<?php echo $price;?>" />
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="requires_bubblewrap">Requires Bubblewrap</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="requires_bubblewrap" name="requires_bubblewrap" <?php if($product['requires_bubblewrap'] > 0) echo "checked";?> />
                        <label for="requires_bubblewrap"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="pack_item">Pack Item</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="pack_item" name="pack_item" <?php if($product['pack_item'] > 0) echo "checked";?> />
                        <label for="pack_item"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="collection">Collection</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="collection" name="collection" <?php if($product['collection'] > 0) echo "checked";?> />
                        <label for="collection"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="double_bay">Double Bay Item</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="double_bay" name="double_bay" <?php if($product['double_bay'] > 0) echo "checked";?> />
                        <label for="double_bay"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="palletized">Dispatch as Whole Pallets</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="palletized" name="palletized" <?php if($product['palletized'] > 0) echo "checked";?> />
                        <label for="palletized"></label>
                    </div>
                </div>
            </div>
            <div id="per_pallet_holder" style="display: <?php echo $per_pallet_display;?>">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Number per pallet</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required" name="per_pallet" id="per_pallet" value="<?php echo $product['per_pallet'];?>" />
                        <?php echo Form::displayError('per_pallet');?>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Package Type(s)</label>
                <div class="col-md-4">
                    <select id="package_type" name="package_type[]" class="form-control selectpicker" multiple><?php echo $this->controller->packingtype->getSelectPackingTypesMultiple(array_keys($packing_types));?></select>
                    <span class="inst">Select all relevent types</span>
                    <?php echo Form::displayError('package_type');?>
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
                    <select id="preferred_pick_location_id" name="preferred_pick_location_id" class="form-control selectpicker" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectLocations($product['preferred_pick_location_id'], $product['id']);?></select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Low Stock Trigger</label>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control number" name="trigger_point" id="trigger_point" value="<?php echo $product['trigger_point'];?>" />
                        <span class="input-group-addon"><em>for warehouse</em></span>
                    </div>
                    <?php echo Form::displayError('trigger_point');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Low Stock Warning</label>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control number" name="low_stock_warning" id="low_stock_warning" value="<?php echo $product['low_stock_warning'];?>" />
                        <span class="input-group-addon"><em>for client</em></span>
                    </div>
                    <?php echo Form::displayError('low_stock_warning');?>
                </div>
            </div>
            <!-- Hidden Inputs -->
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <input type="hidden" name="current_barcode" id="current_barcode" value="<?php echo $product['barcode']; ?>" />
            <input type="hidden" name="current_box_barcode" id="current_box_barcode" value="<?php echo $product['box_barcode']; ?>" />
            <input type="hidden" name="current_sku" id="current_sku" value="<?php echo $product['sku']; ?>" />
            <input type="hidden" name="item_id" value="<?php echo $product['id'];?>" />
            <input type="hidden" name="client_id" value="<?php echo $product['client_id'];?>" />
            <?php foreach($packing_types as $type_id => $number):?>
                <input type="hidden" id="pt_count_<?php echo $type_id;?>" value="<?php echo round(1 / $number);?>" />
            <?php endforeach;?>
            <!-- Hidden Inputs -->
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </div>
            </div>
        </form>
    </div>
</div>