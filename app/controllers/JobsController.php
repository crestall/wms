<?php

/**
 * Jobs controller
 *

 Manages Production Jobs

 * @author     Mark Solly <mark.solly@fsg.com.au>
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

    public function createDeliveryDocket()
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
        //render the page
        Config::setJsConfig('curPage', "create-delivery-docket");
        Config::set('curPage', "create-delivery-docket");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/jobs/", Config::get('VIEWS_PATH') . 'jobs/createDeliveryDocket.php', [
            'page_title'    => "Create Delivery Docket For Job: ".$job_info['job_id'],
            'pht'           => ": Create Delivery Docket",
            'job'           => $job_info
        ]);
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

    public function jobSearch()
    {
        $form = $this->view->render( Config::get('VIEWS_PATH') . "forms/jobsearch.php",[
            'term'              =>  "",
            'customer_ids'       =>  array(),
            'supplier_ids'       =>  array(),
            'salesrep_ids'       =>  array(),
            'status_ids'         =>  array(),
            'date_from_value'   =>  0,
            'date_to_value'     =>  0
        ]);
        //render the page
        Config::setJsConfig('curPage', "job-search");
        Config::set('curPage', "job-search");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/jobs/", Config::get('VIEWS_PATH') . 'jobs/jobSearch.php', [
            'page_title'    =>  "Search production Jobs",
            'pht'           =>  ": Production Job Search",
            'form'          =>  $form
        ]);
    }

    public function jobSearchResults()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        if(!$this->Security->CsrfToken())
        {
            return $this->error(400);
        }
        $customer_ids = isset($this->request->query['customer_ids'])? $this->request->query['customer_ids']: array();
        $supplier_ids = isset($this->request->query['supplier_ids'])? $this->request->query['supplier_ids']: array();
        $salesrep_ids = isset($this->request->query['salesrep_ids'])? $this->request->query['salesrep_ids']: array();
        $status_ids = isset($this->request->query['status_ids'])? $this->request->query['status_ids']: array();;
        $date_from_value = $this->request->query['date_from_value'];
        $date_to_value = $this->request->query['date_to_value'];
        $args = array(
            'term'              =>  $this->request->query['term'],
            'customer_ids'       =>  $customer_ids,
            'supplier_ids'       =>  $supplier_ids,
            'salesrep_ids'       =>  $salesrep_ids,
            'status_ids'         =>  $status_ids,
            'date_from_value'   =>  $date_from_value,
            'date_to_value'     =>  $date_to_value
        );
        $jobs = $this->productionjob->getSearchResults($args);
        $count = count($jobs);
        $s = ($count == 1)? "": "s";
        $form = $this->view->render( Config::get('VIEWS_PATH') . "forms/jobsearch.php",$args);
        //render the page
        Config::setJsConfig('curPage', "job-search-results");
        Config::set('curPage', "job-search-results");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/jobs/", Config::get('VIEWS_PATH') . 'jobs/jobSearchResults.php', [
            'page_title'    =>  "Search Results",
            'pht'           =>  ": Job Search Results",
            'form'          =>  $form,
            'count'         =>  $count,
            's'             =>  $s,
            'term'          =>  $this->request->query['term'],
            'jobs'          =>  $jobs
        ]);
    }

    public function viewJobs()
    {
        //echo "<pre>",print_r($this->request->params),"</pre>";die();
        $filter = "Active";
        $completed = (isset($this->request->params['args']['completed']))? 1 : 0;
        $cancelled = (isset($this->request->params['args']['cancelled']))? 1 : 0;
        $customer_ids = isset($this->request->params['args']['customer_ids'])? explode(',',$this->request->params['args']['customer_ids']): array();
        $finisher_ids = isset($this->request->params['args']['finisher_ids'])? explode(',',$this->request->params['args']['finisher_ids']): array();
        $salesrep_ids = isset($this->request->params['args']['contacts_ids'])? explode(',',$this->request->params['args']['contacts_ids']): array();
        $status_ids = isset($this->request->params['args']['status_ids'])? explode(',',$this->request->params['args']['status_ids']): array();
        if($completed || $cancelled)
            $status_ids = array();
        $jobs = $this->productionjob->getJobsForDisplay(array(
            'completed'         =>  $completed,
            'cancelled'         =>  $cancelled,
            'customer_ids'      =>  (array)$customer_ids,
            'finisher_ids'      =>  (array)$finisher_ids,
            'salesrep_ids'      =>  (array)$salesrep_ids,
            'status_ids'        =>  (array)$status_ids,
        ));
        //render the page
        if($completed == 1)
            $filter = "Completed";
        elseif($cancelled == 1)
            $filter = "Cancelled";
        $head = "Viewing $filter Production Jobs"
        Config::setJsConfig('curPage', "view-jobs");
        Config::set('curPage', "view-jobs");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/jobs/", Config::get('VIEWS_PATH') . 'jobs/viewJobs.php', [
            'page_title'        =>  $head,
            'pht'               =>  ": Production Jobs",
            'jobs'              =>  $jobs,
            'completed'         =>  $completed,
            'cancelled'         =>  $cancelled,
            'filter'            =>  $filter,
            'customer_ids'      =>  (array)$customer_ids,
            'finisher_ids'      =>  (array)$finisher_ids,
            'salesrep_ids'      =>  (array)$salesrep_ids,
            'status_ids'        =>  (array)$status_ids,
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
        $customer_contacts = $this->productioncontact->getCustomerContacts($job_info['customer_id']);


        $finisher_info = ($job_info['finisher_id'] > 0)? $this->productionfinisher->getFinisherById($job_info['finisher_id']) : array();
        $finisher2_info = ($job_info['finisher2_id'] > 0)? $this->productionfinisher->getFinisherById($job_info['finisher2_id']) : array();
        $finisher3_info = ($job_info['finisher3_id'] > 0)? $this->productionfinisher->getFinisherById($job_info['finisher3_id']) : array();
        //render the page
        Config::setJsConfig('curPage', "update-job");
        Config::set('curPage', "update-job");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/jobs/", Config::get('VIEWS_PATH') . 'jobs/updateJob.php', [
            'page_title'    =>  "Update Production Job Details",
            'pht'           =>  ": Update Production Job",
            'job'           =>  $job_info,
            'customer'      =>  $customer_info,
            'customer_contacts' => $customer_contacts,
            'finisher'      =>  $finisher_info,
            'finisher2'     =>  $finisher2_info,
            'finisher3'     =>  $finisher3_info
        ]);
    }

    public function isAuthorized()
    {
        $action = $this->request->param('action');
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        $resource = "jobs";

        //only for production
        Permission::allow(['production admin', 'production'], $resource, "*");
        //warehouse users
        Permission::allow(['admin', 'super admin'], $resource, array(
            'index',
            'createDeliveryDocket',
            'viewJobs'
        ));
        //production sales users
        Permission::allow(['production sales admin', 'production sales'], $resource, array(
            'index',
            'createDeliveryDocket',
            'jobSearch',
            'jobSearchResults',
            'viewJobs'
        ));

        return Permission::check($role, $resource, $action);
    }
}