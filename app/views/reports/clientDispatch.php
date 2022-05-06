<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
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
                        <button id="csv_download" class="btn btn-outline-success"><i class="far fa-file-alt"></i>&nbsp;Download As CSV</button>
                    </p>
                </div>
            </div>
            <div class="row" id="table_holder" style="display:none">
                <div class="col-lg-12">
                    <table id="client_dispatch_table" class="table-striped table-hover">
                        <thead>
                        	<tr>
                                <th data-priority="3">Date Ordered</th>
                                <th data-priority="10001">Entered By</th>
                                <th data-priority="3">Date Dispatched</th>
                            	<th data-priority="10001">FSG Order No</th>
                                <th data-priority="2">Client Order Number</th>
                                <th data-priority="1">Shipped To</th>
                                <th data-priority="1">Items</th>
                                <th data-priority="2">Courier/Comments</th>
                                <th data-priority="1">Con Note</th>
                                <th data-priority="2">Cartons</th>
                                <th data-priority="2">Weight</th>
                                <th data-priority="1">Estimated Shipping Cost</th>
                                <th data-priority="1">Handling Charge</th>
                                <th data-priority="1">GST</th>
                                <th data-priority="1">Total Charge</th>
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
                                <td data-label="Shipped To" class="nowrap"><?php echo $co['shipped_to'];?></td>
                                <td data-label="Items">
                                    <?php //echo $co['items'];?>
                                    <div class="item_list border-bottom border-secondary border-bottom-dashed mb-3 ">
                                        <?php foreach($co['csv_items'] as $i):?>
                                            <p><span class="iname"><?php echo $i['name'];?>:</span><span class="icount"><?php echo $i['qty'];?></span></p>
                                        <?php endforeach;?>
                                    </div>
                                    <div class="item_total text-right">
                                        Total Items: <?php echo $co['total_items'];?>
                                    </div>
                                </td>
                                <td data-label="Courier/Comments" >
                                    <?php echo $co['courier'];?>
                                    <?php if(!empty($co['comments'])):?>
                                        <div class="border-top border-secondary border-top-dashed mt-3">
                                            <?php echo $co['comments'];?>
                                        </div>
                                    <?php endif;?>
                                </td>
                                <td data-label="Con Note" ><?php echo $co['consignment_id'];?></td>
                                <td data-label="Cartons" ><?php echo $co['cartons'];?></td>
                                <td data-label="Weight" ><?php echo $co['weight'];?></td>
                                <td data-label="Postage Charge"><?php echo $co['postage_charge'];?></td>
                                <td data-label="Handling Charge"><?php echo $co['handling_charge'];?></td>
                                <td data-label="GST"><?php echo $co['gst'];?></td>
                                <td data-label="Total Charge"><?php echo $co['total_charge'];?></td>
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
</div>