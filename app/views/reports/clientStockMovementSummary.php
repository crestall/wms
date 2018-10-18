<div id="page-wrapper">
    <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/between_dates.php");?>
    <?php //echo "<pre>",print_r($movements),"</pre>";?>
    <?php if(count($movements)):?>
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
                <table id="client_stock_movement_summary_table" class="table-striped table-hover" width="100%">
                    <thead>
                    	<tr>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Total In</th>
                            <th>Total Out</th>
                            <th>Currently On Hand</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($movements as $i):?>
                    	<tr>
                        	<td data-label="Name"><?php echo $i['name'];?></td>
                            <td data-label="SKU"><?php echo $i['sku'];?></td>
                            <td data-label="Total In" class="number"><?php echo $i['total_in'];?></td>
                            <td data-label="Total Out" class="number"><?php echo $i['total_out'];?></td>
                            <td data-label="Currently On Hand" class="number"><?php echo $i['on_hand'];?></td>
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
                    <p>If you believe this is an error, please contact us to let us know</p>
                    <p>Alternatively, use the date selectors above to change the date range</p>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>