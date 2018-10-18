<div id="page-wrapper">
    <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/between_dates.php");?>
    <?php if(count($returns)):?>
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
                <table id="returns_table" class="table-striped table-hover" width="100%">
                    <thead>
                    	<tr>
                            <th>Date</th>
                            <th>Item</th>
                            <th>WMS Order Number</th>
                            <th>Client Order Number</th>
                            <th>Reason</th>
                            <th>Entered By</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($returns as $r):?>
                    	<tr>
                            <td data-label="Date" class="number nowrap"><?php echo $r['return_date'];?></td>
                            <td data-label="Item"><?php echo $r['item_name'];?></td>
                            <td data-label="WMS Order Number" class="number"><?php echo $r['order_number'];?></td>
                            <td data-label="Client Order Number" class="number"><?php echo $r['client_order_number'];?></td>
                            <td data-label="Reason"><?php echo $r['reason'];?></td>
                            <td data-label="Entered By"><?php echo $r['entered_by'];?></td>
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
                    <h2>No Returns Listed</h2>
                    <p>There are no orders listed as being returned between <?php echo date("d/m/Y", $from);?> and <?php echo date("d/m/Y", $to);?></p>
                    <p>If you believe this is an error, please contact us to let us know</p>
                    <p>Alternatively, use the date selectors above to change the date range</p>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>