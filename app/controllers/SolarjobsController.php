<?php

/**
 * Solar Jobs controller
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class SolarjobsController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
        $this->Security->config("form", [ 'fields' => ['csrf_token']]);
    }

    public function addSolarInstall()
    {
        $page_title = "Add a Solar Install Job";

        Config::setJsConfig('curPage', "add-solar-install");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/addSolarJob.php',[
            'page_title'    =>  $page_title
        ]);
    }

    public function addServiceJob()
    {
        $page_title = "Add a Solar Service Job";

        Config::setJsConfig('curPage', "add-service-job");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/addServiceJob.php',[
            'page_title'    =>  $page_title,
            'client_id'     =>  67
        ]);
    }

    public function editInstall()
    {
        //echo "<pre>",print_r($this->request->params['args']),"</pre>";die();
        if(empty($this->request->params['args']))
        {
            return $this->redirector->to(PUBLIC_ROOT."solar-jobs/view-installs");
        }
        $page_title = "Update a Solar Install Job";
        $type = $this->request->params['args']['type'];
        $id = $this->request->params['args']['id'];

        $forms = array(
            1   => "editOriginInstall.php",
            2   => "editTLJInstall.php"
        );
        $details = $this->solarorder->getOrderDetail($id);
        $order_items = $this->solarorder->getItemsForOrder($id);
        $order_type = $this->solarordertype->getSolarOrderType($type);
        $eb = $this->user->getUserName( $details['entered_by'] );
        if(empty($eb))
        {
            $eb = "Automatically Imported";
        }
        /*  */
        Config::setJsConfig('curPage', "update-solar-install");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/'.$forms[$type],[
            'page_title'    =>  $page_title,
            'details'       =>  $details,
            'id'            =>  $id,
            'type'          =>  $type,
            'order_type'    =>  $order_type,
            'order_items'   =>  $order_items,
            'entered_by'    =>  $eb
        ]);

    }

    public function editServicejob()
    {
        //echo "<pre>",print_r($this->request->params['args']),"</pre>";die();
        if(empty($this->request->params['args']))
        {
            return $this->redirector->to(PUBLIC_ROOT."solar-jobs/view-service-jobs");
        }
        $page_title = "Update a Solar Service Job";
        $id = $this->request->params['args']['id'];

        $details = $this->solarservicejob->getJobDetail($id);
        $order_items = $this->solarservicejob->getItemsForJob($id);
        $order_type = $this->solarordertype->getSolarOrderType($details['type_id']);
        $eb = $this->user->getUserName( $details['entered_by'] );
        if(empty($eb))
        {
            $eb = "Automatically Imported";
        }
        /*  */
        Config::setJsConfig('curPage', "edit-servicejob");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/updateServiceJob.php',[
            'page_title'    =>  $page_title,
            'details'       =>  $details,
            'id'            =>  $id,
            'order_type'    =>  $order_type,
            'order_items'   =>  $order_items,
            'entered_by'    =>  $eb
        ]);

    }

    public function updateServiceDetails()
    {
        if(empty($this->request->params['args']))
        {
            return $this->redirector->to(PUBLIC_ROOT."solar-jobs/view-service-jobs");
        }
        $page_title = "Update Solar Service Job Details";
        $id = $this->request->params['args']['id'];


        Config::setJsConfig('curPage', "update-service-details");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/updateServiceJob.php',[
            'page_title'    =>  $page_title,
            'details'       =>  $details,
            'id'            =>  $id,
            'order_type'    =>  $order_type,
            'order_items'   =>  $order_items,
            'entered_by'    =>  $eb
        ]);
    }

    public function itemsUpdate()
    {
        if(!isset($this->request->params['args']['job']))
        {
            $error = true;
            $job_id = 0;
            $job = array();
            $job_items = array();
        }
        else
        {
            $error = false;
            $job_id = $this->request->params['args']['job'];
            $job = $this->solarorder->getOrderDetail($job_id);
            $job_items = $this->solarorder->getItemsForOrder($job_id);
        }
        //echo "<pre>",print_r($order_items),"</pre>";
        //render the page
        Config::setJsConfig('curPage', "items-update");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/itemsUpdate.php', [
            'page_title'    =>  "Update Items for Solar Job",
            'job_id'        =>  $job_id,
            'job'           =>  $job,
            'error'         =>  $error,
            'job_items'     =>  $job_items
        ]);
    }

    public function addOriginJob()
    {
        //render the page
        Config::setJsConfig('curPage', "add-origin-job");
        $form = $this->view->render( Config::get('VIEWS_PATH') . "forms/addoriginorder.php");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/addOriginJob.php', [
            'page_title'    =>  "Add Origin Install",
            'client_id'     =>  67,
            'order_type_id' =>  1,
            'form'          =>  $form
        ]);
    }

    public function addTljJob()
    {
        //render the page
        Config::setJsConfig('curPage', "add-tlj-job");
        $form = $this->view->render( Config::get('VIEWS_PATH') . "forms/addtljorder.php");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/addTLJJob.php', [
            'page_title'    =>  "Add TLJ Services Install",
            'client_id'     =>  67,
            'order_type_id' =>  2,
            'form'          =>  $form
        ]);
    }

    public function viewInstalls()
    {
        //echo "<pre>",print_r($this->request->params['args']),"</pre>";die();
        $order_type = "All Types";
        $type_id = 0;
        $ff = "Unfulfilled";
        $fulfilled = 0;
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['type']))
            {
                $type_id = $this->request->params['args']['type'];
                $order_type = $this->solarordertype->getSolarOrderType($type_id);
            }
            if(isset($this->request->params['args']['fulfilled']))
            {
                $fulfilled = $this->request->params['args']['fulfilled'];
                $ff = "Fulfilled";
            }
        }
        $page_title = "Latest Installs For $order_type";

        $orders = $this->solarorder->getSolarAllOrders($type_id, $fulfilled);
        //render the page
        Config::setJsConfig('curPage', "view-installs");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/viewInstalls.php', [
            'page_title'    =>  $page_title,
            'order_type'    =>  $order_type,
            'type_id'       =>  $type_id,
            'orders'        =>  $orders,
            'fulfilled'     =>  $fulfilled
        ]);
    }

    public function viewServiceJobs()
    {
        //echo "<pre>",print_r($this->request->params['args']),"</pre>";die();
        $order_type = "All Types";
        $type_id = 0;
        $ff = "Unfulfilled";
        $fulfilled = 0;
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['type']))
            {
                $type_id = $this->request->params['args']['type'];
                $order_type = $this->solarordertype->getSolarOrderType($type_id);
            }
            if(isset($this->request->params['args']['fulfilled']))
            {
                $fulfilled = $this->request->params['args']['fulfilled'];
                $ff = "Fulfilled";
            }
        }
        $page_title = "Latest Service Jobs For $order_type";

        $orders = $this->solarservicejob->getAllServiceJobs($type_id, $fulfilled);
        //render the page
        Config::setJsConfig('curPage', "view-service-jobs");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/viewServiceJobs.php', [
            'page_title'    =>  $page_title,
            'order_type'    =>  $order_type,
            'type_id'       =>  $type_id,
            'orders'        =>  $orders,
            'fulfilled'     =>  $fulfilled
        ]);
    }

    public function isAuthorized(){
        $action = $this->request->param('action');
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        $resource = "orders";

        //only for admin
        Permission::allow('admin', $resource, "*");
        Permission::allow('super admin', $resource, "*");
        Permission::allow('md admin', $resource, "*");
        //solar admin users
        Permission::allow('solar admin', $resource, "*");
        //warehouse users
        Permission::allow('warehouse', $resource, array(
            "jobSearch",
            "viewJobs",
        ));
        return Permission::check($role, $resource, $action);
    }
}
?>