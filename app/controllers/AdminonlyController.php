<?php

/**
 * Financials controller
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class adminonlyController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }

    public function eparcelShipmentDeleter()
    {
        $clients = $this->client->getEparcelClients();
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'adminOnly/eparcelShipmentsDeleter.php',[
            'page_title'    =>  "Deleting eParcel Shipments",
            'clients'       => $clients
        ]);
    }

    public function isAuthorized(){
        $role = Session::getUserRole();
        if( $role === "super admin" )
        {
            return true;
        }
        return false;
    }
}
?>