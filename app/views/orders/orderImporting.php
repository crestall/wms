<?php
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col">
                <p><a class="btn btn-primary" href="#woocommerce">Woocommerce Orders</a></p>
            </div>
            <div class="col">
                <p><a class="btn btn-primary" href="#shopify">Shopify Orders</a></p>
            </div>
            <?php if($super_admin):?>
                <div class="col">
                    <p><a class="btn btn-primary" href="#ebay">Ebay Orders</a></p>
                </div>
                <div class="col-md-2">
                    <p><a class="btn btn-primary" href="#myob">MYOB Orders</a></p>
                </div>
            <?php endif;?>
        </div>
        <div class="row">
            <div class="col">
                <?php if(isset($_SESSION['feedback'])) :?>
                    <?php if(isset($_SESSION['bberror']) && $_SESSION['bberror']):?>
                        <div class='errorbox'><?php Session::destroy('bberror');?>
                    <?php else:?>
                         <div class='feedbackbox'>&nbsp;
                    <?php endif;?>
                    <?php echo Session::getAndDestroy('feedback');?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div id="woocommerce" class="col-12 mb-3 border-top border-secondary pt-3">
                <h2>Woo Commerce Order Importing</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card">
                    <a name="nuchev"></a>
                    <h4 class="card-header">Nuchev Orders</h4>
                    <div class="card-body">
                        <div class="form-group row full_import">
                            <label class="col-5"><h5 class="card-title">Run Full Import</h5></label>
                            <div class="col-7">
                                <button class="btn btn-outline-secondary" id="nuchev_full_import" data-function="importNuchevOrders">Run It</button>
                            </div>
                        </div>
                        <h5 class="card-title">Import single Order</h5>
                        <form id="nuchev_single_import" class="single_order_import" action="/orders/importNuchevOrder" method="post">
                            <div class="form-group row">
                                <label class="col-5">WooCommerce Order ID</label>
                                <div class="col-7">
                                    <input type="text" class="form-control required" name="nuchevwoocommerce_id" id="nuchevwoocommerce_id" value="<?php echo Form::value('nuchevwoocommerce_id');?>" />
                                    <?php echo Form::displayError('nuchevwoocommerce_id');?>
                                </div>
                            </div>
                            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                            <input type="hidden" name="client_id" value="<?php echo $nuchev_clientid; ?>" />
                            <div class="form-group row">
                                <label class="col-5">&nbsp;</label>
                                <div class="col-4">
                                    <button type="submit" class="btn btn-outline-secondary">Import It</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card">
                    <a name="oneplate"></a>
                    <h4 class="card-header">One Plate Orders</h4>
                    <div class="card-body">
                        <div class="form-group row full_import">
                            <label class="col-5"><h5 class="card-title">Run Full Import</h5></label>
                            <div class="col-7">
                                <button class="btn btn-outline-secondary" id="oneplate_full_import" data-function="importOnePlateOrders">Run It</button>
                            </div>
                        </div>
                        <h5 class="card-title">Import single Order</h5>
                        <form id="oneplate_single_import" class="single_order_import" action="/orders/importOneplateOrder" method="post">
                            <div class="form-group row">
                                <label class="col-5">WooCommerce Order ID</label>
                                <div class="col-7">
                                    <input type="text" class="form-control required" name="oneplatewoocommerce_id" id="oneplatewoocommerce_id" value="<?php echo Form::value('oneplatewoocommerce_id');?>" />
                                    <?php echo Form::displayError('oneplatewoocommerce_id');?>
                                </div>
                            </div>
                            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                            <input type="hidden" name="client_id" value="<?php echo $oneplate_clientid; ?>" />
                            <div class="form-group row">
                                <label class="col-5">&nbsp;</label>
                                <div class="col-4">
                                    <button type="submit" class="btn btn-outline-secondary">Import It</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card">
                    <a name="pba-woo"></a>
                    <h4 class="card-header">Performance Brands Woo-Commerce Orders</h4>
                    <div class="card-body">
                        <div class="form-group row full_import">
                            <label class="col-5"><h5 class="card-title">Run Full Import</h5></label>
                            <div class="col-7">
                                <button class="btn btn-outline-secondary" id="pbawoocommerce_full_import" data-function="importPBAOrders">Run It</button>
                            </div>
                        </div>
                        <h5 class="card-title">Import single Order</h5>
                        <form id="oneplate_single_import" action="/orders/importPbaWoocommerceOrder" method="post">
                            <div class="form-group row">
                                <label class="col-5">WooCommerce Order ID</label>
                                <div class="col-7">
                                    <input type="text" class="form-control required" name="pbawoocommerce_id" id="pbawoocommerce_id" value="<?php echo Form::value('pbawoocommerce_id');?>" />
                                    <?php echo Form::displayError('pbawoocommerce_id');?>
                                </div>
                            </div>
                            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                            <input type="hidden" name="client_id" value="<?php echo $pba_clientid; ?>" />
                            <div class="form-group row">
                                <label class="col-5">&nbsp;</label>
                                <div class="col-4">
                                    <button type="submit" class="btn btn-outline-secondary">Import It</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div id="shopify" class="col-12 mb-3 border-top border-secondary pt-3">
                <h2>Shopify Order Importing</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card">
                    <a name="pba-ppg-shopify"></a>
                    <h4 class="card-header">PBA Perfect Practice Golf Shopify Orders</h4>
                    <div class="card-body">
                        <div class="form-group row full_import">
                            <label class="col-5"><h5 class="card-title">Run Full Import</h5></label>
                            <div class="col-7">
                                <button class="btn btn-outline-secondary shopify_import" id="pbappgshopify_full_import" data-function="importPBAPPGShopifyOrders">Run It</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card">
                    <a name="pba-vc-shopify"></a>
                    <h4 class="card-header">PBA Voice Caddy Shopify Orders</h4>
                    <div class="card-body">
                        <div class="form-group row full_import">
                            <label class="col-5"><h5 class="card-title">Run Full Import</h5></label>
                            <div class="col-7">
                                <button class="btn btn-outline-secondary shopify_import" id="pbavcshopify_full_import" data-function="importPBAVCShopifyOrders">Run It</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card">
                    <a name="pba-hcg-shopify"></a>
                    <h4 class="card-header">PBA Home Course Golf Shopify Orders</h4>
                    <div class="card-body">
                        <div class="form-group row full_import">
                            <label class="col-5"><h5 class="card-title">Run Full Import</h5></label>
                            <div class="col-7">
                                <button class="btn btn-outline-secondary shopify_import" id="pbahcgshopify_full_import" data-function="importPBAHCGShopifyOrders">Run It</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card">
                    <a name="pba-hcg-shopify"></a>
                    <h4 class="card-header">PBA Superspeed Golf Shopify Orders</h4>
                    <div class="card-body">
                        <div class="form-group row full_import">
                            <label class="col-5"><h5 class="card-title">Run Full Import</h5></label>
                            <div class="col-7">
                                <button class="btn btn-outline-secondary shopify_import" id="pbassgshopify_full_import" data-function="importPBASSGShopifyOrders">Run It</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card">
                    <a name="pba-rukket-shopify"></a>
                    <h4 class="card-header">PBA Rukket Golf Shopify Orders</h4>
                    <div class="card-body">
                        <div class="form-group row full_import">
                            <label class="col-5"><h5 class="card-title">Run Full Import</h5></label>
                            <div class="col-7">
                                <button class="btn btn-outline-secondary shopify_import" id="pbarukketshopify_full_import" data-function="importPBARUKKETShopifyOrders">Run It</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card">
                    <a name="pba-rukket-shopify"></a>
                    <h4 class="card-header">PBA Arccos Golf Shopify Orders</h4>
                    <div class="card-body">
                        <div class="form-group row full_import">
                            <label class="col-5"><h5 class="card-title">Run Full Import</h5></label>
                            <div class="col-7">
                                <button class="btn btn-outline-secondary shopify_import" id="pbaarccosshopify_full_import" data-function="importPBAARCCOSShopifyOrders">Run It</button>
                            </div>
                        </div>
                        <h5 class="card-title">Import single Order</h5>
                        <form id="buzzbee_shopify_single_import" class="single_order_import" action="/orders/importArccosShopifyOrder" method="post">
                            <div class="form-group row">
                                <label class="col-5">Shopify Order Number</label>
                                <div class="col-7">
                                    <input type="text" class="form-control required" name="pbaArccosshopify_orderno" id="pbaArcossshopify_orderno" value="<?php echo Form::value('pbaArccosshopify_orderno');?>" />
                                    <?php echo Form::displayError('pbaArccosshopify_orderno');?>
                                </div>
                            </div>
                            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                            <input type="hidden" name="client_id" value="<?php echo $pba_clientid; ?>" />
                            <div class="form-group row">
                                <label class="col-5">&nbsp;</label>
                                <div class="col-4">
                                    <button type="submit" class="btn btn-outline-secondary">Import It</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card">
                    <a name="buzz-bee"></a>
                    <h4 class="card-header">Buzz Bee Shopify Orders</h4>
                    <div class="card-body">
                        <div class="form-group row full_import">
                            <label class="col-5"><h5 class="card-title">Run Full Import</h5></label>
                            <div class="col-7">
                                <button class="btn btn-outline-secondary shopify_import" id="bbshopify_full_import" data-function="importBBShopifyOrders">Run It</button>
                            </div>
                        </div>
                        <h5 class="card-title">Import single Order</h5>
                        <form id="buzzbee_shopify_single_import" class="single_order_import" action="/orders/importBBShopifyOrder" method="post">
                            <div class="form-group row">
                                <label class="col-5">Shopify Order Number</label>
                                <div class="col-7">
                                    <input type="text" class="form-control required" name="bbshopify_orderno" id="bbshopify_orderno" value="<?php echo Form::value('bbshopify_orderno');?>" />
                                    <?php echo Form::displayError('bbshopify_orderno');?>
                                </div>
                            </div>
                            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                            <input type="hidden" name="client_id" value="<?php echo $bb_clientid; ?>" />
                            <div class="form-group row">
                                <label class="col-5">&nbsp;</label>
                                <div class="col-4">
                                    <button type="submit" class="btn btn-outline-secondary">Import It</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php if($super_admin):?>
        <div class="row">
            <div id="ebay" class="col-12 mb-3 border-top border-secondary pt-3">
                <h2>EBAY Order Importing</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card">
                    <a name="pba-ebay"></a>
                    <h4 class="card-header">PBA Ebay Orders</h4>
                    <div class="card-body">
                        <div class="form-group row full_import">
                            <label class="col-5"><h5 class="card-title">Run Full Import</h5></label>
                            <div class="col-7">
                                <button class="btn btn-outline-secondary ebay_import" id="PBAebay_full_import" data-function="importPBAEbayOrders">Run It</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div id="myob" class="col-12 mb-3 border-top border-secondary pt-3">
                <h2>MYOB Order Importing</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card">
                    <a name="freedom"></a>
                    <h4 class="card-header">Freedom Orders</h4>
                    <div class="card-body">
                        <div class="form-group row full_import">
                            <label class="col-5"><h5 class="card-title">Run Full Import From MYOB</h5></label>
                            <div class="col-7">
                                <button class="btn btn-outline-secondary" id="freedom_full_import" data-function="importFreedomOrders">Run It</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif;?>
        </div>
    </div>
</div>