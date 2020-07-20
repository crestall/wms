<?php
$icons = Config::get("MENU_ICONS");
if(Session::getIsLoggedIn()):
    //echo "<pre>",print_r($_SESSION),"</pre>";
    $user_role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
    if(empty($user_role))
        //return $this->controller->redirector->login();
        return;
    $user_role = str_replace(" ","_", $user_role);
    //echo strtoupper($user_role."_PAGES");
    $pages = Config::get(strtoupper($user_role."_PAGES"));
    $user_info = $this->controller->user->getProfileInfo(Session::getUserId());
    $image = $user_info['image'];
else:
    $pages = array();
    $image = "/images/profile_pictures/default.png";
endif;
?>
<!-- Navigation -->
<nav class="navbar navbar-expand-md fixed-top navbar-dark" style="background-color: transparent; height:80px;">
    <a class="navbar-brand" href="#">
    <a href="https://www.fsg.com.au/" class="navbar-brand" rel="home" itemprop="url">
        <img width="131" height="39" src="/images/FSG-logo-131x39px.png" class="custom-logo" alt="FSG" itemprop="logo" />
        <img width="131" height="39" src="/images/FSG-logo-131x39px-wh.png" class="custom-logo-transparent" alt="FSG logo" itemprop="logo" /></a>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Features</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Pricing</a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#">Disabled</a>
            </li>
        </ul>
    </div>
</nav>
<!-- End Navigation -->
<!-- Common Page Header -->
<div id="page_header" class="row">
    <div class="col-lg-12">
        <h1>Film Shot Graphics Warehouse Management System</h1>
    </div>
</div>
