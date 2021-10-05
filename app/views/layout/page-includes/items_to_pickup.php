<?php
if(!isset($i)) $i = 1;
?>
<div id="pickup_item_<?php echo $i;?>" class="pickup_item row">
    <div class="col-11 offset-1"><?php echo $label;?></div>
    <div class="form-group row">
        <label class="col-4 col-form-label">Pallets To Collect</label>
        <div class="col-6">
            <input type="text" class="form-control required number" name="pickup_items[<?php echo $item_id;?>]" >
        </div>
        <div class="col-1 text-right">
            <button class="btn btn-sm btn-danger" data-rowid="<?php echo $i;?>">Remove</button>
        </div>
    </div>
</div>