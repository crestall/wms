<?php
$client_id = $product['client_id'];
$product_name = (!empty(Form::value('name')))? Form::value('name'):$product['name'];
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if($error):?>

        <?php else:?>
            <div class="row">
                <div class="col-lg-12">
                    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
                    <?php echo Form::displayError('general');?>
                    <form id="client_edit_product"  method="post" action="/form/procClientProductEdit">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Name</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control required" name="name" id="name" value="<?php echo $product_name;?>" />
                                <?php echo Form::displayError('name');?>
                            </div>
                        </div>
                        <!-- Hidden Inputs -->
                        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                        <input type="hidden" name="item_id" value="<?php echo $product['id'];?>" />
                        <input type="hidden" name="client_id" value="<?php echo $product['client_id'];?>" />
                        <!-- Hidden Inputs -->
                        <div class="form-group row">
                            <div class="col-md-6 offset-md-3">
                                <button type="submit" class="btn btn-outline-secondary">Update Product</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>