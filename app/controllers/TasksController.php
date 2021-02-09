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
                foreach($files as $file)
                {
                    if($this->BdsFTP->getFileSize($file) === -1)
                        continue;
                    echo "<p>Will now process - $file</p>";
                    $responses[] = $this->BdsFTP->collectOrders($file);
                    echo "<p>Have now processed - $file</p>";
                    $this->BdsFTP->renameFile($file, 'collected_orders/'.$file);
                }
                $this->BdsFTP->closeConnection();
                //$file = $files[0]; //there should now be only one
                //echo "IN TASKS CONTROLLER<pre>",var_dump($responses),"<pre>"; //die();
                foreach($responses as $response)
                {
                    Email::sendBDSImportFeedback($response);
                }
                exit();
            }
        }
    }

    public function BDSCompletionTask()
    {
        if(!isset($this->request->params['args']['ua']) || $this->request->params['args']['ua'] !== "FSG")
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
                    $this->BdsFTP->uploadCSVFile($csvData);
                }
                $this->BdsFTP->closeConnection();
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

    public function PBATask()
    {
        if(!isset($this->request->params['args']) || $this->request->params['args']['ua'] !== "FSG")
        {
            return $this->error(403);
        }
        else
        {
            $this->woocommerce->getPBAOrders();
        }
    }

    public function PBAShopifyTask()
    {
        if(!isset($this->request->params['args']) || $this->request->params['args']['ua'] !== "FSG")
        {
            return $this->error(403);
        }
        else
        {
            $this->shopify->getPBAOrders();
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

    public function freedomTask()
    {
        if(!isset($this->request->params['args']['ua']) || $this->request->params['args']['ua'] !== "FSG")
        {
            return $this->error(403);
        }
        else
        {
           //up the memory for this
            ini_set('memory_limit', '2048M');
            $encryptedData = $this->FreedomMYOB->callTask('getMYOBOrders',array());
            $invoices =  json_decode($this->FreedomMYOB->getDecryptedData($encryptedData),true);
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