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

    public function manageDeliveries()
    {
        //echo "<pre>",print_r($this->request->params['args']),"</pre>";die();
        $client_name = "All Delivery Clients";
        $client_id = 0;
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client_name = $this->client->getClientName($client_id);
            }
        }
        $page_title = "Open Deliveries For $client_name";
        $deliveries = $this->delivery->getOpenDeliveries($client_id);
        //render the page
        Config::setJsConfig('curPage', "manage-deliveries");
        Config::set('curPage', "manage-deliveries");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/deliveries/", Config::get('VIEWS_PATH') . 'deliveries/managedeliveries.php', [
            'page_title'    =>  $page_title,
            'pht'           =>  ": Manage deliveries",
            'client_name'   =>  $client_name,
            'client_id'     =>  $client_id,
            'deliveries'    =>  $deliveries
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
        $role = Session::getUserRole();
        //$role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        $resource = "deliveries";
        Permission::allow(['admin','super admin'], $resource, [
            'index',
            'deliverySearch',
            'deliverySearchResults',
            'manageDeliveries',
            'managePickups'
        ]);
        //only for delivery clients
        if(Session::isDeliveryClientUser())
        {
            Permission::allow('client', $resource, [
                'index',
                'bookDelivery',
                'bookPickup',
                'deliverySearch',
                'deliverySearchResults',
                'viewDeliveries',
                'viewPickups'
            ]);

            //return true;
        }
        return Permission::check($role, $resource, $action);
        //return false;
    }
}
?>