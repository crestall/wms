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
                    <label class="col-md-3"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Client</label>
                    <div class="col-md-4">
                        <select id="client_id" name="client_id" class="form-control selectpicker" data-style="btn-outline-secondary" required><option value="0">--Select One--</option><?php echo $this->controller->client->getSelectClients($client_id);?></select>
                        <?php echo Form::displayError('client_id');?>
                    </div>
                </div>
            <?php endif;?>
            <div id="item_selector" class="p-3 pb-0 mb-2 rounded-top mid-grey" style="display:<?php echo $idisp;?>">
                <div class="row mb-0">
                    <div class="col-md-4">
                        <h4>Line Items</h4>
                    </div>
                    <div class="col-md-4">
                        <a class="add-item" style="cursor:pointer" title="Add Another Item"><h4><i class="fad fa-plus-square text-success"></i> Add another</a></h4>
                    </div>
                    <div class="col-md-4">
                        <a id="remove-all-items" style="cursor:pointer" title="Remove All Items"><h4><i class="fad fa-times-square text-danger"></i> Remove all</a></h4>
                    </div>
                </div>
                <div id="items_holder" class="p-3 light-grey">
                    <?php if(is_array(Form::value('items'))):
                        //echo "<pre>",print_r(Form::value('items')),"</pre>";//die();
                        echo Form::displayError('items');
                        foreach(Form::value('items') as $ind => $ita):?>
                            <div class="row item_holder">
                                <div class='col-md-1 delete-image-holder'>
                                    <a class='delete' title='remove this item'><i class='fad fa-times-square text-danger'></i><span class="inst">Remove</span></a>
                                </div>
                                <div class="col-md-6">
                                    <p><input type="text" class="form-control item-searcher" name="items[<?php echo $ind;?>][name]" placeholder="Item Name" value="<?php echo $ita['name'];?>" /></p>
                                </div>
                                <div class="col-md-2 qty-holder">
                                    <?php if(isset($ita['whole_pallet']) && $ita['whole_pallet']):?>
                                        <input type='hidden' name='items[<?php echo $ind;?>][whole_pallet]' value='1' />
                                        <select class='form-control selectpicker pallet_qty' data-style='btn-outline-secondary' name='items[<?php echo $ind;?>][qty]'>
                                            <option value='0'>Quantity</option>
                                            <?php echo $this->controller->item->getSelectLocationAvailableCounts($ita['id'], $ita['qty']);?>
                                        </select>
                                    <?php else:?>
                                        <input type='text' class='form-control number item_qty' name='items[<?php echo $ind;?>][qty]' placeholder='Qty' value="<?php echo $ita['qty'];?>" />
                                    <?php endif;?>
                                </div>
                                <div class="col-md-3 qty-location"></div>
                                <input type="hidden" name="items[<?php echo $ind;?>][id]" class="item_id" value="<?php echo $ita['id'];?>"  />
                            </div>
                        <?php endforeach;?>
                    <?php else:?>
                        <div class="row item_holder">
                            <div class='col-md-1 delete-image-holder'>
                                <a class='delete' title='remove this item' style="display:none;"><i class='fad fa-times-square text-danger'></i><span class="inst">Remove</span></a>
                            </div>
                            <div class="col-md-6">
                                <p><input type="text" class="form-control item-searcher" name="items[0][name]" placeholder="Item Name" /></p>
                            </div>
                            <div class="col-md-2 qty-holder">

                            </div>
                            <div class="col-md-3 qty-location"></div>
                            <input type="hidden" name="items[0][id]" class="item_id"  />
                        </div>
                    <?php endif;?>
                </div>
            </div>
            <div class="form-group form-row">
                <label class="col-md-3 col-form-label">Upload PDF Attachment</label>
                <div class="col-md-4">
                    <input type="file" name="invoice[]" id="invoice" multiple="multiple" onChange="fileUpload.makeFileList();" /> <br/>
                    <span class="inst">(if required) - use ctrl click to select multiple files</span>
                    <ul id="fileList"></ul>
                    <?php echo Form::displayError('invoice');?>
                </div>
            </div>
            <div class="form-group row custom-control custom-checkbox custom-control-right">
                <input class="custom-control-input" type="checkbox" id="express_post" name="express_post" <?php if(!empty(Form::value('express_post'))) echo 'checked';?> />
                <label class="custom-control-label col-md-3" for="express_post">Use Express Post</label>
            </div>
            <div class="form-group row custom-control custom-checkbox custom-control-right">
                <input class="custom-control-input" type="checkbox" id="b2b" name="b2b" <?php if(!empty(Form::value('b2b'))) echo 'checked';?> />
                <label class="custom-control-label col-md-3" for="b2b">Bulk Store Order</label>
            </div>
            <div class="form-group row custom-control custom-checkbox custom-control-right">
                <input class="custom-control-input" type="checkbox" id="signature_req" name="signature_req" <?php if(!empty(Form::value('signature_req'))) echo 'checked';?> />
                <label class="custom-control-label col-md-3" for="signature_req">Signature Required</label><br/>
                <span class="inst">Leaving unchecked will give an 'Authority to Leave'</span>
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
                <label class="col-md-3 col-form-label">FSG Instructions</label>
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
                    <button type="submit" class="btn btn-outline-secondary" id="submitter" disabled>Add This Order</button>
                </div>
            </div>
        </form>
    </div>

