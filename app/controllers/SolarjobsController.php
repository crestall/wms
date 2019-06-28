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

    public function addOriginJob()
    {
        //render the page
        Config::setJsConfig('curPage', "add-origin-job");
        $form = $this->view->render( Config::get('VIEWS_PATH') . "forms/addoriginorder.php");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/addOriginJob.php', [
            'page_title'    =>  "Add Origin Order",
            'client_id'     =>  67,
            'form'          =>  $form
        ]);
    }

    public function viewJobs()
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
        $page_title = "$ff Orders For $order_type";

        $orders = $this->solarorder->getSolarAllOrders($type_id, $fulfilled);
        //render the page
        Config::setJsConfig('curPage', "view-jobs");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarjobs/", Config::get('VIEWS_PATH') . 'solarjobs/viewSolarJobs.php', [
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