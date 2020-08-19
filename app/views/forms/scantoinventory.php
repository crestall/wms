<?php
?>
<?php if(!empty($item)):
    $item_id = $item['id'];
    $add_to_location = (empty(Form::value('add_to_location')))? $item['preferred_pick_location_id'] : Form::value('add_to_location');?>
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
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Location</label>
                <div class="col-md-4">
                    <select id="add_to_location" name="add_to_location" class="form-control selectpicker" data-live-search="true" data-style="btn-outline-secondary"><option value="0">--Select One--</option>
                        <?php echo $this->controller->location->getSelectLocations($add_to_location, $item_id);?>
                    </select>
                </div>
            </div>

            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>" />
            <input type="hidden" name="client_id" value="<?php echo $item['client_id']; ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-secondary">Add Them</button>
                </div>
            </div>
        </form>
<?php else:?>
    <div class="row">
        <div class="col-lg-12">
            <div class='errorbox'><h2><i class="far fa-times-circle"></i> Item Not Found</h2>
                <p>That product was not found in the system</p>
                <p>Please recheck the barcode, or you can add it as a new product using the form below</p>
            </div>
        </div>
    </div>
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
        <div class="form-group row custom-control custom-checkbox custom-control-right">
            <input class="custom-control-input" type="checkbox" id="collection" name="collection" <?php if(!empty(Form::value('collection'))) echo "checked";?> />
            <label class="custom-control-label col-md-3" for="collection">Collection</label>
        </div>
        <div class="form-group row custom-control custom-checkbox custom-control-right">
            <input class="custom-control-input" type="checkbox" id="palletized" name="palletized" <?php if(!empty(Form::value('palletized'))) echo "palletized";?> />
            <label class="custom-control-label col-md-3" for="palletized">Dispatch as Whole Pallets</label>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <h3>Inventory</h3>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">Preferred Pick Location</label>
            <div class="col-md-4">
                <select id="preferred_pick_location_id" name="preferred_pick_location_id" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectLocations();?></select>
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
                <select id="add_to_location" name="add_to_location" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectLocations();?></select>
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

<?php endif;?>