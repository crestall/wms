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

    public function bookPickup()
    {
        $client_id = Session::getUserClientId();
        $client = $this->client->getClientInfo($client_id);
        //$attention = Session::getUsersName();;
        //render the page
        Config::setJsConfig('curPage', "book-pickup");
        Config::set('curPage', "book-pickup");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/deliveries/", Config::get('VIEWS_PATH') . 'deliveries/bookPickup.php', [
            'pht'           =>  ": Book a Pickup",
            'page_title'    =>  "Book A Pickup",
            'client'        =>  $client,
            'client_id'     =>  $client_id
            //'attention'     =>  $attention
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
        //mark them as viewed
        foreach($deliveries as $d)
        {
            //echo "<p>Gonna try and mark ".$d['id']." a viewed</p>";
            $this->delivery->markDeliveryViewed($d['id']);
        }
        //die();
        $deliveries = $this->delivery->getOpenDeliveries($client_id);
        //render the page
        Config::setJsConfig('curPage', "manage-deliveries");
        Config::set('curPage', "manage-deliveries");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/deliveries/", Config::get('VIEWS_PATH') . 'deliveries/manageDeliveries.php', [
            'page_title'    =>  $page_title,
            'pht'           =>  ": Manage deliveries",
            'client_name'   =>  $client_name,
            'client_id'     =>  $client_id,
            'deliveries'    =>  $deliveries
        ]);
    }

    public function managePickups()
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
        $page_title = "Open Pickups For $client_name";
        $pickups = $this->pickup->getOpenPickups($client_id);
        //mark them as viewed
        foreach($pickups as $p)
        {
            //echo "<p>Gonna try and mark ".$d['id']." a viewed</p>";
            $this->pickup->markPickupViewed($p['id']);
        }
        //die();
        $pickups = $this->pickup->getOpenPickups($client_id);
        //render the page
        Config::setJsConfig('curPage', "manage-pickups");
        Config::set('curPage', "manage-pickups");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/deliveries/", Config::get('VIEWS_PATH') . 'deliveries/managePickups.php', [
            'page_title'    =>  $page_title,
            'pht'           =>  ": Manage pickups",
            'client_name'   =>  $client_name,
            'client_id'     =>  $client_id,
            'pickups'       =>  $pickups
        ]);
    }

    public function managePickup()
    {
        Config::set('curPage', "manage-pickups");
        $pickup_info = array();
        if(!isset($this->request->params['args']['pickup']))
        {
            //no job id to update
            return (new SiteErrorsController())->siteError("noPickupId")->send();
            //return $this->noPickupId();
        }
        if(empty($pickup_info))
        {
            //no job data found
            return (new SiteErrorsController())->siteError("noPickupFound")->send();
            //return $this->noPickupFound();
        }
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

    public function viewPickups()
    {
        $client_id = Session::getUserClientId();
        $client = $this->client->getClientInfo($client_id);
        $pickups = $this->pickup->getOpenPickups($client_id);
        //echo "<pre>",print_r($deliveries),"</pre>";
        //render the page
        Config::setJsConfig('curPage', "view-pickups");
        Config::set('curPage', "view-pickups");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/deliveries/", Config::get('VIEWS_PATH') . 'deliveries/viewPickups.php', [
            'pht'           =>  ": View Pickups",
            'page_title'    =>  "Current Open Pickups For ".$client['client_name'],
            'client'        =>  $client,
            'client_id'     =>  $client_id,
            'pickups'       =>  $pickups
        ]);
    }

    private function noPickupFound()
    {
        //render the error page
        Config::setJsConfig('curPage', "errors");
        Config::set('curPage', "errors");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/errors/", Config::get('VIEWS_PATH') . 'errors/noPickupFound.php', []);
    }

    private function noPickupId()
    {
        //render the error page
        Config::setJsConfig('curPage', "errors");
        Config::set('curPage', "errors");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/jobs/", Config::get('VIEWS_PATH') . 'errors/noShipmentFound.php', []);
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
            'manageDelivery',
            'managePickups',
            'managePickup'
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