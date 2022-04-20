<?php

/**
 * Help Centre controller
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class HelpCentreController extends Controller
{
    public function beforeAction()
    {
        $this->helpSections = [
            'creating',
            'viewing',
            'managing'
        ];
    }

    public function index()
    {
        Config::setJsConfig('curPage', 'help-centre');
        Config::set('curPage', "help-centre");
        //return $this->comingSoon();
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/help-centre/", Config::get('VIEWS_PATH') . 'help-centre/index.php',[
            'pht'           =>  ": Help Centre",
            'page_title'    =>  "Help Centre"
        ]);
    }

    public function ordersHelp()
    {
        if( Session::isproductionUser() )
        {
            return $this->productionOrdersHelp();
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
        $sections = $this->createSections('deliveries');
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/help-centre/", Config::get('VIEWS_PATH') . 'help-centre/sectionindex.php',[
            'pht'           =>  ": Deliveries Help",
            'page_title'    =>  "Deliveries Help",
            'sections'      =>  $sections
        ]);
    }

    public function customersHelp()
    {
        Config::setJsConfig('curPage', 'customers-help');
        Config::set('curPage', "customers-help");
        $sections = $this->createSections('customers');
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/help-centre/", Config::get('VIEWS_PATH') . 'help-centre/sectionindex.php',[
            'pht'           =>  ": Customers Help",
            'page_title'    =>  "Customers Help",
            'sections'      =>  $sections
        ]);
    }

    public function finishersHelp()
    {
        Config::setJsConfig('curPage', 'finishers-help');
        Config::set('curPage', "finishers-help");
        $sections = $this->createSections('finishers');
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/help-centre/", Config::get('VIEWS_PATH') . 'help-centre/sectionindex.php',[
            'pht'           =>  ": Finishers Help",
            'page_title'    =>  "Finishers Help",
            'sections'      =>  $sections
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

    private function productionOrdersHelp()
    {
        Config::setJsConfig('curPage', 'production-orders-help');
        Config::set('curPage', "orders-help");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/help-centre/", Config::get('VIEWS_PATH') . 'help-centre/comingsoon.php',[

        ]);
    }

    private function createSections($section)
    {
        $this_section = [];
        $role = Session::getUserRole();
        foreach($this->helpSections as $s)
        {
            if(Permission::check($role, 'helpcentre', $s.ucwords($section), [], false))
                $this_section[] = $s.ucwords($section);
        }
        return $this_section;
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
        //all production users
        Permission::allow([
            'production',
            'production admin',
            'production sales admin',
            'production sales'
        ], $resource, [
            'index',
            'customersHelp',
            'creatingCustomers',
            'managingCustomers',
            'viewingCustomers',
            'finishersHelp',
            'creatingFinishers',
            'viewingFinishers',
            'managingFinishers',
            'jobsHelp',
            'ordersHelp'
        ]);
        //filter production
        Permission::deny([
            'production'
        ], $resource, [
            'managingCustomers',
            'creatingCustomers',
            'creatingFinishers',
            'managingFinishers'
        ]);
        Permission::deny([
            'production sales'
        ], $resource, [
           'creatingFinishers',
           'managingFinishers'
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