<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php
            echo "<p>isAdminUser: ".Session::isAdminUser()."</p>";
            echo "<p>isWarehouseUser: ".Session::isWarehouseUser()."</p>";
            echo "<p>isProductionUser: ".Session::isProductionUser()."</p>";
            echo "<p>isDeliveryClientUser: ".Session::isDeliveryClientUser()."</p>";
            echo "<p>role: ".Session::getUserRole()."</p>";
        ?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    </div>
</div>