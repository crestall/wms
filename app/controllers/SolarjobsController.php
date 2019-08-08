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

    public function addSolarInstallNew()
    {
        $page_title = "Add a Solar Install Job";

        Config::setJsConfig('curPage', "add-solar-install-new");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/addSolarJobNew.php',[
            'page_title'    =>  $page_title
        ]);
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
        $id = $this->request->params['args']['id'];

        $details = $this->solarorder->getOrderDetail($id);
        $order_items = $this->solarorder->getItemsForOrder($id);
        $order_type = $this->solarordertype->getSolarOrderType($details['type_id']);
        $eb = $this->user->getUserName( $details['entered_by'] );
        if(empty($eb))
        {
            $eb = "Automatically Imported";
        }
        /*  */
        Config::setJsConfig('curPage', "update-solar-install");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/updateInstall.php',[
            'page_title'    =>  $page_title,
            'details'       =>  $details,
            'id'            =>  $id,
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

        $details = $this->solarservicejob->getJobDetail($id);
        $order_type = $this->solarordertype->getSolarOrderType($details['type_id']);
        $eb = $this->user->getUserName( $details['entered_by'] );

        Config::setJsConfig('curPage', "update-service-details");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/updateServiceDetails.php',[
            'page_title'    =>  $page_title,
            'details'       =>  $details,
            'id'            =>  $id,
            'order_type'    =>  $order_type,
            'entered_by'    =>  $eb
        ]);
    }

    public function updateDetails()
    {
        if(empty($this->request->params['args']))
        {
            return $this->redirector->to(PUBLIC_ROOT."solar-jobs/view-installs");
        }
        $page_title = "Update Solar Install Job Details";
        $id = $this->request->params['args']['id'];

        $details = $this->solarorder->getOrderDetail($id);
        $order_type = $this->solarordertype->getSolarOrderType($details['type_id']);
        $eb = $this->user->getUserName( $details['entered_by'] );

        Config::setJsConfig('curPage', "update-install-details");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/updateInstallDetails.php',[
            'page_title'    =>  $page_title,
            'details'       =>  $details,
            'id'            =>  $id,
            'order_type'    =>  $order_type,
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

    public function serviceItemsUpdate()
    {
        if(!isset($this->request->params['args']['job']))
        {
            $error = true;
            $job_id = 0;
            $job = array();
            $job_items = array();
            $job_type = "";
        }
        else
        {
            $error = false;
            $job_id = $this->request->params['args']['job'];
            $job = $this->solarservicejob->getJobDetail($job_id);
            $job_items = $this->solarservicejob->getItemsForJob($job_id);
            $job_type = $this->solarordertype->getSolarOrderType($job['type_id']);
        }
        //echo "<pre>",print_r($order_items),"</pre>";
        //render the page
        Config::setJsConfig('curPage', "items-update");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/serviceItemsUpdate.php', [
            'page_title'    =>  "Update Items for Solar Service Job",
            'job_id'        =>  $job_id,
            'job'           =>  $job,
            'error'         =>  $error,
            'job_items'     =>  $job_items,
            'job_type'      =>  $job_type,
        ]);
    }

    public function addOriginInstall()
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

    public function addTljInstall()
    {
        //render the page
        Config::setJsConfig('curPage', "add-tlj-job");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/addTLJJob.php', [
            'page_title'    =>  "Add TLJ Services Install",
            'client_id'     =>  67,
            'order_type_id' =>  2
        ]);
    }

    public function addSolargainInstall()
    {
        //render the page
        Config::setJsConfig('curPage', "add-solargain-job");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/addSolargainJob.php', [
            'page_title'    =>  "Add Solargain Install",
            'client_id'     =>  67,
            'order_type_id' =>  3
        ]);
    }

    public function viewSolarTeamInstalls()
    {
        return $this->redirector->comingSoon();
    }

    public function viewSolarTeamServiceJobs()
    {
        return $this->redirector->comingSoon();
    }

    public function viewInstalls()
    {
        //echo "<pre>",print_r($this->request->params['args']),"</pre>";die();
        if(Session::getUserRole() == "solar")
        {
            return $this->viewSolarTeamInstalls();
        }
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
        if(Session::getUserRole() == "solar")
        {
            return $this->viewSolarTeamServiceJobs();
        }
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
        $resource = "solarjobs";

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
        //solar users
        Permission::allow('solar', $resource, array(
            "viewInstalls",
            "viewServiceJobs"
        ));
        return Permission::check($role, $resource, $action);
    }
}
?>