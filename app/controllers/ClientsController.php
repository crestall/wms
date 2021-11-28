<?php

/**
 * Clients controller
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class ClientsController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
        //set javascript to allocate active class to menu items
    }

    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'clients-index');
        parent::displayIndex(get_class());
    }

    public function viewClients()
    {
        $active = (isset($this->request->params['args']['active']))? $this->request->params['args']['active'] : 1;

        $clients = $this->client->getAllClients($active);
        Config::setJsConfig('curPage', "view-clients");
        Config::set('curPage', "view-clients");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/clients/", Config::get('VIEWS_PATH') . 'clients/viewClients.php',
            [
                'active'        =>  $active,
                'pht'           =>  ": View Clients",
                'clients'       =>  $clients,
                'page_title'    =>  "View Clients"
            ]);
    }

    public function addClient()
    {
        $this->view->assign('page_title', "Add Client");
        Config::setJsConfig('curPage', "add-client");
        Config::set('curPage', "add-client");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/clients/", Config::get('VIEWS_PATH') . 'clients/addClient.php',
            [
                'page_title'    =>  "Add Client",
                'pht'           =>  ": Add Client",
            ]);
    }

    public function editClient()
    {
        //echo "<pre>",print_r($this->request->params['args']),"</pre>";die();
        //$client_id = $this->request->params['args'][0];
        $client_id = $this->request->params['args']['client'];
        $client_info = $this->client->getClientInfo($client_id);
        $uc = $this->client->getClientUteDeliveryCharges($client_id);
        $tc = $this->client->getClientTruckDeliveryCharges($client_id);
        $sc = $this->client->getClientStorageCharges($client_id);
        //render the page
        Config::setJsConfig('curPage', "edit-client");
        Config::set('curPage', "edit-client");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/clients/", Config::get('VIEWS_PATH') . 'clients/editClient.php',
            [
                'client'        =>  $client_info,
                'page_title'    =>  "Edit Client",
                'pht'           =>  ": Edit Client",
                'uc'            =>  $uc,
                'tc'            =>  $tc,
                'sc'            =>  $sc
            ]);
    }

    public function isAuthorized(){
        $role = Session::getUserRole();
        $action = $this->request->param('action');
        $resource = "clients";
        //only for admin
        Permission::allow(['super admin', 'admin'], $resource, ['*']);
        return Permission::check($role, $resource, $action);
    }
}
?>