<?php
$ship_to    = (empty(Form::value('ship_to')))?  $job['ship_to']      : Form::value('ship_to');
$address    = empty(Form::value('address'))?    $job['address']      : Form::value('address');
$address2   = empty(Form::value('address2'))?   $job['address_2']    : Form::value('address2');
$suburb     = empty(Form::value('suburb'))?     $job['suburb']       : Form::value('suburb');
$state      = empty(Form::value('state'))?      $job['state']        : Form::value('state');
$postcode   = empty(Form::value('postcode'))?   $job['postcode']     : Form::value('postcode');
$country    = empty(Form::value('country'))?    $job['country']      : Form::value('country');
$delivery_instructions = empty(Form::value('delivery_instructions'))? $job['delivery_instructions'] : Form::value('delivery_instructions');
$attention = empty(Form::value('attention'))? $job['attention'] : Form::value('attention');
$tracking_email = Form::value('tracking_email');
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <?php //echo "<pre>",print_r($job),"</pre>";?>
<!------------------------------------------------------------------------------------------------------------------------------------------->
<!-------------------------------------------------     Delivery Details     ---------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------------------------------->
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card h-100 border-secondary order-card" id="deliverydetails">
                    <div class="card-header bg-secondary text-white">
                        Delivery Details
                    </div>
                    <div class="card-body">
                        <?php if(isset($_SESSION['jobdeliverydetailsfeedback'])) :?>
                            <div class='feedbackbox'><?php echo Session::getAndDestroy('jobdeliverydetailsfeedback');?></div>
                        <?php endif; ?>
                        <?php if(isset($_SESSION['jobdeliverydetailserrorfeedback'])) :?>
                            <div class='errorbox'><?php echo Session::getAndDestroy('jobdeliverydetailserrorfeedback');?></div>
                        <?php endif; ?>
                        <form id="job_delivery_details_update" method="post" action="/form/procProductionJobDeliveryUpdate">
                            <div id="delivery_address_holder">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Deliver To</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control required" name="ship_to" id="ship_to" value="<?php echo $ship_to;?>" />
                                        <?php echo Form::displayError('ship_to');?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">Attention</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="attention" id="attention" value="<?php echo $attention;?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">Delivery Instructions</label>
                                    <div class="col-md-8">
                                        <textarea class="form-control" name="delivery_instructions" id="delivery_instructions" placeholder="Leave in a safe place out of the weather"><?php echo $delivery_instructions;?></textarea>
                                        <span class="inst">Appears on shipping label. Defaults to 'Leave in a safe place out of the weather' for orders with an Authority To Leave</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">Tracking Email</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control email" name="tracking_email" id="tracking_email" value="<?php echo $tracking_email;?>" />
                                        <span class="inst">Required if you wish to receive tracking notifications</span>
                                        <?php echo Form::displayError('tracking_email');?>
                                    </div>
                                </div>
                                <div class="form-group row custom-control custom-checkbox custom-control-right">
                                    <input class="custom-control-input" type="checkbox" id="signature_req" name="signature_req" <?php if(!empty(Form::value('signature_req'))) echo 'checked';?> />
                                    <label class="custom-control-label col-md-4" for="signature_req">Signature Required</label><br/>
                                    <span class="inst">Leaving unchecked will give an 'Authority to Leave'</span>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Address Line 1</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control required" name="address" id="address" value="<?php echo $address;?>" /><br>
                                        <div class="checkbox checkbox-default" style="margin-left:20px;margin-top:-25px">
                                            <input class="form-check-input styled" type="checkbox" id="ignore_address_error" name="ignore_address_error" <?php if(!empty(Form::value('ignore_address_error'))) echo 'checked';?> />
                                            <label for="ignore_address_error"><span class="inst">No need for a number</span></label>
                                        </div>
                                        <?php echo Form::displayError('address');?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4">Address Line 2</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="address2" id="address2" value="<?php echo $address2;?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Suburb/Town</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control required" name="suburb" id="suburb" value="<?php echo $suburb;?>" />
                                        <?php echo Form::displayError('suburb');?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4">State</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="state" id="state" value="<?php echo $state;?>" />
                                        <span class="inst">for AU addresses use VIC, NSW, QLD, ACT, TAS, WA, SA, NT only</span>
                                        <?php echo Form::displayError('state');?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 "><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Postcode</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control required" name="postcode" id="postcode" value="<?php echo $postcode;?>" />
                                        <?php echo Form::displayError('postcode');?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Country</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control required" name="country" id="country" value="<?php echo $country;?>" />
                                        <span class="inst">use the 2 letter ISO code</span>
                                        <?php echo Form::displayError('country');?>
                                    </div>
                                </div>
                                <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                                <input type="hidden" name="job_id" id="job_id" value="<?php echo $job['id'];?>" >
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-right">
                        <button id="delivery_details_update_submitter" class="btn btn-outline-secondary">Update Details</button>
                    </div>
                </div>
            </div>
