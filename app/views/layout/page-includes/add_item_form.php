<?php

?>

    <form id="add_new_delivery_product" method="post" action="/ajaxfunctions/procAddNewDeliveryItem">
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
        <div class="form-group row">
            <div class="col-md-4 offset-md-5">
                <button type="submit" class="btn btn-outline-fsg">Record Info</button>
            </div>
        </div>
    </form>

