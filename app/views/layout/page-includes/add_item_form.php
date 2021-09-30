<?php
if(!isset($i)) $i = 1;
?>
<div id="form_<?php echo $i;?>_holder" class="little_form_holder col">
    <form id="form_<?php echo $i;?>" method="post" action="/ajaxfunctions/procAddNewDeliveryItem" class="add_item_form">
        <div class="form-group row">
            <label class="col-md-5 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
            <div class="col-md-6">
                <input type="text" class="form-control" name="name" id="name" value="" />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-5 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Your Product ID/SKU</label>
            <div class="col-md-6">
                <input type="text" class="form-control" name="client_product_id" id="client_product_id" value="" />
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
        <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
        <input type="hidden" name="form_id" value="form_<?php echo $i; ?>" />
        <div class="form-group row">
            <div class="col-md-4 offset-md-5">
                <button type="submit" disabled class="btn btn-sm btn-outline-secondary">Add Item</button>
            </div>
        </div>
    </form>
</div>


