<?php
$puaddress = empty(Form::value('puaddress'))? $client['address'] : Form::value('puaddress');
$puaddress2 = empty(Form::value('puaddress2'))? $client['address_2'] : Form::value('puaddress2');
$pusuburb = empty(Form::value('pusuburb'))? $client['suburb'] : Form::value('pusuburb');
$pupostcode = empty(Form::value('pupostcode'))? $client['postcode'] : Form::value('pupostcode');

$threepl_address = Config::get("THREEPL_ADDRESS");
$address = empty(Form::value('address'))? $threepl_address['address'] : Form::value('address');
$address2 = empty(Form::value('address2'))? $threepl_address['address_2'] : Form::value('address2');
$suburb = empty(Form::value('suburb'))? $threepl_address['suburb'] : Form::value('suburb');
$postcode = empty(Form::value('postcode'))? $threepl_address['postcode'] : Form::value('postcode');
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <div class="row">
        <form id="book-pickup" method="post" action="/form/procBookPickup">
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Number of Pallets</label>
                <div class="col-md-4">
                    <input type="text" class="form-control number counter" name="pallets" id="pallets" value="<?php echo Form::value('pallets');?>" placeholder="A value for at least one of these is required" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Number of Cartons</label>
                <div class="col-md-4">
                    <input type="text" class="form-control number counter" name="cartons" id="cartons" value="<?php echo Form::value('cartons');?>" placeholder="A value for at least one of these is required" />
                    <?php echo Form::displayError('counter');?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h2>Pickup Address</h2>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Address Line 1</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="puaddress" id="puaddress" value="<?php echo $puaddress;?>" />
                    <?php echo Form::displayError('puaddress');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Address Line 2</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="puaddress2" id="puaddress2" value="<?php echo $puaddress2;?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Suburb/Town</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="pusuburb" id="pusuburb" value="<?php echo $pusuburb;?>" />
                    <?php echo Form::displayError('pusuburb');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Postcode</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="pupostcode" id="pupostcode" value="<?php echo $pupostcode;?>" />
                    <?php echo Form::displayError('pupostcode');?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h2>Delivery Address</h2>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Address Line 1</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="address" id="address" value="<?php echo $address;?>" />
                    <?php echo Form::displayError('puaddress');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Address Line 2</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="address2" id="address2" value="<?php echo $address2;?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Suburb/Town</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="suburb" id="suburb" value="<?php echo $suburb;?>" />
                    <?php echo Form::displayError('suburb');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Postcode</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="postcode" id="postcode" value="<?php echo $postcode;?>" />
                    <?php echo Form::displayError('postcode');?>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Book It</button>
                </div>
            </div>
        </form>
    </div>
</div>