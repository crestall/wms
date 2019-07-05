<?php

/**
 * Dashboard controller
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
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
        $store_orders = array();
        $solar_installs = array();
        $solar_service_jobs = array();
        $client_id = 0;
        $clients = array();
        $user_role = (Session::isAdminUser())? 'admin' : Session::getUserRole();;
        //if(Session::getUserRole() == "admin" || Session::getUserRole() == "super admin" || Session::getUserRole() == "warehouse")
        if($user_role == "admin" ||  $user_role == "warehouse")
        {
            $orders = $this->order->getCurrentOrders();
            $solar_installs = $this->solarorder->getCurrentOrders();
            //$pickups = $this->pickup->getCurrentPickups();
            $store_orders = $this->order->getCurrentStoreOrders();
        }
        /*
        elseif(Session::getUserRole() == "warehouse")
        {
            //$clients = $this->client->getAllClients();
            $orders = $this->order->getCurrentOrders();
        }
        */
        elseif($user_role == 'client')
        {
            $client_id = $this->user->getUserClientId( Session::getUserId() );
        }
        Config::setJsConfig('curPage', "dashboard");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/dashboard/", Config::get('VIEWS_PATH') . 'dashboard/index.php',[
            'client_id'             =>  $client_id,
            'orders'                =>  $orders,
            'clients'               =>  $clients,
            'solar_service_jobs'    =>  $solar_service_jobs,
            'store_orders'          =>  $store_orders,
            'solar_orders'          =>  $solar_installs,
            'user_role'             =>  $user_role
        ]);
    }

    public function isAuthorized(){
        return true;
    }
}