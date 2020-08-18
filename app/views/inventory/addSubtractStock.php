<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col">
                <p><a class="btn btn-outline-fsg" href="/inventory/view-inventory/client=<?php echo $product_info['client_id'];?>">Return to Clients Inventory</a></p>
            </div>
            <div class="col text-right">
                <p><a class="btn btn-outline-fsg" href="/inventory/move-stock/product=<?php echo $product_id;?>">Move Stock for This Item</a></p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3 col-md-3 col-lg-3">
                <div class="card h-100 border-secondary">
                    <div class="card-header bg-secondary text-white">
                        Current Locations
                    </div>
                    The locations list will go in this box
                </div>
            </div>
            <div class="col-sm-9 col-md-4 col-lg-4">
                <div class="card h-100 border-secondary">
                    <div class="card-header bg-secondary text-white">
                        Add To Stock
                    </div>
                    The add to stock form goes in this box
                </div>
            </div>
            <div class="col-sm-9 col-md-4 col-lg-4 float-sm-right">
                <div class="card h-100 border-secondary">
                    <div class="card-header bg-secondary text-white">
                        Subtract From Stock
                    </div>
                    The subtract from stock form goes in this box
                </div>
            </div>
        </div>
    </div>
</div>