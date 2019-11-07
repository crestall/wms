<?php
$deliver_to = empty(Form::value('deliver_to'))? $deliver_to: Form::value('deliver_to');
$tracking_email = empty(Form::value('tracking_email'))? $tracking_email: Form::value('tracking_email');
$signature_req = !empty(Form::value('signature_req'))? true: $signature_req;
$express_post = !empty(Form::value('express_post'))? true: $express_post;
$store_order = !empty(Form::value('store_order'))? true: $store_order;
$client_order_id = empty(Form::value('client_order_id'))? $client_order_id : Form::value('client_order_id') ;
$instructions = empty(Form::value('delivery_instructions'))? $instructions : Form::value('delivery_instructions') ;
$comments = empty(Form::value('tpl_comments'))? $comments : Form::value('tpl_comments') ;
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php if($error):?>
        <div class="row">
            <div class="col-lg-12">
                <div class="errorbox">
                    <div class="row">
                        <div class="col-lg-2" style="font-size:96px">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="col-lg-6">
                            <h2>No Order ID Supplied</h2>
                            <p>No order was supplied to update</p>
                            <p><a href="/orders/view-orders">Please click here to view all orders to choose from</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif(!$order || !count($order)):?>
        <div class="row">
            <div class="col-lg-12">
                <div class="errorbox">
                    <div class="row">
                        <div class="col-lg-2" style="font-size:96px">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="col-lg-6">
                            <h2>No Order Found</h2>
                            <p>No order was found with that ID</p>
                            <p><a href="/orders/view-orders">Please click here to view all orders to choose from</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else:?>
        <?php //echo "<pre>",print_r($order),"</pre>";?>
        <div class="row">
            <div class="col-lg-4">
                <a class="btn btn-primary" href="/orders/order-update/order=<?php echo $order_id;?>">Return to Order</a>
            </div>
            <div class="col-lg-4">
                <a class="btn btn-primary" href="/orders/view-orders/client=<?php echo $order['client_id'];?>">View Orders For Client</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <h2>Updating Details For Order Number <?php echo $order['order_number'];?></h2>
            </div>
        </div>
        <div class="row">
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
            <form id="order-edit" enctype="multipart/form-data" action="/form/procOrderEdit" method="post">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><?php if(empty($order['uploaded_file'])) echo "Upload"; else echo "Replace";?> PDF Attachment</label>
                    <div class="col-md-4">
                        <input type="file" name="invoice[]" id="invoice" multiple="multiple" onChange="fileUpload.makeFileList();" />
                        <span class="inst">(if required) - use ctrl click to select multiple files</span>
                        <?php if(!empty($order['uploaded_file'])):?>
                            <br/>
                            <span class="inst"><a href='/client_uploads/<?php echo $order['client_id']."/".$order['uploaded_file'];?>' target='_blank'>View Current File</a> </span>
                            <br/>
                            <div class="checkbox checkbox-default">
                                <input class="form-check-input styled" type="checkbox" id="delete_file" name="delete_file" />
                                <label for="delete_file"><span class="inst">Delete Current File</span></label>
                            </div>
                        <?php endif;?>
                        <ul id="fileList"></ul>
                        <?php echo Form::displayError('invoice');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Client Order Number</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="client_order_id" id="client_order_id" value="<?php echo $client_order_id;?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Deliver To</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required" name="deliver_to" id="deliver_to" value="<?php echo $deliver_to;?>" />
                        <?php echo Form::displayError('deliver_to');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Company Name</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="company_name" id="company_name" value="<?php echo $company_name;?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <div class="form-check">
                        <label class="form-check-label col-md-3" for="signature_req">Signature Required</label>
                        <div class="col-md-4 checkbox checkbox-default">
                            <input class="form-check-input styled" type="checkbox" id="signature_req" name="signature_req" <?php if($signature_req) echo "checked";?> />
                            <label for="signature_req"></label>
                            <span class="inst">Leaving unchecked will give an 'Authority to Leave'</span>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="form-check">
                        <label class="form-check-label col-md-3" for="express_post">Use Express Post</label>
                        <div class="col-md-4 checkbox checkbox-default">
                            <input class="form-check-input styled" type="checkbox" id="express_post" name="express_post" <?php if($express_post) echo "checked";?> />
                            <label for="express_post"></label>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="form-check">
                        <label class="form-check-label col-md-3" for="store_order">Store Order</label>
                        <div class="col-md-4 checkbox checkbox-default">
                            <input class="form-check-input styled" type="checkbox" id="store_order" name="store_order" <?php if($store_order) echo "checked";?> />
                            <label for="store_order"></label>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Tracking Email</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control email" name="tracking_email" id="tracking_email" value="<?php echo $tracking_email;?>" />
                        <span class="inst">Required if you wish to receive tracking notifications</span>
                        <?php echo Form::displayError('tracking_email');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Phone</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="contact_phone" id="contact_phone" value="<?php echo $contact_phone;?>" />
                        <?php echo Form::displayError('contact_phone');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Delivery Instructions</label>
                    <div class="col-md-4">
                        <textarea class="form-control" name="delivery_instructions" id="delivery_instructions" placeholder="Leave in a safe place out of the weather"><?php echo $instructions;?></textarea>
                        <span class="inst">Appears on shipping label. Defaults to 'Leave in a safe place out of the weather' for orders with an Authority To Leave</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">3PLPLUS Instructions</label>
                    <div class="col-md-4">
                        <textarea class="form-control" name="tpl_comments" id="tpl_comments"><?php echo $comments;?></textarea>
                        <span class="inst">Instructions for the pickers and packers</span>
                    </div>
                </div>
                <input type="hidden" name="order_id" id="order_id" value="<?php echo $order_id;?>" />
                <input type="hidden" name="client_id" id="client_id" value="<?php echo $order['client_id'];?>" />
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">&nbsp;</label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Edit Order</button>
                    </div>
                </div>
            </form>
        </div>
    <?php endif;?>
</div>