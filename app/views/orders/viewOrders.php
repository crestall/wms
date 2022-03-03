<?php
  $states = array(
    "VIC",
    "NSW",
    "TAS",
    "ACT",
    "QLD",
    "NT",
    "SA",
    "WA",
  );
  asort($states);
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
    <input type="hidden" id="fulfilled" value="<?php echo $fulfilled;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row view-orders-buttons" >
        <?php if($user_role == "admin" || $user_role == "super admin"):?>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <p><a class="btn btn-sm btn-block btn-outline-fsg export-csv"><i class="fas fa-file-csv"></i> Export Selected To CSV</a></p>
            </div>
        <?php endif;?>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
            <p><a class="btn btn-sm btn-block btn-outline-fsg slip-print"><i class="fas fa-file-alt"></i> Print Picking Slips For Selected</a></p>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
            <p><a class="btn btn-sm btn-block btn-outline-fsg print-invoices"><i class="fas fa-file-invoice"></i> Print Invoices For Selected</a> </p>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
            <p><a class="btn btn-sm btn-block btn-outline-fsg add-package"><i class="fas fa-box-open"></i> Add Package For Selected</a> </p>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
            <p><a class="btn btn-sm btn-block btn-outline-fsg select-courier"><i class="fas fa-truck"></i> Update Courier For Selected</a></p>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
            <p><a class="btn btn-sm btn-block btn-outline-fsg eparcel-label-print"><i class="fas fa-tags"></i> Print eParcel Labels For Selected</a></p>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
            <p><a class="btn btn-sm btn-block btn-outline-fsg directfreight-label-print"><i class="fas fa-tags"></i> Print Direct Freight Labels For Selected</a></p>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
            <p><a class="btn btn-sm btn-block btn-outline-success eparcel-fulfill"><i class="fas fa-clipboard-check"></i> Fulfill Selected eParcel Orders</a></p>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
            <p><a class="btn btn-sm btn-block btn-outline-success directfreight-fulfill"><i class="fas fa-clipboard-check"></i> Fulfill Selected Direct Freight Orders</a></p>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
            <p><a class="btn btn-sm btn-block btn-outline-warning consolidate-orders"><i class="fad fa-sign-in"></i> Consolidate Selected Orders</a></p>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
            <p><button class="btn btn-sm btn-block btn-outline-primary" id="runsheet"><i class="fas fa-truck"></i> Add Selected to Chosen Day's Runsheet</button></p>
        </div>
        <?php if($user_role == "admin" || $user_role == "super admin"):?>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <p><a class="btn btn-sm btn-block btn-outline-danger cancel-order"><i class="fas fa-ban"></i> Cancel Selected Orders</a></p>
            </div>
        <?php endif;?>
    </div>
    <div class="row">
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
            <div class="form-group">
                <label>Filter By Client</label>
                <select id="client_selector" class="form-control selectpicker" data-style="btn-outline-secondary"><option value="0">All Clients</option><?php echo $this->controller->client->getSelectClients($client_id);?></select>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
            <div class="form-group">
                <label>Filter By Courier</label>
                <select id="courier_selector" class="form-control selectpicker" data-style="btn-outline-secondary"><option value="-1">All Couriers</option><?php echo $this->controller->courier->getSelectCouriers($courier_id, true, false);?></select>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
            <div class="form-group">
                <label>Filter By State</label>
                <select id="state_selector" class="form-control selectpicker" data-style="btn-outline-secondary">
                    <option value="0">All States</option>
                    <?php
                    foreach($states as $s)
                    {
                        echo "<option";
                        if($s == $state)
                        {
                            echo " selected";
                        }
                        echo ">$s</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
            <div class="form-group">
                <label>Search</label>
                <input type="text" class="form-control" id="table_searcher" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <?php if(isset($_SESSION['feedback'])) :?>
               <div class='feedbackbox'><?php echo Session::getAndDestroy('feedback');?></div>
            <?php endif; ?>
            <?php if(isset($_SESSION['errorfeedback'])) :?>
               <div class='errorbox'><?php echo Session::getAndDestroy('errorfeedback');?></div>
            <?php endif; ?>
        </div>
        <div class="col-lg-12">
            <?php if(isset($_SESSION['courierfeedback'])) :?>
               <div class='feedbackbox'><?php echo Session::getAndDestroy('courierfeedback');?></div>
            <?php endif; ?>
            <?php if(isset($_SESSION['couriererrorfeedback'])) :?>
               <div class='errorbox'><?php echo Session::getAndDestroy('couriererrorfeedback');?></div>
            <?php endif; ?>
        </div>
    </div>
    <?php if(count($orders)):?>
        <div id="waiting" class="row">
            <div class="col-lg-12 text-center">
                <h2>Drawing Table..</h2>
                <p>May take a few moments</p>
                <img class='loading' src='/images/preloader.gif' alt='loading...' />
            </div>
        </div>
        <div class="row" id="table_holder" style="display:none">
            <div class="col-xl-12">
                <table class="table-striped table-hover" id="client_orders_table" style="width: 95%;margin: auto">
                    <thead>
            	    	<tr>
                            <th data-priority="10002"></th>
            	        	<th data-priority="1">Order No</th>
                            <th>Client Order<br/>Number</th>
            				<th data-priority="10001">Client</th>
            				<th data-priority="3">Deliver To</th>
            				<th>Items</th>
            				<th>Date<br/>Ordered</th>
            				<th>Slip<br/>Printed</th>
                            <th>Packages<br/>Entered</th>
                            <?php if($user_role == "admin" || $user_role == "super admin"):?>
            				    <th data-priority="1" nowrap>Courier<br /><select id="courier_all" class="selectpicker" data-style="btn-outline-secondary btn-sm" data-width="fit"><option value="-1">--Select One--</option><option value="0">Auto</option><?php echo $this->controller->courier->getSelectCouriers(false, false, false);?></select>&nbsp;<em><small>(all)</small></em></th>
                            <?php elseif($user_role == "warehouse"):?>
                                <th data-priority="1">Courier</th>
                            <?php endif;?>
                            <th data-priority="2"></th>
                            <th nowrap data-priority="1">
                                Select
                                <div class="checkbox checkbox-default">
                                    <input id="select_all" class="styled" type="checkbox">
                                    <label for="select_all"><em><small>(all)</small></em></label>
                                </div>
                            </th>
            			</tr>
            		</thead>
                    <tbody>
                        <?php $c = 0; foreach($orders as $co):
                            $add_to_runsheet = true;
                            ++$c;
                            if(!empty($co['company_name']))
                            {
                                $ship_to = $co['company_name']."<br>Attn:".$co['ship_to'];
                            }
                            else
                            {
                                $ship_to = $co['ship_to'];
                            }
            				$address = $this->controller->address->getAddressStringForOrder($co['id']);
            				$order_status = $this->controller->order->getStatusName($co['status_id']);
            				$slip_printed = ($co['slip_printed'] > 0)? "Yes": "No";
                            $link = ( $co['store_order'] == 1 )? "/orders/big-bottle-store-orders/order={$co['xero_invoiceno']}":"/orders/order-update/order={$co['id']}";
                            $comments = !empty($co['3pl_comments']);
                            $pick_notice = !empty($co['pick_notices']);
                            $pickup = ($co['pickup']) == 1;
                            $item_count = $this->controller->order->getItemCountForOrder($co['id']);
                            $ifo = $this->controller->order->getItemsForOrder($co['id']);
                            $client_name = $this->controller->client->getClientName($co['client_id']);
                            $can_adjust = $this->controller->client->canAdjustAllocations($co['client_id']);
                            $items = $this->controller->order->getItemsForOrder($co['id']);
            				$fulfill = true;
                            $errors = ( $co['errors'] == 1 ) ;
                            $row_class = "class='filterable'";
                            if($errors)
                            {
                                $row_class = "class='filterable order_error'";
                            }
                            elseif($pick_notice)
                            {
                                $row_class = "class='filterable notice'";
                            }
                            elseif( $comments )
                            {
                                $row_class = "class='filterable replacement'";
                            }
                            elseif($pickup)
                            {
                                $row_class = "class='filterable replacement'";
                            }
                            elseif($co['eparcel_express'] == 1)
                            {
                                $row_class = "class='filterable express'";
                            }
                            foreach($items as $item)
                            {
                                //check availability
            				   	$available = $this->controller->item->getAvailableStock($item['id'], $this->controller->order->fulfilled_id) + $item['qty'];
                                if($available < $item['qty'])
                                    $fulfill = false;
                            }
                            $invoice = "";
                            if(!empty($co['uploaded_file']))
                            {
                                $invoice = "<a href='/client_uploads/{$co['client_id']}/{$co['uploaded_file']}' target='_blank'>Print Invoice</a>";
                            }
                            $ps = "";
                            if($co['client_id'] == 63)
                            {
                                $ps = "<a href='/pdf/packing-slip/order={$co['id']}' target='_blank'>Print Packing Slip</a>";
                            }
                            $address_string = $co['address'];
                            if(!empty($co['address_2']))
                                $address_string .= " ".$co['address_2'];
                            $address_string .= " ".$co['suburb'];
                            $address_string .= " ".$co['state'];
                            $address_string .= " ".$co['postcode'];
                            $address_string .= " ".$co['country'];
                            //$pe = ($this->controller->order->hasAssociatedPackage($co['id']))? "Yes":"No";
                            $package_count = $this->controller->order->countAssociatedPackage($co['id']);
                            /*
                            */
                            ?>
            	        	<tr id="tr_<?php echo $co['id'];?>" <?php echo $row_class;?> >
                                <td class="number" data-label="Count"><?php echo $c;?></td>
            	            	<td class="filterable number" data-label="Order Number">
                                    <a href="<?php echo $link;?>"><?php echo str_pad($co['order_number'],8,'0',STR_PAD_LEFT);?></a>
                                </td>
                                <td class="filterable number" data-label="Client Order Number"><?php echo $co['client_order_id'];?></td>
            					<td data-label="Client Name"><?php echo $client_name;?></td>
            	                <td class="filterable" data-label="Ship To">
            	                    <p class='font-weight-bold'><?php echo $ship_to;?></p>
                                    <p><?php echo $address;?></p></td>
                                <td data-label="Items">
                                    <div class="item_list border-bottom border-secondary border-bottom-dashed mb-3 ">
                                        <?php foreach($ifo as $i):?>
                                            <p><span class="iname"><?php echo $i['name'];?>:</span><span class="icount"><?php echo $i['qty'];?></span><span class="ilocation">(<?php echo $i['location'];?>)</span></p>
                                        <?php endforeach;?>
                                    </div>
                                    <div class="item_total text-right">
                                        Total Items: <?php echo $item_count;?>
                                    </div>
                                </td>
            					<td data-label="Date Ordered" nowrap><?php echo date('d-m-Y', $co['date_ordered']);?></td>
            					<td data-label="Slip printed"><?php echo $slip_printed; ?></td>
                                <td data-label="Package Entered"
                                    <?php if($package_count > 0):?>
                                         class="number"><?php echo $package_count;?>
                                    <?php elseif($item_count == 1 && $ifo[0]['boxed_item'] == 1):?>
                                        ><span class="text-success">Auto Packaging Available</span>
                                    <?php else:?>
                                         class="number"><?php echo $package_count;?>
                                    <?php endif;?>
                                </td>
                                <?php if($user_role == "warehouse" || $user_role == "admin" || $user_role == "super admin"):?>
                					<td data-label="Courier" nowrap>
                					    <?php if($pickup):?>
                                            <?php if($can_adjust):?>
                                                <p><button class="btn btn-sm btn-outline-primary adjust_allocation" data-orderid="<?php echo $co['id'];?>">Adjust Allocations</button></p>
                                            <?php endif;?>
                                            <p><button class="btn btn-sm btn-outline-fsg notify_customer" data-orderid="<?php echo $co['id'];?>">Notify Customer</button></p>
                                        <?php else:?>
                    					    <p><select name="courier" class="selectpicker courier" data-style="btn-outline-secondary btn-sm" data-width="fit" id="courier_<?php echo $co['id'];?>" <?php if($co['courier_id'] > 0 || !$fulfill) echo "disabled";?>><option value="-1">--Select One--</option><option value="0">Auto</option><?php echo $this->controller->courier->getSelectCouriers($co['courier_id'], false, false);?></select></p>
                                            <p><button class="ship_quote btn-sm btn btn-outline-secondary quote_button" data-destination="<?php echo $address_string;?>" data-orderid="<?php echo $co['id'];?>">Get Shipping Prices</button></p>
                                            <?php if($can_adjust):?>
                                                <p><button class="btn btn-sm btn-outline-primary adjust_allocation" data-orderid="<?php echo $co['id'];?>">Adjust Allocations</button></p>
                                            <?php endif;?>
                                            <?php if( ($user_role == "admin" || $user_role == "super admin") && $co['courier_id'] > 0): ?>
                                                <p><a class="btn btn-outline-danger remove_courier" data-orderid="<?php echo $co['id'];?>">Remove Courier</a></p>
                                            <?php endif;?>
                                        <?php endif;?>
                                    </td>
                                <?php endif;?>
                                <td><?php echo $invoice; ?><br/><?php echo $ps; ?></td>
            					<td data-label="Select" class="chkbox">
                                    <div class="checkbox checkbox-default">
                                        <input <?php //if($errors) echo "disabled";?> type="checkbox" class="select styled" data-orderid='<?php echo $co['id'];?>' name="select_<?php echo $co['id'];?>" id="select_<?php echo $co['id'];?>" data-clientid="<?php echo $co['client_id'];?>" />
                                        <label for="select_<?php echo $co['id'];?>"></label>
                                    </div>
                                </td>
            				</tr>
                            <?php if($errors):?>
                                <tr class="table-warning">
                                    <td class="d-none"><?php echo $c;?></td>
                                    <td class="d-none"><?php echo $co['order_number'];?></td>
                                    <td class="d-none"><?php echo $co['client_order_id'];?></td>
                                    <td class="d-none"></td>
                                    <td class="d-none"><p><?php echo $ship_to;?></p><p><?php echo $address;?></p></td>
                                    <td class="d-none">
                                        <div class="item_list border-bottom border-secondary border-bottom-dashed mb-3 ">
                                            <?php foreach($ifo as $i):?>
                                                <p><span class="iname"><?php echo $i['name'];?>:</span><span class="icount"><?php echo $i['qty'];?></span><span class="ilocation">(<?php echo $i['location'];?>)</span></p>
                                            <?php endforeach;?>
                                        </div>
                                        <div class="item_total text-right">
                                            Total Items: <?php echo $item_count;?>
                                        </div>
                                    </td>
                                    <td class="d-none"><?php echo date('d-m-Y', $co['date_ordered']);?></td>
                					<td class="d-none"><?php echo $slip_printed; ?></td>
                                    <td class="d-none"><?php echo $package_count;?></td>
                                    <td colspan="12">
                                        <?php echo $co['error_string'];?>
                                        <p><a class="btn btn-outline-fsg" href="/orders/address-update/order=<?php echo $co['id'];?>">Fix this Address</a></p>
                                    </td>
                                    <?php for($i=1; $i<3; ++$i):?>
                                        <td class="d-none"></td>
                                    <?php endfor;?>
                                </tr>
                            <?php endif;?>
                            <?php if($pickup):?>
                                <tr class="table-info">
                                    <td class="d-none"><?php echo $c;?></td>
                                    <td class="d-none"><?php echo $co['order_number'];?></td>
                                    <td class="d-none"><?php echo $co['client_order_id'];?></td>
                                    <td class="d-none"></td>
                                    <td class="d-none"><p><?php echo $ship_to;?></p><p><?php echo $address;?></p></td>
                                    <td class="d-none">
                                        <div class="item_list border-bottom border-secondary border-bottom-dashed mb-3 ">
                                            <?php foreach($ifo as $i):?>
                                                <p><span class="iname"><?php echo $i['name'];?>:</span><span class="icount"><?php echo $i['qty'];?></span><span class="ilocation">(<?php echo $i['location'];?>)</span></p>
                                            <?php endforeach;?>
                                        </div>
                                        <div class="item_total text-right">
                                            Total Items: <?php echo $item_count;?>
                                        </div>
                                    </td>
                                    <td class="d-none"><?php echo date('d-m-Y', $co['date_ordered']);?></td>
                					<td class="d-none"><?php echo $slip_printed; ?></td>
                                    <td class="d-none"><?php echo $package_count;?></td>
                                    <td colspan="12">Customer Pickup</td>
                                    <?php for($i=1; $i<3; ++$i):?>
                                        <td class="d-none"></td>
                                    <?php endfor;?>
                                </tr>
                            <?php endif;?>
                            <?php if($comments):?>
                                <tr class="table-info">
                                    <td class="d-none"><?php echo $c;?></td>
                                    <td class="d-none"><?php echo $co['order_number'];?></td>
                                    <td class="d-none"><?php echo $co['client_order_id'];?></td>
                                    <td class="d-none"></td>
                                    <td class="d-none"><p><?php echo $ship_to;?></p><p><?php echo $address;?></p></td>
                                    <td class="d-none">
                                        <div class="item_list border-bottom border-secondary border-bottom-dashed mb-3 ">
                                            <?php foreach($ifo as $i):?>
                                                <p><span class="iname"><?php echo $i['name'];?>:</span><span class="icount"><?php echo $i['qty'];?></span><span class="ilocation">(<?php echo $i['location'];?>)</span></p>
                                            <?php endforeach;?>
                                        </div>
                                        <div class="item_total text-right">
                                            Total Items: <?php echo $item_count;?>
                                        </div>
                                    </td>
                                    <td class="d-none"><?php echo date('d-m-Y', $co['date_ordered']);?></td>
                					<td class="d-none"><?php echo $slip_printed; ?></td>
                                    <td class="d-none"><?php echo $package_count;?></td>
                                    <td colspan="12"><?php echo $co['3pl_comments'];?></td>
                                    <?php for($i=1; $i<3; ++$i):?>
                                        <td class="d-none"></td>
                                    <?php endfor;?>
                                </tr>
                            <?php endif;?>
                            <?php if($pick_notice):?>
                                <tr class="table-info">
                                    <td class="filterable d-none"><?php echo $c;?></td>
                                    <td class="filterable d-none"><?php echo $co['order_number'];?></td>
                                    <td class="filterable d-none"><?php echo $co['client_order_id'];?></td>
                                    <td class="d-none"></td>
                                    <td class="d-none"><p><?php echo $ship_to;?></p><p><?php echo $address;?></p></td>
                                    <td class="d-none">
                                        <div class="item_list border-bottom border-secondary border-bottom-dashed mb-3 ">
                                            <?php foreach($ifo as $i):?>
                                                <p><span class="iname"><?php echo $i['name'];?>:</span><span class="icount"><?php echo $i['qty'];?></span><span class="ilocation">(<?php echo $i['location'];?>)</span></p>
                                            <?php endforeach;?>
                                        </div>
                                        <div class="item_total text-right">
                                            Total Items: <?php echo $item_count;?>
                                        </div>
                                    </td>
                                    <td class="d-none"><?php echo date('d-m-Y', $co['date_ordered']);?></td>
                					<td class="d-none"><?php echo $slip_printed; ?></td>
                                    <td class="d-none"><?php echo $package_count;?></td>
                                    <td colspan="12"><?php echo $co['pick_notices'];?></td>
                                    <?php for($i=1; $i<3; ++$i):?>
                                        <td class="d-none"></td>
                                    <?php endfor;?>
                                </tr>
                            <?php endif;?>
            			<?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else:?>
        <div class="row">
            <div class="col-lg-12">
                <div class="errorbox">
                    <h2><i class="fas fa-exclamation-triangle"></i> No Orders Listed</h2>
                    <p>Either all orders are fulfilled or you need to remove some filters</p>
                </div>
            </div>
        </div>
    <?php endif;?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/courierids.php");?>
    </div>
</div>
