<?php

/**
 * Solar Teams controller
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
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
        Config::setJsConfig('curPage', "add-team");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarteams/", Config::get('VIEWS_PATH') . 'solarteams/addTeam.php',
        [
            'page_title'    =>  'Add New Solar Team'
        ]);
    }

    public function editTeam()
    {
        $team_id = $this->request->params['args']['team'];
        $team_info = $this->solarteam->getTeamById($team_id);
        //render the page
        Config::setJsConfig('curPage', "edit-team");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarteams/", Config::get('VIEWS_PATH') . 'solarteams/editTeam.php',
        [
            'page_title'     =>  'Edit Solar Team Details',
            'team'           =>  $team_info
        ]);
    }

    public function viewTeams()
    {
        $active = 1;
        if(!empty($this->request->params['args']))
        {
            $active = (isset($this->request->params['args']['active']))? $this->request->params['args']['active'] : 1;
        }
        $teams = $this->solarteam->getAllTeams($active);
        //render the page
        Config::setJsConfig('curPage', "view-teams");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/solarteams/", Config::get('VIEWS_PATH') . 'solarteams/viewTeams.php',
        [
            'page_title'    =>  'Manage Solar Teams',
            'teams'          =>  $teams,
            'active'        =>  $active
        ]);
    }

    public function isAuthorized(){
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        if( isset($role) && ($role === "admin"  || $role === "super admin" || $role === "solar admin") )
        {
            return true;
        }
        return false;
    }
}
?>