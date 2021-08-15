<?php
$ship_to    = (empty(Form::value('ship_to')))?  (empty($shipment_details['ship_to'])?  $job['ship_to'] : $shipment_details['ship_to']) : Form::value('ship_to');
$address    = (empty(Form::value('address')))?  (empty($shipment_details['address'])?  $job['address'] : $shipment_details['address']) : Form::value('address');
$address2   = (empty(Form::value('address2')))? (empty($shipment_details['address2'])? $job['address2'] : $shipment_details['address2']) : Form::value('address2');
$suburb     = (empty(Form::value('suburb')))?   (empty($shipment_details['suburb'])? $job['suburb'] : $shipment_details['suburb']) : Form::value('suburb');
$state      = (empty(Form::value('state')))?    (empty($shipment_details['state'])? $job['state'] : $shipment_details['state']) : Form::value('state');
$postcode   = (empty(Form::value('postcode')))? (empty($shipment_details['postcode'])? $job['postcode'] : $shipment_details['postcode']) : Form::value('postcode');
$country    = (empty(Form::value('country')))?  (empty($shipment_details['country'])? $job['country'] : $shipment_details['country']) : Form::value('country');
$delivery_instructions = (empty(Form::value('delivery_instructions')))?  (empty($shipment_details['delivery_instructions'])? $job['delivery_instructions'] : $shipment_details['delivery_instructions']) : Form::value('delivery_instructions');
$attention    = (empty(Form::value('attention')))?  (empty($shipment_details['attention'])? $job['attention'] : $shipment_details['attention']) : Form::value('attention');
$contact_phone    = (empty(Form::value('contact_phone')))?  (empty($shipment_details['contact_phone'])? $job['customer_phone'] : $shipment_details['contact_phone']) : Form::value('contact_phone');

$tracking_email = empty(Form::value('tracking_email'))? $shipment_details['delivery_instructions']: Form::value('tracking_email');
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row d-lg-none mb-3">
            <div class="col"><button class="btn btn-sm btn-outline-fsg mobile-link" id="packages">Add Package</button></div>
            <div class="col"><button class="btn btn-sm btn-outline-fsg mobile-link" id="courier">Select Courier</button></div>
        </div>
        <div class="card-columns">
            <?php //echo "<pre>",print_r($job),"</pre>";?>
