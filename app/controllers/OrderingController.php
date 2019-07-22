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

        //render the page
        Config::setJsConfig('curPage', "order-consumables");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/ordering/", Config::get('VIEWS_PATH') . 'ordering/orderConsumables.php', [
            'page_title'    =>  "Order Consumables For Truck"
        ]);
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