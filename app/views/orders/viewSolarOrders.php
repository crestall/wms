<?php

?>
<div id="page-wrapper">
    <input type="hidden" id="fulfilled" value="<?php echo $fulfilled;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php if($user_role == "admin" || $user_role == "super admin"):?>
        <div class="row">
            <div class="col-lg-3 text-center">
                <?php if($fulfilled == 0):?>
                    <p><button class="btn btn-info" id="show_fulfilled">Show Only Fulfilled Orders</button></p>
                <?php else:?>
                    <p><button class="btn btn-primary" id="show_unfulfilled">Show Only Unfulfilled Orders</button></p>
                <?php endif;?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 text-center">
                <p><a class="btn btn-primary slip-print"><i class="fas fa-file-alt"></i> Print Picking Slips For Selected</a></p>
            </div>
            <div class="col-lg-3 text-center">
                <p><a class="btn btn-primary order-label-print"><i class="fas fa-tags"></i> Print Labels For Selected</a></p>
            </div>
            <?php if($fulfilled == 0):?>
                <div class="col-lg-3 text-center">
                    <p><a class="btn btn-primary order-fulfill"><i class="fas fa-clipboard-check"></i> Fulfill Selected Orders</a></p>
                </div>
            <?php endif;?>
            <div class="col-lg-3 text-center">
                <?php if($fulfilled == 0):?>
                    <p><a class="btn btn-danger cancel-order"><i class="fas fa-ban"></i> Cancel Selected Orders</a></p>
                <?php endif;?>
            </div>
        </div>
    <?php elseif($user_role == "warehouse"):?>
        <div class="row">
            <div class="col-lg-3 text-center">
                <p><a class="btn btn-primary slip-print"><i class="fas fa-file-alt"></i> Print Picking Slips For Selected</a></p>
            </div>
            <div class="col-lg-3 text-center">
                <p><a class="btn btn-primary order-label-print"><i class="fas fa-tags"></i> Print eParcel Labels For Selected</a></p>
            </div>
        </div>
    <?php endif;?>
    <div class="row">
        <div class="col-lg-3">
            <div class="form-group">
                <label>Filter By Order Type</label>
                <select id="type_selector" class="form-control selectpicker"><option value="0">All Types</option><?php echo $this->controller->solarordertype->getSelectSolarOrderTypes($type_id);?></select>
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
            <table width="100%" class="table-striped table-hover" id="solar_orders_table" style="width:100%">
                <thead>
        	    	<tr>
                        <th></th>
        	        	<th>Work Order</th>
        				<!--th>Client</th-->
        				<th>Address</th>
        				<th>Panels</th>
        				<th>Date Entered</th>
        				<th>Install Date</th>
        				<th>Slip Printed</th>
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

        				$address = $this->controller->solarorder->getAddressStringForOrder($co['id']);
        				$order_status = $this->controller->order->getStatusName($co['status_id']);
        				$slip_printed = ($co['slip_printed'] > 0)? "Yes": "No";
                        //$link = ( $co['store_order'] == 1 )? "/orders/big-bottle-store-orders/order={$co['xero_invoiceno']}":"/orders/order-update/order={$co['id']}";
                        //$item_count = $this->controller->order->getItemCountForOrder($co['id']);
                        //$items = $this->controller->order->getItemsForOrder($co['id']);
                        /* */
                        ?>
        	        	<tr>
                            <td class="number" data-label="Count"><?php echo $c;?></td>
        	            	<td class="filterable number" data-label="Work Order">
                                <?php echo $co['work_order'];?>
                            </td>
        					<!--td data-label="Client Name"><?php //echo $client_name;?></td-->
        					<td data-label="Delivery Address" class="filterable"><?php echo $address;?></td>
        					<td data-label="Panels" class="number"><?php echo $co['panels'];?></td>
        					<td data-label="Date Entered" nowrap><?php echo date('d-m-Y', $co['date_entered']);?></td>
        					<td data-label="Install Date" nowrap><?php echo date('d-m-Y', $co['install_date']);?></td>
        					<td data-label="Slip printed"><?php echo $slip_printed; ?></td>
        					<td data-label="Select" class="chkbox">
                                <div class="checkbox checkbox-default">
                                    <input <?php //if($errors) echo "disabled";?> type="checkbox" class="select styled" data-orderid='<?php echo $co['id'];?>' name="select_<?php echo $co['id'];?>" id="select_<?php echo $co['id'];?>" />
                                    <label for="select_<?php echo $co['id'];?>"></label>
                                </div>
                            </td>
        				</tr>
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
