<?php
$icons = Config::get("MENU_ICONS");
$app_type = "Warehouse";
if(Session::getIsLoggedIn()):
    //echo "<pre>",print_r($_SESSION),"</pre>";
    $user_role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
    if(empty($user_role))
        return;
    if( Session::isProductionUser() )
        $app_type = "Production";
    $user_role = str_replace(" ","_", $user_role);
    $super_admin = (Session::getUserRole() == "super admin");
    //echo strtoupper($user_role."_PAGES");
    $pages = Config::getPages(strtoupper($user_role."_PAGES"));
    $user_info = $this->controller->user->getProfileInfo(Session::getUserId());
    $image = $user_info['image'];
else:
    $pages = array();
    $image = "/images/profile_pictures/default.png";
endif;
//echo "<pre>",print_r($pages),"</pre>";die();
//echo "<pre>",print_r($_SESSION),"</pre>";
?>
<!-- Navigation -->
<nav class="navbar navbar-expand-lg fixed-top navbar-dark" style="background-color: transparent; height:120px;">
    <a href="/" class="navbar-brand" rel="home" itemprop="url">
        <img width="130" src="/images/FSG_logo@130px.png" class="custom-logo" alt="FSG" style="display:none;" title="WMS Home" />
        <img width="130" src="/images/FSG_logo_white@130px.png" class="custom-logo-transparent" alt="FSG logo" title="WMS Home" />
    </a>
    <button id="navbar_toggler" class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <?php if(isset($pages) && !empty($pages) && count($pages)):?>
                <?php foreach($pages as $section => $spages):
                    if( (isset($pages[$section]['super_admin_only']) && $pages[$section]['super_admin_only'] == true) )
                    {
                        if(Session::getUserRole() != "super admin")
                            continue;
                    }
                    if( isset($pages[$section]['delivery-clients']) )
                    {
                        if( $pages[$section]['delivery-clients'] && !Session::isDeliveryClientUser())
                            continue;
                        if( !$pages[$section]['delivery-clients'] && Session::isDeliveryClientUser())
                            continue;
                    }
                    if($pages[$section][$section."-index"]):
                        $Section = ucwords(str_replace("-", " ", $section));?>
                        <li id="<?php echo $section;?>" class="nav-item">
                            <a href="<?php echo "/$section/";?>" class="nav-link"><?php echo $Section;?></a>
                        </li>
                    <?php endif;?>
                <?php endforeach;?>
            <?php endif;?>
        </ul>
    </div>
    <ul class="navbar user-info">
        <li class="nav-item dropdown">
            <a class="dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img class="img-user" src="<?php echo $image;?>" /><br/>
                <strong><?php echo Session::getUsersName(); ?></strong>
            </a>
            <div id="contact-link"><a href="/contact/contact-us/" class="nav-link"><i class="fad fa-envelope-open"></i> Contact Us</a></div>
            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                <a href="/user/profile" class="dropdown-item"><i class="fa fa-user fa-fw"></i> Profile</a>
                <a href="/login/logOut" class="dropdown-item"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
            </div>
            <?php if(Config::get('curPage') == "dashboard" || Config::get('curPage') == "view-jobs" ):?>
                <div id="countdown" class="text-white">Page will refresh in <span></span></div>
            <?php else:?>
                <div id="countdown" class="text-white">This page does not refresh<span></span></div>
            <?php endif;?>
        </li>
    </ul>
</nav>
<!-- End Navigation -->
<!-- Common Page Header -->
<div id="page_header" class="row">
    <div class="col-lg-12">
        <h1>FSG <?php echo $app_type;?> Management System</h1>
    </div>
</div>
