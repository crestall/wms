<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php if(count($locations)):?>
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
                <table id="location_report_table" class="table-striped table-hover" width="100%">
                    <thead>
                    	<tr>
                            <th>Location</th>
                            <th>Client</th>
                            <th>Item/Notes</th>
                            <th>SKU</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($locations as $l):?>
                    	<tr>
                            <td data-label="Location"><?php echo $l['location'];?></td>
                            <td data-label="Client"><?php echo $l['client_name'];?></td>
                            <td data-label="Item/Notes"><?php if(empty($l['notes'])) echo $l['name']; else echo $l['notes'];?></td>
                            <td data-label="SKU"><?php echo $l['sku'];?></td>
                            <td data-label="Count"><?php echo $l['qty'];?></td>
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
                    <h2>No Locations in use</h2>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>