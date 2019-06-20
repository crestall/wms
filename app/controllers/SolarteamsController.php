<?php

/**
 * Solar Teams controller
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class solarteamsController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }

    public function shipToReps()
    {
        $client_id = (isset($this->request->params['args']['client']))? $this->request->params['args']['client'] : 0;
        //render the page
        Config::setJsConfig('curPage', "shipto-reps");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/salesreps/", Config::get('VIEWS_PATH') . 'salesreps/shipToRep.php',
        [
            'page_title'    => 'Ship Consignment To Sales Rep',
            'client_id'     => $client_id
        ]);
    }

    public function addTeam()
    {
        //render the page
        Config::setJsConfig('curPage', "add-solar-team");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarteams/", Config::get('VIEWS_PATH') . 'solarteams/addTeam.php',
        [
            'page_title'    =>  'Add New Solar Team'
        ]);
    }

    public function editSalesRep()
    {
        $rep_id = $this->request->params['args']['rep'];
        $rep_info = $this->salesrep->getRepById($rep_id);
        //render the page
        Config::setJsConfig('curPage', "edit-sales-rep");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/salesreps/", Config::get('VIEWS_PATH') . 'salesreps/editRep.php',
        [
            'page_title'    =>  'Edit Sales Rep details',
            'rep'           =>  $rep_info
        ]);
    }

    public function viewReps()
    {
        $active = 1;
        if(!empty($this->request->params['args']))
        {
            $active = (isset($this->request->params['args']['active']))? $this->request->params['args']['active'] : 1;
        }
        $reps = $this->salesrep->getAllReps($active);
        //render the page
        Config::setJsConfig('curPage', "view-reps");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/salesreps/", Config::get('VIEWS_PATH') . 'salesreps/viewReps.php',
        [
            'page_title'    =>  'Manage Sales Reps',
            'reps'          =>  $reps,
            'active'        =>  $active
        ]);
    }

    public function isAuthorized(){
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        if( isset($role) && ($role === "admin"  || $role === "super admin") )
        {
            return true;
        }
        return false;
    }
}
?>