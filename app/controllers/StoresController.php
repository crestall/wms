<?php

/**
 * Store controller
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class storesController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }

    public function addStore()
    {
        //render the page
        Config::setJsConfig('curPage', "add-store");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/stores/", Config::get('VIEWS_PATH') . 'stores/addStore.php',
        [
            'page_title'    =>  'Add New Store'
        ]);
    }

    public function editStore()
    {
        $store_id = $this->request->params['args']['store'];
        $store_info = $this->store->getStoreById($store_id);
        //render the page
        Config::setJsConfig('curPage', "edit-store");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/stores/", Config::get('VIEWS_PATH') . 'stores/editStore.php',
        [
            'page_title'    =>  'Edit Store details',
            'store'         =>  $store_info
        ]);
    }

    public function viewStores()
    {
        $active = 1;
        if(!empty($this->request->params['args']))
        {
            $active = (isset($this->request->params['args']['active']))? $this->request->params['args']['active'] : 1;
        }
        $stores = $this->store->getAllStores($active);
        //render the page
        Config::setJsConfig('curPage', "view-stores");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/stores/", Config::get('VIEWS_PATH') . 'stores/viewStores.php',
        [
            'page_title'    =>  'View Stores',
            'stores'        =>  $stores,
            'active'        =>  $active
        ]);
    }

    public function isAuthorized(){
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        if( isset($role) && ($role === "admin"  || $role === "super admin") )
        {
            return true;
        }
        return false;
    }
}
?>