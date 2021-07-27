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
        $client_name = "All Clients";
        $courier_id = -1;
        $client_id = 0;
        $fulfilled = 0;
        $orders = array();
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client_name = $this->client->getClientName($client_id);
            }
        }
        Config::setJsConfig('curPage', "warehouse-orders");
        Config::set('curPage', "warehouse-orders");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/productionreports/", Config::get('VIEWS_PATH') . 'productionreports/warehouseOrders.php',[
            'page_title'    =>  'Production Jobs in Warehouse',
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name,
            'orders'        =>  $orders
        ]);
    }

    public function isAuthorized()
    {
        $action = $this->request->param('action');
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        $resource = "productionreports";

        //only for admin
        Permission::allow('production admin', $resource, "*");
        //production sales users
        Permission::allow(['production sales', 'production sales admin'], $resource, [
            'index',
            'warehouseOrders'
        ]);
        //production users not allowed

        return Permission::check($role, $resource, $action);
    }
}