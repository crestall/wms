<?php

/**
 * Suppliers controller
 *

 Manages Production Suppliers

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class ProductionSettingsController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }

    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'production-settings-index');
        parent::displayIndex(get_class());
    }

    public function drivers()
    {
        parent::manageDrivers('production');
    }

    public function jobCsvImport()
    {
        //render the page
        Config::setJsConfig('curPage', "job-csv-import");
        Config::set('curPage', "job-csv-import");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/productionsettings/", Config::get('VIEWS_PATH') . 'productionsettings/jobsImport.php', [
            'page_title'    =>  "Import Production Jobs",
            'pht'           =>  ": Import Production Jobs"
        ]);
    }

    public function customersCsvImport()
    {
        //render the page
        Config::setJsConfig('curPage', "customers-csv-import");
        Config::set('curPage', "customers-csv-import");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/productionsettings/", Config::get('VIEWS_PATH') . 'productionsettings/customersImport.php', [
            'page_title'    =>  "Import Production Customers",
            'pht'           =>  ": Import Production Customers"
        ]);
    }

    public function suppliersCsvImport()
    {
        //render the page
        Config::setJsConfig('curPage', "suppliers-csv-import");
        Config::set('curPage', "suppliers-csv-import");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/productionsettings/", Config::get('VIEWS_PATH') . 'productionsettings/suppliersImport.php', [
            'page_title'    =>  "Import Production Suppliers",
            'pht'           =>  ": Import Production Suppliers"
        ]);
    }

    public function editJobStatus()
    {
        $status = $this->jobstatus->getStatus();
        //render the page
        Config::setJsConfig('curPage', "edit-job-status");
        Config::set('curPage', "edit-job-status");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/productionsettings/", Config::get('VIEWS_PATH') . 'productionsettings/jobStatus.php', [
            'page_title'    =>  "Production Job Status",
            'pht'           =>  ": Production Job Status",
            'status'        =>  $status
        ]);
    }

    public function finisherCategories()
    {
        $cats = $this->finishercategories->getCategories();
        //render the page
        Config::setJsConfig('curPage', "finisher-categories");
        Config::set('curPage', "finisher-categories");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/productionsettings/", Config::get('VIEWS_PATH') . 'productionsettings/finisherCats.php', [
            'page_title'    =>  "Production Finisher Categories",
            'pht'           =>  ": Production Finisher Categories",
            'cats'          =>  $cats
        ]);
    }

    public function salesReps()
    {
        //render the page
        Config::setJsConfig('curPage', "sales-reps");
        Config::set('curPage', "sales-reps");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/productionsettings/", Config::get('VIEWS_PATH') . 'productionsettings/salesReps.php', [
            'page_title'    =>  "Production Sales Representatives",
            'pht'           =>  ": Production Sales Reps"
        ]);
    }

    public function isAuthorized()
    {
        $action = $this->request->param('action');
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        $resource = "productionsettings";

        //only for admin
        Permission::allow('production admin', $resource, "*");
        //production users not allowed


        return Permission::check($role, $resource, $action);
    }
}