<?php

?>
<input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <!--div class="row view-orders-buttons" >
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <p><a class="btn btn-sm btn-block btn-outline-warning consolidate-orders"><i class="fad fa-sign-in"></i> Consolidate Selected Deliveries</a></p>
            </div>
            <?php if($user_role == "admin" || $user_role == "super admin"):?>
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                    <p><a class="btn btn-sm btn-block btn-outline-danger cancel-order"><i class="fas fa-ban"></i> Cancel Selected Deliveries</a></p>
                </div>
            <?php endif;?>
        </div-->
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="form-group">
                    <label>Filter By Client</label>
                    <select id="client_selector" class="form-control selectpicker" data-style="btn-outline-secondary"><option value="0">All Delivery Clients</option><?php echo $this->controller->client->getSelectDeliveryClients($client_id);?></select>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="form-group">
                    <label>Live Text Filter</label>
                    <input type="search" class="form-control" id="table_searcher" placeholder="Type to Filter" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <?php if(isset($_SESSION['feedback'])) :?>
                   <div class='feedbackbox'><?php echo Session::getAndDestroy('feedback');?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['errorfeedback'])) :?>
                   <div class='errorbox'><?php echo Session::getAndDestroy('errorfeedback');?></div>
                <?php endif; ?>
            </div>
        </div>
        <?php if(count($deliveries)):?>
            <div id="waiting" class="row">
                <div class="col-lg-12 text-center">
                    <h2>Drawing Table..</h2>
                    <p>May take a few moments</p>
                    <img class='loading' src='/images/preloader.gif' alt='loading...' />
                </div>
            </div>
            <div class="row" id="table_holder" style="display:none">
                <div class="col-12">
                    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/manage_deliveries_table.php");?>
                </div>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2><i class="fas fa-exclamation-triangle"></i> No Open Deliveries Found To Manage</h2>
                        <p>You can search for deliveries <a href="/deliveries/delivery-search">here</a></p>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>