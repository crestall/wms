<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/between_dates.php");?>
        <?php if(count($ucs)):?>
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
                        <button id="csv_download" class="btn btn-outline-success"><i class="far fa-file-alt"></i>&nbsp;Download As CSV</button>
                    </p>
                </div>
            </div>
            <div class="row" id="table_holder" style="display:none">
                <div class="col-lg-12">
                    <table id="unloaded_containers" class="table-striped table-hover" width="100%">
                        <thead>
                        	<tr>
                                <th>Date Unloaded</th>
                                <th>Container Size</th>
                                <th>Load Type</th>
                                <th>Loose Item Count</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($ucs as $uc):
                            $item_count = ($uc['item_count'] > 0)? $uc['item_count'] : "N/A";?>
                        	<tr>
                                <td data-label="Date Unloaded" class="number nowrap" ><?php echo $uc['date'];?></td>
                                <td data-label="Container Size"><?php echo $uc['container_size'];?></td>
                                <td data-label="Load Type"><?php echo $uc['load_type'];?></td>
                                <td data-label="Item Count" class="number"><?php echo $item_count;?></td>
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
                        <h2><i class='far fa-times-circle'></i>No Unloaded Containers Listed</h2>
                        <p>There is no container unloads recorded between <?php echo date("d/m/Y", $from);?> and <?php echo date("d/m/Y", $to);?></p>
                        <p>If you believe this is an error, <a href="/contact/conatct-us">please let us know</a></p>
                        <p>Alternatively, use the date selectors above to change the date range</p>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>