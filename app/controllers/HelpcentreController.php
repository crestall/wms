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
            'pht'           =>  ": Help Centre",
            'page_title'    =>  "Help Centre Home"
        ]);
    }

    public function ordersHelp()
    {
        if( Session::isproductionUser() )
        {
            return $this->productioOrdersHelp();
        }
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
        if( Session::isWarehouseUser() )
        {
            return $this->warehouseJobsHelp();
        }
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

    private function warehouseJobsHelp()
    {
        Config::setJsConfig('curPage', 'warehouse-jobs-help');
        Config::set('curPage', "jobs-help");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/help-centre/", Config::get('VIEWS_PATH') . 'help-centre/comingsoon.php',[

        ]);
    }

    private function productioOrdersHelp()
    {
        Config::setJsConfig('curPage', 'production-orders-help');
        Config::set('curPage', "orders-help");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/help-centre/", Config::get('VIEWS_PATH') . 'help-centre/comingsoon.php',[

        ]);
    }

    public function isAuthorized()
    {
        //return true;
        $action = $this->request->param('action');
        $resource = "helpcentre";
        $role = Session::getUserRole();

        //warehouse users
        Permission::allow([
            'warehouse',
            'admin',
            'super admin'
        ], $resource, [
            'index',
            'clientsHelp',
            'deliveriesHelp',
            'jobsHelp',
            'ordersHelp'
        ]);
        //production users
        Permission::allow([
            'production',
            'production admin',
            'production sales admin',
            'production sales'
        ], $resource, [
            'index',
            'jobsHelp',
            'ordersHelp'
        ]);
        //client users
        if(Session::isDeliveryClientUser())
        {
            Permission::allow('client', $resource, [
                'index'
            ]);
        }
        else
        {
            Permission::allow('client', $resource, [
                'index'
            ]);
        }

        return Permission::check($role, $resource, $action);


    }

}