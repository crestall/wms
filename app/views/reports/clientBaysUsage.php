<?php
    $c = 1;
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row form-group">
            <label class="col-md-3">Select a Client</label>
            <div class="col-md-4">
                <p><select id="client_selector" class="form-control selectpicker"  data-style="btn-outline-secondary"><option value="0">Select</option><?php echo $this->controller->client->getSelectClients($client_id);?></select></p>
            </div>
        </div>
        <?php if($client_id > 0):?>
            <?php if(count($bays)):?>
                <?php //echo "<pre>",print_r($bays),"</pre>"; die();?>
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
                        <table id="client_bayusage_table" class="table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Site</th>
                                    <th>Location Name</th>
                                    <th>Oversize</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($bays as $bay):
                                    $oversize = ($bay['oversize'] > 0)? "Yes" : "No";?>
                                    <tr>
                                        <td><?php echo Utility::toWords($bay['site'])?></td>
                                        <td data-label="Location Name"><?php echo $bay['location'];?></td>
                                        <td data-label="Oversize"><?php echo $oversize;?></td>
                                    </tr>
                                <?php ++$c;
                                endforeach;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else:?>
                <div class="row">
                    <div class="col-4 text-right">
                        <i class="fad fa-exclamation-triangle fa-6x"></i>
                    </div>
                    <div class="col-8">
                        <h2>No Bays Being Used By <?php echo $client_name;?></h2>
                    </div>
                </div>
            <?php endif;?>
        <?php endif;?>
    </div>
</div>