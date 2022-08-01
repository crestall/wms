<?php

/**
 * Financials controller
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class AdminOnlyController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }


    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'admin-only-index');
        parent::displayIndex(get_class());
    }

    public function salesianBulkMailOz()
    {
        $sments = array();
        $line = 1;
        //$skip_first = isset($_POST['header_row']);
        $skip_first = true;
        $rfile = fopen(DOC_ROOT.'data/salesians_bulk_au.csv', 'r') or die('could not open');
        while (($row = fgetcsv($rfile)) !== FALSE)
        {
            if($skip_first)
            {
                $skip_first = false;
                continue;
            }
            $sments[] = $row;
        }
        Config::setJsConfig('curPage', "salesian-bulk-mail-oz");
        Config::set('curPage', "salesian-bulk-mail-oz");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/adminonly/", Config::get('VIEWS_PATH') . 'adminOnly/salesianBulksOz.php', [
            'page_title'    => "Salesian Bulk Mailouts AU",
            'pht'           =>  ": Salesian Bulk Mailouts AU",
            'sments'        =>  $sments
        ]);
    }

    public function InventoryComparing()
    {
        $client_id = 0;
        $active = 1;
        $client_name = "";
        //echo "<pre>",print_r($this->request->params),"</pre>";die();
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client_name = $this->client->getClientName($client_id);
            }
        }
        Config::setJsConfig('curPage', "inventory-comparing");
        Config::set('curPage', "inventory-comparing");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/adminonly/", Config::get('VIEWS_PATH') . 'adminOnly/inventoryCompare.php', [
            'page_title'    => "Inventory Comparing",
            'pht'           =>  ": Inventory Comparing",
            'client_id'     => $client_id,
            'client_name'   => $client_name
        ]);
    }

    public function chocolateImport()
    {
        $client_id = 0;
        $active = 1;
        $client_name = "";

        $sments = array();
        $line = 1;
        //$skip_first = isset($_POST['header_row']);
        $skip_first = true;
        $rfile = fopen(DOC_ROOT.'data/chocs.csv', 'r') or die('could not open');
        /*
        [0]     Organisation name
        [1]     Delivery person
        [2]     Address1
        [3]     Suburb
        [4]     State
        [5]     Postcode
        [6]     w
        [7]     l
        [8]     h
        [9]    kg
        */
        while (($row = fgetcsv($rfile)) !== FALSE)
        {
            if($skip_first)
            {
                $skip_first = false;
                continue;
            }
            $sments[] = $row;
        }
        //echo "<pre>",print_r($sments),"</pre>";//die();
        $cons = array();
        $con_id = 100;
        foreach($sments as $s)
        {
            $con = [
                'ConsignmentId'         => $con_id,
                'CustomerReference'     => 'Calvary',
                'ReceiverDetails'       => [
                    'ReceiverName'          => $s[1],
                    'AddressLine1'          => (strlen($s[0]) > 60) ? substr($s[0],0,60) : $s[0],
                    'AddressLine2'          => $s[2],
                    'Suburb'                => $s[3],
                    'State'                 => $s[4],
                    'Postcode'              => str_pad($s[5], 4, "0", STR_PAD_LEFT),
                    'ReceiverContactName'   => $s[1]
                ]
            ];
            $item = [
                        'SenderLineReference'   => 'EventKit',
                        'RateType'              => 'ITEM',
                        'Items'                 => 1,
                        'KGS'                   => $s[9],
                        'Length'                => $s[6],
                        'Width'                 => $s[7],
                        'Height'                => $s[8]
            ];
            $con['ConsignmentLineItems'][] = $item;
            $cons['ConsignmentList'][] = $con;
            ++$con_id;
        }
        //echo "<pre>",print_r($cons),"</pre>";die();
        $response = $this->directfreight->createConsignment($cons);


            echo "<p><a href='{$response['LabelURL']}' target='_blank'>{$response['LabelURL']}</a>";


        echo "<pre>",print_r($response),"</pre>";
        die();
        Config::setJsConfig('curPage', "chocolate-import");
        Config::set('curPage', "chocolate-import");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/adminonly/", Config::get('VIEWS_PATH') . 'adminOnly/chocolateImport.php', [
            'page_title'    => "Chocolate Importing",
            'client_id'     => $client_id,
            'active'		=> $active
        ]);
    }

    public function shopifyAPITesting()
    {
        //die('Done This');

        $config = array(
            'ShopUrl'        => 'https://arccos-golf-au.myshopify.com',
            'ApiKey'         => Config::get('ARCCOSSAPIKEY'),
            'Password'       => Config::get('ARCCOSSAPIPASS')
        );
        echo "<pre>",print_r($config),"</pre>";
        $post_body = [
            "location_id" => "64309952689",
            "tracking_number" => "ZQD5022938",
            "notify_customer" => true,
            "tracking_urls"   => [
                "https://auspost.com.au/track/ZQD5022938"
            ],
        ];
        //$shopify = $this->PbaArccosGolfShopify->resetConfig($config);
        //$result = $shopify->Order('4539735474353')->Fulfillment->post($post_body);
        /*
        try {
            $shopify = $this->PbaArccosGolfShopify->resetConfig($config);
            $result = $shopify->Order('4539735474353')->Fulfillment->post($post_body);
            echo "RESULT<pre>",print_r($result),"</pre>"; die();
        } catch (Exception $e) {
                //echo "RESULT<pre>",print_r($result),"</pre>"; die();
                echo "<pre>",print_r($e),"</pre>";die();
        }

        echo "RESULT<pre>",print_r($result),"</pre>"; die();
        */
        $params = array(
            'status'            => 'open',
            'financial_status'  => 'paid',
        );
        try {
            $shopify = $this->PbaArccosGolfShopify->resetConfig($config);
            $collected_orders = $shopify->Order->get($params);
        } catch (Exception $e) {
                echo "<pre>",print_r($e),"</pre>";die();
        }
        echo count($collected_orders)."<pre>",print_r($collected_orders),"</pre>"; die();

        //$this->BuzzBeeShopify->getAnOrder(1723);
        /*
        $config = array(
            'ShopUrl'        => 'https://buzzbeeaustralia.myshopify.com/',
            'ApiKey'         => Config::get('BBSHOPIFYAPIKEY'),
            'Password'       => Config::get('BBSHOPIFYAPIPASS')
        );

        try {
            $shopify = $this->BuzzBeeShopify->resetConfig($config);
            $shopify->Order(4681069002992)->Fulfillment->post([
                "location_id" => 54288547991,               //Get this from elsewhere in case it changes
                "tracking_number" => "ZQD5020275",
                "tracking_urls" => ["https://auspost.com.au/track/ZQD5020275"],
                "line_items"    => [['id'=> 12012338610416], ['id'=>12012338643184],['id'=>12012338675952],['id'=>12012338675952]],
                "notify_customer" => true
            ]);
            echo "<p>All GOOD</p>";
        } catch (Exception $e) {
            echo "<pre>",print_r($e),"</pre>";die();
        }

        $config = array(
            'ShopUrl'        => 'https://superspeedgolfau.myshopify.com',
            'ApiKey'         => Config::get('PBASUPERSPEEDGOLFSHOPIFYAPIKEY'),
            'Password'       => Config::get('PBASUPERSPEEDGOLFSHOPIFYAPIPASS')
        );
        try {
            $shopify = $this->PbaPerfectPracticeGolfShopify->resetConfig($config);
            $shopify->Order(4034465038485)->Fulfillment->post([
                "location_id" => $shopify->Location->get()[0]['id'],
                "tracking_number" => "3449580369308",
                "tracking_urls" => ["https://directfreight.com.au/"],
                "notify_customer" => true
            ]);
            echo "<p>All GOOD</p>";
        } catch (Exception $e) {
            echo "<pre>",print_r($e),"</pre>";die();
        }
        echo "<p>At the end</p>";
        */
    }

    public function marketplacerTesting()
    {
        Config::setJsConfig('curPage', "marketplacer-testing");
        Config::set('curPage', "marketplacer-testing");
        $result = $this->NuchevMarketplacer->getOrders();
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/adminonly/", Config::get('VIEWS_PATH') . 'adminOnly/marketplacerTesting.php', [
            'page_title'    =>  "Marketplacer Testing",
            'result'        => $result
        ]);
    }

    public function xeroTesting()
    {
        Config::setJsConfig('curPage', "xero-testing");
        Config::set('curPage', "xero-testing");
        //die("XERO TESTING");
        //$org = $this->xero_auth->getOrganisation();
        //$contacts = $this->xero_auth->getContacts();
        $invoices = $this->PBAXero->getInvoices();

        //echo "<pre>",print_r($invoices),"</pre>";die();


        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/adminonly/", Config::get('VIEWS_PATH') . 'adminOnly/xeroTesting.php', [
            'page_title'    =>  "Xero Testing",
            'invoices'      => $invoices,
        ]);
    }

    public function ebayAPITesting()
    {
        /*$this->PBAeBay->connect();
        $this->PBAeBay->fulfillAnOrder();
        */
        Config::setJsConfig('curPage', "ebay-api-testing");
        Config::set('curPage', "ebay-api-testing");
        //$this->ebayapi->firstAuthAppToken();
        $this->PBAeBay->connect();
        //die('connected');
        $orders = $this->PBAeBay->getCurrentOrders();
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/adminonly/", Config::get('VIEWS_PATH') . 'adminOnly/ebayApiTesting.php', [
            'page_title'    =>  "eBay API Testing",
            'orders'        => $orders
        ]);

    }
    /*
    public function updateProductionDatabaseTables()
    {
        Config::setJsConfig('curPage', "production-database-tables-update");
        Config::set('curPage', "production-database-tables-update");
        $production_finishers = $this->productionfinisher->getAllFinishers();
        $production_customers = $this->productioncustomer->getAllCustomers();
        $production_jobs = $this->productionjob->getAllJobs();
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/adminonly/", Config::get('VIEWS_PATH') . 'adminOnly/productionDatabaseTablesUpdate.php', [
            'page_title'    =>  "Production Database Tables Update",
            'pht'           =>  ": Production Database Tables Update",
            'production_finishers'  => $production_finishers,
            'production_customers'  => $production_customers,
            'production_jobs'       => $production_jobs
        ]);
    }
    */
    
    public function reeceDataTidy()
    {
        Config::setJsConfig('curPage', "reece-data-tidy");
        Config::set('curPage', "reece-data-tidy");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/adminonly/", Config::get('VIEWS_PATH') . 'adminOnly/reeceDataTidy.php', [
            'page_title'    =>  "Check and Clean Reece Data"
        ]);
    }

    public function runsheetCompletionTidy()
    {
        Config::setJsConfig('curPage', "runsheet-completion-tidy");
        Config::set('curPage', "runsheet-completion-tidy");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/adminonly/", Config::get('VIEWS_PATH') . 'adminOnly/runsheetCompletion.php', [
            'page_title'    =>  "Update Driver Runsheet completion Status"
        ]);
    }

    public function clientBayFixer()
    {
        $client_id = (isset($this->request->params['args']['client']))? $this->request->params['args']['client'] : 0;
        $client_name = $this->client->getClientName($client_id);
        $bays = $this->clientsbays->getCurrentBayUsage($client_id);
        Config::setJsConfig('curPage', "client-bay-fixer");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/adminonly/", Config::get('VIEWS_PATH') . 'adminOnly/clientBayFixer.php', [
            'page_title'    =>  "Client Bay Fixer",
            'client_name'   =>  $client_name,
            'client_id'     =>  $client_id,
            'bays'          =>  $bays
        ]);
    }

    public function dispatchedOrdersUpdater()
    {
        //echo "<pre>",print_r($this->request->params['args']),"</pre>";die();
        $client_name = "All Clients";
        $courier_id = -1;
        $client_id = 0;
        $fulfilled = 0;
        $state = "";
        if(!empty($this->request->params['args']))
        {
            if(isset($this->request->params['args']['client']))
            {
                $client_id = $this->request->params['args']['client'];
                $client_name = $this->client->getClientName($client_id);
            }
            if(isset($this->request->params['args']['courier']))
            {
                $courier_id = $this->request->params['args']['courier'];
            }
            if(isset($this->request->params['args']['state']))
            {
                $state = $this->request->params['args']['state'];
            }
        }
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $page_title = "Fulfilled Orders For $client_name";
        //$orders = $this->order->getUnfulfilledOrders($client_id, $courier_id, 0);     getAllOrders($client_id, $courier_id = -1, $fulfilled = 0, $store_order = -1)
        $orders = $this->order->getAllOrders($client_id, $courier_id, $fulfilled, 0, $state);
        //render the page
        Config::setJsConfig('curPage', "view-orders");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/orders/", Config::get('VIEWS_PATH') . 'adminOnly/viewOrders.php', [
            'page_title'    =>  $page_title,
            'client_name'   =>  $client_name,
            'client_id'     =>  $client_id,
            'courier_id'    =>  $courier_id,
            'orders'        =>  $orders,
            'fulfilled'     =>  $fulfilled,
            'state'         =>  $state,
            'from'          =>  $from,
            'to'            =>  $to,
            'date_filter'   =>  "Dispatched",
        ]);
    }

    public function eparcelShipmentDeleter()
    {
        Config::setJsConfig('curPage', "eparcel-shipment-deleter");
        Config::set('curPage', "eparcel-shipment-deleter");
        $clients = $this->client->getEparcelClients();
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/reports/", Config::get('VIEWS_PATH') . 'adminOnly/eparcelShipmentsDeleter.php',[
            'page_title'    =>  "Deleting eParcel Shipments",
            'clients'       => $clients
        ]);
    }

    public function encryptSomeShit()
    {
        //encrypts passwords for storing in the config file
        Config::setJsConfig('curPage', "encrypt-some-shit");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/adminonly/", Config::get('VIEWS_PATH') . 'adminOnly/encryptsomeshit.php', [
            'page_title'    =>  "Encrypt Some Shit"
        ]);
    }

    public function updateConfiguration()
    {
        //add sensitive config data - passwords and keys data
        Config::setJsConfig('curPage', "update-configuration");
        Config::set('curPage', "update-configuration");
        $configuration_names = $this->configuration->getConfigurationNames();
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/adminonly/", Config::get('VIEWS_PATH') . 'adminOnly/updateConfiguration.php', [
            'page_title'            => "Configuration Update",
            'configuration_names'   => $configuration_names
        ]);
    }

    public function apiTester()
    {
        //up the memory for this
        ini_set('memory_limit', '2048M');
        //$freedomMYOB = $this->freedomMYOB;
        $encryptedData = $this->FreedomMYOB->callTask('getMYOBOrders',array());
        $invoices =  json_decode($this->FreedomMYOB->getDecryptedData($encryptedData),true);
        //echo "<pre>",print_r($invoices),"</pre>"; //die();
        //echo "<hr/>";
        //$this->FreedomMYOB->processOrders($invoices);
        //echo "<pre>",print_r($orders),"</pre>";
        die();
        Config::setJsConfig('curPage', "api-tester");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/adminonly/", Config::get('VIEWS_PATH') . 'adminOnly/apiTester.php', [
            'page_title'            => "Test the APIs",
            'freedomMYOB'           => $this->FreedomMYOB
        ]);
    }

    public function sendTrackingEmails()
    {
        $db = Database::openConnection();
        $orders = $db->queryData("
            SELECT * FROM orders WHERE client_id = 6 AND date_fulfilled > 1541080800 AND store_order = 0 AND customer_emailed = 0
        ");
        foreach($orders as $o)
        {
            if( !empty($o['tracking_email']) )
            {
                //$this->output .= "Sending tracking email for {$od['order_number']}".PHP_EOL;
                echo "<p>Will send tracking email to {$o['tracking_email']}</p>";
                //$mailer->sendTrackingEmail($id);
                //Email::sendTrackingEmail($o['id']);
                $db->updateDatabaseField('orders', 'customer_emailed', 1, $o['id']);
            }
            else
            {
               echo "<p>No email for {$o['order_number']}</p>";
            }
        }
        echo "<pre>",print_r($orders),"</pre>";
    }

    public function isAuthorized(){
        $role = Session::getUserRole();
        $action = $this->request->param('action');
        $resource = "adminonly";
        // only for super admins
        Permission::allow('super admin', $resource, ['*']);
        return Permission::check($role, $resource, $action);
    }
}
?>