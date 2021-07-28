<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="border border-secondary p-3 m-3 rounded bg-light">
            <h3>Filter These Orders</h3>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/between_dates.php");?>
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="form-group">
                        <label>Filter By Client</label>
                        <select id="client_selector" class="form-control selectpicker" data-style="btn-outline-secondary"><option value="0">All Production Clients</option><?php echo $this->controller->client->getSelectProductionClients($client_id);?></select>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="form-group">
                        <label>Filter By Status</label>
                        <select id="status_selector" class="form-control selectpicker" data-style="btn-outline-secondary"><option value="0">All Status</option><?php echo $this->controller->order->getSelectStatuses($status_id);?></select>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="form-group">
                        <label>Search</label>
                        <input type="text" class="form-control" id="table_searcher" />
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <a href="/production-reports/warehouse-orders" class="btn btn-outline-danger" >Remove Filters</a>
                    </div>
                </div>



            </div>
        </div>
    </div>
</div>