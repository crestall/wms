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
    <input type="hidden" id="fulfilled" value="<?php echo $fulfilled;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-3">
            <div class="form-group">
                <label>Filter By Client</label>
                <select id="client_selector" class="form-control selectpicker"><option value="0">All Clients</option><?php echo $this->controller->client->getSelectClients($client_id);?></select>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label>Filter By Courier</label>
                <select id="courier_selector" class="form-control selectpicker"><option value="-1">All Couriers</option><?php echo $this->controller->courier->getSelectCouriers($courier_id, true, false);?></select>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label>Search</label>
                <input type="text" class="form-control" id="table_searcher" />
            </div>
        </div>
    </div>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/between_dates.php");?> 
    <div class="row">
        <div class="col-lg-12">
            <?php if(isset($_SESSION['feedback'])) :?>
               <div class='feedbackbox'><?php echo Session::getAndDestroy('feedback');?></div>
            <?php endif; ?>
            <?php if(isset($_SESSION['errorfeedback'])) :?>
               <div class='errorbox'><?php echo Session::getAndDestroy('errorfeedback');?></div>
            <?php endif; ?>
        </div>
    </div>
    <?php if(count($orders)):?>
    <div class="row">
        <div class="col-lg-12">
            <table width="100%" class="table-striped table-hover" id="client_orders_table" style="width:100%">
                <thead>
        	    	<tr>
                        <th></th>
        	        	<th>Order No</th>
                        <th>Client Order Number</th>
        				<th>Client</th>
        				<th>Deliver To</th>
        				<th>Delivery Address</th>
        				<th>Items</th>
        				<th>Date Ordered</th>
        				<!--th>Status</th-->
        				<th>Slip Printed</th>
                        <th>
                            Ignore Price Check
                            <div class="checkbox checkbox-default">
                                <input id="select_all_np" class="styled" type="checkbox">
                                <label for="select_all_np"><em><small>(all)</small></em></label>
                            </div>
                        </th>
                        <?php if($user_role == "admin" || $user_role == "super admin"):?>
        				    <th nowrap>Courier<br /><select id="courier_all" class="selectpicker"><option value="-1">--Select One--</option><option value="0">Auto</option><?php echo $this->controller->courier->getSelectCouriers(false, false, false);?></select>&nbsp;<em><small>(all)</small></em></th>
                        <?php elseif($user_role == "warehouse"):?>
                            <th>Courier</th>
                        <?php endif;?>
                        <th></th>
                        <th nowrap>
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
                        ++$c;

                        if(empty($co['ship_to']))
                        {
                            //$customer = $db->queryRow("SELECT * FROM customers WHERE id = {$co['customer_id']}");
                            //$ship_to = ucwords($customer['name']);
                            $ship_to = "";
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
                        $item_count = $this->controller->order->getItemCountForOrder($co['id']);
                        $client_name = $this->controller->client->getClientName($co['client_id']);
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
                        /*
                        */
                        ?>
        	        	<tr <?php echo $row_class;?> >
                            <td class="number" data-label="Count"><?php echo $c;?></td>
        	            	<td class="filterable number" data-label="Order Number">
                                <a href="<?php echo $link;?>"><?php echo str_pad($co['order_number'],8,'0',STR_PAD_LEFT);?></a> 
                            </td>
                            <td class="filterable number" data-label="Client Order Number"><?php echo $co['client_order_id'];?></td>
        					<td data-label="Client Name"><?php echo $client_name;?></td>
        	                <td class="filterable" data-label="Ship To"><?php echo $ship_to;?></td>
        					<td data-label="Delivery Address" class="filterable"><?php echo $address;?></td>
        					<td data-label="Items" class="number"><?php echo $item_count;?></td>
        					<td data-label="Date Ordered" nowrap><?php echo date('d-m-Y', $co['date_ordered']);?></td>
        					<!--td data-label="Status"><?php echo $order_status;?></td-->
        					<td data-label="Slip printed"><?php echo $slip_printed; ?></td>
                            <td data-label="Ignore Price Restriction" class="chkbox">
                                <div class="checkbox checkbox-default">
                                    <input <?php //if($errors) echo "disabled";?> type="checkbox" class="select_np styled" data-orderid='<?php echo $co['id'];?>' name="ignoreprice_<?php echo $co['id'];?>" id="ignoreprice_<?php echo $co['id'];?>" data-clientid="<?php echo $co['client_id'];?>" />
                                    <label for="ignoreprice_<?php echo $co['id'];?>"></label>
                                </div>
                            </td>
                            <?php if($user_role == "admin" || $user_role == "super admin"):?>
            					<td data-label="Courier" nowrap>
            					    <p><select name="courier" class="selectpicker courier" id="courier_<?php echo $co['id'];?>" <?php if($co['courier_id'] > 0 || !$fulfill) echo "disabled";?>><option value="-1">--Select One--</option><option value="0">Auto</option><?php echo $this->controller->courier->getSelectCouriers($co['courier_id'], false, false);?></select></p>
                                    <p><button class="ship_quote btn btn-primary quote_button" data-destination="<?php echo $address_string;?>" data-orderid="<?php echo $co['id'];?>">Get Shipping Prices</button></p>
                                </td>
                            <?php elseif($user_role == "warehouse"):?>
                                <td>
                                    <p><select name="courier" class="selectpicker courier" id="courier_<?php echo $co['id'];?>" disabled><option value="-1">--Select One--</option><option value="0">Auto</option><?php echo $this->controller->courier->getSelectCouriers($co['courier_id'], false, false);?></select></p>
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
                            <tr class="full_width">
                                <td colspan="13" class="error">
                                    <?php echo $co['error_string'];?>
                                    <p><a class="btn btn-primary" href="/orders/address-update/order=<?php echo $co['id'];?>">Fix this Address</a></p>
                                </td>
                            </tr>
                        <?php endif;?>
                        <?php if($comments):?>
                            <tr class="order_comments full_width">
                                <td colspan="13"><?php echo $co['3pl_comments'];?></td>
                            </tr>
                        <?php endif;?>
                        <?php if($pick_notice):?>
                            <tr class="pick_notice full_width">
                                <td colspan="13"><?php echo $co['pick_notices'];?></td>
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
