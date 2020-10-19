<?php

/**
 * Runsheets controller
 *

 Manages Runsheets For The Drivers

 * @author     Mark Solly <mark.solly@3plplus.com.au>
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
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/jobs/", Config::get('VIEWS_PATH') . 'jobs/jobSearchResults.php', [
            'page_title'    =>  "Search Results",
            'pht'           =>  ": Job Search Results",
            'form'          =>  $form,
            'count'         =>  $count,
            's'             =>  $s,
            'term'          =>  $this->request->query['term'],
            'jobs'          =>  $jobs
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

    public function viewRunsheets()
    {
        $rss = $this->runsheet->getRunsheetsForDisplay();
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
        Config::setJsConfig('curPage', "view-runsheets");
        Config::set('curPage', "view-runsheets");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/runsheets/", Config::get('VIEWS_PATH') . 'runsheets/viewRunsheets.php', [
            'page_title'    =>  "View Runsheets",
            'pht'           =>  ": View Runsheets",
            'runsheets'     =>  $runsheets
        ]);
    }

    public function finaliseRunsheets()
    {
        $rss = $this->runsheet->getRunsheetsForDisplay(false, true);  //NOT COMPLTED PRINTED
        $runsheets = array();
        foreach($rss as $rs)
        {
            $di = 0;
            if(!isset($runsheets[$rs['runsheet_day']]))
                $runsheets[$rs['runsheet_day']] = array();
            if(!isset($runsheets[$rs['runsheet_day']]['drivers']))
                $runsheets[$rs['runsheet_day']]['drivers'] = array();
            if(($tdi = array_search($rs['driver_id'], array_column($runsheets[$rs['runsheet_day']]['drivers'], 'id'))) === false)
            {
                echo "<p>No id for {$rs['driver_id']} found</p>";
                $runsheets[$rs['runsheet_day']]['drivers'][$di] = array(
                    'id'    => $rs['driver_id'],
                    'name'  => $rs['driver_name'],
                    'tasks' => array()
                );
                if(!empty($rs['job_id']))
                    $runsheets[$rs['runsheet_day']]['drivers'][$di]['tasks'][] = $rs['job_number'];
                if(!empty($rs['order_number']))
                    $runsheets[$rs['runsheet_day']]['drivers'][$di]['tasks'][] = $rs['order_number'];
                ++$di;
            }
            else
            {
                echo "<p>Id {$rs['driver_id']} found. It is $tdi</p>";
                if(!empty($rs['job_id']))
                    $runsheets[$rs['runsheet_day']]['drivers'][$tdi]['tasks'][] = $rs['job_number'];
                if(!empty($rs['order_number']))
                    $runsheets[$rs['runsheet_day']]['drivers'][$tdi]['tasks'][] = $rs['order_number'];
            }
        }
        echo "<pre>",print_r($runsheets),"/<pre>";die();
        //render the page
        Config::setJsConfig('curPage', "finalise-runsheets");
        Config::set('curPage', "finalise-runsheets");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/runsheets/", Config::get('VIEWS_PATH') . 'runsheets/finaliseRunsheets.php', [
            'page_title'    =>  "Finalise Runsheets",
            'pht'           =>  ": Finalise Runsheets",
            'runsheets'     =>  $runsheets
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