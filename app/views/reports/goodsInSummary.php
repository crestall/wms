<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/between_dates.php");?>
    <?php if(count($summary)):?>
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
                <table id="goods_in_summary" class="table-striped table-hover" width="100%">
                    <thead>
                    	<tr>
                            <th>Client</th>
                            <th>Total Pallets</th>
                            <th>Total Cartons</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($summary as $s):?>
                    	<tr>
                            <td data-label="Client"><?php echo $s['client_name'];?></td>
                            <td data-label="Total Pallets" class="number nowrap"><?php echo $s['pallets'];?></td>
                            <td data-label="Total Cartons" class="number nowrap"><?php echo $s['cartons'];?></td>
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
                    <h2><i class='far fa-times-circle'></i>No Goods In Listed</h2>
                    <p>There is no inwards goods recorded between <?php echo date("d/m/Y", $from);?> and <?php echo date("d/m/Y", $to);?></p>
                    <p>If you believe this is an error, please let Solly know</p>
                    <p>Alternatively, use the date selectors above to change the date range</p>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>