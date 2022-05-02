<?php

/**
 * ItemscollectionsController controller
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class ItemscollectionsController extends Controller{

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

    }

    public function viewCollections()
    {
        
    }

    public function isAuthorised(){
        return true;
    }
}//end class

?>