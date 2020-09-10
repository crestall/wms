<?php

/**
 * Suppliers controller
 *

 Manages Production Suppliers

 * @author     Mark Solly <mark.solly@3plplus.com.au>
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

    public function editJobStatus()
    {
        $status = $this->jobstatus->getStatus();
        //render the page
        Config::setJsConfig('curPage', "job-status");
        Config::set('curPage', "job-status");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/productionsettings/", Config::get('VIEWS_PATH') . 'productionsettings/jobStatus.php', [
            'page_title'    =>  "Production Job Status",
            'pht'           =>  ": Production Job Status",
            'status'        =>  $status
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