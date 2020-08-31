<?php

/**
 * Jobs controller
 *

 Manages Production Jobs

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class JobsController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
        $this->Security->config("form", [ 'fields' => ['csrf_token']]);
    }

    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'jobs-index');
        parent::displayIndex(get_class());
    }

    public function addJob()
    {
        //render the page
        Config::setJsConfig('curPage', "add-job");
        Config::set('curPage', "add-job");
    }

    public function viewJobs()
    {
        //render the page
        Config::setJsConfig('curPage', "view-jobs");
        Config::set('curPage', "view-jobs");
    }

    public function isAuthorized()
    {
        $action = $this->request->param('action');
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        $resource = "jobs";

        //only for admin
        Permission::allow('production admin', $resource, "*");
        //production users
        Permission::allow('production', $resource, array(
            "index",
            "viewJobs"
        ));

        return Permission::check($role, $resource, $action);
    }
}