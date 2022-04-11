<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php/*
            echo "<p>isAdminUser: ".Session::isAdminUser()."</p>";
            echo "<p>isWarehouseUser: ".Session::isWarehouseUser()."</p>";
            echo "<p>isProductionUser: ".Session::isProductionUser()."</p>";
            echo "<p>isDeliveryClientUser: ".Session::isDeliveryClientUser()."</p>";
            echo "<p>role: ".Session::getUserRole()."</p>";
        */?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div id="help-centre-top" class="row">
            <div class="col-lg-6 offset-lg-3 col-md-8 offset-md-2  col-sm-10 offset-sm-1">
                <input type="search" class="form-control" id="help-centre-serach" placeholder="<i class='fa-light fa-magnifying-glass'></i> Search for help">
            </div>
        </div>
    </div>
</div>