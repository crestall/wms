<?php

/**
 * Tasks controller
 *

 Handles scheduled tasks - cron jobs

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class TasksController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }

    public function initialize()
    {
        //no authentication component needed
        $this->loadComponents([
            'Security'
        ]);
    }

    public function sendArccosReports()
    {
        if(!isset($this->request->params['args']) || $this->request->params['args']['ua'] !== "FSG")
        {
            return $this->error(403);
        }
        else
        {
            //$todays_reports = $this->clientreportssent->getTodaysReports();
            $from = strtotime('monday last week 00:00:00');
            $to = strtotime('saturday last week 00:00:00');

            $output = "=========================================================================================================".PHP_EOL;
            $output .= "SENDING ARCCOS REPORTS FOR ".date("jS M Y (D), g:i a (T)").PHP_EOL;
            $output .= "=========================================================================================================".PHP_EOL;

                //Dispatch Report
                $filenames = array();
                $output .= "----------------------------------------------------------------------------------------------------".PHP_EOL;
                $orders = $this->order->getDispatchedOrdersArray($from, $to, 87, true);
                $inventory = $this->item->getArccosInventory();
                //echo "<pre>",print_r($orders),"</pre>"; die();
                $output .= "Doing dispatch report for Arccos Golf".PHP_EOL;
                if(!count($orders))
                {
                    $output .= "No orders dispatched this week".PHP_EOL;
                }
                else
                {
                    $filename = tempnam(sys_get_temp_dir(), 'arccos_dispatch_report_') . '.csv';
                    $filenames[] = $filename;
                    $fp = fopen($filename, 'w');
                    $headers = array(
                        "Date Ordered",
                        "Date Dispatched",
                        "WMS Order Number",
                        "Your Order Number",
                        "Shipped To",
                        "Country",
                        "Consignment ID",
                        "Total Items",
                        "Items"
                    );
                    fputcsv($fp, $headers);
                    foreach($orders as $o)
                    {
                        $row = array(
                            $o['date_ordered'],
                            $o['date_fulfilled'],
                            $o['order_number'],
                            $o['client_order_number'],
                            str_replace("<br/>", ", ",$o['shipped_to']),
                            $o['country'],
                            $o['consignment_id'],
                            $o['total_items'],
                            str_replace("<br/>", "",$o['items'])
                        );
                    	fputcsv($fp, $row);
                    }
                    fclose($fp);
                    $output .= "Dispatch report with $filename will be sent for Arccos".PHP_EOL;
                }
                //Inventory
                $output .= "Doing dispatch report for Arccos Golf".PHP_EOL;
                if(!count($inventory))
                {
                    $output .= "No ARCCOS inventory found".PHP_EOL;
                }
                else
                {
                    $filename = tempnam(sys_get_temp_dir(), 'arccos_inventory_report_') . '.csv';
                    $filenames[] = $filename;
                    $fp = fopen($filename, 'w');
                    $headers = array(
                        "Name",
                        "SKU",
                        "Total On Hand",
                        "Currently Allocated",
                        "Under Quality Control",
                        "Total Available"
                    );
                    fputcsv($fp, $headers);

                    foreach($inventory as $i)
                    {
                        $row = array(
                            $i['name'],
                            $i['sku'],
                            $i['qty'],
                            $i['allocated'],
                            $i['qc_count'],
                            $i['qty'] - $i['allocated'] - $i['qc_count']
                        );
                    	fputcsv($fp, $row);
                    }
                    fclose($fp);
                    $output .= "Inventory report with $filename will be sent for Arccos".PHP_EOL;
                }

                if(count($filenames))
                {
                    //send the mail
                    Email::sendArccosReport($filenames);
                    /*delete the files  */
                    foreach($filenames as $f)
                    {
                        unlink($f);
                    }
                    $output .= "All reports sent for Arccos".PHP_EOL;
                }
                $output .= "----------------------------------------------------------------------------------------------------".PHP_EOL;

            Logger::logReportsSent('sent_reports/log', $output); //die();
        }
    }

    public function BBShopifyTask()
    {
        if(!isset($this->request->params['args']['ua']) || !($this->request->params['args']['ua'] === "FSG" || $this->request->params['args']['ua'] === "CRON"))
        {
            return $this->error(403);
        }
        else
        {
            $this->BuzzBeeShopify->getOrders();
        }
    }

    public function PBATestTask()
    {
        if(!isset($this->request->params['args']) || $this->request->params['args']['ua'] !== "FSG")
        {
            return $this->error(403);
        }
        else
        {
            $this->shopify->getPBAOrders();
        }
        echo "done";
    }

    public function BDSCollectionTask()
    {
        if(!isset($this->request->params['args']['ua']) || $this->request->params['args']['ua'] !== "FSG")
        {
            return $this->error(403);
        }
        else
        {
            if($this->BdsFTP->openConnection('/bdsorders'))
            {
                $responses = array();
                $files = $this->BdsFTP->getFileNames();
                $files_processed = 0;
                foreach($files as $file)
                {
                    if($this->BdsFTP->getFileSize($file) === -1)
                        continue;
                    //echo "<p>Will now process - $file</p>";
                    $responses[] = $this->BdsFTP->collectOrders($file);
                    ++$files_processed;
                    //echo "<p>Have now processed - $file</p>";
                    $this->BdsFTP->renameFile($file, 'collected_orders/'.$file);
                }
                $this->BdsFTP->closeConnection();
                if($files_processed > 0)
                {
                    foreach($responses as $response)
                    {
                        Email::sendBDSImportFeedback($response);
                    }
                }
                else
                {
                    Email::sendBDSNoOrdersFeedback();
                }

                exit();
            }
        }
    }

    public function BDSCompletionTask()
    {
        if(!isset($this->request->params['args']) || $this->request->params['args']['ua'] !== "FSG")
        {
            return $this->error(403);
        }
        else
        {
            if($this->BdsFTP->openConnection('/bdsorders/processed_orders'))
            {
                 //echo "gonna do it";
                $client_id = 86;
                $orders = $this->order->getUnFTPedOrdersArray($client_id);
                //echo "<pre>",print_r($orders),"</pre>"; die();
                $close = true;
                if(count($orders))
                {
                    $cols = array(
                        "Date Ordered",
                        "Entered By",
                        "Date Dispatched",
                        "FSG Order Number",
                        "BDS Order Number",
                        "Shipped To",
                        'Total Items',
                        'Handling Charge',
                        'Postage Charge',
                        'Total Charge (GST Ex)',
                        'GST',
                        'Total Charge (GST Inc)',
                        "Courier",
                        "Consignment ID",
                        "Tracking URL",
                    );

                    $rows = array();
                    $extra_cols = 0;
                    foreach($orders as $o)
                    {
                        $row = array(
                            $o['date_ordered'],
                            $o['entered_by'],
                            $o['date_fulfilled'],
                            $o['order_number'],
                            "#".$o['client_order_number'],
                            str_replace("<br/>", ", ",$o['shipped_to']),
                            $o['total_items'],
                            $o['handling_charge'],
                            $o['postage_charge'],
                            $o['total_exgst'],
                            $o['gst'],
                            $o['total_gstinc'],
                            $o['courier'],
                            $o['consignment_id'],
                            $o['tracking_url']
                        );
                        $extra_cols = max($extra_cols, count($o['csv_items']));
                        $i = 1;
                        foreach($o['csv_items'] as $array)
                        {
                            $row[] = $array['name'];
                            $row[] = $array['qty'];
                            $row[] = $array['cpid'];
                            $row[] = $array['coiid'];
                            ++$i;
                        }
                        $rows[] = $row;
                        $this->order->updateFTPUploaded($o['order_id']);
                    }
                    $i = 1;
                    while($i <= $extra_cols)
                    {
                        $cols[] = "Item $i Name";
                        $cols[] = "Item $i Qty";
                        $cols[] = "Item $i SKU";
                        $cols[] = "Item $i Order Item ID";
                        ++$i;
                    }
                    $csvData = array(
                        'cols'  => $cols,
                        'rows'  => $rows
                    );
                    if( !$this->BdsFTP->uploadCSVFile($csvData) )
                    {
                        $close = false;
                    }
                }
                if($close)
                {
                    $this->BdsFTP->closeConnection();
                }
                else
                {
                    die("cannot close connection");
                }
                Email::sendBDSFinaliseFeedback(count($orders));
            }
            //$this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "bsd_dispatch_report_".date("Ymd")]);
        }
    }

    public function productionJobReminderTask()
    {
        if(!isset($this->request->params['args']['ua']) || $this->request->params['args']['ua'] !== "FSG")
        {
            return $this->error(403);
        }
        else
        {
            //Email::sendNewUserEmail('Mark Solly', 'mark@solly.com.au');
            $dd_jobs = $this->productionjob->getStrictDueDateJobs();
            //echo "<pre>",print_r($dd_jobs),"</pre>";
            $today = strtotime('today');
            $output = "=========================================================================================================".PHP_EOL;
            $output .= "SENDING PRODUCTION REMINDER EMAILS FOR ".date("jS M Y (D), g:i a (T)").PHP_EOL;
            $output .= "=========================================================================================================".PHP_EOL;
            foreach($dd_jobs as $job)
            {
                if( filter_var($job['due_date'], FILTER_VALIDATE_INT) )
                {
                    if( ($job['due_date'] < $today) )
                    {
                        //echo "<p>Will send the 'You Fucked Up email</p>";
                    }
                    elseif( ($job['due_date'] - $today) <= (2 * 24 * 60 * 60))
                    {
                        if(Email::sendProductionJobReminder($job))
                        {
                            $output .= "Email sent for JOB: ".$job['job_id'].PHP_EOL;
                        }
                        else
                        {
                            $output .= "Email failed to sent for JOB: ".$job['job_id'].PHP_EOL;
                        }
                    }
                    else
                    {
                        $output .= "No email required for  for JOB: ".$job['job_id'].PHP_EOL;
                    }
                }
                $output .= "Text based due date for JOB: ".$job['job_id'].". No email can be sent.".PHP_EOL;

            }
            Logger::logRemindersSent('sent_emails/log', $output);
        }
    }

    public function onePlateTask()
    {
        if(!isset($this->request->params['args']) || $this->request->params['args']['ua'] !== "FSG")
        {
            return $this->error(403);
        }
        else
        {
            $this->woocommerce->getOnePlateOrders();
        }
    }

    public function PBAWooTask()
    {
        if(!isset($this->request->params['args']['ua']) || !($this->request->params['args']['ua'] === "FSG" || $this->request->params['args']['ua'] === "CRON"))
        {
            return $this->error(403);
        }
        else
        {
            $this->woocommerce->getPBAOrders();
        }
    }

    public function PBAPerfectPracticeGolfShopifyTask()
    {
        if(!isset($this->request->params['args']['ua']) || !($this->request->params['args']['ua'] === "FSG" || $this->request->params['args']['ua'] === "CRON"))
        {
            return $this->error(403);
        }
        else
        {
            $this->PbaPerfectPracticeGolfShopify->getOrders();
        }
    }

    public function PBASuperspeedGolfShopifyTask()
    {
        if(!isset($this->request->params['args']['ua']) || !($this->request->params['args']['ua'] === "FSG" || $this->request->params['args']['ua'] === "CRON"))
        {
            return $this->error(403);
        }
        else
        {
            $this->PbaSuperspeedGolfShopify->getOrders();
        }
    }

    public function PBAArccosGolfShopifyTask()
    {
        if(!isset($this->request->params['args']['ua']) || !($this->request->params['args']['ua'] === "FSG" || $this->request->params['args']['ua'] === "CRON"))
        {
            return $this->error(403);
        }
        else
        {
            $this->PbaArccosGolfShopify->getOrders();
        }
    }

    public function PBAHomeCourseGolfShopifyTask()
    {
        if(!isset($this->request->params['args']['ua']) || !($this->request->params['args']['ua'] === "FSG" || $this->request->params['args']['ua'] === "CRON"))
        {
            return $this->error(403);
        }
        else
        {
            $this->PbaHomeCourseGolfShopify->getOrders();
        }
    }

    public function PBARukketGolfShopifyTask()
    {
        if(!isset($this->request->params['args']['ua']) || !($this->request->params['args']['ua'] === "FSG" || $this->request->params['args']['ua'] === "CRON"))
        {
            return $this->error(403);
        }
        else
        {
            $this->PbaRukketGolfShopify->getOrders();
        }
    }

    public function PBAVoiceCaddyShopifyTask()
    {
        if(!isset($this->request->params['args']['ua']) || !($this->request->params['args']['ua'] === "FSG" || $this->request->params['args']['ua'] === "CRON"))
        {
            return $this->error(403);
        }
        else
        {
            $this->PbaVoiceCaddyShopify->getOrders();
        }
    }

    public function PBAEbayTask()
    {
        if(!isset($this->request->params['args']['ua']) || !($this->request->params['args']['ua'] === "FSG" || $this->request->params['args']['ua'] === "CRON"))
        {
            return $this->error(403);
        }
        else
        {
            $this->PBAeBay->connect();
            $this->PBAeBay->getCurrentOrders();
        }
    }

    public function nuchevTask()
    {
        if(!isset($this->request->params['args']['ua']) || $this->request->params['args']['ua'] !== "FSG")
        {
            return $this->error(403);
        }
        else
        {
            $this->woocommerce->getNuchevOrders();
        }
    }

    public function nuchevMarketplacerTask()
    {
        if(!isset($this->request->params['args']['ua']) || !($this->request->params['args']['ua'] === "FSG" || $this->request->params['args']['ua'] === "CRON"))
        {
            return $this->error(403);
        }
        else
        {
            $this->NuchevMarketplacer->getOrders();
        }
    }

    public function freedomTask()
    {
        if(!isset($this->request->params['args']['ua']) || $this->request->params['args']['ua'] !== "FSG")
        {
            return $this->error(403);
        }
        else
        {
           //up the memory for this
            $now = DateTime::createFromFormat('U.u', microtime(true));
            $log .= "Upped memory at ".$now->format("m-d-Y H:i:s.u").PHP_EOL;
            ini_set('memory_limit', '2048M');
            $now = DateTime::createFromFormat('U.u', microtime(true));
            $log .= "Collected encrypted data at ".$now->format("m-d-Y H:i:s.u").PHP_EOL;
            $encryptedData = $this->FreedomMYOB->callTask('getMYOBOrders',array());
            $invoices =  json_decode($this->FreedomMYOB->getDecryptedData($encryptedData),true);
            $now = DateTime::createFromFormat('U.u', microtime(true));
            $log .= "Called processOrders at ".$now->format("m-d-Y H:i:s.u").PHP_EOL;
            $log .= "---------------------------------------------------------------------------------------".PHP_EOL;
            Logger::logOrderImports('order_imports/freedomTask', $log);
            $this->FreedomMYOB->processOrders($invoices);
        }
    }

    public function sessionSecretTask()
    {
        if(!isset($this->request->params['args']) || $this->request->params['args']['ua'] !== "FSG")
        {
            return $this->error(403);
        }
        else
        {
            $db = Database::openConnection();
            $current_secret = Encryption::decryptStringBase64($db->queryValue('configuration', array('name' => 'COOKIE_SECRET_KEY'), 'value'));
            $new_secret = Encryption::getRandomToken();
            //echo "new : ".$new_secret;
            $db->updateDatabaseFields('configuration', array('value' => Encryption::encryptStringBase64($new_secret), 'date_modified' => time()), 'COOKIE_SECRET_KEY', "name");
        }
    }

    public function sendClientReports()
    {
        if(!isset($this->request->params['args']) || $this->request->params['args']['ua'] !== "FSG")
        {
            return $this->error(403);
        }
        else
        {
            //$todays_reports = $this->clientreportssent->getTodaysReports();
            $from = strtotime('today midnight');
            $to = time();
            $clients = array(
                "Nuchev"        => 5,
                "Freedom"       => 7,
                "Team Timbuktu" => 69
            );
            $output = "=========================================================================================================".PHP_EOL;
            $output .= "SENDING CLIENT REPORTS FOR ".date("jS M Y (D), g:i a (T)").PHP_EOL;
            $output .= "=========================================================================================================".PHP_EOL;
            foreach($clients as $client_name => $client_id)
            {
                //Dispatch Report
                $filenames = array();
                $output .= "----------------------------------------------------------------------------------------------------".PHP_EOL;
                $orders = $this->order->getDispatchedOrdersArray($from, $to, $client_id);
                $output .= "Doing dispatch report for $client_name".PHP_EOL;
                if(!count($orders))
                {
                    $output .= "No orders dispatched today".PHP_EOL;
                }
                else
                {
                    $hidden = Config::get("HIDE_CHARGE_CLIENTS");
                    $filename = tempnam(sys_get_temp_dir(), 'dispatch_report_') . '.csv';
                    $filenames[] = $filename;
                    $fp = fopen($filename, 'w');
                    $headers = array(
                        "Date Ordered",
                        "Entered By",
                        "Date Dispatched",
                        "WMS Order Number",
                        "Your Order Number",
                        "Shipped To",
                        "Items",
                        "total Items",
                        "Courier",
                        "Charge Code",
                        "Consignment ID"
                    );
                    if( !in_array($client_id, $hidden) )
                    {
                        $headers[] = "Recorded Freight Charge";
                    }
                    fputcsv($fp, $headers);
                    foreach($orders as $o)
                    {
                        $row = array(
                            $o['date_ordered'],
                            $o['entered_by'],
                            $o['date_fulfilled'],
                            $o['order_number'],
                            $o['client_order_number'],
                            str_replace("<br/>", ", ",$o['shipped_to']),
                            str_replace("<br/>", "",$o['items']),
                            $o['total_items'],
                            $o['courier'],
                            $o['charge_code'],
                            $o['consignment_id']
                        );
                        if( !in_array($client_id, $hidden) )
                        {
                            $row[] = $o['charge'];
                        }
                    	fputcsv($fp, $row);
                    }
                    fclose($fp);
                    $output .= "Dispatch report with $filename will be sent for $client_name".PHP_EOL;
                    /*/save the record
                    $this->clientreportssent->recordData(array(
                        'client_id'     =>  $client_id,
                        'report_type'   =>  'Dispatch Report',
                        'date'          =>  time(),
                        'entered_by'    =>  Session::getUserId()
                    ));
                    */
                }
                //Returns Report
                $output .= "----------------------------------------------------------------------------------------------------".PHP_EOL;
                $output .= "Doing returns report for $client_name".PHP_EOL;

                //Stock In Report
                $output .= "----------------------------------------------------------------------------------------------------".PHP_EOL;
                $output .= "Doing stock in report for $client_name".PHP_EOL;
                $items = $this->newstock->getInputsForClient($client_id);
                if(count($items))
                {
                    $filename = tempnam(sys_get_temp_dir(), 'stockin_report_') . '.csv';
                    $filenames[] = $filename;
                    $fp = fopen($filename, 'w');
                    $headers = array(
                        "Item",
                        "SKU",
                        "Quantity Expected",
                        "Quantity Received"
                    );
                    fputcsv($fp, $headers);
                    foreach($items as $i)
                    {
                        $row = array(
                            $i['name'],
                            $i['sku'],
                            $i['qty'],
                            $i['qty_added']
                        );
                    	fputcsv($fp, $row);
                        //update database
                        $this->newstock->updateMailed($i['id']);
                    }
                    fclose($fp);
                    $output .= "Stock In report with $filename will be sent for $client_name".PHP_EOL;
                }
                else
                {
                    $output .= "No items scanned in today".PHP_EOL;
                }

                if(count($filenames))
                {
                    //send the mail
                    Email::sendDailyReport($filenames, $client_id);
                    /*delete the files  */
                    foreach($filenames as $f)
                    {
                        unlink($f);
                    }
                    $output .= "All reports sent for $client_name".PHP_EOL;
                }
                $output .= "----------------------------------------------------------------------------------------------------".PHP_EOL;
            }
            Logger::logReportsSent('sent_reports/log', $output); //die();
        }
    }

    public function isAuthorized(){

        return true;
    }
}