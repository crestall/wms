<?php
if(!isset($i)) $i = 1;
?>
<input type="hidden" name="locations[<?php echo $ii;?>][item_id]" value="<?php echo $item_id;?>">
<div class="pallet_holder border-bottom border-secondary border-bottom-dashed pt-2">
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="col-12">&nbsp;</label>
            Pallet <?php echo $pc;?> of <?php echo $item_name." (".$item_sku.")";?>
        </div>
        <div class="col-md-2 mb-3">
            <label class="col-12">Qty</label>
            <input name="locations[<?php echo $ii;?>][qty]" class="form-control required number" value="<?php echo Form::value("locations,$ii,qty");?>">
        </div>
        <div class="col-md-3 mb-3">
            <label class="col-12">Pallet Size</label>
            <select id="size_<?php echo $ii;?>" name="locations[<?php echo $ii;?>][size]" class="form-control selectpicker pallet_size" data-live-search="true" data-style="btn-outline-secondary" required><option value="0">Select Size</option><?php echo Utility::getPalletSizeSelect(Form::value("locations,$ii,size"));?></select>
        </div>
        <div class="col-md-3 mb-3">
            <label class="col-12">Location</label>
            <select id="location_id_<?php echo $ii;?>" name="locations[<?php echo $ii;?>][location_id]" class="form-control selectpicker pallet_location" data-live-search="true" data-style="btn-outline-secondary" required><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectEmptyLocations(Form::value("locations,$ii,location_id"));?></select>
        </div>
    </div>
</div>