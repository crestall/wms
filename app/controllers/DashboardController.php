<?php

/**
 * Dashboard controller
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class DashboardController extends Controller
{
    /**
     * show dashboard page
     *
     */
    public function index()
    {
        $orders = array();
        $production_orders = array();
        $backorders = array();
        $client_id = 0;
        $clients = array();
        $user_role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        if($user_role == "admin" ||  $user_role == "warehouse")
        {
            $orders = $this->order->getCurrentOrders();
            $backorders = $this->order->getCurrentBackorderOrders();
        }
        elseif($user_role == 'client')
        {
            $client_id = $this->user->getUserClientId( Session::getUserId() );
        }
        elseif($user_role == "production admin")
        {
            $production_orders = $this->order->getCurrentProductionOrders();
        }
        Config::setJsConfig('curPage', "dashboard");
        Config::set('curPage', "dashboard");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/dashboard/", Config::get('VIEWS_PATH') . 'dashboard/index.php',[
            'pht'                   =>  ": Home Page",
            'client_id'             =>  $client_id,
            'orders'                =>  $orders,
            'clients'               =>  $clients,
            'user_role'             =>  $user_role,
            'backorders'            =>  $backorders,
            'production_orders'     =>  $production_orders
        ]);
    }

    public function isAuthorized(){
        return true;
    }
}