<!------------------------------------------------------------------------------------------------------------------------------------------->
<!-------------------------------------------------     Packages And Pallets     ---------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------------------------------->
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card border-secondary h-100 order-card">
                    <div class="card-header bg-secondary text-white">
                        Packages and Pallets
                    </div>
                    <div class="card-body">
                        <?php if(isset($_SESSION['packagefeedback'])) :?>
                           <div class='feedbackbox'><i class="far fa-check-circle"></i> <?php echo Session::getAndDestroy('packagefeedback');?></div>
                        <?php endif; ?>
                        <?php if(isset($_SESSION['packageerrorfeedback'])) :?>
                           <div class='errorbox'><i class="far fa-times-circle"></i> <?php echo Session::getAndDestroy('packageerrorfeedback');?></div>
                        <?php endif; ?>
                        <?php if(count($packages)):?>
                            <?php $pc = 1;
                            foreach($packages as $p):
                                $s = ($p['count'] == 1)? "":"s";?>
                                <div class="container-fluid">
                                    <div class="row">
                                        <h5 class="card-subtitle mb-3"><?php echo $p['count'];?> <?php echo ($p['pallet'] > 0)? "Pallet{$s}":"Package{$s}";?></h5>
                                    </div>
                                    <div class="row border-bottom mb-3">
                                        <div class="col-10">
                                            <div class="row">
                                                <label class="col-lg-3 col-md-7 col-sm-9 col-9">Width</label>
                                                <div class="col-lg-3 col-md-5 col-sm-3 col-3"><?php echo $p['width'];?> cm</div>
                                                <label class="col-lg-3 col-md-7 col-sm-9 col-9">Depth</label>
                                                <div class="col-lg-3 col-md-5 col-sm-3 col-3"><?php echo $p['depth'];?> cm</div>
                                            </div>
                                            <div class="row">
                                                <label class="col-lg-3 col-md-7 col-sm-9 col-9">Height</label>
                                                <div class="col-lg-3 col-md-5 col-sm-3 col-3"><?php echo $p['height'];?> cm</div>
                                                <label class="col-lg-3 col-md-7 col-sm-9 col-9">Weight</label>
                                                <div class="col-lg-3 col-md-5 col-sm-3 col-3"><?php echo $p['weight'];?> kg</div>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <?php if($order['courier_id'] == 0):?>
                                                <a class="delete-package" data-packageid="<?php echo $p['id'];?>" title="remove this package"><i class="fas fa-backspace fa-2x text-danger"></i></a>
                                            <?php endif;?>
                                        </div>
                                    </div>
                                </div>
                            <?php ++$pc;
                            endforeach;?>
                        <?php elseif(count($order_items) == 1 && $order_items[0]['boxed_item'] == 1 && $order_items[0]['qty'] == 1):?>
                            <h6 class="card-subtitle">The Following Package Will Be Auto-Submitted</h6>
                            <div class="container-fluid">
                                <div class="row border-bottom border-top my-3 py-3">
                                    <div class="col">
                                        <div class="row">
                                            <label class="col-lg-3 col-md-7 col-sm-9 col-9">Width</label>
                                            <div class="col-lg-3 col-md-5 col-sm-3 col-3"><?php echo $order_items[0]['width'];?> cm</div>
                                            <label class="col-lg-3 col-md-7 col-sm-9 col-9">Depth</label>
                                            <div class="col-lg-3 col-md-5 col-sm-3 col-3"><?php echo $order_items[0]['depth'];?> cm</div>
                                        </div>
                                        <div class="row">
                                            <label class="col-lg-3 col-md-7 col-sm-9 col-9">Height</label>
                                            <div class="col-lg-3 col-md-5 col-sm-3 col-3"><?php echo $order_items[0]['height'];?> cm</div>
                                            <label class="col-lg-3 col-md-7 col-sm-9 col-9">Weight</label>
                                            <div class="col-lg-3 col-md-5 col-sm-3 col-3"><?php echo $order_items[0]['weight'];?> kg</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else:?>
                            <h6 class="card-subtitle">No Packages or Pallets Listed</h6>
                        <?php endif;?>
                    </div>
                    <div class="card-footer text-right">
                        <?php if($order['courier_id'] == 0):?>
                            <button id="add_package" class="btn btn-outline-secondary" data-orderid="<?php echo $order_id;?>">Add Package/Pallet</button>
                        <?php endif;?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>