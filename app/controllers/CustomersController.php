<?php

/**
 * Customers controller
 *

 Manages Production Customers

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class CustomersController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
        $this->Security->config("form", [ 'fields' => ['csrf_token']]);
    }

    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'customers-index');
        parent::displayIndex(get_class());
    }

    public function addCustomer()
    {
        //render the page
        Config::setJsConfig('curPage', "add-customer");
        Config::set('curPage', "add-customer");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/customers/", Config::get('VIEWS_PATH') . 'customers/addCustomer.php', [
            'page_title'    =>  "Add Customer for Production",
            'pht'           =>  ": Add Production Customer"
        ]);
    }

    public function editCustomer()
    {
        $customer_id = $this->request->params['args']['customer'];
        $customer_info = $this->productioncustomer->getCustomerById($customer_id);
        //render the page
        Config::setJsConfig('curPage', "edit-customer");
        Config::set('curPage', "edit-customer");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/customers/", Config::get('VIEWS_PATH') . 'customers/editCustomer.php', [
            'page_title'    =>  "Update Customer for Production",
            'pht'           =>  ": Update Production Customer",
            'customer_id'   =>  $customer_id,
            'customer'      =>  $customer_info
        ]);
    }

    public function viewCustomers()
    {
        //render the page
        Config::setJsConfig('curPage', "view-customers");
        Config::set('curPage', "view-customers");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/customers/", Config::get('VIEWS_PATH') . 'customers/viewCustomers.php', [
            'page_title'    =>  "View Production Customers",
            'pht'           =>  ": Production Customers"
        ]);
    }

    public function isAuthorized()
    {
        $action = $this->request->param('action');
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        $resource = "customers";

        //only for admin
        Permission::allow('production admin', $resource, "*");
        //production users
        Permission::allow('production', $resource, array(
            "index",
            "viewCustomers"
        ));

        return Permission::check($role, $resource, $action);
    }
}