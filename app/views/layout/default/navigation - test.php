<?php
    $user_role = (Session::isAdminUser())? 'admin' : Session::getUserRole();getUserRole();
    if(empty($user_role))
        //return $this->controller->redirector->login();
        return;
    $pages = Config::get(strtoupper($user_role."_PAGES"));
	$icons = Config::get("MENU_ICONS");
    $user_info = $this->controller->user->getProfileInfo(Session::getUserId());
?>

		<!-- Navigation -->
    <nav class="navbar navbar-inverse sidebar" role="navigation" style="margin-bottom: 0">
        <div class="container-fluid">
            <div class="navbar-header">
                <img class="logo" src="/images/backgrounds/3pl_logo.png" />
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <!-- /.navbar-header -->
            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        Hello,<strong> <?php echo Session::getUsersName(); ?></strong> <img class="img-user" src="<?php echo $user_info['image'];?>" />  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="/user/profile"><i class="fa fa-user fa-fw"></i> Profile</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="/login/logOut"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav" id="side-menu">
                    <li id="dashboard">
                        <a href="/dashboard"><i class="fa fas fa-home fa-fw"></i> Home</a>
                    </li>
                    <?php foreach($pages as $section => $pages):
                        $Section = ucwords(str_replace("-", " ", $section));?>
                        <li id="<?php echo $section;?>" class="collapse">
                            <a href="#" aria-expanded="false"><i class="fa <?php echo $icons[$section];?> fa-fw"></i> <?php echo $Section;?><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level collapse" aria-expanded="false">
                                <?php
                                $mpages =  array_keys($pages);
                                asort($mpages);
                                foreach($mpages as $p):
                                    if($pages[$p]):?>
                                        <li><a href="<?php echo "/$section/$p";?>"><?php echo ucwords(str_replace("-", " ", $p));?></a></li>
                                    <?php endif;?>
                                <?php endforeach;?>
                            </ul>
                        </li>
                    <?php endforeach;?>
                </ul>
            </div>
        </div>
    </nav>