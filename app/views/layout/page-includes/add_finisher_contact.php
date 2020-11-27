<?php
$i = (isset($i))? $i : 0;
?>
<div class="p-3 light-grey mb-3 acontact">
    <div class="form-group row">
        <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
        <div class="col-md-4">
            <input type="text" class="form-control required" name="name" id="name" value="<?php echo Form::value('name');?>" />
            <?php echo Form::displayError('name');?>
        </div>
    </div>
</div>