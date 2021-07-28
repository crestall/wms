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
        $client_name = "All Production Clients";
        $status_id = 0;
        $client_id = 0;
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('first day of this month');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $orders = array();
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client_name = $this->client->getClientName($client_id);
            }
            if(isset($this->request->params['args']['status']))
            {
                $status_id = $this->request->params['args']['status'];
            }
        }
        $orders = $this->order->getProductionOrders($client_id, $status_id, $from, $to);
        Config::setJsConfig('curPage', "warehouse-orders");
        Config::set('curPage', "warehouse-orders");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/productionreports/", Config::get('VIEWS_PATH') . 'productionreports/warehouseOrders.php',[
            'page_title'    =>  'Orders in Warehouse For '.$client_name,
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name,
            'status_id'     => $status_id,
            'orders'        =>  $orders,
            'from'          => $from,
            'to'            => $to
        ]);
    }

    public function orderTracking()
    {
        $order_id = 0;
        $order = array();
        $courier = $order_status = "";
        $tracking = array();
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['order']))
            {
                $order_id = $this->request->params['args']['order'];
                $order = $this->order->getOrderDetail($order_id);
                $courier = $this->courier->getCourierName($order['courier_id']);
                $order_status = $this->order->getStatusName($order['status_id']);
                if($courier == "eParcel" || $courier == "eParcel Express")
                {
                    $eparcel = $this->client->getEparcelClass($order['client_id']);
                    $tracking = $this->$eparcel->GetTracking($order['consignment_id']);
                    //echo "eParcel<pre>",print_r($tracking),"</pre>";die();
                }
                elseif($courier == "Direct Freight")
                {
                    $tracking = $this->directfreight->trackConsignment($order['consignment_id']);
                }
            }
        }
        //render the page
        Config::setJsConfig('curPage', "order-tracking");
        Config::set('curPage', "order-tracking");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/productionreports/", Config::get('VIEWS_PATH') . 'productionreports/orderTracking.php', [
            'page_title'    =>  "Tracking and Details for ".$order['order_number'],
            'pht'           =>  ": Order Tracking",
            'order_id'      =>  $order_id,
            'order'         =>  $order,
            'courier'       =>  $courier,
            'order_status'  =>  $order_status,
            'tracking'      =>  $tracking
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
            'orderTracking',
            'warehouseOrders'
        ]);
        //production users not allowed

        return Permission::check($role, $resource, $action);
    }
}