<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <input type="hidden" id="client_id" value="<?php echo $client_id;?>" />
        <input type="hidden" id="active" value="<?php echo $active;?>" />
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <label class="col-md-3">Select a Client</label>
            <div class="col-md-4">
                <select id="client_selector" class="form-control selectpicker" data-style="btn-outline-secondary" data-live-search="true"><option value="0">Select</option><?php echo $this->controller->client->getSelectClients($client_id);?></select>
            </div>
        </div>
        <?php if($client_id > 0):?>
            <div class="row">
                <div class="col-lg-12">
                    <h2>Products for <?php echo $client_name;?></h2>
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
                    <?php if($active == 1):?>
                        <p class="text-right"><a class="btn btn-outline-fsg" href="/products/view-products/client=<?php echo $client_id;?>/active=0">View Inactive Products</a></p>
                    <?php else:?>
                        <p class="text-right"><a class="btn btn-outline-fsg" href="/products/view-products/client=<?php echo $client_id;?>">View Active Products</a></p>
                    <?php endif;?>
                </div>
            </div>
            <div class="row" id="table_holder" style="display:none">
                <div class="col-lg-12">
                    <table width="100%" class="table-striped table-hover" id="view_items_table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>SKU</th>
                                <th>Client Product ID</th>
                                <th>Barcode</th>
                                <th data-priority="10002">Supplier</th>
                                <th data-priority="10001">Dimensions</th>
                                <th>Weight</th>
                                <th>Pallet Item</th>
                                <th>Boxed Item</th>
                                <th>Dangerous Goods</th>
                                <th>Print On Demand</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>