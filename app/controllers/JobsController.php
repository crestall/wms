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
    /*
    public function getShippingQuotes()
    {
        //$ordersController = new OrdersController();
        //$ordersController->getQuotes();
        //render the page
        Config::setJsConfig('curPage', "get-shipping-quotes");
        Config::set('curPage', "get-shipping-quotes");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/jobs/", Config::get('VIEWS_PATH') . 'orders/getQuotes.php', [
            'page_title'        =>  "Get Shipping Estimates",
            'pht'               =>  ":Get Shipping Estimates"
        ]);
    }
    */
    public function createDeliveryDocket()
    {
        if(!isset($this->request->params['args']['job']))
        {
            //no job id to update
            (new SiteErrorsController())->siteError("noJobId")->send();
            return;
        }
        $job_id = $this->request->params['args']['job'];
        $job_info = $this->productionjob->getJobById($job_id);
        if(empty($job_info))
        {
            //no job id to update
            (new SiteErrorsController())->siteError("noJobFound")->send();
            return;
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
        $head = "Viewing $filter Production Jobs";
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
            (new SiteErrorsController())->siteError("noJobId")->send();
            return;
        }
        $job_id = $this->request->params['args']['job'];
        $job_info = $this->productionjob->getJobById($job_id);
        if(empty($job_info))
        {
            //no job to update
            (new SiteErrorsController())->siteError("noJobFound")->send();
            return;
        }
        $customer_info = $this->productioncustomer->getCustomerById($job_info['customer_id']);
        $customer_contacts = $this->productioncontact->getCustomerContacts($job_info['customer_id']);


        //$finisher_info = ($job_info['finisher_id'] > 0)? $this->productionfinisher->getFinisherById($job_info['finisher_id']) : array();
        //$finisher2_info = ($job_info['finisher2_id'] > 0)? $this->productionfinisher->getFinisherById($job_info['finisher2_id']) : array();
        //$finisher3_info = ($job_info['finisher3_id'] > 0)? $this->productionfinisher->getFinisherById($job_info['finisher3_id']) : array();
        //render the page
        Config::setJsConfig('curPage', "update-job");
        Config::set('curPage', "update-job");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/jobs/", Config::get('VIEWS_PATH') . 'jobs/updateJob.php', [
            'page_title'    =>  "Update Production Job Details",
            'pht'           =>  ": Update Production Job",
            'job'           =>  $job_info,
            'customer'      =>  $customer_info,
            'customer_contacts' => $customer_contacts
        ]);
    }

    public function shipmentAddressUpdate()
    {
        if(!isset($this->request->params['args']['job']))
        {
            //no job id to update
            (new SiteErrorsController())->siteError("noJobId")->send();
            return;
        }
        if(!isset($this->request->params['args']['shipment']))
        {
            //no shipment id to update
            (new SiteErrorsController())->siteError("noShipmentId")->send();
            return;
        }
        $job_id = $this->request->params['args']['job'];
        $shipment_id = $this->request->params['args']['shipment'];
        $shipment_info = $this->productionjobsshipment->getShipmentForJob($job_id, $shipment_id);
        if(empty($shipment_info))
        {
            //no shipment data found
            (new SiteErrorsController())->siteError("noShipmentFound")->send();
            return;
        }
        //echo "<pre>",print_r($shipment_info),"</pre>";//die();
        //render the page
        Config::setJsConfig('curPage', "shipment-address-update");
        Config::set('curPage', "shipment-address-update");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/jobs/", Config::get('VIEWS_PATH') . 'jobs/shipmentAddressUpdate.php', [
            'page_title'    =>  "Update A Shipment address For Job: ".$shipment_info['job_number'],
            'pht'           =>  ": Update Shipment Address",
            'shipment'      =>  $shipment_info
        ]);
    }

    public function createShipment()
    {
        $packages_added = false;
        //$shipment_id = 0;
        if(!isset($this->request->params['args']['job']))
        {
            //no job id to update
            (new SiteErrorsController())->siteError("noJobId")->send();
            return;
        }
        $job_id = $this->request->params['args']['job'];
        $job_info = $this->productionjob->getJobById($job_id);
        if(empty($job_info))
        {
            //no job id to update
            (new SiteErrorsController())->siteError("noJobFound")->send();
            return;
        }
        $shipment_details = $this->productionjobsshipment->getPartShipmentDetailsForJob($job_id);
        $shipment_id = (empty($shipment_details['id']))? 0 : $shipment_details['id'];
        $packages = $this->productionjobsshipment->getPackagesForJob($job_id, $shipment_id);
        $address_string = $shipment_details['address'];
        if(!empty($order['address_2']))
            $address_string .= " ".$shipment_details['address_2'];
        $address_string .= " ".$shipment_details['suburb'];
        $address_string .= " ".$shipment_details['state'];
        $address_string .= " ".$shipment_details['postcode'];
        $address_string .= " ".$shipment_details['country'];
        //render the page
        Config::setJsConfig('curPage', "create-shipment");
        Config::set('curPage', "create-shipment");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/jobs/", Config::get('VIEWS_PATH') . 'jobs/createShipment.php', [
            'page_title'    =>  "Create a Shipment For Job: ".$job_info['job_id'],
            'pht'           =>  ": Create a Shipment",
            'job'           =>  $job_info,
            'shipment_details'      =>  $shipment_details,
            'packages'      =>  $packages,
            'shipment_id'   =>  $shipment_id,
            'address_string'    => $address_string
        ]);

    }

    public function manageShipments()
    {
        $dispatched = 0;
        if(!empty($this->request->params['args']))
        {
            $dispatched = (isset($this->request->params['args']['dispatched']))? $this->request->params['args']['dispatched'] : 0;
        }
        //$shipments = $this->productionjob->getJobShipments($dispatched);
        $jobs = $this->productionjobsshipment->getJobsWithShipments($dispatched);
        echo "<pre>",print_r($jobs),"</pre>";
    }

    public function manageShipment()
    {

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
        //production sales admin users
        Permission::allow(['production sales admin'], $resource, array(
            'index',
            'bookCarrier',
            'addJob',
            'createDeliveryDocket',
            'getShippingQuotes',
            'jobSearch',
            'jobSearchResults',
            'manageDispatches',
            'manageDispatch',
            'updateJob',
            'viewJobs'
        ));
        //production sales users
        Permission::allow(['production sales'], $resource, array(
            'index',
            'bookCarrier',
            'createDeliveryDocket',
            'getShippingQuotes',
            'jobSearch',
            'jobSearchResults',
            'manageDispatches',
            'manageDispatch',
            'updateJob',
            'viewJobs'
        ));

        return Permission::check($role, $resource, $action);
    }
}