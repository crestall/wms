<?php

/**
 * Site Settings controller
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class sitesettingsController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }

    public function locations()
    {
        $locations = $this->location->getAllLocations();
        //render the page
        Config::setJsConfig('curPage', "locations");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/sitesettings/", Config::get('VIEWS_PATH') . 'sitesettings/locations.php',
        [
            'page_title'    =>  'Manage Locations',
            'locations'     =>  $locations
        ]);
    }

    public function manageUsers()
    {
        $client_users = $this->user->getAllUsers('client');
        $admin_users = $this->user->getAllUsers('admin');
        $user_roles = $this->user->getUserRoles();
        $active = (isset($this->request->params['args']['active']))? $this->request->params['args']['active'] : -1;
        //render the page
        Config::setJsConfig('curPage', "manage-users");
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
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/default/", Config::get('VIEWS_PATH') . 'sitesettings/orderStatus.php',
        [
            'page_title'    =>  'Manage Order Status Labels'
        ]);
    }

    public function staff()
    {
        //render the page
        Config::setJsConfig('curPage', "staff");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/default/", Config::get('VIEWS_PATH') . 'sitesettings/staff.php',
        [
            'page_title'    =>  'Manage Staff Records'
        ]);
    }

    public function stockMovementReasons()
    {
        //render the page
        Config::setJsConfig('curPage', "stock-movement-reasons");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/default/", Config::get('VIEWS_PATH') . 'sitesettings/stockMovementReasons.php',
        [
            'page_title'    =>  'Manage Stock Movement Reasons'
        ]);
    }

    public function packingTypes()
    {
        //render the page
        Config::setJsConfig('curPage', "packing-types");
        $packings = $this->packingtype->getPackingTypes();
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/sitesettings/", Config::get('VIEWS_PATH') . 'sitesettings/packingTypes.php',[
            'page_title'    =>  'Manage Packing Types',
            'packings'      =>  $packings
        ]);
    }

    public function storeChains()
    {
        $chains = $this->storechain->getChains();
        //render the page
        Config::setJsConfig('curPage', "store-chains");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/sitesettings/", Config::get('VIEWS_PATH') . 'sitesettings/storeChains.php',[
            'page_title'    =>  'Manage Store Chains',
            'chains'      =>  $chains
        ]);
    }

    public function userRoles()
    {
        $roles = $this->user->getUserRoles();
        //render the page
        Config::setJsConfig('curPage', "user-roles");
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
        Permission::allow(['admin', 'md admin'], $resource, [
            'addSalesRep',
            'editSalesRep',
            'locations',
            'manageUsers',
            'orderStatus',
            'packingType',
            'staff',
            'stockMovementReasons',
            'storeChains'
        ]);

        //echo "<pre>",print_r(Permission::$perms),"</pre>"; die();
        return Permission::check($role, $resource, $action);
    }
}
?>