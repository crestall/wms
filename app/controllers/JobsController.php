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
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/jobs/", Config::get('VIEWS_PATH') . 'jobs/addJob.php', [
            'page_title'    =>  "Add Job for Production",
            'pht'           =>  ": Add Production Job"
        ]);
    }

    public function viewJobs()
    {
        $completed = (isset($this->request->params['args']['completed']))? true : false;
        $cancelled = (isset($this->request->params['args']['cancelled']))? true : false;
        $jobs = $this->productionjob->getJobsForDisplay($completed, $cancelled);
        //render the page
        Config::setJsConfig('curPage', "view-jobs");
        Config::set('curPage', "view-jobs");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/jobs/", Config::get('VIEWS_PATH') . 'jobs/viewJobs.php', [
            'page_title'    =>  "View Production Jobs",
            'pht'           =>  ": Production Jobs",
            'jobs'          =>  $jobs
        ]);
    }

    public function updateJob()
    {
        if(!isset($this->request->params['args']['job']))
        {
            //no job id to update
            return (new ErrorsController())->error(400)->send();
        }
        $job_id = $this->request->params['args']['job'];
        $job_info = $this->productionjob->getJobById($job_id);
        if(empty($job_info))
        {
            //no job data found
            return (new ErrorsController())->error(404)->send();
        }
        $customer_info = $this->productioncustomer->getCustomerById($job_info['customer_id']);
        $supplier_info = ($job_info['supplier_id'] > 0)? $this->productionsupplier->etSupplierById($job_info['supplier_id']) : array();
        //render the page
        Config::setJsConfig('curPage', "view-jobs");
        Config::set('curPage', "view-jobs");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/jobs/", Config::get('VIEWS_PATH') . 'jobs/EditJob.php', [
            'page_title'    =>  "Update Production Job Details",
            'pht'           =>  ": Update Production Job",
            'job'           =>  $job_info,
            'customer'      =>  $customer_info,
            'supplier'      =>  $supplier_info
        ]);
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