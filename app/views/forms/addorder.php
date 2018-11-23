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
        <form id="add_order" method="post" action="/form/procOrderAdd"  enctype="multipart/form-data" autocomplete="off">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Order Details</h3>
                </div>
            </div>
            <?php if($user_role == "client"):?>
                <input type="hidden" name="client_id" id="client_id" value="<?php echo Session::get("client_id");?>" />
            <?php else:?>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Client</label>
                    <div class="col-md-4">
                        <select id="client_id" name="client_id" class="form-control selectpicker"><option value="0">--Select One--</option><?php echo $this->controller->client->getSelectClients($client_id);?></select>
                        <?php echo Form::displayError('client_id');?>
                    </div>
                </div>
            <?php endif;?>
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
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Upload PDF Attachment</label>
                <div class="col-md-4">
                    <input type="file" name="invoice[]" id="invoice" multiple="multiple" onChange="fileUpload.makeFileList();" />
                    <span class="inst">(if required) - use ctrl click to select multiple files</span>
                    <ul id="fileList"></ul>
                    <?php echo Form::displayError('invoice');?>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="express_post">Use Express Post</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="express_post" name="express_post" <?php if(!empty(Form::value('express_post'))) echo 'checked';?> />
                        <label for="express_post"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="b2b">Bulk Store Order</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="b2b" name="b2b" <?php if(!empty(Form::value('b2b'))) echo 'checked';?> />
                        <label for="b2b"></label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label col-md-3" for="signature_req">Signature Required</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="signature_req" name="signature_req" />
                        <label for="signature_req"></label>
                        <span class="inst">Leaving unchecked will give an 'Authority to Leave'</span>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Client Order Number</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="client_order_id" id="client_order_id" value="<?php echo Form::value('client_order_id');?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Customer Order Number</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="customer_order_id" id="customer_order_id" value="<?php echo Form::value('customer_order_id');?>" />
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <h3>Delivery Details</h3>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Deliver To</label>
                <div class="col-md-4">
                    <input type="text" class="form-control required" name="deliver_to" id="deliver_to" value="<?php echo Form::value('deliver_to');?>" />
                    <?php echo Form::displayError('deliver_to');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Company Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="company_name" id="company_name" value="<?php echo Form::value('company_name');?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Tracking Email</label>
                <div class="col-md-4">
                    <input type="text" class="form-control email" name="tracking_email" id="tracking_email" value="<?php echo Form::value('tracking_email');?>" />
                    <span class="inst">Required if you wish to receive tracking notifications</span>
                    <?php echo Form::displayError('tracking_email');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Phone</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="contact_phone" id="contact_phone" value="<?php echo Form::value('contact_phone');?>" />
                    <?php echo Form::displayError('contact_phone');?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Delivery Instructions</label>
                <div class="col-md-4">
                    <textarea class="form-control" name="delivery_instructions" id="delivery_instructions" placeholder="Leave in a safe place out of the weather"><?php echo Form::value('delivery_instructions');?></textarea>
                    <span class="inst">Appears on shipping label. Defaults to 'Leave in a safe place out of the weather' for orders with an Authority To Leave</span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">3PLPLUS Instructions</label>
                <div class="col-md-4">
                    <textarea class="form-control" name="3pl_comments" id="3pl_comments"><?php echo Form::value('3pl_comments');?></textarea>
                    <span class="inst">Instructions for the pickers and packers</span>
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