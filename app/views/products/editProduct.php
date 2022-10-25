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
$is_arccos =  0;

if($product['client_id'] == 87)
{
    if(!empty(Form::value('is_arccos')))
        $is_arcoss = 1;
    else
        $is_arccos = $product['is_arccos'];
}

if(preg_match('/https?/i', $product['image']))
{
    $eximage_display = "display:inline";
    $image_display = "display:none";
    $check = true;
}
else
{
    $eximage_display = "display:none";
    $image_display = "display:inline";
    $check = false;
}
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php //echo "<pre>",print_r($packing_types),"</pre>"; ?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <?php echo Form::displayError('general');?>
        <div class="row mb-3">
            <div class="col-6">
                <a class="btn btn-outline-fsg" href="/inventory/view-inventory/client=<?php echo $client_id;?>">Return to Client Inventory</a>
            </div>
            <div class="col-6 text-right">
                <a class="btn btn-outline-fsg" href="/products/view-products/client=<?php echo $client_id;?>">Return to Client Products</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
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
                            <div class="col checkbox checkbox-default">
                                <input class="form-check-input styled" type="checkbox" id="external_image" name="external_image" <?php if($check) echo "checked";?> />
                                <label for="external_image"><small><em>Image URL</em></small></label>
                            </div>
                            <input type="file" name="image" id="image" class="product_image" style="<?php echo $image_display;?>" />
                            <input type="text" class="product_image form-control" name="eximage" id="eximage" style="<?php echo $eximage_display;?>" value="<?php echo $product['image'];?>">
                            <?php echo Form::displayError('image');?>
                        </div>
                    </div>
                    <?php if( !is_null($product['image']) && !empty($product['image']) ) :
                        if(preg_match('/https?/i', $product['image']))
                        {
                            $image = "<img src='{$product['image']}' class='thumbnail' />";
                            $image_text = "This Is The Image Currently In Use.<br>It Is On An External Server";
                        }
                        else
                        {
                            $image = "<img src='/images/products/tn_{$product['image']}' class='thumbnail' />";
                            $image_text = "This Is The Image Currently In Use.<br>It Is On This Server.";
                        }
                        ?>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Current Image</label>
                            <div class="col-md-6">
                                <div class="col-md-4">
                                    <?php echo $image;?><br>
                                    <span class="inst"><?php echo $image_text;?></span>
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
                    <div id="is_arccos_holder" style="display:<?php echo ($client_id == 87)? "block":"none";?>">
                        <div class="form-group row custom-control custom-checkbox custom-control-right">
                            <input class="custom-control-input" type="checkbox" id="is_arccos" name="is_arccos" <?php if($is_arccos > 0) echo "checked";?> />
                            <label class="custom-control-label col-md-3" for="is_arccos">Arccos product</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" id="type_label">Supplier</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="supplier" id="supplier" value="<?php echo $supplier;?>" />
                        </div>
                    </div>
                    <div class="form-group row custom-control custom-checkbox custom-control-right">
                        <input class="custom-control-input" type="checkbox" id="active" name="active" <?php if($product['active'] > 0) echo "checked";?> />
                        <label class="custom-control-label col-md-3" for="active">Active</label>
                    </div>
                    <div class="form-group row custom-control custom-checkbox custom-control-right">
                        <input class="custom-control-input" type="checkbox" id="is_pod" name="is_pod" <?php if($product['is_pod'] > 0) echo "checked";?> />
                        <label class="custom-control-label col-md-3" for="is_pod">Print on Demand</label>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> SKU</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control required" name="sku" id="sku" value="<?php echo $product['sku']?>" />
                            <?php echo Form::displayError('sku');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Client Product ID</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="client_product_id" id="client_product_id" value="<?php echo $product['client_product_id'];?>" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Barcode</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="barcode" id="barcode" value="<?php echo $product['barcode'];?>" />
                            <?php echo Form::displayError('barcode');?>
                        </div>
                    </div>
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
                    <div class="form-group row custom-control custom-checkbox custom-control-right">
                        <input class="custom-control-input" type="checkbox" id="boxed_item" name="boxed_item" <?php if($product['boxed_item'] > 0) echo "checked";?> />
                        <label class="custom-control-label col-md-3" for="boxed_item">Boxed Item</label>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3">Weight</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control number" name="weight" id="weight" value="<?php echo $weight;?>" />
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
                                <input type="text" class="form-control number" name="width" id="width" value="<?php echo $width;?>" />
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
                                <input type="text" class="form-control number" name="depth" id="depth" value="<?php echo $depth;?>" />
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
                                <input type="text" class="form-control number" name="height" id="height" value="<?php echo $height;?>" />
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
                                <input type="text" class="form-control" name="price" id="price" value="<?php echo $price;?>" />
                            </div>
                            <?php echo Form::displayError('price');?>
                        </div>
                    </div>
                    <div class="form-group row custom-control custom-checkbox custom-control-right">
                        <input class="custom-control-input" type="checkbox" id="requires_bubblewrap" name="requires_bubblewrap" <?php if($product['requires_bubblewrap'] > 0) echo "checked";?> />
                        <label class="custom-control-label col-md-3" for="requires_bubblewrap">Requires Bubblewrap</label>
                    </div>
                    <div class="form-group row custom-control custom-checkbox custom-control-right">
                        <input class="custom-control-input" type="checkbox" id="collection" name="collection" <?php if($product['collection'] > 0) echo "checked";?> />
                        <label class="custom-control-label col-md-3" for="collection">Collection</label>
                    </div>
                    <div class="form-group row custom-control custom-checkbox custom-control-right">
                        <input class="custom-control-input" type="checkbox" id="palletized" name="palletized" <?php if($product['palletized'] > 0) echo "checked";?> />
                        <label class="custom-control-label col-md-3" for="palletized">Dispatch as Whole Pallets</label>
                    </div>

                    <div class="form-group row custom-control custom-checkbox custom-control-right">
                        <input class="custom-control-input" type="checkbox" id="is_lengths" name="is_lengths" <?php if($product['is_lengths'] > 0) echo "checked";?> />
                        <label class="custom-control-label col-md-3" for="is_lengths">Pallets Have Different Lengths</label>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <h3>Inventory</h3>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3">Low Stock Trigger</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control number" name="trigger_point" id="trigger_point" value="<?php echo $product['trigger_point'];?>" />
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
                                <input type="text" class="form-control number" name="low_stock_warning" id="low_stock_warning" value="<?php echo $product['low_stock_warning'];?>" />
                                <div class="input-group-append">
                                    <span class="input-group-text"><em>for client</em></span>
                                </div>
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
    </div>
</div>