<div id="page-wrapper">
    <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row form-group">
        <label class="col-md-3">Select a Client</label>
        <div class="col-md-4">
            <p><select id="client_selector" class="form-control selectpicker"><option value="0">Select</option><?php echo $this->controller->client->getSelectClients($client_id);?></select></p>
        </div>
    </div>
    <?php if($client_id > 0):?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/between_dates.php");?>
        <?php if(count($client_orders)):?>
            <?php //echo "<pre>",print_r($client_orders),"</pre>"; die();?>
            <div id="waiting" class="row">
                <div class="col-lg-12 text-center">
                    <h2>Drawing Table..</h2>
                    <p>May take a few moments</p>
                    <img class='loading' src='/images/preloader.gif' alt='loading...' />
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <p class="text-right">
                        <button id="csv_download" class="btn btn-success"><i class="far fa-file-alt"></i>&nbsp;Download As CSV</button>
                    </p>
                </div>
            </div>
            <div class="row" id="table_holder" style="display:none">
                <div class="col-lg-12">
                    <table id="client_dispatch_table" class="table-striped table-hover">
                        <thead>
                        	<tr>
                                <th>Date Ordered</th>
                                <th>Entered By</th>
                                <th>Date Dispatched</th>
                            	<th>Order No</th>
                                <th>Client Order Number</th>
                                <th>Shipped To</th>
                                <th>Items</th>
                                <th>Charge Code</th>
                                <th>Total Charge</th>
                                <th>Weight</th>
                                <th>Courier</th>
                                <th>Con Note</th>
                                <th>Cartons</th>
                                <th>Extras<br/><button class="btn btn-success btn-sm" id="extras_update">Update Extras</button></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($client_orders as $co):
                            $invoice = "";
                            if(!empty($co['uploaded_file']))
                            {
                                $invoice = "<a href='/client_uploads/{$co['client_id']}/{$co['uploaded_file']}' target='_blank'>Print Invoice</a>";
                            }?>
                        	<tr>
                                <td data-label="Date Ordered" class="number" ><?php echo $co['date_ordered'];?></td>
                                <td data-label="Entered By"><?php echo $co['entered_by'];?></td>
                                <td data-label="Date Fulfilled" class="number" ><?php echo $co['date_fulfilled'];?></td>
                            	<td data-label="WMS Order Number"  class="number"><a href="/orders/order-update/order=<?php echo $co['id'];?>"><?php echo str_pad($co['order_number'],8,'0',STR_PAD_LEFT);?></a></td>
                                <td data-label="Client Order ID" class="number"><?php echo $co['client_order_number'];?></td>
                                <td data-label="Shipped To" class="nowrap shipped_to"><?php echo $co['shipped_to'];?></td>
                                <td data-label="Items" class="nowrap items"><?php echo $co['items'];?></td>
                                <td data-label="Charge Code"><?php echo $co['charge_code'];?></td>
                                <td data-label="Total Charge"><?php echo $co['charge'];?></td>
                                <td data-label="Weight"><?php echo $co['weight'];?></td>
                                <td data-label="Courier" ><?php echo $co['courier'];?></td>
                                <td data-label="Con Note"><?php echo $co['consignment_id'];?></td>
                                <td data-label="Cartons" class="number"><?php echo $co['cartons'];?></td>
                                <!--td data-label="Comments"><textarea class="form-control 3pl_comments" data-orderid="<?php echo $co['id'];?>"><?php echo $co['comments'];?></textarea></td-->
                                <td data-label="Extras Update" class="extras">
                                    <div class="checkbox checkbox-default">
                                        <input class="styled" type="checkbox" id="bubblewrap_<?php echo $co['id'];?>" name="bubblewrap_<?php echo $co['id'];?>" <?php if($co['bubble_wrap']) echo "checked"; ?> />
                                        <label for="bubblewrap_<?php echo $co['id'];?>">Bubble Wrap</label>
                                    </div>
                                    <div class="checkbox checkbox-default">
                                        <input class="styled" type="checkbox" id="shrinkwrap_<?php echo $co['id'];?>" name="shrinkwrap_<?php echo $co['id'];?>" <?php if($co['shrink_wrap']) echo "checked"; ?> />
                                        <label for="shrinkwrap_<?php echo $co['id'];?>">Shrink Wrap</label>
                                    </div>
                                    <div class="pallets">
                                        <input type="text" class="number form-control" name="pallets_<?php echo $co['id'];?>" id="pallets_<?php echo $co['id'];?>" value="<?php echo $co['pallets'];?>" />
                                        <label class="col-form-label">Pallets</label>
                                    </div>
                                </td>
                                <td><?php echo $invoice; ?></td>
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
                        <h2>No Orders Listed</h2>
                        <p>There are no orders listed as being dispatched between <?php echo date("d/m/Y", $from);?> and <?php echo date("d/m/Y", $to);?></p>
                        <p>If you believe this is an error, please let Solly know</p>
                        <p>Alternatively, use the date selectors above to change the date range</p>
                    </div>
                </div>
            </div>
        <?php endif;?>
    <?php endif;?>
</div>
<div id="block_message"></div>