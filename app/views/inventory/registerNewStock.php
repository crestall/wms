<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <input type="hidden" id="client_id" value="<?php echo $client_id;?>" />
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <form method="post" action="/form/procRegisterNewStock" id="register_new_stock"  autocomplete="off" class="p-3 border border-fsg rounded">
            <div class="form-group row">
                <div class="inst_holder p-3 w-75 mx-auto">
                    <p class="mb-1">At lease one of Your Product ID/SKU or Barcode is required</p>
                    <p>A unique FSG SKU is generated from these - you cannot edit this</p>
                    <p>The Print on Demand checkbox <strong>must</strong> be checked for all print on demand items</p>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                <div class="col-md-6">
                    <input type="text" class="form-control required" name="name" id="name" value="<?php echo Form::value('name');?>" />
                    <?php echo Form::displayError('name');?>
                </div>
            </div>
            <div class="form-group row custom-control custom-checkbox custom-control-right">
                <input class="custom-control-input" type="checkbox" id="is_pod" name="is_pod" <?php if( !empty(Form::value('is_pod'))) echo "checked";?> />
                <label class="custom-control-label col-md-3" for="is_pod">Print on Demand Product</label>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Your Product ID/SKU</label>
                <div class="col-md-6">
                    <input type="text" class="form-control sku_calc" name="client_product_id" id="client_product_id" value="<?php echo Form::value('client_product_id');?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Barcode or ISBN</label>
                <div class="col-md-6">
                    <input type="text" class="form-control sku_calc" name="barcode" id="barcode" value="<?php echo Form::value('barcode');?>" />
                    <?php echo Form::displayError('counter');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">FSG SKU</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" readonly name="sku" id="sku" value="<?php echo Form::value('sku');?>" />
                    <?php echo Form::displayError('sku');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Image URL</label>
                <div class="col-md-6">
                    <input type="text" class="product_image form-control url" name="image" id="image" value="<?php echo Form::value('image');?>">
                    <span class="inst">Use a fully formed URL, including the http(s) part</span>
                    <?php echo Form::displayError('image');?>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-fsg">Record Info</button>
                </div>
            </div>
        </form>
    </div>
</div>