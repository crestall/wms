<?php
?>
<?php if(!empty($item)):
    $item_id = $item['id'];
    $add_to_location = (empty(Form::value('add_to_location')))? $item['preferred_pick_location_id'] : Form::value('add_to_location');?>
    <div class="row">
        <form id="add_to_stock" method="post" action="/form/procScanToInventory">
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control readonly" name="name" id="name" readonly value="<?php echo $item['name'];?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Qty To Add</label>
                <div class="col-md-4">
                    <input type="text" class="form-control number required" name="qty" id="qty" value="" />
                </div>
            </div>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/location_selector.php");?>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>" />
            <input type="hidden" name="client_id" value="<?php echo $item['client_id']; ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Add Them</button>
                </div>
            </div>
        </form>
    </div>
<?php else:?>
    <div class="row">
        <div class="col-lg-12">
            <div class='errorbox'><h2><i class="far fa-times-circle"></i> Item Not Found</h2>
                <p>That product was not found in the system</p>
                <p>Please recheck the barcode, or you can add it as a new product using the form below</p>
            </div>
        </div>
    </div>
    <div class="row">
        <form id="add_new_item"  method="post" action="/form/procBasicProductAdd">
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="name" id="name" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> SKU</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="sku" id="sku" value="<?php echo $barcode;?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Barcode</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="barcode" id="barcode" value="<?php echo $barcode;?>" />
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
                    <label class="form-check-label col-md-3" for="double_bay">Double Bay Item</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="double_bay" name="double_bay" <?php if(!empty(Form::value('double_bay'))) echo "checked";?> />
                        <label for="double_bay"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Package Type(s)</label>
                <div class="col-md-4">
                    <select id="package_type" name="package_type[]" class="form-control selectpicker" multiple><?php echo $this->controller->packingtype->getSelectPackingTypesMultiple();?></select>
                    <span class="inst">Select all relevent types</span>
                </div>
            </div>
            <div id="type_holder"></div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="palletized">Dispatch as Whole Pallets</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="palletized" name="palletized" />
                        <label for="palletized"></label>
                    </div>
                </div>
            </div>
            <div id="per_pallet_holder" style="display: none">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Number per pallet</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required number" name="per_pallet" id="per_pallet" value="" />
                        <?php echo Form::displayError('per_pallet');?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <h3>Inventory</h3>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Preferred Pick Location</label>
                <div class="col-md-4">
                    <select id="preferred_pick_location_id" name="preferred_pick_location_id" class="form-control selectpicker" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectLocations();?></select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Qty To Add</label>
                <div class="col-md-4">
                    <input type="text" class="form-control number required" name="qty" id="qty" value="" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Add To Location</label>
                <div class="col-md-4">
                    <select id="add_to_location" name="add_to_location" class="form-control selectpicker" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectLocations();?></select>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Add Product and Inventory</button>
                </div>
            </div>
        </form>
    </div>
<?php endif;?>