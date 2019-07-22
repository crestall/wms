<?php

/**
 * Ordering controller
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class orderingController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }

    public function orderConsumables()
    {
        return $this->redirector->comingSoon();
    }

    public function isAuthorized(){
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        if( isset($role) && ($role === "admin"  || $role === "super admin" || $role === "solar admin" || $role === "solar") )
        {
            return true;
        }
        return false;
    }
}
?>