<?php

/**
 * Deliveries controller
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class DeliveriesController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
        $this->Security->config("form", [ 'fields' => ['csrf_token']]);
    }

    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'deliveries-index');
        parent::displayIndex(get_class());
    }

    public function bookDelivery()
    {
        $client_id = Session::getUserClientId();
        $client = $this->client->getClientInfo($client_id);
        $attention = Session::getUsersName();;
        //render the page
        Config::setJsConfig('curPage', "book-delivery");
        Config::set('curPage', "book-delivery");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/deliveries/", Config::get('VIEWS_PATH') . 'deliveries/bookDelivery.php', [
            'pht'           =>  ": Book a Delivery",
            'page_title'    =>  "Book A Delivery",
            'client'        =>  $client,
            'client_id'     =>  $client_id,
            'attention'     =>  $attention
        ]);
    }

    public function viewDeliveries()
    {
        $client_id = Session::getUserClientId();
        $client = $this->client->getClientInfo($client_id);
        $deliveries = $this->delivery->getOpenDeliveries($client_id);
        //echo "<pre>",print_r($deliveries),"</pre>";
        //render the page
        Config::setJsConfig('curPage', "view-deliveries");
        Config::set('curPage', "view-deliveries");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/deliveries/", Config::get('VIEWS_PATH') . 'deliveries/viewDeliveries.php', [
            'pht'           =>  ": View Deliveries",
            'page_title'    =>  "Current Open Deliveries For ".$client['client_name'],
            'client'        =>  $client,
            'client_id'     =>  $client_id,
            'deliveries'    =>  $deliveries
        ]);
    }

    public function isAuthorized(){
        $action = $this->request->param('action');
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        $resource = "deliveries";
        Permission::allow('admin', $resource, [
            'index',
            'delivery-search',
            'delivery-search-results',
            'manage-deliveries'
        ]);
        //only for delivery clients
        if(Session::isDeliveryClientUser())
        {
            Permission::allow('client', $resource, [
                'index',
                'book-delivery',
                'delivery-search',
                'delivery-search-results',
                'view-deliveries'
            ]);

            //return true;
        }
        return Permission::check($role, $resource, $action);
        //return false;
    }
}
?>