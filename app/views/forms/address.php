        <div class="form-group row">
            <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Address Line 1</label>
            <div class="col-md-4">
                <input type="text" class="form-control required" name="address" id="address" value="<?php echo $address;?>" />
                <?php echo Form::displayError('address');?>
            </div>
            <div class="col-md-3 checkbox checkbox-default">
                <input class="form-check-input styled" type="checkbox" id="ignore_address_error" name="ignore_address_error" <?php if(!empty(Form::value('ignore_address_error'))) echo 'checked';?> />
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
            <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Suburb/Town</label>
            <div class="col-md-4">
                <input type="text" class="form-control required" name="suburb" id="suburb" value="<?php echo $suburb;?>" />
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
            <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Postcode</label>
            <div class="col-md-4">
                <input type="text" class="form-control required" name="postcode" id="postcode" value="<?php echo $postcode;?>" />
                <?php echo Form::displayError('postcode');?>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Country</label>
            <div class="col-md-4">
                <input type="text" class="form-control required" name="country" id="country" value="<?php echo $country;?>" />
                <span class="inst">use the 2 letter ISO code</span>
                <p><a href="https://www.nationsonline.org/oneworld/country_code_list.htm" target="_blank" class="btn btn-success">Click Here To Look Up Codes</a></p>
                <?php echo Form::displayError('country');?>
            </div>
        </div>