<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/between_dates.php");?>
    <?php if(count($bays)):?>
        <div class="row">
            <div class="col-lg-12">
                <p class="text-right">
                    <button id="csv_download" class="btn btn-success"><i class="far fa-file-alt"></i>&nbsp;Download As CSV</button>
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table id="bay_usage_table" class="table-striped table-hover" width="100%">
                    <thead>
                        <tr>
                            <th rowspan="2">Client</th>
                            <?php foreach($fridays as $f):?>
                                <th nowrap colspan="3"><?php echo $f['string'];?></th>
                            <?php endforeach;?>
                        </tr>
                        <tr>
                            <?php foreach($fridays as $f):?>
                                <th>Standard Bays</th>
                                <th>Oversize Bays</th>
                                <th>Pickfaces</th>
                            <?php endforeach;?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($bays as $client_name => $carray):
                        //$client_name = $db->queryValue('clients', array('id' => $client_id), 'client_name');?>
                        <tr>
                            <td data-label="Client"><?php echo $client_name;?></td>
                            <?php foreach($fridays as $f):
                                $usage = (isset($carray[$f['string']]['standard']))? round($carray[$f['string']]['standard']) : 0 ;?>
                                <td data-label="<?php echo $f['string'];?> standard" class="number"><?php echo $usage;?></td>
                                <?php $usage = (isset($carray[$f['string']]['oversize']))? round($carray[$f['string']]['oversize']) : 0 ;?>
                                <td data-label="<?php echo $f['string'];?> oversize" class="number"><?php echo $usage;?></td>
                                <?php $usage = (isset($carray[$f['string']]['pickfaces']))? round($carray[$f['string']]['pickfaces']) : 0 ;?>
                                <td data-label="<?php echo $f['string'];?> pickfaces" class="number"><?php echo $usage;?></td>
                            <?php endforeach;?>
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
                    <p>Try changing the date range</p>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>