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

    public function orderUpdate()
    {
        //echo "<pre>",print_r($this->request),"</pre>";
        $this->request->data['link'] = 'warehouse';
        $order = new OrdersController($this->request, $this->response);
        $order->orderUpdate();
    }

    public function orderDetail()
    {
        //echo "<pre>",print_r($this->request),"</pre>";
        $this->request->data['link'] = 'warehouse';
        $order = new OrdersController($this->request, $this->response);
        $order->orderDetail();
    }

    public function viewOrders()
    {
        //echo "<pre>",print_r($this->request->params['args']),"</pre>";die();
        $client_name = "All Production Clients";
        $courier_id = -1;
        $client_id = 0;
        $fulfilled = 0;
        $state = "";
        $ff = "Unfulfilled";
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client_name = $this->client->getClientName($client_id);
            }
            if(isset($this->request->params['args']['courier']))
            {
                $courier_id = $this->request->params['args']['courier'];
            }
            if(isset($this->request->params['args']['state']))
            {
                $state = $this->request->params['args']['state'];
            }
        }
        $page_title = "$ff Orders For $client_name";
        //$orders = $this->order->getUnfulfilledOrders($client_id, $courier_id, 0);     getAllOrders($client_id, $courier_id = -1, $fulfilled = 0, $store_order = -1)
        $orders = $this->order->getUnfulfilledProductionOrders($client_id, $courier_id, $state);
        //render the page
        Config::setJsConfig('curPage', "view-orders");
        Config::set('curPage', "view-orders");

        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/warehouseorders/", Config::get('VIEWS_PATH') . 'warehouseorders/viewOrders.php', [
            'page_title'    =>  $page_title,
            'pht'           =>  ": View Orders",
            'client_name'   =>  $client_name,
            'client_id'     =>  $client_id,
            'courier_id'    =>  $courier_id,
            'orders'        =>  $orders,
            'fulfilled'     =>  $fulfilled,
            'state'         =>  $state
        ]);

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