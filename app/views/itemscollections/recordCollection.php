<?php
$puaddress = empty(Form::value('puaddress'))? "" : Form::value('puaddress');
$puaddress2 = empty(Form::value('puaddress2'))? "" : Form::value('puaddress2');
$pusuburb = empty(Form::value('pusuburb'))? "" : Form::value('pusuburb');
$pupostcode = empty(Form::value('pupostcode'))? "" : Form::value('pupostcode');

$fsg_address = Config::get("FSG_ADDRESS");
$address = empty(Form::value('address'))? $fsg_address['address'] : Form::value('address');
$address2 = empty(Form::value('address2'))? $fsg_address['address_2'] : Form::value('address2');
$suburb = empty(Form::value('suburb'))? $fsg_address['suburb'] : Form::value('suburb');
$postcode = empty(Form::value('postcode'))? $fsg_address['postcode'] : Form::value('postcode');
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php"); ?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <form id="record-item-collection" method="post" action="/form/procRecordItemCollection">
            <div id="record-item-collection-holder" class="mt-2 mb-3 p-2 border border-secondary rounded bg-fsg">
                <h3 class="text-center">Collection Details</h3>
                <div class="m-2 p-2 border border-secondary rounded bg-light">
                    <div class="form-group row">
                        <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Client</label>
                        <div class="col-md-4">
                            <select id="client_id" name="client_id" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true" required><option value="0">--Select One--</option><?php echo $this->controller->client->getSelectPPClients($client_id);?></select>
                            <?php echo Form::displayError('client_id');?>
                        </div>
                    </div>
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
                </div>
            </div>
            <div id="record-item-collection-holder" class="mt-2 mb-3 p-2 border border-secondary rounded bg-fsg">
                <h3 class="text-center">Collection Addresses</h3>
                <div class="m-2 p-2 border border-secondary rounded bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="text-center">Pickup address</h4>
                        </div>
                        <div class="col-md-6">
                            <h4 class="text-center">Delivery address</h4>
                        </div>
                    </div>
                </div>
            </div>
        </form>

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