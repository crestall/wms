<?php
$truck_display = (empty(Form::value('truck_display')))? 'none' : "block";
$local_display = (empty(Form::value('local_display')))? 'none' : "block";
$truck_pallets = (empty(Form::value('truck_pallets')))? 1:Form::value('truck_pallets');
$pallets = (empty(Form::value('pallets')))? 1:Form::value('pallets');
$truck_charge = (empty(Form::value('truck_charge')))? $order['total_cost']:Form::value('truck_charge');
$courier_name = (empty(Form::value('courier_name')))? $order['courier_name']:Form::value('courier_name');
$local_charge = (empty(Form::value('local_charge')))? $order['total_cost']:Form::value('local_charge');
$direct_charge = (empty(Form::value('direct_charge')))? $order['total_cost']:Form::value('direct_charge');
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
            <div class="col-md-12">
                <div class="errorbox">
                    <div class="row">
                        <div class="col-md-2" style="font-size:96px">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="col-md-6">
                            <h2>No Order Found</h2>
                            <p>No order was found with that ID</p>
                            <p><a href="/orders/view-orders">Please click here to view all orders to choose from</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else:?>
        <div class="row">
            <div class="col-md-4">
                <?php if($store_order):?>
                    <p><a class="btn btn-primary" href="/orders/view-storeorders/client=<?php echo $order['client_id'];?>">View Store Orders For Client</a> </p>
                <?php else:?>
                    <p><a class="btn btn-primary" href="/orders/view-orders/client=<?php echo $order['client_id'];?>">View Orders For Client</a></p>
                <?php endif;?>
            </div>
            <?php if($user_role == "admin" || $user_role == "super admin"):?>
                <div class="col-md-4">
                    <p><a class="btn btn-primary" href="/orders/order-detail/order=<?php echo $order_id;?>">View and Print Details</a></p>
                </div>
            <?php endif;?>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h2>Updating Order Number <?php echo $order['order_number'];?></h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h3>Order For <?php echo $client_name;?></h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php if(isset($_SESSION['feedback'])) :?>
                   <div class='feedbackbox'><?php echo Session::getAndDestroy('feedback');?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['errorfeedback'])) :?>
                   <div class='errorbox'><?php echo Session::getAndDestroy('errorfeedback');?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h3>Delivery Details</h3>
            </div>
        </div>
        <div class="bs-callout bs-callout-primary bs-callout-more">
            <div class="row ">
                <div class="col-md-8">
                    <dl class="dl-horizontal order-details">
                        <dt>Deliver To</dt>
                        <dd><?php echo $order['ship_to'];?></dd>
                        <dt>Address</dt>
                        <dd><?php echo $order['address'];?></dd>
                        <?php if(!empty($order['address_2'])):?>
                            <dt>&nbsp;</dt>
                            <dd><?php echo $order['address_2'];?></dd>
                        <?php endif;?>
                        <dt>&nbsp;</dt>
                        <dd><?php echo $order['suburb'];?></dd>
                        <dt>&nbsp;</dt>
                        <dd><?php echo $order['state'];?></dd>
                        <dt>&nbsp;</dt>
                        <dd><?php echo $order['postcode'];?></dd>
                        <dt>&nbsp;</dt>
                        <dd><?php echo $order['country'];?></dd>
                    </dl>
                </div>
            </div>
            <?php if($order['courier_id'] == 0 && ($user_role == "admin" || $user_role == "super admin")):?>
                <div class='row'>
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <a class="btn btn-primary" href="/orders/address-update/order=<?php echo $order_id;?>">Update Address Details</a>
                    </div>
                </div>
            <?php endif;?>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h3>Order Details</h3>
            </div>
        </div>
        <div class="bs-callout bs-callout-primary row bs-callout-more">
            <div class="row">
                <div class="col-md-7">
                    <dl class="dl-horizontal order-details">
                        <dt>Deliver To</dt>
                        <dd><?php echo $order['ship_to'];?></dd>
                        <dt>Company</dt>
                        <dd><?php echo $order['company_name'];?></dd>
                        <dt>Contact Phone</dt>
                        <dd><?php echo $order['contact_phone'];?></dd>
                        <dt>Tracking Email</dt>
                        <dd><?php echo $order['tracking_email'];?></dd>
                        <dt>Delivery Instructions</dt>
                        <dd><?php echo $order['instructions'];?></dd>
                        <dt>Use Express</dt>
                        <dd><?php if($order['eparcel_express'] > 0) echo "Yes"; else echo "No";?></dd>
                        <dt>Signature Required</dt>
                        <dd><?php if($order['signature_req'] > 0) echo "Yes"; else echo "No";?></dd>
                    </dl>
                </div>
                <div class="col-md-5">
                    <dl class="dl-horizontal order-details">
                        <dt>Client Order Number</dt>
                        <dd><?php echo $order['client_order_id'];?></dd>
                        <dt>Client Invoice</dt>
                        <dd><?php echo $order['uploaded_file'];?></dd>
                        <dt>3PL Instructions</dt>
                        <dd><?php echo $order['3pl_comments'];?></dd>
                        <dt>Entered By</dt>
                        <dd><?php echo $entered_by;?></dd>
                    </dl>
                </div>
            </div>
            <?php if($order['courier_id'] == 0 && ($user_role == "admin" || $user_role == "super admin")):?>
                <div class='row'>
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <a class="btn btn-primary" href="/orders/order-edit/order=<?php echo $order_id;?>">Update These Details</a>
                    </div>
                </div>
            <?php endif;?>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h3>Order Items</h3>
            </div>
        </div>
        <div class="bs-callout bs-callout-primary bs-callout-more">
            <div class="row">
                <div class="col-md-10">
                    <dl class="dl-horizontal order-items">
                        <?php foreach($order_items as $oi):?>
                            <dt><?php echo $oi['name'];?></dt>
                            <dd><?php echo $oi['qty'];?></dd>
                        <?php endforeach;?>
                    </dl>
                </div>
            </div>
            <?php if($order['courier_id'] == 0 && ($user_role == "admin" || $user_role == "super admin")):?>
                <div class='row'>
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <p><a class="btn btn-primary" href="/orders/add-serials/order=<?php echo $order_id;?>">Add Serial Numbers</a> </p>
                    </div>
                </div>
            <?php endif;?>
            <?php if($order['courier_id'] == 0 && ($user_role == "admin" || $user_role == "super admin")):?>
                <div class='row'>
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <p><a class="btn btn-primary" href="/orders/items-update/order=<?php echo $order_id;?>">Update Order Items</a></p>
                    </div>
                </div>
            <?php endif;?>
        </div>
        <?php if($order['courier_id'] > 0):?>
            <?php if(count($packages)):?>
                <div class="row">
                    <div class="col-md-12">
                        <h3>Packages in this Order</h3>
                    </div>
                </div>
                <div class="bs-callout bs-callout-primary bs-callout-more">
                    <?php $pc = 1; foreach($packages as $p):?>
                        <div class="row">
                            <div class="col-md-10">
                                <div class="row">
                                    <label class="col-md-4 col-form-label">Width</label>
                                    <div class="col-md-2"><?php echo $p['width'];?> cm</div>
                                    <label class="col-md-4 col-form-label">Depth</label>
                                    <div class="col-md-2"><?php echo $p['depth'];?> cm</div>
                                </div>
                                <div class='row'>
                                    <label class="col-md-4 col-form-label">Height</label>
                                    <div class="col-md-2"><?php echo $p['height'];?> cm</div>
                                    <label class="col-md-4 col-form-label">Weight</label>
                                    <div class="col-md-2"><?php echo $p['weight'];?> kg</div>
                                </div>
                            </div>
                        </div>
                    <?php ++$pc; endforeach;?>
                </div>

            <?php endif;?>
        <?php endif;?>
        <?php if($order['courier_id'] == 0):?>
            <div class="row">
                <div class="col-md-12">
                    <h3>Order Updating</h3>
                </div>
            </div>
            <div class="bs-callout bs-callout-primary row bs-callout-more">
                <div class="col-md-10">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Add a Package</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?php if(isset($_SESSION['packagefeedback'])) :?>
                               <div class='feedbackbox'><i class="far fa-check-circle"></i> <?php echo Session::getAndDestroy('packagefeedback');?></div>
                            <?php endif; ?>
                            <?php if(isset($_SESSION['packageerrorfeedback'])) :?>
                               <div class='errorbox'><i class="far fa-times-circle"></i> <?php echo Session::getAndDestroy('packageerrorfeedback');?></div>
                            <?php endif; ?>
                            <a name="package"></a>
                            <p class="text-info">fields marked <sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> are required</p>
                        </div>
                    </div>
                    <div class="row">
                        <form id="order-add-package" method="post" action="/form/procAddPackage">
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Width</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control required number" name="width" id="width" value="<?php echo Form::value('width');?>" />
                                        <span class="input-group-addon">cm</span>
                                    </div>
                                </div>
                                <label class="col-md-2 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Depth</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control required number" name="depth" id="depth" value="<?php echo Form::value('depth');?>" />
                                        <span class="input-group-addon">cm</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Height</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control required number" name="height" id="height" value="<?php echo Form::value('height');?>" />
                                        <span class="input-group-addon">cm</span>
                                    </div>
                                </div>
                                <label class="col-md-2 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Weight</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control required number" name="weight" id="weight" value="<?php echo Form::value('weight');?>" />
                                        <span class="input-group-addon">Kg</span>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                            <input type="hidden" name="order_id" value="<?php echo $order_id;?>" />
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label">&nbsp;</label>
                                <div class="form-check">
                                    <div class="col-md-1 checkbox checkbox-default">
                                        <input class="form-check-input styled" type="checkbox" id="pallet" name="pallet" <?php if(!empty(Form::value('pallet'))) echo 'checked';?> />
                                        <label for="pallet"></label>
                                    </div>
                                    <label class="form-check-label col-md-3" for="pallet">Pallet</label>
                                </div>
                                <label class="col-md-2 col-form-label">&nbsp;</label>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary">Add Package</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php if(count($packages)):?>
                <div class="row">
                    <div class="col-md-12">
                        <h4>Packages in this Order</h4>
                    </div>
                </div>
                <?php $pc = 1; foreach($packages as $p):?>
                    <div class="row alert alert-info">
                        <div class="col-md-9">
                            <div class="row">
                                <label class="col-md-4 col-form-label">Width</label>
                                <div class="col-md-2"><?php echo $p['width'];?> cm</div>
                                <label class="col-md-4 col-form-label">Depth</label>
                                <div class="col-md-2"><?php echo $p['depth'];?> cm</div>
                            </div>
                            <div class='row'>
                                <label class="col-md-4 col-form-label">Height</label>
                                <div class="col-md-2"><?php echo $p['height'];?> cm</div>
                                <label class="col-md-4 col-form-label">Weight</label>
                                <div class="col-md-2"><?php echo $p['weight'];?> kg</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <a class="delete-package" data-packageid="<?php echo $p['id'];?>" title="remove this package"><i class="fas fa-backspace fa-3x text-danger"></i></a>
                        </div>
                    </div>
                <?php ++$pc; endforeach;?>
            <?php endif;?>
            <?php if( $user_role == "admin" || $user_role == "super admin" ):?>
                <div class="row">
                    <div class="col-md-12">
                        <h3>Update Courier</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">&nbsp;</div>
                    <div class="col-md-4">
                        <p><button class="ship_quote btn btn-primary quote_button" data-orderid="<?php echo $order_id;?>" data-destination="<?php echo $address_string;?>">Get Shipping Prices</button> </p>
                    </div>
                </div>
                <div class="row">
                    <form id="order-courier-update" method="post" action="/form/procOrderCourierUpdate">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Courier</label>
                            <div class="col-md-4">
                                <select id="courier_id" name="courier_id" class="form-control selectpicker"><option value="0">-- Select One --</option><?php echo $this->controller->courier->getSelectCouriers(Form::value('courier_id'), false, true);?></select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="form-check">
                                <label class="form-check-label col-md-3" for="ignore_pc">Ignore Price Check</label>
                                <div class="col-md-4 checkbox checkbox-default">
                                    <input class="form-check-input styled" type="checkbox" id="ignore_pc" name="ignore_pc" />
                                    <label for="ignore_pc"></label>
                                    
                                </div>
                            </div>
                        </div>
                        <div id="local-details" style="display:<?php echo $local_display;?>">
                            <input type="hidden" name="local_display" id="local_display" value="1" <?php if(empty(Form::value('local_display'))) echo "disabled";?> />
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Courier Name</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control required" name="courier_name" id="courier_name" value="<?php echo Form::value('courier_name');?>" />
                                    <?php echo Form::displayError('courier_name');?>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                        <input type="hidden" name="truck_id" id="truck_id" value="<?php echo $truck_id;?>" />
                        <input type="hidden" name="local_id" id="local_id" value="<?php echo $local_id;?>" />
                        <input type="hidden" name="order_id" value="<?php echo $order_id;?>" />
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">&nbsp;</label>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Update Courier</button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endif;?>
        <?php else:?>
            <?php include(Config::get('VIEWS_PATH')."forms/addmisc.php");?>
            <div class="row">
                <div class="col-md-12">
                    <h3>Order Fulfillment</h3>
                </div>
            </div>
            <?php if($order['status_id'] == $this->controller->order->fulfilled_id):?>
                <div class="row">
                    <div class="col-md-12">
                        <h2>This order has already been fulfilled</h2>
                        <p>Fulfilled on <?php echo date("d/m/Y", $order['date_fulfilled']);?></p>
                        <p>Dispatched using <?php echo $this->controller->courier->getCourierNameForOrder($order['courier_id'], $order_id);?></p>
                    </div>
                </div>
            <?php elseif( $user_role == "admin" || $user_role == "super admin" ):?>
                <div class="row">
                    <?php if($order['courier_id'] == $this->controller->courier->eParcelId || $order['courier_id'] == $this->controller->courier->eParcelExpressId):?>
                        <label class="col-md-3 col-form-label">&nbsp;</label>
                        <div class="col-md-4">
                            <p><a class="btn btn-primary eparcel-label" data-orderid="<?php echo $order_id;?>">Print eParcel Label</a></p>
                        </div>
                    <?php elseif($order['courier_id'] == $this->controller->courier->huntersId || $order['courier_id'] == $this->controller->courier->huntersPluId|| $order['courier_id'] == $this->controller->courier->huntersPalId):?>
                        <label class="col-md-3 col-form-label">&nbsp;</label>
                        <div class="col-md-4">
                            <p><a class="btn btn-primary hunters-label" data-orderid="<?php echo $order_id;?>">Print Hunters Label</a></p>
                        </div>
                    <?php elseif($order['courier_id'] == $this->controller->courier->threePlTruckId):?>
                        <form id="our_truck">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>3PL Truck</h4>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Consignment ID</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control required" name="consignment_id" id="consignment_id" value="<?php echo Form::value('consignment_id');?>" />
                                    <?php echo Form::displayError('consignment_id');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Pallets</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control required digits" data-rule-min="1" name="truck_pallets" id="truck_pallets" value="<?php echo $truck_pallets;?>" />
                                    <?php echo Form::displayError('pallets');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Charge Amount</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                        <input type="text" class="form-control required number" data-rule-min="0" name="truck_charge" id="truck_charge" value="<?php echo $truck_charge;?>" />
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
                        </form>
                    <?php elseif($order['courier_id'] == $this->controller->courier->directFreightId):?>
                        <form id="direct_freight">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Direct Freight</h4>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Consignment ID</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control required" name="direct_consignment_id" id="direct_consignment_id" value="<?php echo Form::value('direct_consignment_id');?>" />
                                    <?php echo Form::displayError('direct_consignment_id');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Charge Amount</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                        <input type="text" class="form-control number" name="direct_charge" id="direct_charge" value="<?php echo $direct_charge;?>" />
                                    </div>
                                    <?php echo Form::displayError('direct_charge');?>
                                </div>
                            </div>
                        </form>
                    <?php elseif($order['courier_id'] == $this->controller->courier->localId):?>
                        <form id="local_courier">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Local Courier</h4>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Courier Name</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control required" name="courier_name" id="courier_name" value="<?php echo $courier_name;?>" />
                                    <?php echo Form::displayError('courier_name');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Consignment ID</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control required" name="consignment_id" id="consignment_id" value="<?php echo Form::value('consignment_id');?>" />
                                    <?php echo Form::displayError('consignment_id');?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Charge Amount</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                        <input type="text" class="form-control number" name="local_charge" id="local_charge" value="<?php echo $local_charge;?>" />
                                    </div>
                                    <?php echo Form::displayError('local_charge');?>
                                </div>
                            </div>
                        </form>
                    <?php endif;?>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label">&nbsp;</label>
                    <div class="col-md-4">
                        <p><button class="btn-md btn-danger btn" id="order_fulfill" data-orderid="<?php echo $order_id;?>" data-courierid="<?php echo $order['courier_id'];?>">Fulfill Order</button></p>
                    </div>
                </div>
            <?php endif;?>
        <?php endif;?>
    <?php endif;?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/courierids.php");?>
</div>