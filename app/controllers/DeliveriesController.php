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

    public function adjustDelivery()
    {
        Config::set('curPage', "adjust-delivery");
        Config::setJsConfig('curPage', "adjust-delivery");
        if(!isset($this->request->params['args']['delivery']))
        {
            //no delivery id to view
            (new SiteErrorsController())->siteError("noDeliveryId")->send();
            return;
        }
        $delivery_id = $this->request->params['args']['delivery'];
        $delivery = $this->delivery->getDeliveryDetails($delivery_id);
        if(empty($delivery))
        {
            //no delivery data found
            (new SiteErrorsController())->siteError("noDeliveryFound")->send();
            return;
        }
    }

    public function addDelivery()
    {
        $client_id = 0;
        $client_name = "";
        $client = array();
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client = $this->client->getClientInfo($client_id);
            }
        }
        $page_title = "Open Deliveries For $client_name";
        //render the page
        Config::setJsConfig('curPage', "add-delivery");
        Config::set('curPage', "add-delivery");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/deliveries/", Config::get('VIEWS_PATH') . 'deliveries/addDelivery.php', [
            'pht'           =>  ": Enter a Delivery",
            'page_title'    =>  "Manually Add A Delivery",
            'client_id'     =>  $client_id,
            'client'        =>  $client
        ]);
    }

    public function addPickup()
    {
        $client_id = 0;
        $client_name = "";
        $client = array();
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client = $this->client->getClientInfo($client_id);
            }
        }
        $page_title = "Open Deliveries For $client_name";
        //render the page
        Config::setJsConfig('curPage', "add-pickup");
        Config::set('curPage', "add-pickup");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/deliveries/", Config::get('VIEWS_PATH') . 'deliveries/addPickup.php', [
            'pht'           =>  ": Enter a Pickup",
            'page_title'    =>  "Manually Add A Pickup",
            'client_id'     =>  $client_id,
            'client'        =>  $client
        ]);
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
        Config::set('curPage', "manage-pickup");
        Config::setJsConfig('curPage', "manage-pickup");
        if(!isset($this->request->params['args']['pickup']))
        {
            //no pickup id to update
            (new SiteErrorsController())->siteError("noPickupId")->send();
            return;
        }
        $pickup_id = $this->request->params['args']['pickup'];
        $pickup = $this->pickup->getPickupDetails($pickup_id);
        if(empty($pickup))
        {
            //no pickup data found
            (new SiteErrorsController())->siteError("noPickupFound")->send();
            return;
        }
        $client = $this->client->getClientInfo($pickup['client_id']);
        //render the page
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/deliveries/", Config::get('VIEWS_PATH') . 'deliveries/managePickup.php', [
            'pht'           =>  ": Manage Pickup",
            'page_title'    =>  "Managing Pickup Number ".$pickup['pickup_number']."<br>For ".$client['client_name'],
            'pickup'        =>  $pickup,
            'client'        =>  $client
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

    public function viewPickups()
    {
        $client_id = Session::getUserClientId();
        //echo "<p>Client ID: $client_id</p>";
        $client = $this->client->getClientInfo($client_id);
        $pickups = $this->pickup->getOpenPickups($client_id);
        //echo "<pre>",print_r($pickups),"</pre>";die();
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

    public function deliverySearch()
    {
        //render the page
        Config::setJsConfig('curPage', "delivery-search");
        Config::set('curPage', "delivery-search");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/deliveries/", Config::get('VIEWS_PATH') . 'deliveries/deliverySearch.php', [
            'pht'           =>  ": Delivery Search",
            'page_title'    =>  "Search All Deliveries"
        ]);
    }

    public function pickupSearch()
    {
        //echo "THE REQUEST<pre>",print_r($this->request),"</pre>"; //die();
        //render the page
        Config::setJsConfig('curPage', "pickup-search");
        Config::set('curPage', "pickup-search");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/deliveries/", Config::get('VIEWS_PATH') . 'deliveries/pickupSearch.php', [
            'pht'           =>  ": Pickup Search",
            'page_title'    =>  "Search All Pickups"
        ]);
    }

    public function pickupDetail()
    {
        Config::set('curPage', "pickup-detail");
        Config::setJsConfig('curPage', "pickup-detail");
        if(!isset($this->request->params['args']['pickup']))
        {
            //no pickup id to update
            //die("no id");
            (new SiteErrorsController())->siteError("noPickupId")->send();
            return;
        }
        $pickup_id = $this->request->params['args']['pickup'];
        $pickup = $this->pickup->getPickupDetails($pickup_id);
        if(empty($pickup))
        {
            //no pickup data found
            //die('no data');
            (new SiteErrorsController())->siteError("noPickupFound")->send();
            return;
        }
        //echo "<pre>",print_r($pickup),"</pre>";die();
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/deliveries/", Config::get('VIEWS_PATH') . 'deliveries/pickupDetail.php', [
            'page_title'    =>  "Pickup Detail",
            'pht'           =>  ": Pickup Detail",
            'pickup_id'     =>  $pickup_id,
            'pickup'        =>  $pickup
        ]);
    }

    public function deliveryDetail()
    {
        Config::set('curPage', "delivery-detail");
        Config::setJsConfig('curPage', "delivery-detail");
        if(!isset($this->request->params['args']['delivery']))
        {
            //no delivery id to view
            (new SiteErrorsController())->siteError("noDeliveryId")->send();
            return;
        }
        $delivery_id = $this->request->params['args']['delivery'];
        $delivery = $this->delivery->getDeliveryDetails($delivery_id);
        if(empty($delivery))
        {
            //no delivery data found
            (new SiteErrorsController())->siteError("noDeliveryFound")->send();
            return;
        }
        //echo "<pre>",print_r($pickup),"</pre>";die();
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/deliveries/", Config::get('VIEWS_PATH') . 'deliveries/deliveryDetail.php', [
            'page_title'    =>  "Delivery Detail",
            'pht'           =>  ": Delivery Detail",
            'delivery_id'   =>  $delivery_id,
            'delivery'      =>  $delivery
        ]);
    }

    public function isAuthorized(){
        $action = $this->request->param('action');
        $role = Session::getUserRole();
        //$role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        $resource = "deliveries";
        Permission::allow(['admin','super admin'], $resource, [
            'index',
            'addDelivery',
            'adjustDelivery',
            'addPickup',
            'deliveryDetail',
            'deliverySearch',
            'deliverySearchResults',
            'manageDeliveries',
            'manageDelivery',
            'managePickups',
            'managePickup',
            'pickupDetail',
            'pickupSearch',
            'pickupSearchResults',
        ]);
        //only for delivery clients
        if(Session::isDeliveryClientUser())
        {
            Permission::allow('client', $resource, [
                'index',
                'bookDelivery',
                'bookPickup',
                'deliveryDetail',
                'deliverySearch',
                'deliverySearchResults',
                'pickupDetail',
                'pickupSearch',
                'pickupSearchResults',
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