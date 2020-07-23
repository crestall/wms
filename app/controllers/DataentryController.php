<?php

/**
 * Data Entry controller
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class DataEntryController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }

    public function index()
    {
        parent::displayIndex(get_class());
    }

    public function containerUnloading()
    {

        //render the page
        Config::setJsConfig('curPage', "container-unloading");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/dataentry/", Config::get('VIEWS_PATH') . 'dataentry/containerUnloading.php',
        [
            'page_title'    =>  "Container Unloading"
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
            'containerUnloading'
        ]);

        //echo "<pre>",print_r(Permission::$perms),"</pre>"; die();
        return Permission::check($role, $resource, $action);
    }
}
?>