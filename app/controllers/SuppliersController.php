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

    public function editSupplier()
    {
        $supplier_id = $this->request->params['args']['supplier'];
        $supplier_info = $this->productionsupplier->getSupplierById($supplier_id);
        //render the page
        Config::setJsConfig('curPage', "edit-supplier");
        Config::set('curPage', "edit-supplier");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/suppliers/", Config::get('VIEWS_PATH') . 'suppliers/editSupplier.php', [
            'page_title'    =>  "Update Supplier for Production",
            'pht'           =>  ": Update Supplier Customer",
            'supplier_id'   =>  $supplier_id,
            'supplier'      =>  $supplier_info
        ]);
    }

    public function addSupplier()
    {
        //render the page
        Config::setJsConfig('curPage', "add-supplier");
        Config::set('curPage', "add-supplier");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/suppliers/", Config::get('VIEWS_PATH') . 'suppliers/addSupplier.php', [
            'page_title'    =>  "Add Supplier for Production",
            'pht'           =>  ": Add Supplier Customer"
        ]);
    }

    public function viewSuppliers()
    {
        $active = (isset($this->request->params['args']['active']))? $this->request->params['args']['active'] : 1;

        $suppliers = $this->productionsupplier->getAllSuppliers($active);
        //render the page
        Config::setJsConfig('curPage', "view-suppliers");
        Config::set('curPage', "view-suppliers");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/suppliers/", Config::get('VIEWS_PATH') . 'suppliers/viewSuppliers.php', [
            'page_title'    =>  "View Production Suppliers",
            'pht'           =>  ": Production Suppliers",
            'active'        =>  $active,
            'suppliers'     =>  $suppliers
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