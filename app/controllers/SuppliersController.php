<?php

/**
 * Suppliers controller
 *

 Manages Production Suppliers

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class SuppliersController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
        $this->Security->config("form", [ 'fields' => ['csrf_token']]);
    }

    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'suppliers-index');
        parent::displayIndex(get_class());
    }

    public function addSupplier()
    {
        //render the page
        Config::setJsConfig('curPage', "add-supplier");
        Config::set('curPage', "add-supplier");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/jobs/", Config::get('VIEWS_PATH') . 'jobs/addSupplier.php', [
            'page_title'    =>  "Add Supplier for Production",
            'pht'           =>  ": Add Supplier Customer"
        ]);
    }

    public function viewSuppliers()
    {
        //render the page
        Config::setJsConfig('curPage', "view-suppliers");
        Config::set('curPage', "view-suppliers");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/jobs/", Config::get('VIEWS_PATH') . 'jobs/viewSuppliers.php', [
            'page_title'    =>  "View Production Suppliers",
            'pht'           =>  ": Production Suppliers"
        ]);
    }

    public function isAuthorized()
    {
        $action = $this->request->param('action');
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        $resource = "suppliers";

        //only for admin
        Permission::allow('production admin', $resource, "*");
        //production users
        Permission::allow('production', $resource, array(
            "index",
            "viewSuppliers"
        ));

        return Permission::check($role, $resource, $action);
    }
}