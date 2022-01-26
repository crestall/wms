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

    public function deliveryClientCharges()
    {
        $client_id = (isset($this->request->params['args']['client']))? $this->request->params['args']['client'] : 0;
        $client_name = $this->client->getClientName($client_id);
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('first day of last month 00:00:00');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : strtotime('first day of this month 00:00:00');
        $charges = $this->delivery->getCharges($client_id, $from, $to);
        echo "<pre>",print_r($charges),"</pre>";die();
        Config::setJsConfig('curPage', "delivery-client-charges");
        Config::set('curPage', "delivery-client-charges");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/financials/", Config::get('VIEWS_PATH') . 'financials/deliveryClientCharges.php',[
            'page_title'    =>  'Delivery Client Chages',
            'pht'           =>  ':Delivery Client Charges',
            'client_id'     =>  $client_id,
            'from'          =>  $from,
            'to'            =>  $to,
            'date_filter'   =>  "",
            'client_name'   =>  $client_name
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