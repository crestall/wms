<?php
$order_num_display = (!empty(Form::value('partof_order')))? "block":"none";
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php if($error):?>
        <div class="row">
            <div class="col-md-12">
                <div class="errorbox">
                    <div class="row">
                        <div class="col-md-2" style="font-size:96px">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="col-md-6">
                            <h2>No Pickup ID Supplied</h2>
                            <p>No pickup was supplied to update</p>
                            <p><a href="/orders/view-pickups">Please click here to view all pickups to choose from</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif(!$pickup || !count($pickup)):?>
        <div class="row">
            <div class="col-md-12">
                <div class="errorbox">
                    <div class="row">
                        <div class="col-md-2" style="font-size:96px">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="col-md-6">
                            <h2>No Pickup Found</h2>
                            <p>No pickup was found with that ID</p>
                            <p><a href="/orders/view-pickups">Please click here to view all pickups to choose from</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif($pickup['date_completed'] > 0):?>
        <div class="row">
            <div class="col-md-12">
                <div class="errorbox">
                    <div class="row">
                        <div class="col-md-2" style="font-size:96px">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="col-md-6">
                            <h2>Pickup Already Completed</h2>
                            <p>This pickup has already been recorded as complete</p>
                            <p><a href="/orders/view-pickups">Please click here to view all pickups to choose from</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php //echo "<pre>",print_r($pickup),"</pre>";?>
        <div class="row">
            <div class="col-md-12">
                <h3>Updating Pickup: <?php echo $pickup['pickup_number'];?></h3>
            </div>
        </div>
        <div class="row">
            <form id="pickup-update" method="post" action="/form/procPickupUpdate">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Client</label>
                    <div class="col-md-4">
                        <select id="client_id" name="client_id" class="form-control selectpicker" disabled><option value="0">--Select One--</option><?php echo $this->controller->client->getSelectClients($pickup['client_id']);?></select>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label">Pickup Address</label>
                    <div class="col-md-4">
                        <?php echo $pickup_address;?>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label">Delivery Address</label>
                    <div class="col-md-4">
                        <?php echo $dropoff_address;?>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="form-check">
                        <label class="form-check-label col-md-3" for="partof_order">Picked Up With Order</label>
                        <div class="col-md-4 checkbox checkbox-default">
                            <input class="form-check-input styled" type="checkbox" id="partof_order" name="partof_order" <?php if(!empty(Form::value('partof_order'))) echo "checked";?> />
                            <label for="partof_order"></label>
                        </div>
                    </div>
                </div>
                <div id="order_number_holder" class="form-group row" style="display: <?php echo $order_num_display;?>">
                    <label class="col-md-3 col-form-label">Order Number</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="order_number" id="order_number" value="<?php echo Form::value('order_number');?>" />
                        <?php echo Form::displayError('order_number');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Charge Amount</label>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="text" class="form-control required number" data-rule-min="0" name="truck_charge" id="truck_charge" value="<?php echo Form::value('truck_charge');?>" />
                        </div>
                        <?php echo Form::displayError('truck_charge');?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">&nbsp;</label>
                    <div class="col-md-4 text-right">
                        <button class="btn btn-warning" id="truck_charge_calc" data-destination="<?php echo $address_string;?>">Calculate Truck Charge</button>
                    </div>
                </div>
                <input type="hidden" id="origin" value="<?php echo $puaddress_string;?>" />
                <input type="hidden" id="truck_pallets" name ="pallets" value="<?php echo $pallets;?>" />
                <input type="hidden" id="pickup_id" name="pickup_id" value="<?php echo $pickup_id;?>" />
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">&nbsp;</label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    <?php endif;?>
</div>