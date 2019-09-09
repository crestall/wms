<?php

/**
 * Reports controller
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class reportsController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }
    /*
    public function clientDailyReports()
    {
        $todays_reports = $this->clientreportssent->getTodaysReports();
        Config::setJsConfig('curPage', "client-daily-reports");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/clientDailyReports.php',[
            'page_title'        =>  "Send Daily Reports to Client",
            'todays_reports'    => $todays_reports
        ]);
    }
    */

    public function solarConsumablesReorder()
    {
        $products = $this->item->getSolarConsumablesReordering();
        Config::setJsConfig('curPage', "solar-consumables-reorder");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/solarConsumablesReorder.php',[
            'page_title'    =>  'Solar Consumables Requiring Ordering',
            'products'      => $products
        ]);
    }

    public function stockAtDate()
    {
        $date = (isset($this->request->params['args']['date']))? $this->request->params['args']['date'] : time();
        if(Session::getUserRole() == "client")
        {
            $client_id = Session::getUserClientId();
            $scs = false;
        }
        else
        {
            $client_id = (isset($this->request->params['args']['client']))? $this->request->params['args']['client'] : 0;
            $scs = true;
        }
        $stock = $this->itemmovement->getStockAtDateArray($client_id, $date);

        Config::setJsConfig('curPage', "stock-at-date");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/stockAtDate.php',[
            'page_title'            =>  'Stock At Date: '.date("d/m/Y", $date),
            'stock'                 =>  $stock,
            'date'                  =>  $date,
            'client_id'             =>  $client_id,
            'show_client_selector'  =>  $scs
        ]);
    }

    public function clientSpaceUsageReport()
    {
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $bays = $this->clientsbays->getBayUsage($from, $to);
        Config::setJsConfig('curPage', "client-bay-usage");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/clientBayUsage.php',[
            'page_title'    => 'Client Spaces Usage Report',
            'from'          =>  $from,
            'to'            =>  $to,
            'date_filter'   =>  "",
            'bays'          => $bays['data'],
            'fridays'       => $bays['fridays']
        ]);
    }

    public function emptyBayReport()
    {
        $locations = $this->location->getEmptyLocations();
        Config::setJsConfig('curPage', "empty-bay-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/emptyBays.php',[
            'page_title'    => 'Empty Bay Report',
            'locations'     => $locations
        ]);
    }

    public function locationReport()
    {
        $locations = $this->location->getLocationUsage();
        Config::setJsConfig('curPage', "location-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/locationReport.php',[
            'page_title'    => 'Location Report',
            'locations'     => $locations
        ]);
    }

    public function dispatchReport()
    {
        if(Session::getUserRole() == "client")
        {
            return $this->clientDispatchReport();
        }
        else
        {
            $client_id = (isset($this->request->params['args']['client']))? $this->request->params['args']['client'] : 0;
            $client_name = $this->client->getClientName($client_id);
            $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week');
            $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
            $orders = $this->order->getDispatchedOrdersArray($from, $to, $client_id);
        }
        Config::setJsConfig('curPage', "3pl-dispatch-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/3plDispatch.php',[
            'page_title'    =>  '3PL Dispatch Report',
            'client_id'     =>  $client_id,
            'from'          =>  $from,
            'to'            =>  $to,
            'date_filter'   =>  "Dispatched",
            'client_orders' =>  $orders,
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name
        ]);
    }

    public function pickupsReport()
    {
        $client_id = (isset($this->request->params['args']['client']))? $this->request->params['args']['client'] : 0;
        $client_name = $this->client->getClientName($client_id);
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();

        $pickups = $this->recordedpickup->getPickups($from, $to, $client_id);

        Config::setJsConfig('curPage', "pickups-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/pickups.php',[
            'page_title'    =>  'Pickups Report',
            'client_id'     =>  $client_id,
            'from'          =>  $from,
            'to'            =>  $to,
            'date_filter'   =>  "Dispatched",
            'pickups'       =>  $pickups,
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name
        ]);
    }
    /*
    public function carrierReport()
    {
        $carriert_id = (isset($this->request->params['args']['client']))? $this->request->params['args']['client'] : 0;
        //$client_name = $this->client->getClientName($client_id);
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $orders = $this->order->getDispatchedOrdersArray($from, $to, $client_id);

        Config::setJsConfig('curPage', "carrier-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/carrierReport.php',[
            'page_title'    =>  'Carier Report',
            'client_id'     =>  $client_id,
            'from'          =>  $from,
            'to'            =>  $to,
            'date_filter'   =>  "Dispatched",
            'client_orders' =>  $orders,
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name
        ]);
    }
    */
    public function stockMovementReport()
    {
        if(Session::getUserRole() == "client")
        {
            return $this->clientStockMovementReport();
        }
        else
        {
            $client_id = (isset($this->request->params['args']['client']))? $this->request->params['args']['client'] : 0;
            $client_name = $this->client->getClientName($client_id);
            $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('first day of this month');
            $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
            $movements = $this->itemmovement->getItemMovementsArray($client_id, $from, $to);
        }
        Config::setJsConfig('curPage', "3pl-stock-movement-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/3plStockMovement.php',[
            'page_title'        =>  '3PL Stock Movement Report',
            'from'              =>  $from,
            'to'                =>  $to,
            'date_filter'       =>  "",
            'client_id'         =>  $client_id,
            'client_name'       =>  $client_name,
            'movements'         =>  $movements
        ]);
    }

    public function inventoryReport()
    {
        $client_id = 0;
        $active = 1;
        $client_name = "";
        $products = array();
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client_name = $this->client->getClientName($client_id);
                //$products = $this->item->getItemsForClient($client_id, $active);
                $products = $this->item->getClientInventoryArray($client_id, $active);
            }
        }
        Config::setJsConfig('curPage', "inventory-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/inventoryReport.php',[
            'page_title'    =>  'Current Inventory',
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name,
            'products'      =>  $products
        ]);
    }

    public function stockMovementSummary()
    {
        if(Session::getUserRole() == "client")
        {
            return $this->clientStockMovementSummary();
        }
        Config::setJsConfig('curPage', "3pl-stock-movement-summary");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/3plStockMovementSummary.php',[
            'page_title'        =>  '3PL Stock Movement Summary'
        ]);
    }

    private function clientStockMovementSummary()
    {
        $client_id = Session::getUserClientId();
        $client_name = $this->client->getClientName($client_id);
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('first day of this month');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $exc = array($this->stockmovementlabels->getLabelId('Internal Stock Movement'));
        $movements = $this->itemmovement->getItemMovementsSummaryArray($client_id, $from, $to, $exc);
        Config::setJsConfig('curPage', "client-stock-movement-summary");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/clientStockMovementSummary.php',[
            'page_title'        =>  ucwords(strtolower($client_name)).' Stock Movement Summary',
            'from'              =>  $from,
            'to'                =>  $to,
            'date_filter'       =>  "",
            'client_id'         =>  $client_id,
            'client_name'       =>  $client_name,
            'movements'         =>  $movements
        ]);
    }

    private function clientStockMovementReport()
    {
        $client_id = Session::getUserClientId();
        $client_name = $this->client->getClientName($client_id);
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('first day of this month');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $exc = array($this->stockmovementlabels->getLabelId('Internal Stock Movement'));
        $movements = $this->itemmovement->getItemMovementsArray($client_id, $from, $to, $exc);
        Config::setJsConfig('curPage', "client-stock-movement-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/clientStockMovement.php',[
            'page_title'        =>  ucwords(strtolower($client_name)).' Stock Movement Report',
            'from'              =>  $from,
            'to'                =>  $to,
            'date_filter'       =>  "",
            'client_id'         =>  $client_id,
            'client_name'       =>  $client_name,
            'movements'         =>  $movements
        ]);
    }

    public function returnsReport()
    {
        $client_id = Session::getUserClientId();
        $client_name = $this->client->getClientName($client_id);
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $returns = $this->orderreturn->getReturnedOrdersArray($from, $to, $client_id);
        Config::setJsConfig('curPage', "returns-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/returnsReport.php',[
            'page_title'        =>  'Returns Report',
            'from'              =>  $from,
            'to'                =>  $to,
            'client_id'         =>  $client_id,
            'client_name'       =>  $client_name,
            'returns'           =>  $returns
        ]);
    }

    public function goodsInReport()
    {
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $goods = $this->inwardsgoods->getInwardsGoodsArray($from, $to);
        Config::setJsConfig('curPage', "goods-in-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/goodsinReport.php',[
            'page_title'    =>  'Goods In Report',
            'from'          =>  $from,
            'to'            =>  $to,
            'goods'         =>  $goods,
            'date_filter'   =>  'Returned'
        ]);
    }

    public function goodsOutReport()
    {
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $goods = $this->outwardsgoods->getOutwardsGoodsArray($from, $to);
        Config::setJsConfig('curPage', "goods-out-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/goodsoutReport.php',[
            'page_title'    =>  'Goods Out Report',
            'from'          =>  $from,
            'to'            =>  $to,
            'goods'         =>  $goods,
            'date_filter'   =>  'Sent'
        ]);
    }

    public function truckRunSheet()
    {
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $runs = $this->truckusage->getRunSheetArray($from, $to);
        Config::setJsConfig('curPage', "truck-run-sheet");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/truckRunSheet.php',[
            'page_title'    =>  'Truck Run Sheet',
            'from'          =>  $from,
            'to'            =>  $to,
            'runs'          =>  $runs,
            'date_filter'   =>  'Dispatched'
        ]);
    }

    public function goodsInSummary()
    {
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $summary = $this->inwardsgoods->getSummaryArray($from, $to);
        Config::setJsConfig('curPage', "goods-in-summary");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/goodsInSummary.php',[
            'page_title'    =>  'Goods In Summary',
            'from'          =>  $from,
            'to'            =>  $to,
            'summary'       =>  $summary,
            'date_filter'   =>  ''
        ]);
    }

    public function goodsOutSummary()
    {
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $summary = $this->outwardsgoods->getSummaryArray($from, $to);
        Config::setJsConfig('curPage', "goods-out-summary");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/goodsOutSummary.php',[
            'page_title'    =>  'Goods Out Summary',
            'from'          =>  $from,
            'to'            =>  $to,
            'summary'       =>  $summary,
            'date_filter'   =>  ''
        ]);
    }

    public function solarReturnsReport()
    {
        return $this->redirector->comingSoon();
    }

    public function unloadedContainersReport()
    {
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $unloaded_containers = $this->unloadedcontainer->getUnloadedContainersArray($from, $to);
        Config::setJsConfig('curPage', "unloaded-containers-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/unloadedContainers.php',[
            'page_title'            =>  'Unloaded Containers Report',
            'from'                  =>  $from,
            'to'                    =>  $to,
            'unloaded_containers'   =>  $unloaded_containers,
            'date_filter'           =>  ''
        ]);
    }

    private function clientDispatchReport()
    {
        $client_id = Session::getUserClientId();
        $client_name = $this->client->getClientName($client_id);
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $orders = $this->order->getDispatchedOrdersArray($from, $to, $client_id);
        $hidden = Config::get("HIDE_CHARGE_CLIENTS");
        Config::setJsConfig('curPage', "client-dispatch-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/clientDispatch.php',[
            'page_title'        =>  'Client Dispatch Report',
            'from'              =>  $from,
            'to'                =>  $to,
            'date_filter'       =>  "Dispatched",
            'client_orders'     =>  $orders,
            'hidden'            =>  $hidden,
            'client_id'         =>  $client_id,
            'client_name'       =>  $client_name
        ]);
    }

    public function isAuthorized(){
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        if( isset($role) && ($role === "admin"  || $role === "super admin") )
        {
            return true;
        }
        $action = $this->request->param('action');
        $resource = "reports";

        //warehouse users
        Permission::allow('warehouse', $resource, array(

        ));

        //solar admin users
        Permission::allow('solar admin', $resource, array(
            "jobsReport",
            "solarReturnsReport",
            "solarConsumablesReorder"
        ));

        //client users
        Permission::allow('client', $resource, array(
            "dispatchReport",
            "returnsReport",
            "stockMovementReport",
            "stockMovementSummary",
            "stockAtDate"
        ));

        return Permission::check($role, $resource, $action);
        return false;
    }
}
?>