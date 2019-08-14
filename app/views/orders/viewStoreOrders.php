<div id="page-wrapper">
    <input type="hidden" id="fulfilled" value="<?php echo $fulfilled;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php if($user_role == "admin" || $user_role == "super admin"):?>
        <div class="row">
            <div class="col-lg-3 text-center">
                <?php if($fulfilled == 0):?>
                    <p><button class="btn btn-info" id="show_fulfilled">Show Only Fulfilled Store Orders</button></p>
                <?php else:?>
                    <p><button class="btn btn-primary" id="show_unfulfilled">Show Only Unfulfilled Store Orders</button></p>
                <?php endif;?>
            </div>
        </div>
    <?php endif;?>
    <div class="row">
        <?php if($user_role == "admin" || $user_role == "super admin"):?>
            <div class="col-lg-3 text-center">
                <p><a class="btn btn-primary cancel-order">Cancel Selected Orders</a></p>
            </div>
        <?php endif;?>
        <div class="col-lg-3 text-center">
            <p><a class="btn btn-primary packslip-print">Print Packing Slips For Selected</a></p>
            <p><a class="btn btn-primary pickslip-print">Print Picking Slips For Selected</a></p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <div class="form-group">
                <label>Filter By Client</label>
                <select id="client_selector" class="form-control selectpicker"><option value="0">All Clients</option><?php echo $this->controller->client->getSelectClients($client_id);?></select>
            </div>
        </div>
        <div class="col-lg-3">
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
    </div>
    <?php if(count($orders)):?>
    <div class="row">
        <div class="col-lg-12">
            <table width="100%" class="table-striped table-hover" id="client_orders_table" style="width:100%">
                <thead>
        	    	<tr>
                        <th></th>
        	        	<th>Order No</th>
                        <th>Client Invoice Number</th>
                        <th>Customer Invoice Number</th>
        				<th>Client</th>
        				<th>Deliver To</th>
        				<th>Delivery Address</th>
        				<th>Items</th>
        				<th>Date Ordered</th>
                        <th>Package Entered</th>
                        <th></th>
                        <th nowrap>
                            <div class="checkbox checkbox-default">
                                <input id="select_all" class="styled" type="checkbox">
                                <label for="select_all"><strong>Select</strong></label>&nbsp;<em><small>(all)</small></em>
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
                        $link = "/orders/order-update/order={$co['id']}";
                        $comments = !empty($co['3pl_comments']);
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
                        elseif( $comments )
                        {
                            $row_class = "class='filterable replacement'";
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
                            $invoice = "<p><a href='/client_uploads/{$co['client_id']}/{$co['uploaded_file']}' target='_blank' class='btn btn-primary'>Print Invoice</a></p>";
                        }
                        $ps = "<p><a href='/pdf/packing-slip/order={$co['id']}' target='_blank' class='btn btn-primary'>Print Packing Slip</a></p>";
                        $pe = ($this->controller->order->hasAssociatedPackage($co['id']))? "Yes":"No";
                        ?>
        	        	<tr <?php echo $row_class;?> >
                            <td class="number" data-label="Count"><?php echo $c;?></td>
        	            	<td class="filterable number" data-label="Order Number"><a href="<?php echo $link;?>"><?php echo str_pad($co['order_number'],8,'0',STR_PAD_LEFT);?></a></td>
                            <td class="filterable number" data-label="Client Invoice Number"><?php echo $co['client_order_id'];?></td>
                            <td class="filterable number" data-label="CUstomer Invoice Number"></td>
        					<td data-label="Client Name"><?php echo $client_name;?></td>
        	                <td class="filterable" data-label="Ship To"><?php echo $ship_to;?></td>
        					<td data-label="Delivery Address" class="filterable"><?php echo $address;?></td>
        					<td data-label="Items" class="number"><?php echo $item_count;?></td>
        					<td data-label="Date Ordered" nowrap><?php echo date('d-m-Y', $co['date_ordered']);?></td>
                            <td data-label="Package Entered"><?php echo $pe;?></td>
                            <td>
                                <?php echo $invoice; ?>
                                <?php echo $ps; ?>
                                <p><button class="btn btn-warning adjust_allocation" data-orderid="<?php echo $co['id'];?>">Adjust Allocations</button></p>
                            </td>
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
