<?php

?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-md-2">
            <p><a class="btn btn-primary" href="#ndc">Natural Distilling Co</a> </p>
        </div>
        <div class="col-md-2">
            <p><a class="btn btn-primary" href="#nuchev">Nuchev</a></p>
        </div>
        <div class="col-md-2">
            <p><a class="btn btn-primary" href="#nuchev_samples">Nuchev Samples</a> </p>
        </div>
        <div class="col-md-2">
            <p><a class="btn btn-primary" href="#noa">Noa Sleep</a></p>
        </div>
        <div class="col-md-2">
            <p><a class="btn btn-primary" href="#tt_au">Twin Towers Australia</a></p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
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

    <div class="bs-callout bs-callout-primary bs-callout-more">
        <a name="ndc"></a>
        <div class="row">
            <div class="col-md-12">
                <h2>Natural Distilling Co Orders</h2>
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
                <p><button class="btn btn-primary" id="ndc_full_import" data-function="importNaturalDistillingOrders">Run It</button></p>
            </div>
        </div>
    </div>

    <!--div class="bs-callout bs-callout-primary bs-callout-more">
        <a name="team_timbuktu"></a>
        <div class="row">
            <div class="col-md-12">
                <h2>Team Timbuktu Orders</h2>
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
                <p><button class="btn btn-primary" id="tt_full_import" data-function="importTeamTimbuktuOrders">Run It</button></p>
            </div>
        </div>
    </div-->
    <div class="bs-callout bs-callout-primary bs-callout-more">
        <a name="tt_au"></a>
        <div class="row">
            <div class="col-md-12">
                <h2>TT Australia Orders</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">&nbsp;</div>
            <div class="col-md-4">
                <h3>Import single Order</h3>
            </div>
        </div>
        <div class="row">
            <form id="ttau_single_import" action="/orders/importTTOrder" method="post">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">WooCommerce Order ID</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required" name="ttwoocommerce_id" id="ttwoocommerce_id" value="<?php echo Form::value('ttwoocommerce_id');?>" />
                        <?php echo Form::displayError('ttwoocommerce_id');?>
                    </div>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <input type="hidden" name="client_id" value="<?php echo $ttau_clientid; ?>" />
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
                <p><button class="btn btn-primary" id="ttau_full_import" data-function="importTTAUOrders">Run It</button></p>
            </div>
        </div>
    </div>
    <div class="bs-callout bs-callout-primary bs-callout-more">
        <a name="nuchev"></a>
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
            <form id="nuchev_single_import" action="/orders/importNuchevOrder" method="post">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">WooCommerce Order ID</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required" name="nuchevwoocommerce_id" id="nuchevwoocommerce_id" value="<?php echo Form::value('nuchevwoocommerce_id');?>" />
                        <?php echo Form::displayError('nuchevwoocommerce_id');?>
                    </div>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <input type="hidden" name="client_id" value="<?php echo $nuchev_clientid; ?>" />
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
                <p><button class="btn btn-primary" id="nuchev_full_import" data-function="importNuchevOrders">Run It</button></p>
            </div>
        </div>
    </div>
    <div class="bs-callout bs-callout-primary bs-callout-more">
        <a name="noa"></a>
        <div class="row">
            <div class="col-md-12">
                <h2>Noa Sleep Orders</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">&nbsp;</div>
            <div class="col-md-4">
                <h3>Import single Order</h3>
            </div>
        </div>
        <div class="row">
            <form id="noa_single_import" action="/orders/importNoaOrder" method="post">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">WooCommerce Order ID</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control required" name="noawoocommerce_id" id="noawoocommerce_id" value="<?php echo Form::value('noawoocommerce_id');?>" />
                        <?php echo Form::displayError('noawoocommerce_id');?>
                    </div>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <input type="hidden" name="client_id" value="<?php echo $noa_clientid; ?>" />
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
                <p><button class="btn btn-primary" id="noa_full_import" data-function="importNoaOrders">Run It</button></p>
            </div>
        </div>
    </div>
    <div class="bs-callout bs-callout-primary bs-callout-more">
        <a name="nuchev_samples"></a>
        <div class="row">
            <div class="col-md-12">
                <h2>Nuchev Samples</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">&nbsp;</div>
            <div class="col-md-4">
                <h3>Collect Sample Requests</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">&nbsp;</div>
            <div class="col-md-4">
                <p><button class="btn btn-primary" id="nuchev_samples">Collect Sample Requests</button></p>
            </div>
        </div>
    </div>
</div>