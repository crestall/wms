<?php

/**
 * Reports controller
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class ReportsController extends Controller
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

    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'reports-index');
        parent::displayIndex(get_class());
    }

    public function clientSpaceUsageReport()
    {
        $client_id = (isset($this->request->params['args']['client']))? $this->request->params['args']['client'] : 0;
        $client_name = $this->client->getClientName($client_id);
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('- 7 days', strtotime('monday this week 00:00:00'));
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : strtotime('monday this week 00:00:00');
        $bays = $this->clientsbays->getSpaceUsage($from, $to, $client_id);
        //echo "SPACES<pre>",print_r($bays),"</pre>"; die();
        Config::setJsConfig('curPage', "client-space-usage-report");
        Config::set('curPage', "client-space-usage-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/clientSpaceUsageReport.php',[
            'page_title'    =>  'Client Space Usage Report',
            'client_id'     =>  $client_id,
            'from'          =>  $from,
            'to'            =>  $to,
            'date_filter'   =>  "",
            'client_name'   =>  $client_name,
            'bays'          =>  $bays
        ]);
    }

    public function spaceUsageReport()
    {
        if( !(Session::getUserRole() == "client" || Session::getUserRole() == "freedom warehouse") )
        {
            //return $this->clientDispatchReport();
            return $this->error(403);
        }
        elseif(Session::isDeliveryClientUser())
        {
            return $this->clientDeliveryClientSpaceUsageReport();
        }
        else
        {
            return $this->clientClientSpaceUsageReport();
        }
    }

    private function clientDeliveryClientSpaceUsageReport()
    {
        //echo "this is for delivery clients";
        $client_id = Session::getUserClientId();
        $client_name = $this->client->getClientName($client_id);

        if(isset($this->request->params['args']['from']))
            $from = $this->request->params['args']['from'];
        elseif($client_id == 3)
            $from = strtotime('last saturday 00:00:00', mktime(0,0,0,date("m")-2,25,date("Y")));
        else
            $from = strtotime('first day of last month 00:00:00');

        if(isset($this->request->params['args']['to']))
            $to = $this->request->params['args']['to'];
        elseif($client_id == 3)
            $to = strtotime('last saturday 00:00:00', mktime(0,0,0,date("m")-1,25,date("Y")));
        else
            $to = strtotime('first day of this month 00:00:00');

        //$bays = $this->deliveryclientsbay->getClientSpaceUsage($date, $client_id);
        $bays = $this->deliveryclientsbay->getSpaceUsage($from, $to, $client_id);
        Config::setJsConfig('curPage', "space-usage-report");
        Config::set('curPage', "space-usage-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/clientDeliveryClientSpaceUsageReport.php',[
            'page_title'    =>  'Space Usage Report For '.$client_name,
            'pht'           =>  ':'.'Space Usage Report For '.$client_name,
            'client_id'     =>  $client_id,
            'from'          =>  $from,
            'to'            =>  $to, 
            'date_filter'   =>  "",
            'client_name'   =>  $client_name,
            'bays'          =>  $bays
        ]);
    }

    private function clientClientSpaceUsageReport()
    {
        //echo "this is for pick pack clients";
        $client_id = Session::getUserClientId();
        $client_name = $this->client->getClientName($client_id);
        $date = (isset($this->request->params['args']['date']))? $this->request->params['args']['date'] : strtotime('saturday this week 00:00:00');
        $bays = $this->clientsbays->getClientSpaceUsage($date, $client_id);
        Config::setJsConfig('curPage', "space-usage-report");
        Config::set('curPage', "space-usage-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/clientClientSpaceUsageReport.php',[
            'page_title'    =>  'Space Usage Report For '.$client_name,
            'client_id'     =>  $client_id,
            'date'          =>  $date,
            'date_filter'   =>  "Spaces Used Prior To",
            'client_name'   =>  $client_name,
            'bays'          =>  $bays
        ]);
    }

    public function deliveryClientSpaceUsageReport()
    {
        $client_id = (isset($this->request->params['args']['client']))? $this->request->params['args']['client'] : 0;
        $client_name = $this->client->getClientName($client_id);
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('first day of last month 00:00:00');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : strtotime('first day of this month 00:00:00');
        $bays = $this->deliveryclientsbay->getSpaceUsage($from, $to, $client_id);
        //echo "SPACES<pre>",print_r($bays),"</pre>"; die();
        Config::setJsConfig('curPage', "delivery-client-space-usage-report");
        Config::set('curPage', "delivery-client-space-usage-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/deliveryClientSpaceUsageReport.php',[
            'page_title'    =>  'Delivery Client Space Usage Report',
            'pht'           =>  ':Delivery Client Space Usage',
            'client_id'     =>  $client_id,
            'from'          =>  $from,
            'to'            =>  $to,
            'date_filter'   =>  "",
            'client_name'   =>  $client_name,
            'bays'          =>  $bays
        ]);
    }

    public function stockAtDate()
    {
        $date = (isset($this->request->params['args']['date']))? $this->request->params['args']['date'] : time();
        if(Session::getUserRole() == "client" || Session::getUserRole() == "freedom warehouse")
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
        Config::set('curPage', "stock-at-date");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/stockAtDate.php',[
            'page_title'            =>  'Stock At Date: '.date("d/m/Y", $date),
            'stock'                 =>  $stock,
            'date'                  =>  $date,
            'client_id'             =>  $client_id,
            'show_client_selector'  =>  $scs
        ]);
    }

    public function clientBaysUsageReport()
    {
        $client_id = (isset($this->request->params['args']['client']))? $this->request->params['args']['client'] : 0;
        $client_name = $this->client->getClientName($client_id);
        $bays = $this->location->getClientsBaysUsage($client_id);
        Config::setJsConfig('curPage', "client-bays-usage-report");
        Config::set('curPage', "client-bays-usage-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/clientBaysUsage.php',[
            'page_title'    => ucwords(strtolower($client_name)).' Bays In Use',
            'pht'           => ': Bays Used',
            'bays'          => $bays,
            'client_id'     => $client_id,
            'client_name'   => $client_name
        ]);
    }

    public function emptyBayReport()
    {
        $locations = $this->location->getEmptyLocations();
        Config::setJsConfig('curPage', "empty-bay-report");
        Config::set('curPage', "empty-bay-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/emptyBays.php',[
            'page_title'    => 'Empty Bay Report',
            'pht'           => ': Empty Bays',
            'locations'     => $locations
        ]);
    }

    public function locationReport()
    {
        $locations = $this->location->getLocationUsage();
        Config::setJsConfig('curPage', "location-report");
        Config::set('curPage', "location-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/locationReport.php',[
            'pht'           => ': Location Report',
            'page_title'    => 'Location Report',
            'locations'     => $locations
        ]);
    }

    public function deliveriesReport()
    {
        if(Session::getUserRole() == "client")
        {
            return $this->clientDeliveriesReport();
        }
        else
        {
            $client_id = (isset($this->request->params['args']['client']))? $this->request->params['args']['client'] : 0;
            $client_name = $this->client->getClientName($client_id);
            $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week 00:00:00');
            $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
            //$orders = $this->order->getDispatchedOrdersArray($from, $to, $client_id);
            $deliveries = $this->delivery->getClosedDeliveries($client_id, $from, $to);
        }
        Config::setJsConfig('curPage', "deliveries-report");
        Config::set('curPage', "deliveries-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/fsgDeliveryReport.php',[
            'page_title'    =>  'Deliveries Report',
            'client_id'     =>  $client_id,
            'from'          =>  $from,
            'to'            =>  $to,
            'date_filter'   =>  "Dispatched",
            'client_name'   =>  $client_name,
            'deliveries'    => $deliveries
        ]);
    }

    public function clientDeliveriesReport()
    {
        $client_id = Session::getUserClientId();
        $client_name = $this->client->getClientName($client_id);
        if(isset($this->request->params['args']['from']))
            $from = $this->request->params['args']['from'];
        elseif($client_id == 3)
            $from = strtotime('last saturday 00:00:00', mktime(0,0,0,date("m")-2,25,date("Y")));
        else
            $from = strtotime('first day of last month 00:00:00');

        if(isset($this->request->params['args']['to']))
            $to = $this->request->params['args']['to'];
        elseif($client_id == 3)
            $to = strtotime('last saturday 00:00:00', mktime(0,0,0,date("m")-1,25,date("Y")));
        else
            $to = strtotime('first day of this month 00:00:00');

        $deliveries = $this->delivery->getClosedDeliveries($client_id, $from, $to);
        Config::setJsConfig('curPage', "deliveries-report");
        Config::set('curPage', "deliveries-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/clientDeliveryReport.php',[
            'page_title'    =>  $client_name.' Deliveries Report',
            'pht'           =>  ":".$client_name.' Deliveries Report',
            'client_id'     =>  $client_id,
            'from'          =>  $from,
            'to'            =>  $to,
            'date_filter'   =>  "Dispatched",
            'client_name'   =>  $client_name,
            'deliveries'    => $deliveries
        ]);
    }

    public function pickupsReport()
    {
        if(Session::getUserRole() == "client")
        {
            return $this->clientPickupsReport();
        }
        else
        {
            $client_id = (isset($this->request->params['args']['client']))? $this->request->params['args']['client'] : 0;
            $client_name = $this->client->getClientName($client_id);
            $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('first day of last month 00:00:00');
            $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : strtotime('first day of this month 00:00:00');
            $pickups = $this->pickup->getClosedPickups($client_id, $from, $to);
        }
        Config::setJsConfig('curPage', "pickups-report");
        Config::set('curPage', "pickups-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/pickupsReport.php',[
            'page_title'    =>  'Pickups Report',
            'client_id'     =>  $client_id,
            'from'          =>  $from,
            'to'            =>  $to,
            'date_filter'   =>  "Received",
            'client_name'   =>  $client_name,
            'pickups'       =>  $pickups
        ]);
    }

    public function clientPickupsReport()
    {
        $client_id = Session::getUserClientId();
        $client_name = $this->client->getClientName($client_id);
        if(isset($this->request->params['args']['from']))
            $from = $this->request->params['args']['from'];
        elseif($client_id == 3)
            $from = strtotime('last saturday 00:00:00', mktime(0,0,0,date("m")-2,25,date("Y")));
        else
            $from = strtotime('first day of last month 00:00:00');

        if(isset($this->request->params['args']['to']))
            $to = $this->request->params['args']['to'];
        elseif($client_id == 3)
            $to = strtotime('last saturday 00:00:00', mktime(0,0,0,date("m")-1,25,date("Y")));
        else
            $to = strtotime('first day of this month 00:00:00');
        $pickups = $this->pickup->getClosedPickups($client_id, $from, $to);
        Config::setJsConfig('curPage', "pickups-report");
        Config::set('curPage', "pickups-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/clientPickupsReport.php',[
            'page_title'    =>  $client_name.' Pickups Report',
            'pht'           =>  ":".$client_name.' Pickups Report',
            'client_id'     =>  $client_id,
            'from'          =>  $from,
            'to'            =>  $to,
            'date_filter'   =>  "Received",
            'client_name'   =>  $client_name,
            'pickups'       =>  $pickups
        ]);
    }

    public function dispatchReport()
    {
        //die(Session::getUserRole());
        if(Session::getUserRole() == "client" || Session::getUserRole() == "freedom warehouse")
        {
            return $this->clientDispatchReport();
        }
        else
        {
            $client_id = (isset($this->request->params['args']['client']))? $this->request->params['args']['client'] : 0;
            $client_name = $this->client->getClientName($client_id);
            $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week 00:00:00');
            $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
            $orders = $this->order->getDispatchedOrdersArray($from, $to, $client_id);
        }
        Config::setJsConfig('curPage', "3pl-dispatch-report");
        Config::set('curPage', "dispatch-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/3plDispatch.php',[
            'page_title'    =>  'FSG Dispatch Report',
            'client_id'     =>  $client_id,
            'from'          =>  $from,
            'to'            =>  $to,
            'date_filter'   =>  "Dispatched",
            'client_orders' =>  $orders,
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name
        ]);
    }

    public function orderSerialNumbersReport()
    {
        if(Session::getUserRole() == "client")
        {
            return $this->clientOrderSerialNumbersReport();
        }
        else
        {
            $client_id = (isset($this->request->params['args']['client']))? $this->request->params['args']['client'] : 0;
            $client_name = $this->client->getClientName($client_id);
            $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week 00:00:00');
            $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
            $orders = $this->order->getUndispatchedOrdersWithSerialsArray($from, $to, $client_id);
        }
        Config::setJsConfig('curPage', "3pl-order-serials-report");
        Config::set('curPage', "3pl-order-serials-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/3plUndispatchedSerials.php',[
            'page_title'    =>  'FSG Undispatched With Invoices Report',
            'client_id'     =>  $client_id,
            'from'          =>  $from,
            'to'            =>  $to,
            'date_filter'   =>  "Ordered",
            'client_orders' =>  $orders,
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name
        ]);
    }
    /*
    public function pickupsReport()
    {
        $client_id = (isset($this->request->params['args']['client']))? $this->request->params['args']['client'] : 0;
        $client_name = $this->client->getClientName($client_id);
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week 00:00:00');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();

        $pickups = $this->recordedpickup->getPickups($from, $to, $client_id);

        Config::setJsConfig('curPage', "pickups-report");
        Config::set('curPage', "pickups-report");
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
        if(Session::getUserRole() == "client" || Session::getUserRole() == "freedom warehouse")
        {
            return $this->clientStockMovementReport();
        }
        else
        {
            $client_id = (isset($this->request->params['args']['client']))? $this->request->params['args']['client'] : 0;
            $client_name = $this->client->getClientName($client_id);
            $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('first day of this month 00:00:00');
            $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
            $movements = $this->itemmovement->getItemMovementsArray($client_id, $from, $to);
        }
        Config::setJsConfig('curPage', "3pl-stock-movement-report");
        Config::set('curPage', "stock-movement-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/3plStockMovement.php',[
            'page_title'        =>  'FSG Stock Movement Report',
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
        Config::set('curPage', "inventory-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/inventoryReport.php',[
            'page_title'    =>  'Current Inventory',
            'client_id'     =>  $client_id,
            'client_name'   =>  $client_name,
            'products'      =>  $products
        ]);
    }

    public function stockMovementSummary()
    {
        if(Session::getUserRole() == "client" || Session::getUserRole() == "freedom warehouse")
        {
            return $this->clientStockMovementSummary();
        }
        Config::setJsConfig('curPage', "3pl-stock-movement-summary");
        Config::set('curPage', "stock-movement-summary");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/3plStockMovementSummary.php',[
            'page_title'        =>  'FSG Stock Movement Summary'
        ]);
    }

    private function clientStockMovementSummary()
    {
        $client_id = Session::getUserClientId();
        $client_name = $this->client->getClientName($client_id);
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('first day of this month 00:00:00');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $exc = array($this->stockmovementlabels->getLabelId('Internal Stock Movement'));
        $movements = $this->itemmovement->getItemMovementsSummaryArray($client_id, $from, $to, $exc);
        Config::setJsConfig('curPage', "stock-movement-summary");
        Config::set('curPage', "stock-movement-summary");
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
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('first day of this month 00:00:00');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $exc = array($this->stockmovementlabels->getLabelId('Internal Stock Movement'));
        $movements = $this->itemmovement->getItemMovementsArray($client_id, $from, $to, $exc);
        Config::setJsConfig('curPage', "client-stock-movement-report");
        Config::set('curPage', "stock-movement-report");
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
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week 00:00:00');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $returns = $this->orderreturn->getReturnedOrdersArray($from, $to, $client_id);
        Config::setJsConfig('curPage', "returns-report");
        Config::set('curPage', "returns-report");
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
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week 00:00:00');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $goods = $this->inwardsgoods->getInwardsGoodsArray($from, $to);
        Config::setJsConfig('curPage', "goods-in-report");
        Config::set('curPage', "goods-in-report");
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
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week 00:00:00');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $goods = $this->outwardsgoods->getOutwardsGoodsArray($from, $to);
        Config::setJsConfig('curPage', "goods-out-report");
        Config::set('curPage', "goods-out-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/goodsoutReport.php',[
            'page_title'    =>  'Goods Out Report',
            'from'          =>  $from,
            'to'            =>  $to,
            'goods'         =>  $goods,
            'date_filter'   =>  'Sent'
        ]);
    }

    public function goodsInSummary()
    {
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week 00:00:00');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $summary = $this->inwardsgoods->getSummaryArray($from, $to);
        Config::setJsConfig('curPage', "goods-in-summary");
        Config::set('curPage', "goods-in-summary");
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
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week 00:00:00');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $summary = $this->outwardsgoods->getSummaryArray($from, $to);
        Config::setJsConfig('curPage', "goods-out-summary");
        Config::set('curPage', "goods-out-summary");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/goodsOutSummary.php',[
            'page_title'    =>  'Goods Out Summary',
            'from'          =>  $from,
            'to'            =>  $to,
            'summary'       =>  $summary,
            'date_filter'   =>  ''
        ]);
    }

    public function unloadedContainersReport()
    {
        if(Session::getUserRole() == "client")
        {
            return $this->clientUnloadedContainersReport();
        }
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week 00:00:00');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $unloaded_containers = $this->unloadedcontainer->getUnloadedContainersArray($from, $to);
        Config::setJsConfig('curPage', "unloaded-containers-report");
        Config::set('curPage', "unloaded-containers-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/unloadedContainers.php',[
            'page_title'            =>  'Unloaded Containers Report',
            'from'                  =>  $from,
            'to'                    =>  $to,
            'unloaded_containers'   =>  $unloaded_containers,
            'date_filter'           =>  'Unloaded'
        ]);
    }

    private function clientUnloadedContainersReport()
    {
        $client_id = Session::getUserClientId();
        $client_name = $this->client->getClientName($client_id);
        if(isset($this->request->params['args']['from']))
            $from = $this->request->params['args']['from'];
        elseif($client_id == 3)
            $from = strtotime('last saturday 00:00:00', mktime(0,0,0,date("m")-2,25,date("Y")));
        else
            $from = strtotime('first day of last month 00:00:00');

        if(isset($this->request->params['args']['to']))
            $to = $this->request->params['args']['to'];
        elseif($client_id == 3)
            $to = strtotime('last saturday 00:00:00', mktime(0,0,0,date("m")-1,25,date("Y")));
        else
            $to = strtotime('first day of this month 00:00:00');
        $unloaded_containers = $this->unloadedcontainer->getUnloadedContainersArray($from, $to, $client_id);
        //echo "<pre>",print_r($unloaded_containers),"</pre>";
        Config::setJsConfig('curPage', "client-unloaded-containers-report");
        Config::set('curPage', "unloaded-containers-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/clientUnloadedContainersReport.php',[
            'page_title'        =>  'Unloaded Containers For '.$client_name,
            'pht'               =>  ':Unloaded Containers For '.$client_name,
            'from'              =>  $from,
            'to'                =>  $to,
            'ucs'               =>  $unloaded_containers,
            'client_id'         =>  $client_id,
            'client_name'       =>  $client_name,
            'date_filter'       =>  'Unloaded'
        ]);
    }

    private function clientDispatchReport()
    {
        $client_id = Session::getUserClientId();
        $client_name = $this->client->getClientName($client_id);
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week 00:00:00');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $orders = $this->order->getDispatchedOrdersArray($from, $to, $client_id);
        $hidden = Config::get("HIDE_CHARGE_CLIENTS");
        Config::setJsConfig('curPage', "client-dispatch-report");
        Config::set('curPage', "dispatch-report");
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
        $role = Session::getUserRole();

        $action = $this->request->param('action');
        $resource = "reports";
        // all admins
        Permission::allow(['super admin', 'admin'], $resource, ['*']);

        //warehouse users
        Permission::allow('warehouse', $resource, array(
            'index'
        ));

        //all client users
        Permission::allow(['client', 'freedom warehouse'], $resource, array(
            'index',
            'unloadedContainersReport',
            'spaceUsageReport',
            "stockMovementReport",
            "stockMovementSummary",
            "stockAtDate"
        ));

        //only for delivery clients
        if(Session::isDeliveryClientUser())
        {
            Permission::allow('client', $resource,[
                "deliveriesReport",
                "pickupsReport"
            ]);
        }
        else
        {
            Permission::allow(['client', 'freedom warehouse'], $resource,[
                "dispatchReport",
                "returnsReport",
            ]);
        }
        return Permission::check($role, $resource, $action);
        //return false;
    }

    /* DEPRECATED 07/04/2022
    public function clientBayUsageReport()
    {
        $usage = $this->location->getAllClientsBayUsage();
        Config::setJsConfig('curPage', "client-bay-usage-report");
        Config::set('curPage', "client-bay-usage-report");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'reports/clientBayUsage.php',[
            'page_title'    => 'Client Bay Usage Report',
            'pht'           => ': Client Bay Usage',
            'usage'         => $usage
        ]);
    }
    */
}
?>