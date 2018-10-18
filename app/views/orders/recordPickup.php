<?php
$address = Form::value('address');
$address2 = Form::value('address2');
$suburb = Form::value('suburb');
$state = Form::value('state');
$postcode = Form::value('postcode');
$country = Form::value('country');
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <label class="col-lg-3">Select a Client</label>
                <div class="col-lg-4">
                    <p>
                      <select id="client_selector" class="form-control selectpicker"><option value="0">Select</option><?php echo $this->controller->client->getSelectClients($client_id);?></select>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <hr />
    <?php if($client_id > 0):?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
        <div class="row">
            <form id="record-pickup" method="post" action="/form/procRecordPickup">
                <div class="row">
                    <div class="col-md-12">
                        <h2>Pickup Details</h2>
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
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Courier Name</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required" name="courier_name" id="courier_name" value="<?php echo Form::value('courier_name');?>" />
                        <?php echo Form::displayError('courier_name');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Courier Consignment Number</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required" name="con_id" id="con_id" value="<?php echo Form::value('con_id');?>" />
                        <?php echo Form::displayError('con_id');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Courier Charge</label>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="text" class="form-control" name="courier_charge" id="courier_charge" value="<?php echo Form::value('courier_charge');?>" />
                            <?php echo Form::displayError('courier_charge');?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h2>Pickup Address</h2>
                    </div>
                </div>
                <?php include(Config::get('VIEWS_PATH')."forms/address.php");?>
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">&nbsp;</label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Record It</button>
                    </div>
                </div>
            </form>
        </div>
    <?php endif;?>
</div>