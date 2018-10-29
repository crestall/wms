<?php

?>
<div class="form-group row">
    <label class="col-md-5 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Location</label>
    <div class="col-md-7">
        <select id="add_to_location" name="add_to_location" class="form-control selectpicker" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectLocations($add_to_location, $item_id);?></select>
        <?php echo Form::displayError('add_to_location');?>
    </div>
</div>