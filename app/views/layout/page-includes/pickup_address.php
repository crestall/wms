<div class="form-group row">
    <label class="col-md-4 col-form-label">Address Line 1</label>
    <div class="col-md-8">
        <input type="text" class="form-control pickup" name="pickup_address" id="pickup_address" value="<?php echo $pickup_address;?>" />
        <?php echo Form::displayError('pickup_address');?>
    </div>
</div>
<div class="form-group row">
    <label class="col-md-4 col-form-label">Address Line 2</label>
    <div class="col-md-8">
        <input type="text" class="form-control pickup" name="pickup_address2" id="pickup_address2" value="<?php echo $pickup_address2;?>" />
    </div>
</div>
<div class="form-group row">
    <label class="col-md-4 col-form-label">Suburb/Town</label>
    <div class="col-md-8">
        <input type="text" class="form-control pickup" name="pickup_suburb" id="pickup_suburb" value="<?php echo $pickup_suburb;?>" />
        <?php echo Form::displayError('pickup_suburb');?>
    </div>
</div>
<div class="form-group row">
    <label class="col-md-4 col-form-label">State</label>
    <div class="col-md-8">
        <input type="text" class="form-control pickup" name="pickup_state" id="pickup_state" value="<?php echo $pickup_state;?>" />
        <span class="inst">for AU addresses use VIC, NSW, QLD, ACT, TAS, WA, SA, NT only</span>
        <?php echo Form::displayError('pickup_state');?>
    </div>
</div>
<div class="form-group row">
    <label class="col-md-4 col-form-label">Postcode</label>
    <div class="col-md-8">
        <input type="text" class="form-control pickup" name="pickup_postcode" id="pickup_postcode" value="<?php echo $pickup_postcode;?>" />
        <?php echo Form::displayError('pickup_postcode');?>
    </div>
</div>