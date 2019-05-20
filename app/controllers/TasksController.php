<?php

/**
 * Tasks controller
 *

 Handles scheduled tasks - cron jobs

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class TasksController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
    }

    public function initialize(){

         $this->loadEparcelLocations([
            'BigBottle',
            'Freedom',
            'Nuchev'
        ]);

        $this->loadHuntersLocations([
            '3KG',
            'PLU',
            'PAL',
            'Test'
        ]);

        $this->loadComponents([
            'Security'
        ]);
    }

    public function sendClientReports()
    {
        if ($_SERVER['HTTP_USER_AGENT'] != '3PLPLUSAGENT')
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

    public function bigBottleTask()
    {
        if ($_SERVER['HTTP_USER_AGENT'] != '3PLPLUSAGENT')
        {
            return $this->error(403);
        }
        else
        {
            $this->woocommerce->getBBOrders();
        }
    }

    public function nuchevTask()
    {
        if ($_SERVER['HTTP_USER_AGENT'] != '3PLPLUSAGENT')
        {
            return $this->error(403);
        }
        else
        {
            $this->woocommerce->getNuchevOrders();
        }
    }

    public function noaTask()
    {
        if ($_SERVER['HTTP_USER_AGENT'] != '3PLPLUSAGENT')
        {
            return $this->error(403);
        }
        else
        {
            $this->woocommerce->getNoaOrders();
        }
    }

    public function figureEightTask()
    {
        if ($_SERVER['HTTP_USER_AGENT'] != '3PLPLUSAGENT')
        {
            return $this->error(403);
        }
        else
        {
            $this->emailordersparser->getFigure8Orders();
        }
    }

    public function nuchevSampleTask()
    {
        if ($_SERVER['HTTP_USER_AGENT'] != '3PLPLUSAGENT')
        {
            return $this->error(403);
        }
        else
        {
            $this->emailordersparser->getNuchevSamples();
        }
    }

    public function isAuthorized(){
        
        return true;
    }
}