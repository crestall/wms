<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/between_dates.php");?>
    <?php if(count($goods)):?>
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
                <table id="goodsout_table" class="table-striped table-hover" width="100%">
                    <thead>
                    	<tr>
                            <th>Date</th>
                            <th>Client</th>
                        	<th>Pallets</th>
                            <th>Cartons</th>
                            <th>Satchels</th>
                            <th>Entered By</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($goods as $gi):?>
                    	<tr>
                            <td data-label="Date" class="number" ><?php echo $gi['date'];?></td>
                            <td data-label="Client"><?php echo $gi['client_name'];?></td>
                        	<td data-label="Pallets" class="number"><?php echo $gi['pallets'];?></td>
                            <td data-label="Cartons" class="number"><?php echo $gi['cartons'];?></td>
                            <td data-label="Satchels" class="number"><?php echo $gi['satchels'];?></td>
                            <td data-label="Entered By"><?php echo $gi['entered_by'];?></td>
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