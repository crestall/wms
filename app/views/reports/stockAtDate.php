<div id="page-wrapper">
    <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php if($show_client_selector):?>
        <input type="hidden" name="show_client" id="show_client" value="1" />
        <div class="row form-group">
            <label class="col-md-3">Select a Client</label>
            <div class="col-md-4">
                <p><select id="client_selector" class="form-control selectpicker"><option value="0">Select</option><?php echo $this->controller->client->getSelectClients($client_id);?></select></p>
            </div>
        </div>
    <?php endif;?>
    <?php if($client_id > 0):?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/from_date.php");?> 
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
                <table id="sad_table" class="table-striped table-hover" width="100%">
                    <thead>
                    	<tr>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Stock on Hand at <?php echo date("d/m/Y", $date);?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($stock as $i):?>
                    	<tr>
                            <td data-label="Name" class="product_name"><?php echo $i['name'];?></td>
                            <td data-label="SKU" class="sku nowrap"><?php echo $i['sku'];?></td>
                            <td data-label="Stock on Hand at <?php echo date("d/m/Y", $date);?>" class="number nowrap" ><?php echo $i['on_hand'];?></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif;?>
</div>