<?php
$address = Form::value('address');
$address2 = Form::value('address2');
$suburb = Form::value('suburb');
$state =  (empty(Form::value('state')))? "VIC" : Form::value('state');
$postcode = Form::value('postcode');
$country = (empty(Form::value('country')))? "AU" : Form::value('country');
$date_filter = "Install Date";
$date = (empty(Form::value('date_value')))? time() : Form::value('date_value');
$inverter_qty = empty(Form::value('inverter_qty'))? 0 : Form::value('inverter_qty');
$panel_qty = empty(Form::value('panel_qty'))? 0 : Form::value('panel_qty');
$panel_id = empty(Form::value('panel_id'))? 0 : Form::value('panel_id');
$inverter_id = empty(Form::value('inverter_id'))? 0 : Form::value('inverter_id');
$disp = (empty(Form::value('csrf_token')))? 'none' : 'block';
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <div class="row">
        <div class="col-lg-12">
            <form id="add_solar_install" method="post" action="/form/procAddSolarInstall" autocomplete="off">
                <div class="row">
                    <div class="col-lg-12">
                        <h3>Job Details</h3>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Select an Install Type</label>
                    <div class="col-md-4">
                        <p><select id="type_id" name="type_id" class="form-control selectpicker"><option value="0">--Choose One--</option><?php echo $this->controller->solarordertype->getSelectSolarOrderTypes(Form::value('type_id'));?></select></p>
                    </div>
                </div>
                <div id="rest_of_form" style="display:<?php echo $disp;?>">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Team</label>
                        <div class="col-md-4">
                            <select id="team_id" name="team_id" class="form-control selectpicker"><option value="0">--Select One--</option><?php echo $this->controller->solarteam->getSelectTeam(Form::value('team_id'));?></select>
                            <?php echo Form::displayError('team_id');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Work Order</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control required" name="work_order" id="work_order" value="<?php echo Form::value('work_order');?>" />
                            <?php echo Form::displayError('work_order');?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Customer Name</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="customer_name" id="customer_name" value="<?php echo Form::value('customer_name');?>" />
                            <?php echo Form::displayError('customer_name');?>
                        </div>
                    </div>
                    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/select_date.php");?>
                    <div class="row">
                        <div class="col-lg-12">
                            <h3>Address Details</h3>
                        </div>
                    </div>
                    <?php include(Config::get('VIEWS_PATH')."forms/address.php");?>
                    <div class="row">
                        <div class="col-lg-12">
                            <h3>Install Details</h3>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Panel</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control solar-item-searcher" name="panel" id="panel" value="<?php echo Form::value('panel');?>" />
                            <?php echo Form::displayError('panel');?>
                        </div>
                        <div class="col-md-4">
                            <label class="col-md-3 col-form-label">Qty</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control number" name="panel_qty" id="panel_qty" value="<?php echo $panel_qty;?>" <?php if($panel_qty == 0) echo "disabled";?> />
                                <span class="inst" id="panel_count"></span>
                                <?php echo Form::displayError('panel_qty');?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Inverter</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control solar-item-searcher" name="inverter" id="inverter" value="<?php echo Form::value('inverter');?>" />

                            <?php echo Form::displayError('inverter');?>
                        </div>
                        <div class="col-md-4">
                            <label class="col-md-3 col-form-label">Qty</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control number" name="inverter_qty" id="inverter_qty" value="<?php echo $inverter_qty;?>" <?php if($inverter_qty == 0) echo "disabled";?> />
                                <span class="inst" id="inverter_count"></span>
                                <?php echo Form::displayError('inverter_qty');?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="form-radio">
                            <label class="form-check-label col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Roof Type</label>
                            <div class="col-md-2 checkbox checkbox-default">
                                <input class="form-check-input styled required" type="radio" id="tin" name="roof_type" <?php if(Form::value('roof_type') == "tin") echo 'checked';?> value="tin" />
                                <label for="tin"> Tin</label>
                            </div>
                            <div class="col-md-2 checkbox checkbox-default">
                                <input type="radio" class="form-check-input styled" id="tile" name="roof_type" <?php if(Form::value('roof_type') == "tile") echo 'checked';?> value="tile" />
                                <label for="tile"> Tile</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="form-check">
                            <label class="form-check-label col-md-3" for="battery">Battery Install</label>
                            <div class="col-md-4 checkbox checkbox-default">
                                <input class="form-check-input styled" type="checkbox" id="battery" name="battery" <?php if(!empty(Form::value('battery'))) echo 'checked';?> />
                                <label for="battery"></label>
                            </div>
                        </div>
                    </div>
                    <div id="banks" class="form-group row">
                        <label class="col-md-3 col-form-label">Banks</label>
                        <div class="col-md-9" id="banks_holder">
                            <?php if(is_array(Form::value('banks'))):?>
                                <?php foreach(Form::value('banks') as $b => $bank):
                                    $qty = (isset($bank['qty']))? $bank['qty']: "";?>
                                    <div class="row bank_holder">
                                        <?php if($b == 0):?>
                                            <div class="col-sm-1 add-image-holder">
                                                <a class="addbank" style="cursor:pointer" title="Add Another Bank">
                                                    <i class="fas fa-plus-circle fa-2x text-success"></i>
                                                </a>
                                            </div>
                                        <?php else:?>
                                            <div class="col-sm-1 delete-image-holder">
                                                <a class="delete" style="cursor:pointer" title="Remove This Bank">
                                                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                                                </a>
                                            </div>
                                        <?php endif;?>
                                        <div class="col-sm-4">
                                            <p><input type="text" class="form-control number banks" name="banks[<?php echo $b;?>][qty]" placeholder="Panel Count" value="<?php echo $qty;?>" /></p>
                                        </div>
                                    </div>
                                <?php endforeach;?>
                            <?php else:?>
                                <div class="row bank_holder">
                                    <div class="col-sm-4">
                                        <p><input type="text" class="form-control number banks" name="banks[0][qty]" placeholder="Panel Count" /></p>
                                    </div>
                                    <div class="col-sm-1 add-image-holder">
                                        <a class="addbank" style="cursor:pointer" title="Add Another Bank">
                                            <i class="fas fa-plus-circle fa-2x text-success"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endif;?>
                        </div>
                        <?php echo Form::displayError('banks');?>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">&nbsp;</label>
                        <div class="col-md-4">
                            <button class="btn btn-success btn-small" id="calc_items" disabled>Get Parts</button>
                        </div>
                    </div>
                    <div id="install_items_holder" style="display:none">

                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <h3>Additional Items</h3>
                        </div>
                    </div>
                    <?php include(Config::get('VIEWS_PATH')."forms/item_adder.php");?>
                    <input type="hidden" name="selected_items" id="selected_items" />
                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                    <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id; ?>" />
                    <input type="hidden" name="panel_id" id="panel_id" value="<?php echo $panel_id ?>" />
                    <input type="hidden" name="inverter_id" id="inverter_id" value="<?php echo $inverter_id ?>" />
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">&nbsp;</label>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary" id="add_service_job_submitter">Submit</button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>