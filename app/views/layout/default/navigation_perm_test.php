<?php
$icons = Config::get("MENU_ICONS");
if(Session::getIsLoggedIn()):
    //echo "<pre>",print_r($_SESSION),"</pre>";
    $user_role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
    if(empty($user_role))
        //return $this->controller->redirector->login();
        return;
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
            <?php echo Menu::createMenu();?>
            <!-- /.navbar-static-side -->
        </nav>