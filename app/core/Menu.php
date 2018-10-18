<?php
 /**
  * Menu class
  *
  * Constructs the navigation menu based on the user's permissions
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>
  */

class Menu{
    protected $icons = array(
        'orders'            =>  'fas fa-truck',
		'clients'	        =>	'fas fa-user-tie',
		'products'	        =>	'fas fa-dolly',
		'inventory'	        =>	'fas fa-tasks',
		'reports'	        =>	'far fa-chart-bar',
		'site-settings'	    =>	'fas fa-cog',
		'staff'		        =>	'fas fa-users',
        'stock-movement'    =>  'fas fa-dolly',
        'data-entry'        =>  'fas fa-indent',
        'sales-reps'		=>	'fas fa-users',
        'stores'            =>  'fas fa-store-alt',
        'downloads'         =>  'fas fa-download',
        'financials'        =>  'fas fa-file-invoice-dollar'
    );

    /**
     * Constructor
     *
     */
    private function __construct()
    {
        //$this->getPagesForUser();

    }

    public static function createMenu()
    {
        //echo "<pre>",print_r(Permission::$perms),"</pre>"; die();
        $string = "
            <div class='navbar-default sidebar' role='navigation'>
                <div class='sidebar-nav navbar-collapse'>
                    <ul class='nav' id='side-menu'>
						<li id='logo' class='text-center'>
                            <img src='/images/backgrounds/3pl_logo.png' />
                        </li>
                        <li id='dashboard'>
                            <a href='/dashboard'><i class='fa fas fa-home fa-fw'></i> Home</a>
                        </li>
                        <!-- user specific page links -->
        ";
        /* */
        foreach(Permission::$perms as $perm)
        {
            $string .= "<li>".print_r($perm, true)."</li>";
        }

        $string .= "
                        <!-- user specific page links -->
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
        ";
        return $string;
    }

    private function getPagesForUser()
    {
        //print_r(Permission::perms);
    }
}
?>