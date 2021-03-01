<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <!--div class="row">
            <div class="col">
                <p><a class="btn btn-primary" href="#nuchev">Nuchev</a></p>
            </div>
            <div class="col">
                <p><a class="btn btn-primary" href="#oneplate">One Plate</a></p>
            </div>
            <?php if($user_role == "super admin"):?>
                <div class="col-md-2">
                    <p><a class="btn btn-primary" href="#freedom">Freedom</a></p>
                </div>
            <?php endif;?>
        </div-->
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
                        <form id="nuchev_single_import" action="/orders/importNuchevOrder" method="post">
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
                        <form id="oneplate_single_import" action="/orders/importOneplateOrder" method="post">
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
            <div class="errorbox col-12">
                <h2>This is Done Automatically Now</h2>
                <p>press any buttons with caution!</p>
                <p>and check for double ups!</p>
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
                <div class="col-sm-12 col-md-6 mb-3">
                    <div class="card">
                        <a name="pba-shopify"></a>
                        <h4 class="card-header">Performance Brands Shopify Orders</h4>
                        <div class="card-body">
                            <div class="form-group row full_import">
                                <label class="col-5"><h5 class="card-title">Run Full Import</h5></label>
                                <div class="col-7">
                                    <button class="btn btn-outline-secondary" id="pbashopfy_full_import" data-function="importPBAShopifyOrders">Run It</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if($user_role == "super admin"):?>
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