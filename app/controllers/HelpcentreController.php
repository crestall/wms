<?php

/**
 * Help Centre controller
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class HelpCentreController extends Controller
{
    /**
     * show coming soon page
     *
     */
    public function index()
    {
        Config::setJsConfig('curPage', 'help-centre-index');
        Config::set('curPage', "help-centre-index");
        //return $this->comingSoon();
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/help-centre/", Config::get('VIEWS_PATH') . 'help-centre/index.php',[

        ]);
    }

    public function ordersHelp()
    {
        Config::setJsConfig('curPage', 'orders-help');
        Config::set('curPage', "orders-help");
        //return $this->comingSoon();
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/help-centre/", Config::get('VIEWS_PATH') . 'help-centre/comingsoon.php',[

        ]);
    }

    public function deliveriesHelp()
    {
        Config::setJsConfig('curPage', 'deliveries-help');
        Config::set('curPage', "deliveries-help");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/help-centre/", Config::get('VIEWS_PATH') . 'help-centre/comingsoon.php',[

        ]);
    }

    public function jobsHelp()
    {
        Config::setJsConfig('curPage', 'jobs-help');
        Config::set('curPage', "jobs-help");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/help-centre/", Config::get('VIEWS_PATH') . 'help-centre/comingsoon.php',[

        ]);
    }

    public function clientsHelp()
    {
        Config::setJsConfig('curPage', 'clients-help');
        Config::set('curPage', "clients-help");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/help-centre/", Config::get('VIEWS_PATH') . 'help-centre/comingsoon.php',[

        ]);
    }

    public function isAuthorized()
    {
        //return true;
        $action = $this->request->param('action');
        $resource = "helpcentre";
        // everyone
        Permission::allow(['*'], $resource, ['*']);


        $resource = "helpcentre";
        $action = $this->request->param('action');
        //$role = Session::getUserRole();
        //$role = (Session::isWarehouseUser())? 'warehouse' : Session::getUserRole();
        if(Session::isWarehouseUser())
            $help_role = 'warehouse';
        elseif(Session::isProductionUser())
            $help_role = 'production';
        else
            $help_role = Session::getUserRole();
        echo $help_role; die();return true;
        //warehouse users
        Permission::allow('warehouse', $resource, [
            'index',
            'clientsHelp',
            'deliveriesHelp',
            'jobsHelp',
            'ordersHelp'
        ]);
        //production users

        //client users

        //only for admin
        Permission::allow('admin', $resource, "*");
        Permission::allow('super admin', $resource, "*");
        //production users
        $allowed_resources = array(
            "orderUpdate",
            "createDeliveryDocket"
        );
        Permission::allow('production admin', $resource, $allowed_resources);
        //warehouse users
        Permission::allow('warehouse', $resource, array(
            "index",
            "orderDispatching",
            "orderPacking",
            "orderPicking",
            "orderSearch",
            "orderSearchResults",
            "viewOrders",
            "orderUpdate",
            "addressUpdate",
            "orderEdit",
            "viewDetails",
            "viewStoreorders",
            "getQotes"
        ));
        //only for clients
        $allowed_resources = array(
            "index",
            "addOrder",
            "addOrderTest",
            "bookPickup",
            "bulkUploadOrders",
            "clientOrders",
            "orderTracking",
            "orderDetail",
        );
        Permission::allow('client', $resource, $allowed_resources);
        return Permission::check($role, $resource, $action);


    }

}