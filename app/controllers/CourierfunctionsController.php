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

    public function getQuotes()
    {
        //render the page
        Config::setJsConfig('curPage', "get-quotes");
        Config::set('curPage', "get-quotes");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'orders/getQuotes.php', [
            'page_title'        =>  "Get Shipping Estimates",
            'pht'               =>  ":Get Shipping Estimates"
        ]);
    }

    public function isAuthorized(){
        $role = Session::getUserRole();
        $action = $this->request->param('action');
        $resource = "courierfunctions";
        // only for all FSG users
        Permission::allow(
            ['super admin','admin','warehouse','production admin','production','production sales admin'],
            $resource,
            ['*']
        );
        // but sales cannot book
        Permission::allow(['production sales'], $resource, [
            'index',
            'getQuotes'
        ]);

        //echo "<pre>",print_r(Permission::$perms),"</pre>"; die();
        return Permission::check($role, $resource, $action);
    }
}//end class
?>