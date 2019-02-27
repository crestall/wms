<?php
$address = Form::value('address');
$address2 = Form::value('address2');
$suburb = Form::value('suburb');
$state = Form::value('state');
$postcode = Form::value('postcode');
$country = Form::value('country');
$user_role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
$idisp = "none";
if(!empty(Form::value('items')))
    $idisp = "block";
if($user_role == "client")
    $idisp = "block";
$client_id = (!empty(Form::value('client_id')))? (int)Form::value('client_id') : 0;
?>
<?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
<?php echo Form::displayError('general');?>
<?php //echo "<pre>",var_dump(Form::value('items')),"</pre>";?>
<div class="row">
    <div class="col-lg-12">
        <form id="add_origin_order" method="post" action="/form/procOriginOrderAdd"  enctype="multipart/form-data" autocomplete="off">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Order Details</h3>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Work Order</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="work_order" id="work_order" value="<?php echo Form::value('work_order');?>" />
                    <?php echo Form::displayError('work_order');?>
                </div>
            </div>
            <?php include(Config::get('VIEWS_PATH')."forms/address.php");?>
            <input type="hidden" name="selected_items" id="selected_items" />
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" /> 
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Add Order</button>
                </div>
            </div>
        </form>
    </div>
</div>