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
        $action = $this->request->param('action');
        $actions = [
            'procHuntersCheck'
        ];
        $actions = [
            'procHuntersCheck'
        ];
        $this->Security->config("validateForm", false);
        /*  */
        $this->Security->requirePost($actions); 
        if(in_array($action, $actions))
        {
            $this->Security->config("form", [ 'fields' => ['csrf_token']]);
        }

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