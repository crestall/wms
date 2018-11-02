<div id="page-wrapper">
    <input type="hidden" id="client_id" value="<?php echo $client_id;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <div class="row">
        <div class="col-md-12">
            <form method="post" action="/form/procRegisterNewStock" id="register_new_stock" autocomplete="off">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required" name="name" id="name" value="<?php echo Form::value('name');?>" />
                        <?php echo Form::displayError('name');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> SKU</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required" name="sku" id="sku" value="<?php echo Form::value('sku');?>" />
                        <?php echo Form::displayError('sku');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Publisher/Supplier</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="supplier" id="supplier" value="<?php echo Form::value('supplier');?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Expected Quantity</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control digits required" name="qty" id="qty" value="<?php echo Form::value('qty');?>" />
                        <?php echo Form::displayError('qty');?>
                    </div>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">&nbsp;</label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Record Info</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>