<?php

/**
 * FSG Contacts controller
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class FsgContactsController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }

    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'fsg-contacts-index');
        parent::displayIndex(get_class());
    }

    public function addContact()
    {
        //render the page
        Config::setJsConfig('curPage', "add-contact");
        Config::set('curPage', "add-contact");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/salesreps/", Config::get('VIEWS_PATH') . 'salesreps/addRep.php',
        [
            'page_title'    =>  'Add New FSG Contact'
        ]);
    }

    public function editContact()
    {
        $rep_id = $this->request->params['args']['contact'];
        $rep_info = $this->salesrep->getRepById($rep_id);
        //render the page
        Config::setJsConfig('curPage', "edit-contact");
        Config::set('curPage', "edit-contact");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/salesreps/", Config::get('VIEWS_PATH') . 'salesreps/editRep.php',
        [
            'page_title'    =>  'Edit FSG Contact details',
            'rep'           =>  $rep_info
        ]);
    }

    public function viewContacts()
    {
        $active = 1;
        if(!empty($this->request->params['args']))
        {
            $active = (isset($this->request->params['args']['active']))? $this->request->params['args']['active'] : 1;
        }
        $reps = $this->salesrep->getAllReps($active);
        $role = Session::getUserRole();
        //render the page
        Config::setJsConfig('curPage', "view-contacts");
        Config::set('curPage', "view-contacts");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/salesreps/", Config::get('VIEWS_PATH') . 'salesreps/viewReps.php',
        [
            'page_title'    =>  'Manage FSG Contacts',
            'reps'          =>  $reps,
            'role'          =>  $role,
            'active'        =>  $active
        ]);
    }

    public function isAuthorized(){
        $action = $this->request->param('action');
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        $resource = "fsgcontacts";

        //only for admin
        Permission::allow('production admin', $resource, "*");
        //production users
        Permission::allow('production', $resource, array(
            "index",
            "viewContacts"
        ));

        return Permission::check($role, $resource, $action);
    }
}
?>