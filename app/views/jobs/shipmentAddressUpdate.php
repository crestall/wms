<?php
    $ship_to    = (empty(Form::value('ship_to')))?  $shipment['ship_to']      : Form::value('ship_to');
    $attention    = (empty(Form::value('attention')))?  $shipment['attention'] : Form::value('attention');
    $address    = empty(Form::value('address'))?    $shipment['address']      : Form::value('address');
    $address2   = empty(Form::value('address2'))?   $shipment['address_2']    : Form::value('address2');
    $suburb     = empty(Form::value('suburb'))?     $shipment['suburb']       : Form::value('suburb');
    $state      = empty(Form::value('state'))?      $shipment['state']        : Form::value('state');
    $postcode   = empty(Form::value('postcode'))?   $shipment['postcode']     : Form::value('postcode');
    $country    = empty(Form::value('country'))?    $shipment['country']      : Form::value('country');
    $tracking_email    = empty(Form::value('tracking_email'))?    $shipment['tracking_email']      : Form::value('tracking_email');
    $signature_req    = empty(Form::value('signature_req'))?    $shipment['signature_req'] == 1     : true;
    $delivery_instructions    = empty(Form::value('delivery_instructions'))?    $shipment['delivery_instructions']      : Form::value('delivery_instructions');
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php echo "<pre>",print_r($shipment),"</pre>";?>
        <?php if($shipment['courier_id'] != 0):?>
            <div class="row">
                <div class="col">
                    <div class="errorbox">
                        <div class="row">
                            <div class="col-4 text-right">
                                <i class="fad fa-exclamation-triangle fa-6x"></i>
                            </div>
                            <div class="col-8">
                                <h2>Courier Already Selected</h2>
                                <p>Sorry, dispatches that have had their courier assigned cannot have the address updated</p>
                                <p>See the warehouse about what can be done</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else:?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
            <?php echo Form::displayError('general');?>
            <form id="shipment-address-update" autocomplete="off" method="post" action="/form/procShipmentAddressUpdate">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Deliver To</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required" name="ship_to" id="ship_to" value="<?php echo $ship_to;?>" />
                        <?php echo Form::displayError('ship_to');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Attention</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="attention" id="attention" value="<?php echo $attention;?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Delivery Instructions</label>
                    <div class="col-md-4">
                        <textarea class="form-control" name="delivery_instructions" id="delivery_instructions" placeholder="Leave in a safe place out of the weather"><?php echo $delivery_instructions;?></textarea>
                        <span class="inst">Appears on shipping label. Defaults to 'Leave in a safe place out of the weather' for orders with an Authority To Leave</span>
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
                <div class="form-group row custom-control custom-checkbox custom-control-right">
                    <input class="custom-control-input" type="checkbox" id="signature_req" name="signature_req" <?php if($signature_req) echo 'checked';?> />
                    <label class="custom-control-label col-md-3" for="signature_req">Signature Required</label><br/>
                    <span class="inst">Leaving unchecked will give an 'Authority to Leave'</span>
                </div>
                <?php include(Config::get('VIEWS_PATH')."forms/address.php");?>
                <input type="hidden" name="job_id" value="<?php echo $shipment['job_id'];?>" />
                <input type="hidden" name="jshipment_id" value="<?php echo $shipment['id'];?>" />
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <div class="form-group row">
                    <div class="col-md-4 offset-md-3">
                        <button type="submit" class="btn btn-outline-secondary">Save Changes</button>
                    </div>
                </div>
            </form>
        <?php endif;?>
    </div>
</div>