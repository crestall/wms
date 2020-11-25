<?php

/**
 * Runsheets controller
 *

 Manages Runsheets For The Drivers

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class RunsheetsController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
        $this->Security->config("form", [ 'fields' => ['csrf_token']]);
    }

    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'runsheets-index');
        parent::displayIndex(get_class());
    }

    public function viewRunsheets()
    {
        $rss = $this->runsheet->getRunsheetsForViewing();

        $runsheets = Utility::generateRunsheetDriverArray($rss);
        array_multisort(array_map(function($e){
            array_map(function($d){
                echo "<p>Driver ID: ".$d['id']."</p>";
                return $d['id'];
            }, $e['drivers']);
        }, $runsheets), SORT_DESC, SORT_NUMERIC, $runsheets);
        echo "<pre>",print_r($runsheets),"</pre>";die();
    }

    public function completedRunsheets()
    {
        $driver_id = (isset($this->request->params['args']['driver']))? $this->request->params['args']['driver'] : 0;
        $client_id = (isset($this->request->params['args']['client']))? $this->request->params['args']['client'] : 0;
        $customer_id = (isset($this->request->params['args']['customer']))? $this->request->params['args']['customer'] : 0;
        $from = (isset($this->request->params['args']['from']))? $this->request->params['args']['from'] : strtotime('monday this week');
        $to = (isset($this->request->params['args']['to']))? $this->request->params['args']['to'] : time();
        $rs_array = array(
            'driver_id'     =>  $driver_id,
            'client_id'     =>  $client_id,
            'customer_id'   =>  $customer_id,
            'from'          =>  $from,
            'to'            =>  $to
        );
        $rss = $this->runsheet->getCompletedRunsheets($rs_array);
        //$runsheets = $this->generateRunsheetDriverArray($rss);
        $runsheets = Utility::generateRunsheetDriverArray($rss);
        $page_array = array(
            'page_title'    =>  "Completed Runsheets",
            'pht'           =>  ": Completed Runsheets",
            'runsheets'     =>  $runsheets
        );
        $page_array = array_merge($page_array, $rs_array);
        //render the page
        Config::setJsConfig('curPage', "completed-runsheets");
        Config::set('curPage', "completed-runsheets");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/runsheets/", Config::get('VIEWS_PATH') . 'runsheets/completeRunsheets.php', $page_array);
    }

    public function runsheetSearch()
    {
        $form = $this->view->render( Config::get('VIEWS_PATH') . "forms/jobsearch.php",[
            'term'              =>  "",
            'customer_ids'       =>  array(),
            'supplier_ids'       =>  array(),
            'salesrep_ids'       =>  array(),
            'status_ids'         =>  array(),
            'date_from_value'   =>  0,
            'date_to_value'     =>  0
        ]);
        //render the page
        Config::setJsConfig('curPage', "job-search");
        Config::set('curPage', "job-search");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/jobs/", Config::get('VIEWS_PATH') . 'jobs/jobSearch.php', [
            'page_title'    =>  "Search production Jobs",
            'pht'           =>  ": Production Job Search",
            'form'          =>  $form
        ]);
    }

    public function runsheetSearchResults()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        if(!$this->Security->CsrfToken())
        {
            return $this->error(400);
        }
        $customer_ids = isset($this->request->query['customer_ids'])? $this->request->query['customer_ids']: array();
        $supplier_ids = isset($this->request->query['supplier_ids'])? $this->request->query['supplier_ids']: array();
        $salesrep_ids = isset($this->request->query['salesrep_ids'])? $this->request->query['salesrep_ids']: array();
        $status_ids = isset($this->request->query['status_ids'])? $this->request->query['status_ids']: array();;
        $date_from_value = $this->request->query['date_from_value'];
        $date_to_value = $this->request->query['date_to_value'];
        $args = array(
            'term'              =>  $this->request->query['term'],
            'customer_ids'       =>  $customer_ids,
            'supplier_ids'       =>  $supplier_ids,
            'salesrep_ids'       =>  $salesrep_ids,
            'status_ids'         =>  $status_ids,
            'date_from_value'   =>  $date_from_value,
            'date_to_value'     =>  $date_to_value
        );
        $jobs = $this->productionjob->getSearchResults($args);
        $count = count($jobs);
        $s = ($count == 1)? "": "s";
        $form = $this->view->render( Config::get('VIEWS_PATH') . "forms/jobsearch.php",$args);
        //render the page
        Config::setJsConfig('curPage', "job-search-results");
        Config::set('curPage', "job-search-results");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/runsheets/", Config::get('VIEWS_PATH') . 'jobs/jobSearchResults.php', [
            'page_title'    =>  "Search Results",
            'pht'           =>  ": Job Search Results",
            'form'          =>  $form,
            'count'         =>  $count,
            's'             =>  $s,
            'term'          =>  $this->request->query['term'],
            'jobs'          =>  $jobs
        ]);
    }

    public function prepareRunsheet()
    {
        $runsheet = array();
        if(!isset($this->request->params['args']['runsheet']))
        {
            $runsheet_id = 0;
            $tasks = array();
        }
        else
        {
            $runsheet_id = $this->request->params['args']['runsheet'];
            //$tasks = $this->runsheet->getRunsheetDetailsById($runsheet_id);
            $tasks = $this->runsheet->getRunsheetsForPreparation($runsheet_id);
        }
        //echo "TASK<pre>",print_r($tasks),"</pre>";
        foreach($tasks as $task)
        {
            $runsheet['runsheet_day'] = $task['runsheet_day'];
            $runsheet['driver_id'] = $task['driver_id'];
            if(!isset($runsheet['jobs']))
            {
                $runsheet['jobs'] =array();
            }
            if(!isset($runsheet['orders']))
            {
                $runsheet['orders'] =array();
            }
            if(!empty($task['job_id']))
            {
                $runsheet['jobs'][] = array(
                    'task_id'                   => $task['id'],
                    'job_shipto'                => $task['job_shipto'],
                    'job_units'                 => $task['units'],
                    'job_attention'             => $task['job_attention'],
                    'job_number'                => $task['job_number'],
                    'job_id'                    => $task['job_id'],
                    'job_customer'              => $task['customer_name'],
                    'job_address'               => $task['job_address'],
                    'job_address2'              => $task['job_address2'],
                    'job_suburb'                => $task['job_suburb'],
                    'job_postcode'              => $task['job_postcode'],
                    'job_delivery_instructions' => $task['job_delivery_instructions']
                );
            }
            if(!empty($task['order_number']))
            {
                $runsheet['orders'][] = array(
                    'task_id'                       => $task['id'],
                    'order_number'                  => $task['order_number'],
                    'client_order_id'               => $task['client_order_id'],
                    'order_id'                      => $task['order_id'],
                    'order_units'                   => $task['units'],
                    'order_customer'                => $task['order_customer'],
                    'order_address'                 => $task['order_address'],
                    'order_address2'                => $task['order_address2'],
                    'order_suburb'                  => $task['order_suburb'],
                    'order_postcode'                => $task['order_postcode'],
                    'order_client'                  => $task['order_client_name'],
                    'order_delivery_instructions'   => $task['order_delivery_instructions']
                );
            }
        }
        //render the page
        Config::setJsConfig('curPage', "prepare-runsheet");
        Config::set('curPage', "prepare-runsheet");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/runsheets/", Config::get('VIEWS_PATH') . 'runsheets/prepareSheet.php', [
            'page_title'    =>  "Update and Prepare Runsheet For Printing",
            'pht'           =>  ": Prepare Runsheet",
            'runsheet_id'   =>  $runsheet_id,
            'runsheet'      =>  $runsheet
        ]);
    }

    public function printRunsheet()
    {
        $runsheet = array();
        if(!isset($this->request->params['args']['runsheet']))
        {
            $runsheet_id = 0;
            $tasks = array();
        }
        else
        {
            $runsheet_id = $this->request->params['args']['runsheet'];
            $tasks = $this->runsheet->getRunsheetDetailsById($runsheet_id);
        }
        //echo "TASK<pre>",print_r($tasks),"</pre>";
        foreach($tasks as $task)
        {
            $runsheet['runsheet_day'] = $task['runsheet_day'];
            $runsheet['driver_id'] = $task['driver_id'];
            $runsheet['units'] = $task['units'];
            if(!isset($runsheet['jobs']))
            {
                $runsheet['jobs'] =array();
            }
            if(!isset($runsheet['orders']))
            {
                $runsheet['orders'] =array();
            }
            if(!empty($task['job_id']))
            {
                $runsheet['jobs'][] = array(
                    'task_id'       => $task['id'],
                    'job_number'    => $task['job_number'],
                    'job_id'        => $task['job_id'],
                    'job_customer'  => $task['customer_name'],
                    'job_suburb'    => $task['job_suburb']
                );
            }
            if(!empty($task['order_number']))
            {
                $runsheet['orders'][] = array(
                    'task_id'           => $task['id'],
                    'order_number'      => $task['order_number'],
                    'order_id'          => $task['order_id'],
                    'order_customer'    => $task['order_customer'],
                    'order_suburb'      => $task['order_suburb'],
                    'order_client'      => $task['order_client_name']
                );
            }
        }
        //render the page
        Config::setJsConfig('curPage', "print-runsheet");
        Config::set('curPage', "print-runsheet");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/runsheets/", Config::get('VIEWS_PATH') . 'runsheets/printRunsheet.php', [
            'page_title'    =>  "Update and Print Runsheet",
            'pht'           =>  ": Print Runsheet",
            'runsheet_id'   =>  $runsheet_id,
            'runsheet'      =>  $runsheet
        ]);
    }

    public function prepareRunsheets()
    {
        $rss = $this->runsheet->getRunsheetsForPreparation();
        $runsheets = array();
        foreach($rss as $rs)
        {
            if(!isset($runsheets[$rs['runsheet_day']]))
            {
                $runsheets[$rs['runsheet_day']] =array();
            }
            if(!isset($runsheets[$rs['runsheet_day']]['jobs']))
            {
                $runsheets[$rs['runsheet_day']]['jobs'] =array();
            }
            if(!isset($runsheets[$rs['runsheet_day']]['orders']))
            {
                $runsheets[$rs['runsheet_day']]['orders'] =array();
            }
            $runsheets[$rs['runsheet_day']]['created_date'] = $rs['created_date'];
            $runsheets[$rs['runsheet_day']]['updated_date'] = $rs['updated_date'];
            $runsheets[$rs['runsheet_day']]['created_by'] = $rs['created_by'];
            $runsheets[$rs['runsheet_day']]['updated_by'] = $rs['updated_by'];
            $runsheets[$rs['runsheet_day']]['runsheet_id'] = $rs['runsheet_id'];
            if($rs['job_id'] > 0)
            {
                $runsheets[$rs['runsheet_day']]['jobs'][] = array(
                    'job_id'        => $rs['job_id'],
                    'job_number'    => $rs['job_number'],
                    'driver_name'   => $rs['driver_name'],
                    'customer'      => $rs['customer_name'],
                    'suburb'        => $rs['job_suburb'],
                    'printed'       => $rs['printed'],
                    'completed'     => $rs['completed']
                );
            }
            if($rs['order_id'] > 0)
            {
                $runsheets[$rs['runsheet_day']]['orders'][] = array(
                    'order_id'      => $rs['order_id'],
                    'order_number'  => $rs['order_number'],
                    'driver_name'   => $rs['driver_name'],
                    'customer'      => $rs['order_customer'],
                    'suburb'        => $rs['order_suburb'],
                    'client'        => $rs['order_client_name'],
                    'printed'       => $rs['printed'],
                    'completed'     => $rs['completed']
                );
            }
        }
        //render the page
        Config::setJsConfig('curPage', "prepare-runsheets");
        Config::set('curPage', "prepare-runsheets");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/runsheets/", Config::get('VIEWS_PATH') . 'runsheets/prepareRunsheets.php', [
            'page_title'    =>  "Prepare Runsheets",
            'pht'           =>  ": Prepare Runsheets",
            'runsheets'     =>  $runsheets
        ]);
    }

    public function finaliseRunsheets()
    {
        $rss = $this->runsheet->getRunsheetsForFinalising();
        //echo "<pre>",print_r($rss),"</pre>";die();
        //$runsheets = $this->generateRunsheetDriverArray($rss);
        $runsheets = Utility::generateRunsheetDriverArray($rss);
        //echo "<pre>",print_r($runsheets),"</pre>";die();
        //render the page
        Config::setJsConfig('curPage', "finalise-runsheets");
        Config::set('curPage', "finalise-runsheets");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/runsheets/", Config::get('VIEWS_PATH') . 'runsheets/finaliseRunsheets.php', [
            'page_title'    =>  "Runsheets Requiring Finalising",
            'pht'           =>  ": Finalise Runsheets",
            'runsheets'     =>  $runsheets
        ]);
    }

    public function finaliseRunsheet()
    {
        $runsheet = array();
        if(!(isset($this->request->params['args']['runsheet']) && isset($this->request->params['args']['driver'])))
        {
            $runsheet_id = 0;
            $driver_id = 0;
            $tasks = array();
        }
        else
        {
            $runsheet_id = $this->request->params['args']['runsheet'];
            $driver_id = $this->request->params['args']['driver'];
            $tasks = $this->runsheet->getTasksForCompletion($runsheet_id, $driver_id);
        }
        //render the page
        Config::setJsConfig('curPage', "finalise-runsheet");
        Config::set('curPage', "finalise-runsheet");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/runsheets/", Config::get('VIEWS_PATH') . 'runsheets/completeRunsheet.php', [
            'page_title'    =>  "Complete Runsheet",
            'pht'           =>  ": Complete Runsheet",
            'driver_id'     =>  $driver_id,
            'runsheet_id'   =>  $runsheet_id,
            'tasks'         =>  $tasks
        ]);
    }

    public function printRunsheets()
    {
        $completed = (isset($this->request->params['args']['complete']))? false : 1;
        $rss = $this->runsheet->getRunsheetsForPrinting($completed);
        //$runsheets = $this->generateRunsheetDriverArray($rss);
        $runsheets = Utility::generateRunsheetDriverArray($rss);
        //echo "<pre>",print_r($runsheets),"</pre>";die();
        //render the page
        Config::setJsConfig('curPage', "print-runsheets");
        Config::set('curPage', "print-runsheets");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/runsheets/", Config::get('VIEWS_PATH') . 'runsheets/printRunsheets.php', [
            'page_title'    =>  "Print Runsheets",
            'pht'           =>  ": Print Runsheets",
            'runsheets'     =>  $runsheets,
            'completed'     =>  $completed
        ]);
    }

    public function updateJob()
    {
        if(!isset($this->request->params['args']['job']))
        {
            //no job id to update
            return (new ErrorsController())->error(400)->send();
        }
        $job_id = $this->request->params['args']['job'];
        $job_info = $this->productionjob->getJobById($job_id);
        if(empty($job_info))
        {
            //no job data found
            return (new ErrorsController())->error(404)->send();
        }
        $customer_info = $this->productioncustomer->getCustomerById($job_info['customer_id']);
        $finisher_info = ($job_info['finisher_id'] > 0)? $this->productionfinisher->getFinisherById($job_info['finisher_id']) : array();
        $finisher2_info = ($job_info['finisher2_id'] > 0)? $this->productionfinisher->getFinisherById($job_info['finisher2_id']) : array();
        //render the page
        Config::setJsConfig('curPage', "update-job");
        Config::set('curPage', "update-job");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/jobs/", Config::get('VIEWS_PATH') . 'jobs/updateJob.php', [
            'page_title'    =>  "Update Production Job Details",
            'pht'           =>  ": Update Production Job",
            'job'           =>  $job_info,
            'customer'      =>  $customer_info,
            'finisher'      =>  $finisher_info,
            'finisher2'     =>  $finisher2_info
        ]);
    }

    public function isAuthorized()
    {
        $action = $this->request->param('action');
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        $resource = "runsheets";

        //only for admin
        Permission::allow(['admin', 'super admin', 'production admin'], $resource, "*");
        //production users
        Permission::allow('production', $resource, array(
            "index",
            "viewRunsheets",
            "runsheetSearch",
            "runsheetSearchResults"
        ));

        return Permission::check($role, $resource, $action);
    }
}