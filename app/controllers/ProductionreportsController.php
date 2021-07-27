<?php

/**
 * Production reports controller
 *

 Manages Production Reports

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class ProductionReportsController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }

    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'production-reports-index');
        parent::displayIndex(get_class());
    }

    public function warehouseOrders()
    {

    }

    public function isAuthorized()
    {
        $action = $this->request->param('action');
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        $resource = "productionreports";

        //only for admin
        Permission::allow('production admin', $resource, "*");
        //production users not allowed

        return Permission::check($role, $resource, $action);
    }
}