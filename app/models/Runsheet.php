<?php

/**
    * Runsheet Class
    *

    * @author     Mark Solly <mark.solly@3plplus.com.au>

        FUNCTIONS

        addRunsheet($data)
        updateRunsheet($data)
        getAllRunsheets($printed = false, $completed = false)
        getRunsheetById($id = 0)
        getRunsheetForDay($day = 0)
        getRunsheetsForDriver($driver_id = 0)
    */

class Runsheet extends Model{
    public $table = "runsheets";
    public $tasks_table = "runsheet_tasks";

    public function getAllRunsheets($completed = false)
    {
        $db = Database::openConnection();
        $q = "SELECT * FROM {$this->table} rs LEFT JOIN {$this->tasks_table} rst ON rs.id = rst.runsheet_id";
        if($completed)
        {
            $q .= " WHERE rs.completed = 1";
        }
        else
        {
            $q .= " WHERE rs.completed = 0";
        }
        $q .= " ORDER BY rs.runsheet_day DESC";
        return $db->queryData($q);
    }

    public function getTasksForRunsheet($runsheet_id = 0, $printed = false)
    {
        $db = Database::openConnection();
        $q = "SELECT * FROM {$this->tasks_table} WHERE runsheet_id = $runsheet_id";
        if($printed)
        {
            $q .= " AND printed = 1";
        }
        else
        {
            $q .= " AND printed = 0";
        }
        return $db->queryData($q);
    }

    public function getRunsheetById($id = 0)
    {
        $db = Database::openConnection();
        $q = "SELECT * FROM {$this->table} rs LEFT JOIN {$this->tasks_table} rst ON rs.id = rst.runsheet_id WHERE rs.`id` = $id";
        return $db->queryData($q);
    }

    public function getRunsheetForDay($day = 0)
    {
        $db = Database::openConnection();
        $q = "SELECT * FROM {$this->table} rs LEFT JOIN {$this->tasks_table} rst ON rs.id = rst.runsheet_id WHERE rs.`day` = $day";
        return $db->queryData($q);
    }

    public function getRunsheetsForDriver($driver_id = 0)
    {
        $db = Database::openConnection();
        $q = "SELECT * FROM {$this->table} rs LEFT JOIN {$this->tasks_table} rst ON rs.id = rst.runsheet_id WHERE rs.`driver_id` = $driver_id";
        return $db->queryData($q);
    }

    public function removeJob($job_id, $runsheet_id)
    {
       $db = Database::openConnection();
       $query = "DELETE FROM {$this->tasks_table} WHERE runsheet_id = :runsheet_id AND job_id = :job_id";
       $params = array(
            'runsheet_id'   => $runsheet_id,
            'job_id'        => $job_id
       );
       return $db->query($query, $params)
    }

    public function removeOrder($order_id, $runsheet_id)
    {
       $db = Database::openConnection();
       $query = "DELETE FROM {$this->tasks_table} WHERE runsheet_id = :runsheet_id AND order_id = :order_id";
       $params = array(
            'runsheet_id'   => $runsheet_id,
            'order_id'      => $order_id
       );
       return $db->query($query, $params)
    }

    public function addRunsheet($data)
    {
        $db = Database::openConnection();
        foreach($data as $runsheet_day => $details)
        {
            if(!$runsheet_id = $db->queryIdByFieldNumber($this->table, 'runsheet_day', $runsheet_day))
            {
                $vals = array(
                    'runsheet_day'  =>  $runsheet_day,
                    'created_date'  =>  time(),
                    'updated_date'  =>  time()
                );
                $runsheet_id = $db->insertQuery($this->table, $vals);
            }
            // now add the jobs/orders
            $tvals = array(
                'runsheet_id'   => $runsheet_id
            );
            if($details['driver_id'] > 0)
                $tvals['driver_id'] = $details['driver_id'];
            if(isset($details['jobs']))
            {
                foreach($details['jobs'] as $job_id)
                {
                    $tvals['job_id'] = $job_id;
                    $task_id = $db->insertQuery($this->tasks_table, $tvals);
                }
            }
            if(isset($details['orders']))
            {
                foreach($details['orders'] as $order_id)
                {
                    $tvals['order__id'] = $order_id;
                    $task_id = $db->insertQuery($this->tasks_table, $tvals);
                }
            }
        }
        return true;
    }

    public function updateRunsheet($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'driver_id'     => $data['driver_id'],
            'updated_date'  => time()
        );
        $db->updateDatabaseFields($this->table, $vals, $data['runsheet_id']);
        return true;
    }
}
?>