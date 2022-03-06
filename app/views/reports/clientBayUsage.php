<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if(count($usage)):?>
            <div class="row mb-3">
                <div class="col-12 text-right">
                    <button id="csv_download" class="btn btn-outline-success"><i class="far fa-file-alt"></i>&nbsp;Download As CSV</button>
                </div>

            </div>
            <div class="row">
                <div class="col-lg-12">
                    <table id="bay_usage_table" class="table-striped table-hover" width="100%">
                        <thead>
                            <tr>
                                <th rowspan="2">Client</th>
                                <th colspan="3">Generated <?php echo date("d/m/Y");?></th>
                            </tr>
                            <tr>
                                <th>Standard Bays</th>
                                <th>Oversize Bays</th>
                                <th>Pick Faces</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($usage as $cu):?>
                                <tr>
                                    <td data-label="Client Name"><?php echo $cu['client_name'];?></td>
                                    <td data-label="Standard Bays" class="number"><?php echo $cu['location_count'];?></td>
                                    <td data-label="Oversize Bays" class="number"><?php echo $cu['oversize_count'];?></td>
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
                        <h2><i class='far fa-times-circle'></i>Nothing To Report</h2>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>