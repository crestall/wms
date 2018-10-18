<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php if(count($locations)):?>
        <div class="row">
            <div class="col-md-12">
                <h4>There are <strong><?php echo count($locations);?></strong> empty locations as at <?php echo date('d/m/Y');?></h4>
            </div>
        </div>
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
                <table id="emptybay_table" class="table-striped table-hover" width="100%">
                    <thead>
                    	<tr>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($locations as $l):?>
                    	<tr>
                            <td data-label="Location" class="nowrap"><?php echo $l['location'];?></td>
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
                    <h2><i class='far fa-times-circle'></i>No Empty Bays</h2>
                    <p>There are currently no bays listed as being empty</p>
                    <p>If you believe this is an error, please let Solly know</p>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>