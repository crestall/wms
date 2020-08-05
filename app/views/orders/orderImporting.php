<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col">
                <p><a class="btn btn-primary" href="#nuchev">Nuchev</a></p>
            </div>
            <div class="col">
                <p><a class="btn btn-primary" href="#oneplate">One Plate</a></p>
            </div>
            <div class="col">
                <p><a class="btn btn-primary" href="#freedom">Freedom</a></p>
            </div>
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
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card">
                    <a name="nuchev"></a>
                    <h4 class="card-header">Nuchev Orders</h4>
                    <div class="card-body">
                        <h5 class="card-title">Run Full Import</h5>
                        <div class="form-group row">
                            <label class="col-5">&nbsp;</label>
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
                    <div class="card-header">
                        One Plate Orders
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        Freedom Orders
                    </div>
                </div>
            </div>
        </div>











        <div class="bs-callout bs-callout-primary bs-callout-more">

            <div class="row">
                <div class="col-md-12">
                    <h2>Nuchev Orders</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">&nbsp;</div>
                <div class="col-md-4">
                    <h3>Import single Order</h3>
                </div>
            </div>
            <div class="row">

            </div>
            <hr/>
            <div class="row">
                <div class="col-md-3">&nbsp;</div>
                <div class="col-md-4">
                    <h3>Run Full Import</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">&nbsp;</div>
                <div class="col-md-4">
                    <p><button class="btn btn-primary" id="nuchev_full_import" data-function="importNuchevOrders">Run It</button></p>
                </div>
            </div>
        </div>
        <div class="bs-callout bs-callout-primary bs-callout-more">
            <a name="oneplate"></a>
            <div class="row">
                <div class="col-md-12">
                    <h2>One Plate Orders</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">&nbsp;</div>
                <div class="col-md-4">
                    <h3>Import single Order</h3>
                </div>
            </div>
            <div class="row">
                <form id="oneplate_single_import" action="/orders/importOneplateOrder" method="post">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">WooCommerce Order ID</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control required" name="oneplatewoocommerce_id" id="oneplatewoocommerce_id" value="<?php echo Form::value('oneplatewoocommerce_id');?>" />
                            <?php echo Form::displayError('oneplatewoocommerce_id');?>
                        </div>
                    </div>
                    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                    <input type="hidden" name="client_id" value="<?php echo $oneplate_clientid; ?>" />
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">&nbsp;</label>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">Import It</button>
                        </div>
                    </div>
                </form>
            </div>
            <hr/>
            <div class="row">
                <div class="col-md-3">&nbsp;</div>
                <div class="col-md-4">
                    <h3>Run Full Import</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">&nbsp;</div>
                <div class="col-md-4">
                    <p><button class="btn btn-primary" id="oneplate_full_import" data-function="importOnePlateOrders">Run It</button></p>
                </div>
            </div>
        </div>
        <div class="bs-callout bs-callout-primary bs-callout-more">
            <a name="freedom"></a>
            <div class="row">
                <div class="col-md-12">
                    <h2>Freedom Orders</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">&nbsp;</div>
                <div class="col-md-4">
                    <h3>Run Full Import From MYOB</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">&nbsp;</div>
                <div class="col-md-4">
                    <p><button class="btn btn-primary" id="freedom_full_import" data-function="importFreedomOrders">Run It</button></p>
                </div>
            </div>
        </div>
    </div>
</div>