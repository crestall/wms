<?php

?>
<div class="form-group row">
    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Rails</label>
    <div class="col-md-1"><input type="text" name="items[0][qty]" class="form-control required number" value="<?php echo $rails['qty'];?>" /></div>
    <input type="hidden" name="items[0][id]" value="<?pgp echo $rails['id'];?>" />
</div>