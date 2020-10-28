<?php
$i = (isset($i))? $i : 0;
?>
<div class="p-3 light-grey mb-3 apackage">
    <div class="form-group row">
        <label class="col-md-1 mb-3">Width</label>
        <div class="col-md-3 mb-3">
            <div class="input-group">
                <input type="text" class="form-control number" data-rule-positiveNumber="true" name="item[<?php echo $i;?>][width]" value="" />
                <div class="input-group-append">
                    <span class="input-group-text">cm</span>
                </div>
            </div>
        </div>
        <label class="col-md-1 mb-3">Length</label>
        <div class="col-md-3 mb-3">
            <div class="input-group">
                <input type="text" class="form-control number" data-rule-positiveNumber="true" name="item[<?php echo $i;?>][length]" value="" />
                <div class="input-group-append">
                    <span class="input-group-text">cm</span>
                </div>
            </div>
        </div>
        <label class="col-md-1 mb-3">Height</label>
        <div class="col-md-3 mb-3">
            <div class="input-group">
                <input type="text" class="form-control number" data-rule-positiveNumber="true" name="item[<?php echo $i;?>][height]" value="" />
                <div class="input-group-append">
                    <span class="input-group-text">cm</span>
                </div>
            </div>
        </div>
        <label class="col-md-1 mb-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Weight</label>
        <div class="col-md-3 mb-3">
            <div class="input-group">
                <input type="text" class="form-control required number" data-rule-positiveNumber="true" name="item[<?php echo $i;?>][weight]" value="<?php echo Form::value('carton_weight');?>" />
                <div class="input-group-append">
                    <span class="input-group-text">kg</span>
                </div>
            </div>
        </div>
        <label class="col-md-1 mb-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Count</label>
        <div class="col-md-3 mb-3">
            <input type="text" class="form-control required digits" data-rule-positiveNumber="true" name="item[<?php echo $i;?>][count]" value="1" />
        </div>
    </div>
</div>