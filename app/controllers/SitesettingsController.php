<?php

/**
 * Site Settings controller
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class SiteSettingsController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }

    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'site-settings-index');
        parent::displayIndex(get_class());
    }

    public function warehouseLocations()
    {
        $active = (isset($this->request->params['args']['active']))? $this->request->params['args']['active'] : 1;
        $sites = $this->site->getAllSites($active);
        //render the page
        Config::setJsConfig('curPage', "warehouse-locations");
        Config::set('curPage', "warehouse-locations");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/sitesettings/", Config::get('VIEWS_PATH') . 'sitesettings/warehouseLocations.php',
        [
            'pht'           =>  ": Manage Sites",
            'page_title'    =>  'Manage Sites',
            'sites'         =>  $sites,
            'active'        =>  $active
        ]);
    }

    public function locations()
    {
        $active = (isset($this->request->params['args']['active']))? $this->request->params['args']['active'] : 1;
        $site = (isset($this->request->params['args']['site']))? $this->request->params['args']['site'] : 0;
        $locations = $this->location->getAllLocations($site, $active);
        //render the page
        Config::setJsConfig('curPage', "locations");
        Config::set('curPage', "locations");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/sitesettings/", Config::get('VIEWS_PATH') . 'sitesettings/locations.php',
        [
            'pht'           =>  ": Manage Locations",
            'page_title'    =>  'Manage Locations',
            'locations'     =>  $locations,
            'active'        =>  $active
        ]);
    }

    public function manageUsers()
    {
        //$client_users = $this->user->getAllUsers('client');
        //$admin_users = $this->user->getAllUsers('admin');
        $user_roles = $this->user->getUserRoles();
        $active = (isset($this->request->params['args']['active']))? $this->request->params['args']['active'] : 1;
        //render the page
        Config::setJsConfig('curPage', "manage-users");
        Config::set('curPage', "manage-users");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/sitesettings/", Config::get('VIEWS_PATH') . 'sitesettings/manageUsers.php',
        [
            'page_title'    =>  'Manage Users',
            'user_roles'    =>  $user_roles,
            'active'        =>  $active
        ]);
    }

    public function orderStatus()
    {
        //render the page
        Config::setJsConfig('curPage', "order-status");
        Config::set('curPage', "order-status");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/default/", Config::get('VIEWS_PATH') . 'sitesettings/orderStatus.php',
        [
            'page_title'    =>  'Manage Order Status Labels'
        ]);
    }

    public function staff()
    {
        //render the page
        Config::setJsConfig('curPage', "staff");
        Config::set('curPage', "staff");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/default/", Config::get('VIEWS_PATH') . 'sitesettings/staff.php',
        [
            'page_title'    =>  'Manage Staff Records'
        ]);
    }

    public function stockMovementReasons()
    {
        //render the page
        $reasons = $this->stockmovementlabels->getMovementLabels();
        Config::setJsConfig('curPage', "stock-movement-reasons");
        Config::set('curPage', "stock-movement-reasons");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/sitesettings/", Config::get('VIEWS_PATH') . 'sitesettings/stockMovementReasons.php',
        [
            'page_title'    =>  'Manage Stock Movement Reasons',
            'reasons'        =>  $reasons
        ]);
    }

    public function deliveryUrgencies()
    {
        //render the page
        $urgencies = $this->deliveryurgency->getUrgencies();
        Config::setJsConfig('curPage', "delivery-urgencies");
        Config::set('curPage', "delivery-urgencies");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/sitesettings/", Config::get('VIEWS_PATH') . 'sitesettings/deliveryUrgencies.php',
        [
            'pht'           =>  ": Urgencies Edit",
            'page_title'    =>  'Manage Delivery/Pickup Urgencies',
            'urgencies'     =>  $urgencies
        ]);
    }

    public function couriers()
    {
        $couriers = $this->courier->getCouriers();
        //render the page
        Config::setJsConfig('curPage', "couriers");
        Config::set('curPage', "couriers");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/sitesettings/", Config::get('VIEWS_PATH') . 'sitesettings/couriers.php',[
            'page_title'    =>  'Manage Couriers',
            'couriers'      =>  $couriers
        ]);
    }

    public function drivers()
    {
        parent::manageDrivers('site');
    }

    public function userRoles()
    {
        $roles = $this->user->getUserRoles();
        //render the page
        Config::setJsConfig('curPage', "user-roles");
        Config::set('curPage', "user-roles");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/sitesettings/", Config::get('VIEWS_PATH') . 'sitesettings/userRoles.php',[
            'page_title'  =>  'Manage User Roles',
            'roles'       =>  $roles
        ]);
    }

    public function isAuthorized(){
        $role = Session::getUserRole();
        $action = $this->request->param('action');
        $resource = "sitesettings";
        // only for super admins
        Permission::allow('super admin', $resource, ['*']);
        // all other admins
        Permission::allow(['admin'], $resource, [
            'index',
            'deliveryUrgencies',
            'drivers',
            'locations',
            'stockMovementReasons'
        ]);

        //echo "<pre>",print_r(Permission::$perms),"</pre>"; die();
        return Permission::check($role, $resource, $action);
    }
}
?>