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
                            	<th>WMS Order No</th>
                                <th>Client Order<br/>Number</th>
                                <th>Customer Order<br/>Number</th>
                                <th>Shipped To</th>
                                <th>Items</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($client_orders as $co):?>
                        	<tr>
                                <td data-label="Date Ordered" class="number" ><?php echo $co['date_ordered'];?></td>
                                <td data-label="Entered By"><?php echo $co['entered_by'];?></td>
                            	<td data-label="WMS Order Number"  class="number"><a href="/orders/order-update/order=<?php echo $co['id'];?>"><?php echo str_pad($co['order_number'],8,'0',STR_PAD_LEFT);?></a></td>
                                <td data-label="Client Order Number" class="number"><?php echo $co['client_order_number'];?></td>
                                <td data-label="Customer Order Number" class="number"><?php echo $co['customer_order_number'];?></td>
                                <td data-label="Shipped To" class="nowrap shipped_to"><?php echo $co['shipped_to'];?></td>
                                <td data-label="Items" class="nowrap items"><?php echo $co['items'];?></td>
                                <td></td>
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
                        <p>There are no undispatched orders listed between <?php echo date("d/m/Y", $from);?> and <?php echo date("d/m/Y", $to);?></p>
                        <p>If you believe this is an error, please let Solly know</p>
                        <p>Alternatively, use the date selectors above to change the date range</p>
                    </div>
                </div>
            </div>
        <?php endif;?>
    <?php endif;?>
</div>
<div id="block_message"></div>