<?php

/**
 * User controller
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class UserController extends Controller{

    public function beforeAction(){
        parent::beforeAction();
    }

    public function addUser()
    {
        $client_role_id = $this->user->getClientRoleId();
        //render the page
        Config::setJsConfig('curPage', "add-user");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/users/", Config::get('VIEWS_PATH') . 'user/addUser.php',
        [
            'page_title'        =>  'Add New User',
            'client_role_id'    =>  $client_role_id
        ]);
    }

    public function profile(){
        //data
        $info = $this->user->getProfileInfo(Session::getUserId());

        //render the page
        Config::setJsConfig('curPage', "profile");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/users/", Config::get('VIEWS_PATH') . 'user/profile.php',[
            'page_title'    =>  "User Profile",
            'info'          =>  $info
        ]);
    }

    public function editUserProfile(){
        if(!isset($this->request->params['args']['user']))
        {
            return $this->redirector->to("/site-settings/manage-users");
        }
        //data
        $info = $this->user->getProfileInfo(Session::getUserId($this->request->params['args']['user']));

        //render the page
        Config::setJsConfig('curPage', "edit-user-profile");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/users/", Config::get('VIEWS_PATH') . 'user/editUserProfile.php',[
            'page_title'    =>  "Edit User Profile",
            'info'          =>  $info
        ]);
    }

    /**
     * users can report bugs, features, or enhancement
     * - Bug is an error you encountered
     * - Feature is a new functionality you suggest to add
     * - Enhancement is an existing feature, but you want to improve
     *
     */
    public function bugs(){
        Config::setJsConfig('curPage', "bugs");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/default/", Config::get('VIEWS_PATH') . 'bugs/index.php');
    }


    public function isAuthorized(){
        return true;
    }
}
