<?php

?>
<div class="form-group row">
    <label class="<?php if(isset($label_class)) echo $label_class; else echo "col-md-3";?> col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Location</label>
    <div class="<?php if(isset($div_class)) echo $div_class; else echo "col-md-4";?>">
        <select id="add_to_location" name="add_to_location" class="form-control selectpicker" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectLocations($add_to_location, $item_id);?></select>
        <?php echo Form::displayError('add_to_location');?>
    </div>
</div>