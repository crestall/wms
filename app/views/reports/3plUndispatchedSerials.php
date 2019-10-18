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
                        <?php echo "<pre>",print_r($client_orders),"</pre>";?>
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