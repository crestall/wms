<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/between_dates.php");?>
    <?php if(count($runs)):?>
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
                <table id="runsheet_table" class="table-striped table-hover" width="100%">
                    <thead>
                    	<tr>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Order Number</th>
                            <th>Suburb</th>
                            <th>Charge</th>
                            <th>Entered By</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($runs as $charge):?>
                    	<tr>
                            <td data-label="Date" class="number nowrap"><?php echo $charge['date'];?></td>
                            <td data-label="Client"><?php echo $charge['client_name'];?></td>
                            <td data-label="Order Number" class="number nowrap"><?php echo $charge['order_number'];?></td>
                            <td data-label="Suburb"><?php echo $charge['suburb'];?></td>
                            <td data-label="Charge" class="number nowrap"><?php echo $charge['charge'];?></td>
                            <td data-label="Entered by"><?php echo $charge['entered_by'];?></td>
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
                    <h2><i class='far fa-times-circle'></i>No Truck Usage Listed</h2>
                    <p>There is no truck usage recorded between <?php echo date("d/m/Y", $from);?> and <?php echo date("d/m/Y", $to);?></p>
                    <p>If you believe this is an error, please let Solly know</p>
                    <p>Alternatively, use the date selectors above to change the date range</p>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>