<?php

/**
 * Financials controller
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class FinancialsController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }

    public function pickpackClientCharges()
    {
        $client_id = (isset($this->request->params['args']['client']))? $this->request->params['args']['client'] : 0;
        $client_name = $this->client->getClientName($client_id);
        $from = ( isset($this->request->params['args']['from']) )?
            $this->request->params['args']['from'] :
            strtotime('monday last week 00:00:00');
        $to = ( isset($this->request->params['args']['to']) )?
            $this->request->params['args']['to'] :
            strtotime('friday last week 23:59:59');

        $general_charges = $this->client->getPPClientGeneralCharges($client_id, $from, $to);
        //echo "<pre>",print_r($general_charges),"</pre>";//die();
        $container_unloading_charges = $this->client->getClientContainerUnloadingCharges($client_id, $from, $to);
        //echo "<pre>",print_r($container_unloading_charges),"</pre>";die();
        $delivery_handling_charges = $this->client->getPPClientDeliveryHandlingCharges($client_id, $from, $to);
        //echo "<pre>",print_r($delivery_handling_charges),"</pre>";die();
        $storage_charges = $this->client->getPPClientStorageCharges($client_id, $from, $to);
        Config::setJsConfig('curPage', "pickpack-client-charges");
        Config::set('curPage', "pickpack-client-charges");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/financials/", Config::get('VIEWS_PATH') . 'financials/pickpackClientCharges.php',[
            'page_title'                    =>  'Pickpack Client Charges',
            'pht'                           =>  ':Pickpack Client Charges',
            'client_id'                     =>  $client_id,
            'client_name'                   =>  $client_name,
            'from'                          =>  $from,
            'to'                            =>  $to,
            'date_filter'                   =>  "",
            'general_charges'               =>  $general_charges,
            'container_unloading_charges'   =>  $container_unloading_charges,
            'delivery_handling_charges'     =>  $delivery_handling_charges,
            'storage_charges'               =>  $storage_charges
        ]);
    }

    public function deliveryClientCharges()
    {
        $client_id = (isset($this->request->params['args']['client']))? $this->request->params['args']['client'] : 0;
        $client_name = $this->client->getClientName($client_id);
        if(isset($this->request->params['args']['from']))
            $from = $this->request->params['args']['from'];
        elseif($client_id == 3)
            $from = strtotime('last saturday 00:00:00', mktime(0,0,0,date("m")-1,25,date("Y")));
        else
            $from = strtotime('first day of last month 00:00:00');

        if(isset($this->request->params['args']['to']))
            $to = $this->request->params['args']['to'];
        elseif($client_id == 3)
            $to = strtotime('last saturday 00:00:00', mktime(0,0,0,date("m"),25,date("Y")));
        else
            $to = strtotime('first day of this month 00:00:00');
        $delivery_charges = $this->client->getDeliveryClientDeliveryCharges($client_id, $from, $to);
        $general_charges = $this->client->getDeliveryClientGeneralCharges($client_id, $from, $to);
        $storage_charges = $this->client->getDeliveryClientStorageCharges($client_id, $from, $to);
        $container_unloading_charges = $this->client->getClientContainerUnloadingCharges($client_id, $from, $to);
        //echo "<pre>",print_r($general_charges),"</pre>";die();
        Config::setJsConfig('curPage', "delivery-client-charges");
        Config::set('curPage', "delivery-client-charges");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/financials/", Config::get('VIEWS_PATH') . 'financials/deliveryClientCharges.php',[
            'page_title'                    =>  'Delivery Client Charges',
            'pht'                           =>  ':Delivery Client Charges',
            'client_id'                     =>  $client_id,
            'from'                          =>  $from,
            'to'                            =>  $to,
            'date_filter'                   =>  "",
            'client_name'                   =>  $client_name,
            'delivery_charges'              =>  $delivery_charges,
            'general_charges'               =>  $general_charges,
            'storage_charges'               =>  $storage_charges,
            'container_unloading_charges'   =>  $container_unloading_charges
        ]);
    }

    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'financials-index');
        parent::displayIndex(get_class());
    }


    public function isAuthorized(){
        $action = $this->request->param('action');
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        $resource = "financials";
        //only for admin
        Permission::allow('admin', $resource, "*");
        Permission::allow('super admin', $resource, "*");
        return Permission::check($role, $resource, $action);
    }
}
?>