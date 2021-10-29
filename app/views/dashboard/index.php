<?php
$page_title = ": Home Page";
$card_classes = array(
    'primary',
    'secondary',
    'info',
    'success',
    'warning',
    'danger'
);
$c = 1;
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php //echo $user_role;
        if($user_role == "admin" || $user_role == "warehouse"):
            //---------------------------------------------------------------------------------------------------------
            //---------------------------------------     Warehouse Users     -----------------------------------------
            //--------------------------------------------------------------------------------------------------------
            //include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/warehouse_home.php");
            include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/warehouse_home_new.php");
        elseif($user_role == "client"):
            //--------------------------------------------------------------------------------------------------------
            //---------------------------------------     Client Users     ------------------------------------------
            //-------------------------------------------------------------------------------------------------------
            include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/client_home.php");
        elseif($user_role == "production"):
            //--------------------------------------------------------------------------------------------------------
            //---------------------------------------     Production Users     ------------------------------------------
            //-------------------------------------------------------------------------------------------------------
            include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/production_home.php");
        elseif($user_role == "production_admin"):
            //--------------------------------------------------------------------------------------------------------
            //----------------------------------     Production Admin Users     ------------------------------------------
            //-------------------------------------------------------------------------------------------------------
            include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/production_admin_home.php");
        elseif($user_role == "production_sales_admin"):
            //--------------------------------------------------------------------------------------------------------
            //----------------------------------     Production Sales Admin Users     ------------------------------------------
            //-------------------------------------------------------------------------------------------------------
            include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/production_sales_admin_home.php");
        elseif($user_role == "production_sales"):
            //--------------------------------------------------------------------------------------------------------
            //----------------------------------     Production Sales Users     ------------------------------------------
            //-------------------------------------------------------------------------------------------------------
            include(Config::get('VIEWS_PATH')."layout/page-includes/home_pages/production_sales_home.php");
        else:
            //--------------------------------------------------------------------------------------------------------
            //-----------------------------     User Classification Not Found     ---------------------------------------
            //-------------------------------------------------------------------------------------------------------?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <div class="row">
                            <div class="col-lg-2" style="font-size:96px">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="col-lg-6">
                                <h2>User Classification Error</h2>
                                <p>Sorry, there has been an error determining your access priviledges</p>
                                <p><a href="/login/logout">Please click here to login again</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>