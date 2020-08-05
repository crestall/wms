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
        $client_id = 0;
        $clients = array();
        $user_role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        if($user_role == "admin" ||  $user_role == "warehouse")
        {
            $orders = $this->order->getCurrentOrders();
        }
        elseif($user_role == 'client')
        {
            $client_id = $this->user->getUserClientId( Session::getUserId() );
        }
        Config::setJsConfig('curPage', "dashboard");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/dashboard/", Config::get('VIEWS_PATH') . 'dashboard/index.php',[
            'pht'                   =>  ": Home Page",
            'client_id'             =>  $client_id,
            'orders'                =>  $orders,
            'clients'               =>  $clients,
            'user_role'             =>  $user_role
        ]);
    }

    public function isAuthorized(){
        return true;
    }
}