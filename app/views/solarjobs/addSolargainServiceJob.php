<?php
$address = Form::value('address');
$address2 = Form::value('address2');
$suburb = Form::value('suburb');
$state = Form::value('state');
$postcode = Form::value('postcode');
$country = Form::value('country');
$date_filter = "Install Date";
$date = (empty(Form::value('date_value')))? time() : Form::value('date_value');
$idisp = "none";
if(!empty(Form::value('items')))
    $idisp = "block";
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <?php echo Form::displayError('general');?>
    <div class="col-lg-12">
        <form id="solargain-service-job" method="post" action="/form/procAddSolargainServiceJob" autocomplete="off">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Job Details</h3>
                </div>
            </div>
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
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Customer Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="customer_name" id="customer_name" value="<?php echo Form::value('customer_name');?>" />
                    <?php echo Form::displayError('customer_name');?>
                </div>
            </div>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/select_date.php");?>
            <div class="row">
                <div class="col-lg-12">
                    <h3>Address Details</h3>
                </div>
            </div>
            <div id="item_selector" class="form-group row" style="display:<?php echo $idisp;?>">
                <div class="bs-callout bs-callout-primary bs-callout-more col-md-11">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Line Items</label>
                    <div class="col-md-9" id="items_holder">
                        <?php if(is_array(Form::value('items'))):?>
                            <?php foreach(Form::value('items') as $i => $item):
                                $qty = (isset($item['qty']))? $item['qty']: "";
                                $pallet_qty = (isset($item['pallet_qty']))? $item['pallet_qty']: "";?>
                                <div class="row item_holder">
                                    <?php if($i == 0):?>
                                        <div class="col-sm-1 add-image-holder">
                                            <a class="add" style="cursor:pointer" title="Add Another Item">
                                                <i class="fas fa-plus-circle fa-2x text-success"></i>
                                            </a>
                                        </div>
                                    <?php else:?>
                                        <div class="col-sm-1 delete-image-holder">
                                            <a class="delete" style="cursor:pointer" title="Remove This Item">
                                                <i class="fas fa-times-circle fa-2x text-danger"></i>
                                            </a>
                                        </div>
                                    <?php endif;?>
                                    <div class="col-sm-4">
                                        <p><input type="text" class="form-control item-searcher" name="items[<?php echo $i;?>][name]" placeholder="Item Name" value="<?php echo $item['name'];?>" /></p>
                                    </div>
                                    <div class="col-sm-4 qty-holder">
                                    <?php if($this->controller->item->isPalletItem($item['id'])):
                                        $select_values = $this->controller->item->getPalletCountSelect($item['id']);
                                        //var_dump($select_values);?>
                                        <div class='col-sm-4'><input type='text' class='form-control number item_qty' name='items[<?php echo $i;?>][qty]' placeholder='Qty' value="<?php echo $qty;?>" /></div>
                                        <div class='col-sm-8'><select class='form-control selectpicker pallet_qty' name='items[<?php echo $i;?>][pallet_qty]'><option value='0'>Whole Pallet Qty</option>
                                        <?php foreach($select_values as $sv):
                                            if($sv['available'] == 0) continue;;?>
                                            <option <?php if($sv['available'] == $pallet_qty) echo "selected";?>><?php echo $sv['available'];?></option>
                                        <?php endforeach;?>
                                        </select>
                                        </div>
                                    <?php else:?>
                                        <input type='text' class='form-control number item_qty' name='items[<?php echo $i;?>][qty]' placeholder='Qty' value="<?php echo $qty;?>"  />
                                    <?php endif;?>
                                    </div>
                                    <div class="col-sm-5 qty-location"></div>
                                    <input type="hidden" name="items[<?php echo $i;?>][id]" class="item_id" value="<?php echo $item['id'];?>" />
                                </div>
                            <?php endforeach;?>
                        <?php else:?>
                            <div class="row item_holder">
                                <div class="col-sm-1 add-image-holder">
                                    <a class="add" style="cursor:pointer" title="Add Another Item">
                                        <i class="fas fa-plus-circle fa-2x text-success"></i>
                                    </a>
                                </div>
                                <div class="col-sm-4">
                                    <p><input type="text" class="form-control item-searcher" name="items[0][name]" placeholder="Item Name" /></p>
                                </div>
                                <div class="col-sm-4 qty-holder">

                                </div>
                                <div class="col-sm-3 qty-location"></div>
                                <input type="hidden" name="items[0][id]" class="item_id"  />
                            </div>
                        <?php endif;?>
                    </div>
                    <?php echo Form::displayError('items');?>
                </div>
            </div>
            <?php include(Config::get('VIEWS_PATH')."forms/address.php");?>
            <input type="hidden" name="selected_items" id="selected_items" />
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <input type="hidden" name="client_id" id="client_id" value="67" />
            <input type="hidden" name="type_id" id="type_id" value="<?php echo $order_type_id; ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary" id="add_origin_order_submitter">Add Job</button>
                </div>
            </div>
        </form>
    </div>
</div>