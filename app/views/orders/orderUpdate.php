<?php
$truck_display = (empty(Form::value('truck_display')))? 'none' : "block";
$local_display = (empty(Form::value('local_display')))? 'none' : "block";
$truck_pallets = (empty(Form::value('truck_pallets')))? 1:Form::value('truck_pallets');
$pallets = (empty(Form::value('pallets')))? 1:Form::value('pallets');
$p_count = (empty(Form::value('count')))? 1:Form::value('count');
if(!$error)
{
    $truck_charge = (empty(Form::value('truck_charge')))? $order['total_cost']:Form::value('truck_charge');
    $courier_name = (empty(Form::value('courier_name')))? $order['courier_name']:Form::value('courier_name');
    $local_charge = (empty(Form::value('local_charge')))? $order['total_cost']:Form::value('local_charge');
    $direct_charge = (empty(Form::value('direct_charge')))? $order['total_cost']:Form::value('direct_charge');
}

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if($error):?>
            <div class="row">
                <div class="col">
                    <div class="errorbox">
                        <div class="row">
                            <div class="col-4 text-right">
                                <i class="fad fa-exclamation-triangle fa-6x"></i>
                            </div>
                            <div class="col-8">
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
                            <div class="col-4 text-right">
                                <i class="fad fa-exclamation-triangle fa-6x"></i>
                            </div>
                            <div class="col-8">
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
                <div class="col">
                    <p><a class="btn btn-outline-secondary" href="/orders/view-orders/client=<?php echo $order['client_id'];?>">View Orders For Client</a></p>
                </div>
                <div class="col">
                    <p><a class="btn btn-outline-secondary" href="/orders/order-detail/order=<?php echo $order_id;?>">View and Print Details</a></p>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <h2>Updating Order Number <?php echo $order['order_number'];?></h2>
                </div>
            </div>
            <div class="row">
                <div class="col">
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
                <div class="col-sm-12 col-md-6 mb-3">
                    <div class="card h-100 order-card">
                        <div class="card-header">
                            Delivery Details
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <label class="col-5">Deliver To:</label>
                                <div class="col-7"><?php echo $order['ship_to'];?></div>
                            </div>
                            <div class="row">
                                <label class="col-5">Address:</label>
                                <div class="col-7"><?php echo $order['address'];?></div>
                            </div>
                            <?php if(!empty($order['address_2'])):?>
                                <div class="row">
                                    <label class="col-5">&nbsp;</label>
                                    <div class="col-7"><?php echo $order['address_2'];?></div>
                                </div>
                            <?php endif;?>
                            <div class="row">
                                <label class="col-5">&nbsp;</label>
                                <div class="col-7"><?php echo $order['suburb'];?></div>
                            </div>
                            <div class="row">
                                <label class="col-5">&nbsp;</label>
                                <div class="col-7"><?php echo $order['state'];?></div>
                            </div>
                            <div class="row">
                                <label class="col-5">&nbsp;</label>
                                <div class="col-7"><?php echo $order['country'];?></div>
                            </div>
                            <div class="row">
                                <label class="col-5">&nbsp;</label>
                                <div class="col-7"><?php echo $order['postcode'];?></div>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <?php if($order['courier_id'] == 0 && ($user_role == "admin" || $user_role == "super admin")):?>
                                <a class="btn btn-outline-secondary" href="/orders/address-update/order=<?php echo $order_id;?>">Update Address Details</a>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 mb-3">
                    <div class="card h-100 order-card">
                        <div class="card-header">
                            Order Details
                        </div>
                        <div class="card-body">
                            <div class="row">
                                    <label class="col-5">Client Order Number</label>
                                    <div class="col-7"><?php echo $order['client_order_id'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">Deliver To</label>
                                    <div class="col-7"><?php echo $order['ship_to'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">Company</label>
                                    <div class="col-7"><?php echo $order['company_name'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">Contact Phone</label>
                                    <div class="col-7"><?php echo $order['contact_phone'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">Tracking Email</label>
                                    <div class="col-7"><?php echo $order['tracking_email'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">Delivery Instructions</label>
                                    <div class="col-7"><?php echo $order['instructions'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">Use Express</label>
                                    <div class="col-7"><?php if($order['eparcel_express'] > 0) echo "Yes"; else echo "No";?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">Signature Required</label>
                                    <div class="col-7"><?php if($order['signature_req'] > 0) echo "Yes"; else echo "No";?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">Client Invoice</label>
                                    <div class="col-7"><?php echo $order['uploaded_file'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">Picking Instructions</label>
                                    <div class="col-7"><?php echo $order['3pl_comments'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">Entered By</label>
                                    <div class="col-7"><?php echo $entered_by;?></div>
                                </div>
                        </div>
                        <div class="card-footer text-right">
                            <?php if($order['courier_id'] == 0 && ($user_role == "admin" || $user_role == "super admin")):?>
                                <a class="btn btn-outline-secondary" href="/orders/order-edit/order=<?php echo $order_id;?>">Update Order Details</a>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 mb-3">
                    <div class="card h-100 order-card">
                        <div class="card-header">
                            Order Items
                        </div>
                        <div class="card-body">
                            <?php foreach($order_items as $oi):?>
                                <div class="row">
                                    <label class="col-9"><?php echo $oi['name'];?></label>
                                    <div class="col-3"><?php echo $oi['qty'];?></div>
                                </div>
                            <?php endforeach;?>
                        </div>
                        <div class="card-footer text-right">
                            <?php if($order['courier_id'] == 0 && ($user_role == "admin" || $user_role == "super admin")):?>
                                <a class="btn btn-outline-secondary" href="/orders/items-update/order=<?php echo $order_id;?>">Update Order Items</a>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
                <a name="package"></a>
                <div class="col-sm-12 col-md-6 mb-3">
                    <div class="card h-100 order-card">
                        <div class="card-header">
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
                                            <h6 class="card-subtitle mb-3"><?php echo $p['count'];?> <?php echo ($p['pallet'] > 0)? "Pallet{$s}":"Package{$s}";?></h6>
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
                        <div class="card-footer text-right">
                            <?php if($order['courier_id'] == 0):?>
                                <button id="add_package" class="btn btn-outline-secondary" data-orderid="<?php echo $order_id;?>">Add Package/Pallet</button>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
                <?php if($order['courier_id'] == 0):    //Courier Selection?>
                    <div class="col-sm-12 col-md-6 mb-3">
                        <div class="card h-100 order-card">
                            <div class="card-header">
                                Update Courier
                            </div>
                            <div class="card-body">
                                <form id="order-courier-update" method="post" action="/form/procOrderCourierUpdate">
                                    <div class="form-group row">
                                        <label class="col"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Courier</label>
                                        <div class="col">
                                            <select id="courier_id" name="courier_id" class="form-control selectpicker" data-style="btn-outline-secondary" required><option value="0">-- Select One --</option><?php echo $this->controller->courier->getSelectCouriers(Form::value('courier_id'), false, true);?></select>
                                        </div>
                                    </div>
                                    <div class="form-group row custom-control custom-checkbox custom-control-right">
                                        <input class="custom-control-input" type="checkbox" id="ignore_pc" name="ignore_pc" />
                                        <label class="custom-control-label col-md-6" for="ignore_pc">Ignore Price Check</label>
                                    </div>
                                    <div id="local-details" style="display:<?php echo $local_display;?>">
                                        <input type="hidden" name="local_display" id="local_display" value="1" <?php if(empty(Form::value('local_display'))) echo "disabled";?> />
                                        <div class="form-group row">
                                            <label class="col"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Courier Name</label>
                                            <div class="col">
                                                <input type="text" class="form-control required" name="courier_name" id="courier_name" value="<?php echo Form::value('courier_name');?>" />
                                                <?php echo Form::displayError('courier_name');?>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                                    <input type="hidden" name="truck_id" id="truck_id" value="<?php echo $truck_id;?>" />
                                    <input type="hidden" name="local_id" id="local_id" value="<?php echo $local_id;?>" />
                                    <input type="hidden" name="order_id" value="<?php echo $order_id;?>" />
                                </form>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-6">
                                        <button class="ship_quote btn btn-outline-info quote_button" data-orderid="<?php echo $order_id;?>" data-destination="<?php echo $address_string;?>">Get Shipping Prices</button>
                                    </div>
                                    <div class="col-6 text-right">
                                        <button id="update_courier" class="btn btn-outline-secondary">Update Courier</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else:                             //Order Fulfillment?>
                    <a name="misc"></a>
                    <div class="col-sm-12 col-md-6 mb-3">
                        <div class="card h-100 order-card">
                            <div class="card-header">
                                Add Miscellaneous Items
                            </div>
                            <div class="card-body">
                                <?php include(Config::get('VIEWS_PATH')."forms/addmisc.php");?>
                            </div>
                            <div class="card-footer text-right">
                                <button id="add_misc" class="btn btn-outline-secondary">Add/Update These</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 mb-3">
                        <div class="card h-100 order-card">
                            <div class="card-header">
                                Order Fulfillment
                            </div>
                            <div class="card-body">
                                <?php if($order['status_id'] == $this->controller->order->fulfilled_id):?>
                                    <h6 class="card-subtitle">This order has already been fulfilled</h6>
                                    <p>Fulfilled on <?php echo date("d/m/Y", $order['date_fulfilled']);?></p>
                                    <p>Dispatched using <?php echo $this->controller->courier->getCourierNameForOrder($order['courier_id'], $order_id);?></p>
                                    </div><!--End Card Body-->
                                    <div class="card-footer"></div>
                                <?php else:?>
                                    </div><!--End Card Body-->
                                    <div class="card-footer text-right">
                                        <button class="btn-outline-danger btn" id="order_fulfill" data-orderid="<?php echo $order_id;?>" data-courierid="<?php echo $order['courier_id'];?>">Fulfill Order</button>
                                    </div>
                                <?php endif;?>

                        </div>
                    </div>
                <?php endif;?>
            </div>





            <?php if($order['courier_id'] == 0):?>

            <?php else:?>

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
                        <?php elseif($order['courier_id'] == $this->controller->courier->fsgId):?>
                            <form id="our_truck">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>FSG Deliveries</h4>
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
                        <?php elseif($order['courier_id'] == $this->controller->courier->bayswaterEparcelId):?>
                            <form id="bayswater_eparcel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Bayswater Eparcel</h4>
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
</div>