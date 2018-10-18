        <div class="form-group row">
            <label class="col-md-3 col-form-label">Address Line 1</label>
            <div class="col-md-4">
                <input type="text" class="form-control" name="address" id="address" value="<?php echo $address;?>" />
                <?php echo Form::displayError('address');?>
            </div>
            <div class="col-md-3 checkbox checkbox-default">
                <input class="form-check-input styled" type="checkbox" id="ignore_address_error" name="ignore_address_error" />
                <label for="ignore_address_error">No need for a number</label>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">Address Line 2</label>
            <div class="col-md-4">
                <input type="text" class="form-control" name="address2" id="address2" value="<?php echo $address2;?>" />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">Suburb/Town</label>
            <div class="col-md-4">
                <input type="text" class="form-control" name="suburb" id="suburb" value="<?php echo $suburb;?>" />
                <?php echo Form::displayError('suburb');?>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">State</label>
            <div class="col-md-4">
                <input type="text" class="form-control" name="state" id="state" value="<?php echo $state;?>" />
                <span class="inst">for AU addresses use VIC, NSW, QLD, ACT, TAS, WA, SA, NT only</span>
                <?php echo Form::displayError('state');?>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">Postcode</label>
            <div class="col-md-4">
                <input type="text" class="form-control" name="postcode" id="postcode" value="<?php echo $postcode;?>" />
                <?php echo Form::displayError('postcode');?>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">Country</label>
            <div class="col-md-4">
                <input type="text" class="form-control" name="country" id="country" value="<?php echo $country;?>" />
                <span class="inst">use the 2 letter ISO code</span>
                <?php echo Form::displayError('country');?>
            </div>
        </div>