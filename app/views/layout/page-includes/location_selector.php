<?php
$display = ($add_to_location == $this->controller->location->getReceivingId())? "block" : "none";
//$display = "block";
$pallet_multiplier = empty(Form::value('pallet_multiplier'))? 1 : Form::value('pallet_multiplier');
?>
<div class="form-group row">
    <label class="<?php if(isset($label_class)) echo $label_class; else echo "col-md-3";?> col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Location</label>
    <div class="<?php if(isset($div_class)) echo $div_class; else echo "col-md-4";?>">
        <select id="add_to_location" name="add_to_location" class="form-control selectpicker" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectLocations($add_to_location, $item_id);?></select>
        <?php echo Form::displayError('add_to_location');?>
    </div>
</div>
<div class="form-group-row" id="pallet_count_holder" style="display: <?php echo $display;?>;">
    <label class="<?php if(isset($label_class)) echo $label_class; else echo "col-md-3";?> col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Pallet Count</label>
    <div class="<?php if(isset($div_class)) echo $div_class; else echo "col-md-4";?>">
        <input type="text" class="form-control required digits" name="pallet_multiplier" id="pallet_multiplier" value="<?php echo $pallet_multiplier;?>" />
    </div>
</div>