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

    public function viewBookings()
    {
        Config::setJsConfig('curPage', "view-bookings");
        Config::set('curPage', "view-bookings");
    }

    public function bookCourier()
    {
        Config::setJsConfig('curPage', "book-courier");
        Config::set('curPage', "book-courier");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/courierfunctions/", Config::get('VIEWS_PATH') . 'courierfunctions/bookCourier.php', [
            'page_title'        =>  "Book Courier",
            'pht'               =>  ":Book Courier",
            'dfe_id'        => $this->courier->directFreightId,
            'ep_id'         => $this->courier->eParcelId,
            'epe_id'        => $this->courier->eParcelExpressId,
        ]);
    }

    public function getQuotes()
    {
        //render the page
        Config::setJsConfig('curPage', "get-quotes");
        Config::set('curPage', "get-quotes");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/courierfunctions/", Config::get('VIEWS_PATH') . 'courierfunctions/getQuotes.php', [
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
            'getQuotes',
            'viewBookings'
        ]);

        //echo "<pre>",print_r(Permission::$perms),"</pre>"; die();
        return Permission::check($role, $resource, $action);
    }
}//end class
?>