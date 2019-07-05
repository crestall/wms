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
            'page_title'    =>  $page_title
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
        /*
        Config::setJsConfig('curPage', "update-solar-install");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/addServiceJob.php',[
            'page_title'    =>  $page_title
        ]);
        */
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

    public function addOriginServiceJob()
    {
        $type_id = $this->controller->solarordertype->getTypeId('origin');
        //render the page
        Config::setJsConfig('curPage', "add-origin-service-job");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/addOriginServiceJob.php', [
            'page_title'    =>  "Add Origin Service Job",
            'client_id'     =>  67,
            'type_id'       =>  $type_id
        ]);
    }

    public function addTljServiceJob()
    {
        //render the page
        Config::setJsConfig('curPage', "add-tlj-service-job");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/addTLJServiceJob.php', [
            'page_title'    =>  "Add TLJ Services Service Job",
            'client_id'     =>  67,
            'order_type_id' =>  2
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