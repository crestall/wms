<?php
//echo "<p>FROM: ".date("Y-m-d H:i:s", $from)."</p>";
//echo "<p>TO: ".date("Y-m-d H:i:s", $to)."</p>";
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row form-group">
            <label class="col-md-3">Select a Client</label>
            <div class="col-md-4">
                <p><select id="client_selector" class="form-control selectpicker"  data-style="btn-outline-secondary"><option value="0">All Clients</option><?php echo $this->controller->client->getSelectPPClients($client_id);?></select></p>
            </div>
        </div>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/between_dates.php");?>
        <?php echo "<pre>",print_r($bays),"</pre>"; ?>
        <?php if(count($bays)):?>
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
                    <table id="client_space_usage_table" class="table-striped table-hover" style="width:99%">
                        <thead>
                            <tr>
                                <th data-priority="2">Client</th>
                                <th>Bay Name</th>
                                <th>Date Added</th>
                                <th>Date Removed</th>
                                <th data-priority="1">Days Held</th>
                                <th data-priorty="1">Charge Rate</th>
                                <th data-priority="1">Charge</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2>No Space Usage Found</h2>
                        <p>There are no spaces listed as being used between <?php echo date("d/m/Y", $from);?> and <?php echo date("d/m/Y", $to);?></p>
                        <p>If you believe this is an error, please let Solly know</p>
                        <p>Alternatively, use the date selectors above to change the date range</p>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>