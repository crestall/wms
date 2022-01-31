<?php

/**
 * Data Entry controller
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class DataEntryController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }

    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'data-entry-index');
        parent::displayIndex(get_class());
    }

    public function containerUnloading()
    {

        //render the page
        Config::setJsConfig('curPage', "container-unloading");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/dataentry/", Config::get('VIEWS_PATH') . 'dataentry/containerUnloading.php',
        [
            'page_title'    =>  "Container Unloading",
            "pht"           => " :Container Unloading",
            'date_filter'   => "Date Unloaded"
        ]);
    }

    public function repalletisingShrinkwrapping()
    {

        //render the page
        Config::setJsConfig('curPage', "repalletising-shrinkwrapping");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/dataentry/", Config::get('VIEWS_PATH') . 'dataentry/repalletisingShrinkwrapping.php',
        [
            'page_title'    =>  "Record Repalletising and Shrinkwrapping",
            "pht"           => " :Repalletising and Shrinkwrapping",
            'date_filter'   => "Date"
        ]);
    }

    public function isAuthorized(){
        $role = Session::getUserRole();
        $action = $this->request->param('action');
        $resource = "dataentry";
        // only for super admins
        Permission::allow('super admin', $resource, ['*']);
        // all other admins
        Permission::allow('admin', $resource, [
            'index',
            'containerUnloading',
            'repalletisingShrinkwrapping'
        ]);

        //echo "<pre>",print_r(Permission::$perms),"</pre>"; die();
        return Permission::check($role, $resource, $action);
    }
}
?>