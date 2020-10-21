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
        getRunsheetsForDisplay($completed = false, $printed = false)
        getRunsheetsForDriver($driver_id = 0)
    */

class Runsheet extends Model{
    public $table = "runsheets";
    public $tasks_table = "runsheet_tasks";

    public function getRunsheetsForDisplay($completed = false, $printed = false)
    {
        $db = Database::openConnection();
        $q = $this->getRunsheetQuery();
        if($completed)
        {
            $q .= " WHERE rst.completed = 1";
        }
        else
        {
            $q .= " WHERE rst.completed = 0";
        }
        if($printed)
        {
            $q .= " AND rst.printed = 1";
        }
        else
        {
            $q .= " AND rst.printed = 0";
        }
        $q .= "
            ORDER BY
                rs.runsheet_day DESC
        ";
        return $db->queryData($q);
    }

    public function getAllRunsheets($completed = false)
    {
        $db = Database::openConnection();
        $q = $this->getRunsheetQuery();
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

    public function getRunsheetDetailsById($id = 0, $printed = false)
    {
        $db = Database::openConnection();
        $q = $this->getRunsheetQuery();
        $q .= " WHERE rs.`id` = $id";
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
       //record runsheet update
       $new_vals = array(
            'updated_date'  =>  time(),
            'updated_by'    =>  Session::getUserId()
        );
        $db->updateDatabaseFields($this->table, $new_vals, $runsheet_id);
       $query = "DELETE FROM {$this->tasks_table} WHERE runsheet_id = :runsheet_id AND job_id = :job_id";
       $params = array(
            'runsheet_id'   => $runsheet_id,
            'job_id'        => $job_id
       );
       return $db->query($query, $params);
    }

    public function removeTasks($task_ids, $runsheet_id)
    {
       $db = Database::openConnection();
       //record runsheet update
       $new_vals = array(
            'updated_date'  =>  time(),
            'updated_by'    =>  Session::getUserId()
        );
        $db->updateDatabaseFields($this->table, $new_vals, $runsheet_id);
        foreach($task_ids as $task_id)
        {
            $db->deleteQuery($this->tasks_table, $task_id);
        }
        return true;
    }

    public function completeTasks($task_ids, $runsheet_id)
    {
       $db = Database::openConnection();
       //record runsheet update
       $new_vals = array(
            'updated_date'  =>  time(),
            'updated_by'    =>  Session::getUserId()
        );
        $db->updateDatabaseFields($this->table, $new_vals, $runsheet_id);
        foreach($task_ids as $task_id)
        {
            $db->updateDatabaseField($this->tasks_table, 'completed', 1, $task_id);
        }
        return true;
    }

    public function removeOrder($order_id, $runsheet_id)
    {
       $db = Database::openConnection();
       //record runsheet update
       $new_vals = array(
            'updated_date'  =>  time(),
            'updated_by'    =>  Session::getUserId()
        );
        $db->updateDatabaseFields($this->table, $new_vals, $runsheet_id);
       $query = "DELETE FROM {$this->tasks_table} WHERE runsheet_id = :runsheet_id AND order_id = :order_id";
       $params = array(
            'runsheet_id'   => $runsheet_id,
            'order_id'      => $order_id
       );
       return $db->query($query, $params);
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
                    'updated_date'  =>  time(),
                    'created_by'    =>  Session::getUserId()
                );
                $runsheet_id = $db->insertQuery($this->table, $vals);
            }
            else
            {
                $new_vals = array(
                    'updated_date'  =>  time(),
                    'updated_by'    =>  Session::getUserId()
                );
                $db->updateDatabaseFields($this->table, $new_vals, $runsheet_id);
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
                    $tvals['order_id'] = $order_id;
                    $task_id = $db->insertQuery($this->tasks_table, $tvals);
                }
            }
        }
        return true;
    }

    public function runsheetPrinted($data = array())
    {
        if(count($data))
        {
            $db = Database::openConnection();
            $vals = array(
                'updated_date'  => time(),
                'updated_by'    => Session::getUserId()
            );
            $task_vals = array(
                'printed'   => 1,
            );
            if(isset($data['driver_id']))
                $task_vals['driver_id'] = $data['driver_id'];
            if(isset($data['units']))
                $task_vals['units'] = $data['units'];
            $db->updateDatabaseFields($this->table, $vals, $data['runsheet_id']);
            $db->updateDatabaseFields($this->tasks_table, $task_vals, $data['task_id']);
        }

        return true;
    }

    private function getRunsheetQuery()
    {
        return "
            SELECT
                rs.runsheet_day, rs.created_date, rs.updated_date, rs.created_by, rs.updated_by,
                rst.*,
                d.name AS driver_name,
                pj.job_id AS job_number, pj.description,
                pc.name AS customer_name, pc.address AS job_address, pc.address_2 AS job_address2, pc.suburb AS job_suburb, pc.postcode AS job_postcode,
                o.ship_to AS order_customer, o.order_number, o.address AS order_address, o.address_2 AS order_address2, o.suburb AS order_suburb, o.postcode AS order_postcode,
                c.client_name AS order_client_name
            FROM
                {$this->table} rs
                JOIN {$this->tasks_table} rst ON rs.id = rst.runsheet_id
                LEFT JOIN production_jobs pj ON rst.job_id = pj.id
                LEFT JOIN drivers d ON d.id = rst.driver_id
                LEFT JOIN `production_customers` pc ON pj.customer_id = pc.id
                LEFT JOIN orders o ON rst.order_id = o.id
                LEFT JOIN clients c ON o.client_id = c.id
        ";
    }
}
?>