<?php

/**
 * warehouse orders (for production) controller
 *

 Allows production to manage orders in the warehouse

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class WarehouseOrdersController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }

    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'warehouse-orders-index');
        parent::displayIndex(get_class());
    }

    public function isAuthorized()
    {
        $action = $this->request->param('action');
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        $resource = "warehouseorders";
        //only for admin
        Permission::allow('production admin', $resource, "*");
        //other production users not allowed

        return Permission::check($role, $resource, $action);
    }
}