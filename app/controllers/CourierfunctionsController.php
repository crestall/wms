<?php
/**
 * Courier Functions controller
 *
 * Get Quotes and bokk extraneous courier jobs

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class CourierFunctionsController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }

    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'courier-functions-index');
        parent::displayIndex(get_class());
    }

    public function isAuthorized(){
        $role = Session::getUserRole();
        $action = $this->request->param('action');
        $resource = "courierfunctions";
        // only for all users
        Permission::allowAllRoles($resource, $actions = "*");
        // some super admin restrictions

        //echo "<pre>",print_r(Permission::$perms),"</pre>"; die();
        return Permission::check($role, $resource, $action);
    }
}//end class
?>