<?php
$i = (isset($i))? $i : 0;
$details['contact_id'] = isset($d['contact_id'])? $d['contact_id']:"";
$details['name'] = isset($d['name'])? $d['name']:"";
$details['role'] = isset($d['role'])? $d['role']:"";
$details['email'] = isset($d['email'])? $d['email']:"";
$details['phone'] = isset($d['phone'])? $d['phone']:"";
?>
<div class="p-3 light-grey mb-3 acontact">
    <div class="form-group row">
        <label class="col-md-2 mb-3">Name</label>
        <div class="col-md-4 mb-3">
            <input type="text" class="form-control required" name="contacts[<?php echo $i;?>][name]" value="<?php echo $details['name'];?>" />
            <?php echo Form::displayError('contactname_'.$i);?>
        </div>
        <label class="col-md-2 mb-3">Role</label>
        <div class="col-md-4 mb-3">
            <input type="text" class="form-control" name="contacts[<?php echo $i;?>][role]" value="<?php echo $details['role'];?>" />
        </div>
        <label class="col-md-2 mb-3">Email</label>
        <div class="col-md-4 mb-3">
            <input type="text" class="form-control email" name="contacts[<?php echo $i;?>][email]" value="<?php echo $details['email'];?>" />
            <?php echo Form::displayError('contactemail_'.$i);?>
        </div>
        <label class="col-md-2 mb-3">Phone</label>
        <div class="col-md-4 mb-3">
            <input type="text" class="form-control" name="contacts[<?php echo $i;?>][phone]" value="<?php echo $details['phone'];?>" />
        </div>
        <input type="hidden" name="contacts[<?php echo $i;?>][contact_id]" value="<?php echo $details['contact_id'];?>" />
    </div>
</div>