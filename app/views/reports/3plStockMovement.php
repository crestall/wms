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
        <?php //echo "<pre>",print_r($movements),"</pre>";?>
        <?php if(count($movements)):
            $h = ($client_id == 67)? "Work Order":"Order Number";?>
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
                    <table id="stock_movement_table" class="table-striped table-hover" width="100%">
                        <thead>
                        	<tr>
                                <th>Date</th>
                                <th>SKU</th>
                                <th>Product</th>
                                <th>Number In</th>
                                <th>Number Out</th>
                                <th>Reason</th>
                                <th>Reference<br/><?php echo $h;?></th>
                                <th>Location</th>
                                <th>Entered by</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($movements as $sm):?>
                        	<tr>
                            	<td data-label="Date" class="nowrap"><?php echo $sm['date'];?></td>
                                <td data-label="SKU"><?php echo $sm['sku'];?></td>
                                <td data-label="Product"><?php echo $sm['name'];?></td>
                                <td data-label="Number In"><?php echo $sm['qty_in'];?></td>
                                <td data-label="Number Out"><?php echo $sm['qty_out'];?></td>
                                <td data-label="Reason"><?php echo $sm['reason'];?></td>
                                <td data-label="Reference/Order Number"><?php echo $sm['order_number'];?></td>
                                <td data-label="Location"><?php echo $sm['location'];?></td>
                                <td data-label="Entered By"><?php echo $sm['entered_by'];?></td>
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
                        <h2>No Movements Listed</h2>
                        <p>There are no items listed as being moved between <?php echo date("d/m/Y", $from);?> and <?php echo date("d/m/Y", $to);?></p>
                        <p>If you believe this is an error, please let Solly Know</p>
                        <p>Alternatively, use the date selectors above to change the date range</p>
                    </div>
                </div>
            </div>
        <?php endif;?>
    <?php endif;?>
</div>