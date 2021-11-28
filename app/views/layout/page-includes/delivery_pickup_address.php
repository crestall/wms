<div class="form-group row">
    <label class="col-md-4 col-form-label">Address Line 1</label>
    <div class="col-md-8">
        <input type="text" class="form-control delivery" name="delivery_address" id="delivery_address" value="<?php echo $delivery_address;?>" />
        <?php echo Form::displayError('delivery_address');?>
    </div>
</div>
<div class="form-group row">
    <label class="col-md-4 col-form-label">Address Line 2</label>
    <div class="col-md-8">
        <input type="text" class="form-control delivery" name="delivery_address2" id="delivery_address2" value="<?php echo $delivery_address2;?>" />
    </div>
</div>
<div class="form-group row">
    <label class="col-md-4 col-form-label">Suburb/Town</label>
    <div class="col-md-8">
        <input type="text" class="form-control delivery" name="delivery_suburb" id="delivery_suburb" value="<?php echo $delivery_suburb;?>" />
        <?php echo Form::displayError('delivery_suburb');?>
    </div>
</div>
<div class="form-group row">
    <label class="col-md-4 col-form-label">State</label>
    <div class="col-md-8">
        <input type="text" class="form-control delivery" name="delivery_state" id="delivery_state" value="<?php echo $delivery_state;?>" />
        <span class="inst">for AU addresses use VIC, NSW, QLD, ACT, TAS, WA, SA, NT only</span>
        <?php echo Form::displayError('delivery_state');?>
    </div>
</div>
<div class="form-group row">
    <label class="col-md-4 col-form-label">Postcode</label>
    <div class="col-md-8">
        <input type="text" class="form-control delivery" name="delivery_postcode" id="delivery_postcode" value="<?php echo $delivery_postcode;?>" />
        <?php echo Form::displayError('delivery_postcode');?>
    </div>
</div>