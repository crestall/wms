<?php
$i = (isset($i))? $i : 0;
?>
<div class="p-3 light-grey mb-3 acontact">
    <div class="form-group row">
        <label class="col-md-2 mb-3">Name</label>
        <div class="col-md-4 mb-3">
            <input type="text" class="form-control required" name="contact[<?php echo $i;?>][name]" value="" />
        </div>
        <label class="col-md-2 mb-3">Role</label>
        <div class="col-md-4 mb-3">
            <input type="text" class="form-control" name="contact[<?php echo $i;?>][role]" value="" />
        </div>
        <label class="col-md-2 mb-3">Email</label>
        <div class="col-md-4 mb-3">
            <input type="text" class="form-control email" name="contact[<?php echo $i;?>][email]" value="" />
        </div>
        <label class="col-md-2 mb-3">Phone</label>
        <div class="col-md-4 mb-3">
            <input type="text" class="form-control" name="contact[<?php echo $i;?>][phone]" value="" />
        </div>
    </div>
</div>