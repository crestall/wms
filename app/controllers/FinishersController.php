<?php

/**
 * Finishers controller
 *

 Manages Production Finishers

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class FinishersController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
        $this->Security->config("form", [ 'fields' => ['csrf_token']]);
    }

    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'suppliers-index');
        parent::displayIndex(get_class());
    }

    public function editFinisher()
    {
        $finisher_id = $this->request->params['args']['finisher'];
        $finisher_info = $this->productionfinisher->getFinisherById($finisher_id);
        $fcats = $this->finishercategories->getCategoriesForFinisher($finisher_id);
        $cat_ids = array();
        foreach($fcats as $fcat)
        {
            $cat_ids[] = $fcat['id'];
        }
        //render the page
        Config::setJsConfig('curPage', "edit-finisher");
        Config::set('curPage', "edit-finisher");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/finishers/", Config::get('VIEWS_PATH') . 'finishers/editFinisher.php', [
            'page_title'    =>  "Update Finisher for Production",
            'pht'           =>  ": Update Finisher Customer",
            'finisher_id'   =>  $finisher_id,
            'finisher'      =>  $finisher_info,
            'cat_ids'       =>  $cat_ids
        ]);
    }

    public function addFinisher()
    {
        //render the page
        Config::setJsConfig('curPage', "add-finisher");
        Config::set('curPage', "add-finisher");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/finishers/", Config::get('VIEWS_PATH') . 'finishers/addFinisher.php', [
            'page_title'    =>  "Add Finisher for Production",
            'pht'           =>  ": Add Production Finisher"
        ]);
    }

    public function viewFinishers()
    {
        $active = (isset($this->request->params['args']['active']))? $this->request->params['args']['active'] : 1;

        $finishers = $this->productionfinisher->getAllFinishers($active);
        //render the page
        Config::setJsConfig('curPage', "view-finishers");
        Config::set('curPage', "view-finishers");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/finishers/", Config::get('VIEWS_PATH') . 'finishers/viewFinishers.php', [
            'page_title'    =>  "View Production Finishers",
            'pht'           =>  ": Production Finishers",
            'active'        =>  $active,
            'finishers'     =>  $finishers
        ]);
    }

    public function viewFinisher()
    {
        if(!isset($this->request->params['args']['finisher']))
        {
            return (new ErrorsController())->error(404)->send();
        }
        $finisher_id = $this->request->params['args']['finisher'];
        $finisher_info = $this->productionfinisher->getFinisherById($finisher_id);
        //render the page
        Config::setJsConfig('curPage', "view-finisher");
        Config::set('curPage', "view-finisher");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/finishers/", Config::get('VIEWS_PATH') . 'finishers/viewFinisher.php', [
            'page_title'    =>  "Viewing ".$finisher_info['name'],
            'pht'           =>  ": ".$finisher_info['name'],
            'finisher'     =>  $finisher_info
        ]);
    }

    public function isAuthorized()
    {
        $action = $this->request->param('action');
        $role = Session::getUserRole();
        //$role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        $resource = "finishers";

        //only for admin
        Permission::allow('production admin', $resource, "*");
        //view only permissions
        Permission::allow(['production', 'production sales'], $resource,[
            'index',
            'viewFinishers'
        ]);
        //view, edit and add permissions
        Permission::allow('production sales admin', $resource, array(
            "index",
            "viewFinishers",
            "editFinisher",
            "addFinisher"
        ));

        return Permission::check($role, $resource, $action);
    }
}