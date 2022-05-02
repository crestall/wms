<?php

/**
 * ItemscollectionsController controller
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class ItemsCollectionsController extends Controller{

    public function beforeAction()
    {
        parent::beforeAction();
        $this->Security->config("form", [ 'fields' => ['csrf_token']]);
    }

    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'items-collections-index');
        parent::displayIndex(get_class());
    }

    public function recordCollection()
    {
        //render the page
        Config::setJsConfig('curPage', "book-item-collection");
        Config::set('curPage', "book-item-collection");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/bookPickup.php', [
            'page_title'    =>  "Record Item Collection Booking",
            'pht'           =>  ": Collection Booking",
        ]);
    }

    public function viewCollections()
    {

    }

    public function isAuthorized(){
        $action = $this->request->param('action');
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        $resource = "itemscollections";

        //only for admin
        Permission::allow('admin', $resource, "*");
        Permission::allow([
            'super admin',
            'admin',
            'warehouse'
        ], $resource, "*");
        return Permission::check($role, $resource, $action);
    }
}//end class

?>