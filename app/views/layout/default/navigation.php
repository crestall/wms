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
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                
            </div>
            <!-- /.navbar-header -->
			<?php //echo "<pre>",print_r($icons),"</pre>";?>
            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        Hello,<strong> <?php echo Session::getUsersName(); ?></strong> <img class="img-user" src="<?php echo $image;?>" />  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="/user/profile"><i class="fa fa-user fa-fw"></i> Profile</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="/login/logOut"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
			
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
						<li id="logo" class="text-center">
                            <img src="/images/backgrounds/3pl_logo.png" />
                        </li>
                        <li id="dashboard">
                            <a href="/dashboard"><i class="fa fas fa-home fa-fw"></i> Home</a>
                        </li>
                        <?php if(count($pages)):?>
                            <?php foreach($pages as $section => $spages):
                                if( (isset($pages[$section]['super_admin_only']) && $pages[$section]['super_admin_only'] == true) )
                                {
                                    if(Session::getUserRole() != "super admin")
                                        continue;
                                }
                                $Section = ucwords(str_replace("-", " ", $section));?>
                                <li id="<?php echo $section;?>" class="collapse">
                                    <a href="#" aria-expanded="false"><i class="fa <?php echo $icons[$section];?> fa-fw"></i> <?php echo $Section;?><span class="fa arrow"></span></a>
                                    <ul class="nav nav-second-level collapse" aria-expanded="false">
                                        <?php
                                        $mpages =  array_keys($spages);
                                        asort($mpages);
                                        foreach($mpages as $p):
                                            //if($pages[$p] && Permission::check($user_role, $section, Utility::toCamelCase($p), array(), false)):
                                            if($p == 'super_admin_only')
                                                continue;
                                            if($spages[$p]):?>
                                                <li><a href="<?php echo "/$section/$p";?>"><?php echo ucwords(str_replace("-", " ", $p));?></a></li>
                                            <?php endif;?>
                                        <?php endforeach;?>
                                    </ul>
                                </li>
                            <?php endforeach;?>
                        <?php endif;?>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>