<!------------------------------------------------------------------------------------------------------------------------------------------->
<!-------------------------------------------------     Delivery Details     ---------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------------------------------->
            <!--div class="col-sm-12 col-md-6 mb-3"-->
                <div class="card border-secondary order-card" id="deliverydetails">
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
                        <?php if($shipment_id > 0):?>
                            <div class="row">
                                <label class="col-5">Deliver To:</label>
                                <div class="col-7"><?php echo $shipment_details['ship_to'];?></div>
                            </div>
                            <?php if(!empty($shipment_details['attention'])):?>
                                <div class="row">
                                    <label class="col-5">Attention</label>
                                    <div class="col-7"><?php echo $shipment_details['attention'];?></div>
                                </div>
                            <?php endif;?>
                            <div class="row">
                                <label class="col-5">Signature Required</label>
                                <div class="col-7"><?php if($shipment_details['signature_required'] == 0) echo "NO"; else echo "YES";?></div>
                            </div>
                            <div class="row">
                                <label class="col-5">Tracking Email</label>
                                <div class="col-7"><?php if(!empty($shipment_details['tracking_email'])) echo $shipment_details['tracking_email']; else echo "NOT LISTED"?></div>
                            </div>
                            <div class="row">
                                <label class="col-5">Contact Phone</label>
                                <div class="col-7"><?php if(!empty($shipment_details['contact_phone'])) echo $shipment_details['contact_phone']; else echo "NOT LISTED"?></div>
                            </div>
                            <div class="row">
                                <label class="col-5">Address:</label>
                                <div class="col-7"><?php echo $shipment_details['address'];?></div>
                            </div>
                            <?php if(!empty($shipment_details['address_2'])):?>
                                <div class="row">
                                    <label class="col-5">&nbsp;</label>
                                    <div class="col-7"><?php echo $shipment_details['address_2'];?></div>
                                </div>
                            <?php endif;?>
                            <div class="row">
                                <label class="col-5">&nbsp;</label>
                                <div class="col-7"><?php echo $shipment_details['suburb'];?></div>
                            </div>
                            <div class="row">
                                <label class="col-5">&nbsp;</label>
                                <div class="col-7"><?php echo $shipment_details['state'];?></div>
                            </div>
                            <div class="row">
                                <label class="col-5">&nbsp;</label>
                                <div class="col-7"><?php echo $shipment_details['country'];?></div>
                            </div>
                            <div class="row">
                                <label class="col-5">&nbsp;</label>
                                <div class="col-7"><?php echo $shipment_details['postcode'];?></div>
                            </div>
                            <div class="row">
                                <label class="col-5">Delivery Instructions</label>
                                <div class="col-7"><?php if(!empty($shipment_details['delivery_instructions'])) echo $shipment_details['delivery_instructions']; else echo "NONE GIVEN"?></div>
                            </div>
                        <?php else:?>
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
                                        <label class="col-md-4">Tracking Email</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control email" name="tracking_email" id="tracking_email" value="<?php echo $tracking_email;?>" />
                                            <span class="inst">Required if you wish to receive tracking notifications</span>
                                            <?php echo Form::displayError('tracking_email');?>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-4">Phone</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="contact_phone" id="contact_phone" value="<?php echo $contact_phone;?>" />
                                            <?php echo Form::displayError('contact_phone');?>
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
                                    <input type="hidden" name="shipment_id" id="shipment_id" value="<?php echo $shipment_id;?>" >
                                </div>
                            </form>
                        <?php endif;?>
                    </div>
                    <div class="card-footer text-right">
                        <?php if($shipment_id === 0):?>
                            <button id="delivery_details_update_submitter" class="btn btn-outline-secondary">Save Details</button>
                        <?php elseif($shipment_details['courier_id'] == 0):?>
                            <a class="btn btn-outline-secondary" href="/jobs/shipment-address-update/shipment=<?php echo $shipment_id;?>/job=<?php echo $job['id'];?>">Update Delivery Details</a>
                        <?php endif?>
                    </div>
                </div>
            <!--/div>
<!------------------------------------------------------------------------------------------------------------------------------------------->
<!-------------------------------------------------     Packages And Pallets     ---------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------------------------------->
            <!--div class="col-sm-12 col-md-6 mb-3"-->
                <div id="packages" class="card border-secondary order-card">
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
                        <?php else:?>
                            <h6 class="card-subtitle">No Packages or Pallets Listed</h6>
                        <?php endif;?>
                    </div>
                    <div class="card-footer">
                        <?php if($shipment_id == 0):?>
                            <p class="text-danger font-italic">Delivery details must be submitted before packages can be added</p>
                        <?php else:?>
                            <p class="text-right">
                                <button id="add_package" class="btn btn-outline-secondary" data-jobid="<?php echo $job['id'];?>" data-shipmentid="<?php echo $shipment_id;?>">Add Package/Pallet</button>
                            </p>
                        <?php endif;?>
                    </div>
                </div>
            <!--/div>
<!------------------------------------------------------------------------------------------------------------------------------------------->
<!-------------------------------------------------     Courier Selection    ---------------------------------------------------------------->
<!------------------------------------------------------------------------------------------------------------------------------------------->
            <!--div class="col-sm-12 col-md-6 mb-3"-->
                <div class="card border-secondary order-card" id="courier">
                    <div class="card-header bg-secondary text-white">
                        Select Courier
                    </div>
                    <div class="card-body">

                    </div>
                    <div class="card-footer">
                        <?php if($shipment_id == 0  || !count($packages)):?>
                            <p class="text-danger font-italic">Delivery details must be submitted and packages added before a courier can be chosen</p>
                        <?php else:?>
                            <div class="row">
                                <div class="col-6">
                                    <button class="ship_quote btn btn-outline-info quote_button" data-shipmentid="<?php echo $shipment_id;?>" data-destination="<?php echo $address_string;?>" >Get Shipping Prices</button>
                                </div>
                                <div class="col-6 text-right">
                                    <button id="update_courier" class="btn btn-outline-secondary">Update Courier</button>
                                </div>
                            </div>
                        <?php endif;?>
                    </div>
                </div>
            <!--/div-->
        </div>

    </div>
</div>