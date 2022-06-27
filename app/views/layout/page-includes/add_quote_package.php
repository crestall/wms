<?php
$i = (isset($i))? $i : 0;
?>
<div class="p-3 light-grey mb-3 apackage">
    <div class="form-group row">
        <label class="col-md-1 mb-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Width</label>
        <div class="col-md-3 mb-3">
            <div class="input-group">
                <input type="text" class="form-control required number" data-rule-positiveNumber="true" name="items[<?php echo $i;?>][width]" value="" />
                <div class="input-group-append">
                    <span class="input-group-text">cm</span>
                </div>
            </div>
        </div>
        <label class="col-md-1 mb-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Length</label>
        <div class="col-md-3 mb-3">
            <div class="input-group">
                <input type="text" class="form-control required number" data-rule-positiveNumber="true" name="items[<?php echo $i;?>][length]" value="" />
                <div class="input-group-append">
                    <span class="input-group-text">cm</span>
                </div>
            </div>
        </div>
        <label class="col-md-1 mb-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Height</label>
        <div class="col-md-3 mb-3">
            <div class="input-group">
                <input type="text" class="form-control required number" data-rule-positiveNumber="true" name="items[<?php echo $i;?>][height]" value="" />
                <div class="input-group-append">
                    <span class="input-group-text">cm</span>
                </div>
            </div>
        </div>
        <label class="col-md-1 mb-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Weight</label>
        <div class="col-md-3 mb-3">
            <div class="input-group">
                <input type="text" class="form-control required number" data-rule-number="true" name="items[<?php echo $i;?>][weight]" value="<?php echo Form::value('carton_weight');?>" />
                <div class="input-group-append">
                    <span class="input-group-text">kg</span>
                </div>
            </div>
        </div>
        <label class="col-md-1 mb-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Count</label>
        <div class="col-md-3 mb-3">
            <input type="text" class="form-control required digits" data-rule-positiveNumber="true" name="items[<?php echo $i;?>][count]" value="1" />
        </div>
        <div class="col-md-4 mb-3">
            <div class="custom-control custom-checkbox col-sm-2">
                <input class="custom-control-input" type="checkbox" id="pallet_<?php echo $i;?>" name="items[<?php echo $i;?>][pallet]" />
                <label class="custom-control-label" for="pallet_<?php echo $i;?>">Pallet(s)?</label>
            </div>
        </div>

    </div>
</div>