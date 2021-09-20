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
        Config::set('curPage', "cbook-delivery");
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
        $deliveries->$this->delivery->getOpenDeliveries($client_id);
        echo "<pre>",print_r($deliveries),"</pre>";
        echo "<pre>",print_r($deliveries),"</pre>";
    }

    public function isAuthorized(){
        $action = $this->request->param('action');
        $role = Session::getUserRole();
        //$role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        $resource = "deliveries";

        //only for delivery clients
        if(Session::isDeliveryClientUser())
        {
            Permission::allow('client', $resource, "*");
            return Permission::check($role, $resource, $action);
            //return true;
        }
        return false;
    }
}
?>