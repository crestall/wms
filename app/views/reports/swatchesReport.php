<div id="page-wrapper">
    <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row form-group">
        <label class="col-md-3">Select a Client</label>
        <div class="col-md-4">
            <p><select id="client_selector" class="form-control selectpicker"><option value="0">Select</option><?php echo $this->controller->client->getSelectClients($client_id);?></select></p>
        </div>
    </div>
    <?php if($client_id > 0):?>
        <?php if(count($swatches)):?>
            <?php echo "<pre>",print_r($products),"</pre>";?>
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
                    <table id="inventory_report_table" class="table-striped table-hover">
                        <thead>
                        	<tr>
                                <th>Name</th>
                                <th>SKU</th>
                                <th>Total On Hand</th>
                                <th>Currently Allocated</th>
                                <th>Under Quality Controll</th>
                                <th>Total Available</th>
                                <th>Locations</th>
                        	</tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2>No Swatches Listed</h2>
                        <p></p>
                        <p>If you believe this is an error, please let Solly know</p>
                    </div>
                </div>
            </div>
        <?php endif;?>
    <?php endif;?>
</div>
<div id="block_message"></div>