<div id="page-wrapper">
    <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/between_dates.php");?>
    <?php if(count($client_orders)):?>
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
                            <th>Total Items</th>
                            <th>Courier</th>
                            <th>Con Note</th>
                            <?php if( !in_array($client_id, $hidden) ):?>
                                <th>Estimated Freight Charge</th>
                            <?php endif;?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($client_orders as $co):?>
                    	<tr>
                            <td data-label="Date Ordered" class="number" ><?php echo $co['date_ordered'];?></td>
                            <td data-label="Entered By"><?php echo $co['entered_by'];?></td>
                            <td data-label="Date Fulfilled" class="number" ><?php echo $co['date_fulfilled'];?></td>
                        	<td data-label="WMS Order Number"  class="number"><?php echo str_pad($co['order_number'],8,'0',STR_PAD_LEFT);?></td>
                            <td data-label="Your Order Number" class="number"><?php echo $co['client_order_number'];?></td>
                            <td data-label="Shipped To" class="nowrap shipped_to"><?php echo $co['shipped_to'];?></td>
                            <td data-label="Items" class="nowrap"><?php echo $co['items'];?></td>
                            <td data-label="Total Items" class="number"><?php echo $co['total_items'];?></td>
                            <td data-label="Courier" ><?php echo $co['courier'];?></td>
                            <td ><?php echo $co['consignment_id'];?></td>
                            <?php if( !in_array($client_id, $hidden) ):?>
                                <td data-label="Estimated Freight Charge" class="number"><?php echo $co['charge'];?></td>
                            <?php endif;?>
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
                    <h2><i class='far fa-times-circle'></i>No Orders Listed</h2>
                    <p>There are no orders listed as being dispatched between <?php echo date("d/m/Y", $from);?> and <?php echo date("d/m/Y", $to);?></p>
                    <p>If you believe this is an error, please contact us to let us know</p>
                    <p>Alternatively, use the date selectors above to change the date range</p>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>