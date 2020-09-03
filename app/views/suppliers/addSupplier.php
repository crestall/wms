<?php
$address = Form::value('address');
$address2 = Form::value('address2');
$suburb = Form::value('suburb');
$state = Form::value('state');
$postcode = Form::value('postcode');
$country = Form::value('country');
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <form id="add_production_supplier" method="post" action="/form/procAddProductionSupplier">
            <div class="form-group row">
                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="name" id="name" value="<?php echo Form::value('name');?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Contact</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="contact" id="contact" value="<?php echo Form::value('contact');?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Email</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required email" name="email" id="email" value="<?php echo Form::value('email');?>" />
                </div>
            </div>
            <?php include(Config::get('VIEWS_PATH')."forms/address.php");?>
            <div class="form-group row">
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <div class="col-md-4 offset-md-3">
                    <button type="submit" class="btn btn-outline-secondary">Add This Supplier</button>
                </div>
            </div>
        </form>
    </div>
</div>