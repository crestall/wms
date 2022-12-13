<?php
$truck_display = (empty(Form::value('truck_display')))? 'none' : "block";
$local_display = (empty(Form::value('local_display')))? 'none' : "block";
$truck_pallets = (empty(Form::value('truck_pallets')))? 1:Form::value('truck_pallets');
$pallets = (empty(Form::value('pallets')))? 1:Form::value('pallets');
$p_count = (empty(Form::value('count')))? 1:Form::value('count');
$can_adjust = $this->controller->client->canAdjustAllocations($order['client_id']);
if(!$error)
{
    $truck_charge = (empty(Form::value('truck_charge')))? $order['postage_charge']:Form::value('truck_charge');
    $courier_name = (empty(Form::value('courier_name')))? $order['courier_name']:Form::value('courier_name');
    $local_charge = (empty(Form::value('local_charge')))? $order['postage_charge']:Form::value('local_charge');
    $direct_charge = (empty(Form::value('direct_charge')))? $order['postage_charge']:Form::value('direct_charge');
    if(!empty(Form::value('inc_gst')))
    {
        $pgst_check = "checked";
    }
    elseif( strtoupper($order['country']) == "AU" )
    {
        $pgst_check = "checked";
    }
    else
    {
        $pgst_check = "";
    }
}

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if($order_id == 0):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/no_order_id.php");?>
        <?php elseif(empty($order)):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/no_order_found.php");?>
        <?php else:?>
            <div class="row">
                <div class="col">
                    <p><a class="btn btn-outline-secondary" href="/<?php echo $cont;?>/view-orders/client=<?php echo $order['client_id'];?>">View Orders For Client</a></p>
                </div>
                <div class="col">
                    <p><a class="btn btn-outline-secondary" href="/<?php echo $cont;?>/order-detail/order=<?php echo $order_id;?>">View and Print Details</a></p>
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
                    <div class="card h-100 border-secondary order-card">
                        <div class="card-header bg-secondary text-white">
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
                    <div class="card border-secondary h-100 order-card">
                        <div class="card-header bg-secondary text-white">
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
                                    <div class="col-7">
                                        <?php if( file_exists(UPLOADS.$order['client_id']."/".$order['uploaded_file']) ):?>
                                            <a href='/client_uploads/<?php echo $order['client_id']."/".$order['uploaded_file'];?>' target='_blank'>Print Invoice</a>
                                        <?php else:?>
                                            <?php echo $order['uploaded_file'];?>
                                        <?php endif;?>
                                    </div>
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
                    <div class="card border-secondary h-100 order-card">
                        <div class="card-header bg-secondary text-white">
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
                            <?php if($order['courier_id'] == 0 && ($user_role == "admin" || $user_role == "super admin" || $user_role == "production_admin") && $can_adjust):?>
                                <a class="btn btn-outline-secondary" href="/orders/items-update/order=<?php echo $order_id;?>">Update Order Items</a>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
                <a name="package"></a>
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
                <a name="courier"></a>
                <?php if($order['courier_id'] == 0):    //Courier Selection?>
                    <div class="col-sm-12 col-md-6 mb-3">
                        <div class="card border-secondary h-100 order-card">
                            <div class="card-header bg-secondary text-white">
                                Update Courier
                            </div>
                            <div class="card-body">
                                <?php if(isset($_SESSION['showcourierfeedback']) && $_SESSION['showcourierfeedback']) :
                                    Session::destroy('showcourierfeedback')?>
                                   <div class='feedbackbox'><?php echo Session::getAndDestroy('courierfeedback');?></div>
                                <?php endif; ?>
                                <?php if(isset($_SESSION['showcouriererrorfeedback']) && $_SESSION['showcouriererrorfeedback']) :
                                    Session::destroy('showcouriererrorfeedback')?>
                                   <div class='errorbox'><?php echo Session::getAndDestroy('couriererrorfeedback');?></div>
                                <?php endif; ?>
                                <form id="order-courier-update" method="post" action="/form/procOrderCourierUpdate">
                                    <div class="form-group row">
                                        <label class="col"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Courier</label>
                                        <div class="col">
                                            <select id="courier_id" name="courier_id" class="form-control selectpicker" data-style="btn-outline-secondary" required><option value="-1">-- Select One --</option><option value="0">Auto</option><?php echo $this->controller->courier->getSelectCouriers(Form::value('courier_id'), false, true);?></select>
                                        </div>
                                    </div>
                                    <!--div class="form-group row custom-control custom-checkbox custom-control-right">
                                        <input class="custom-control-input" type="checkbox" id="ignore_pc" name="ignore_pc" />
                                        <label class="custom-control-label col-md-6" for="ignore_pc">Ignore Price Check</label>
                                    </div-->
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
                                        <button class="ship_quote btn btn-outline-info quote_button" data-orderid="<?php echo $order_id;?>" data-destination="<?php echo $address_string;?>" <?php if($order['pickup'] == 1) echo "disabled";?>>Get Shipping Prices</button>
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
                        <div class="card border-secondary h-100 order-card">
                            <div class="card-header bg-secondary text-white">
                                Add Miscellaneous Items
                            </div>
                            <div class="card-body">
                                <p class="inst text-danger">
                                    All Charges Are GST Exclusive
                                </p>
                                <?php include(Config::get('VIEWS_PATH')."forms/addmisc.php");?>
                            </div>
                            <div class="card-footer text-right">
                                <button id="add_misc" class="btn btn-outline-secondary">Add/Update These</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 mb-3">
                        <div class="card border-secondary h-100 order-card">
                            <div class="card-header bg-secondary text-white">
                                Order Fulfillment
                            </div>
                            <div class="card-body">
                                <?php if($order['status_id'] == $this->controller->order->fulfilled_id):?>
                                    <div class="feedbackbox">
                                        <h5 class="card-subtitle mb-3"><i class="fad fa-box-check"></i> This order has been fulfilled</h5>
                                        <div class="ml-4">
                                            <p>Fulfilled on <?php echo date("d/m/Y", $order['date_fulfilled']);?></p>
                                            <p>Dispatched using <span class="font-weight-bold"><?php echo $this->controller->courier->getCourierNameForOrder($order['courier_id'], $order_id);?></span></p>
                                            <p>You may be able to track its delivery status. <a href="/orders/order-tracking/order=<?php echo $order['id'];?>" class="btn btn-sm btn-outline-fsg">View Tracking</a></p>
                                            <p><a href="<?php echo $order['label_url'];?>" class="btn btn-outline-secondary" target="_blank">Reprint Direct Freight Label</a></p>
                                        </div>
                                    </div>
                                    </div><!--End Card Body-->
                                    <div class="card-footer"></div>
                                <?php else:?>
                                    <div class="row">
                                        <?php if($order['courier_id'] == $this->controller->courier->eParcelId || $order['courier_id'] == $this->controller->courier->eParcelExpressId):?>
                                            <label class="col">&nbsp;</label>
                                            <div class="col">
                                                <p><a class="btn btn-outline-secondary eparcel-label" data-orderid="<?php echo $order_id;?>">Print eParcel Label</a></p>
                                            </div>
                                        <?php elseif($order['courier_id'] == $this->controller->courier->fsgId):?>
                                            <form id="our_truck">
                                                <h5 class="card-subtitle">FSG Delivery</h5>
                                                <p class="inst text-danger">
                                                    All Charges Are GST Exclusive
                                                </p>
                                                <div class="form-group row">
                                                    <label class="col"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Consignment ID</label>
                                                    <div class="col">
                                                        <input type="text" class="form-control required" name="consignment_id" id="consignment_id" value="<?php echo Form::value('consignment_id');?>" />
                                                        <?php echo Form::displayError('consignment_id');?>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col">Charge Amount</label>
                                                    <div class="col">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">$</span>
                                                            </div>
                                                            <input type="text" class="form-control number" name="truck_charge" id="truck_charge" value="<?php echo $truck_charge;?>" />
                                                        </div>
                                                        <?php echo Form::displayError('truck_charge');?>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col">&nbsp;</label>
                                                    <div class="col">
                                                        <a href="/orders/create-delivery-docket/order=<?php echo $order_id;?>" class="btn-outline-fsg btn" id="delivery_docket" data-orderid="<?php echo $order_id;?>" data-courierid="<?php echo $order['courier_id'];?>">Print Delivery Docket</a>
                                                    </div>
                                                </div>
                                            </form>
                                        <?php elseif($order['courier_id'] == $this->controller->courier->directFreightId):?>
                                            <h5 class="card-title">Direct Freight</h5>
                                            <div class="container-fluid">
                                                <div class="row mb-4">
                                                    Already assigned to Direct Freight with Consignment ID <?php echo $order['consignment_id'];?>
                                                </div>
                                                <div class="row">
                                                    <label class="col">&nbsp;</label>
                                                    <div class="col">
                                                        <!--p><a class="btn btn-outline-secondary direct-freight-label" data-orderid="<?php echo $order_id;?>">Print Direct Freight Label</a></p-->
                                                        <p><a href="<?php echo $order['label_url'];?>" class="btn btn-outline-secondary" target="_blank">Download Direct Freight Label</a></p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php elseif($order['courier_id'] == $this->controller->courier->localId):?>
                                            <form id="local_courier">
                                                <h5 class="card-subtitle">Local Courier</h5>
                                                <p class="inst text-danger">
                                                    All Charges Are GST Exclusive
                                                </p>
                                                <div class="form-group row">
                                                    <label class="col"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Courier Name</label>
                                                    <div class="col">
                                                        <input type="text" class="form-control required" name="courier_name" id="courier_name" value="<?php echo $courier_name;?>" />
                                                        <?php echo Form::displayError('courier_name');?>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Consignment ID</label>
                                                    <div class="col">
                                                        <input type="text" class="form-control required" name="consignment_id" id="consignment_id" value="<?php echo Form::value('consignment_id');?>" />
                                                        <?php echo Form::displayError('consignment_id');?>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col">Postage Amount</label>
                                                    <div class="col">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">$</span>
                                                            </div>
                                                            <input type="text" class="form-control number" name="local_charge" id="local_charge" value="<?php echo $local_charge;?>" />
                                                        </div>
                                                        <?php echo Form::displayError('local_charge');?>
                                                    </div>
                                                </div>
                                                <div class="form-group row custom-control custom-checkbox custom-control-right">
                                                    <input class="custom-control-input col" type="checkbox" id="inc_pgst" name="inc_pgst" <?php echo $pgst_check;?> />
                                                    <label class="custom-control-label col" for="inc_pgst">Add GST To Postage<br><span class="inst">Uncheck for international orders</span></label>
                                                </div>
                                            </form>
                                        <?php endif;?>
                                    </div>
                                    </div><!--End Card Body-->
                                    <div class="card-footer text-right">
                                        <button class="btn-outline-danger btn" id="order_fulfill" data-orderid="<?php echo $order_id;?>" data-courierid="<?php echo $order['courier_id'];?>">Fulfill Order</button>
                                    </div>
                                <?php endif;?>
                        </div>
                    </div>
                <?php endif;?>
            </div>
        <?php endif;?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/courierids.php");?>
    </div>
</div>