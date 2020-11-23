<?php

/**
    * Runsheet Class
    *

    * @author     Mark Solly <mark.solly@fsg.com.au>

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

    public function getCompletedRunsheets($args)
    {
        $db = Database::openConnection();
        $q = $this->getCompletedRunsheetsQuery($args);
        return $db->queryData($q);
    }

    public function getRunsheetsForPreparation($runsheet_id = false)
    {
        return $this->getRunsheetsForDisplay(false, 0, 0, $runsheet_id);
    }

    public function getViewRunsheets($runsheet_id = false)
    {
        return $this->getRunsheetsForDisplay(false, 0, false, $runsheet_id, true);
    }

    public function getRunsheetForPrinting($runsheet_id, $driver_id)
    {
        return $this->getRunsheetsForDisplay(1, 1, $driver_id, $runsheet_id, true);
    }

    public function getRunsheetsForFinalising()
    {
        return $this->getRunsheetsForDisplay(false, true, false, false, true);
    }

    public function getTasksForCompletion($runsheet_id, $driver_id)
    {
        return $this->getRunsheetsForDisplay(false, true, $driver_id, $runsheet_id, true);
    }

    public function getRunsheetsForDisplay($completed = false, $printed = false, $driver_id = false, $runsheet_id = false, $driver_set = false)
    {
        $db = Database::openConnection();
        $q = $this->getRunsheetQuery();
        $q .= " WHERE";
        if($completed === false)
        {
            $q .= " rst.completed = 0 AND";
        }
        elseif($completed === true)
        {
            $q .= " rst.completed = 1 AND";
        }
        if($printed === true)
        {
            $q .= " rst.printed = 1 AND";
        }
        elseif($printed === false)
        {
            $q .= " rst.printed = 0 AND";
        }
        if($driver_id !== false)
        {
            $q .= " rst.driver_id = $driver_id AND";
        }
        elseif($driver_set)
        {
            $q .= " rst.driver_id != 0 AND";
        }
        if($runsheet_id !== false)
        {
            $q .= " rs.id = $runsheet_id ";
        }
        $q = rtrim($q, "AND");
        $q .= "
            ORDER BY
                rs.runsheet_day DESC
        ";
        //echo $q; die();
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

    public function updateTask($details)
    {
       $db = Database::openConnection();
       $runsheet_id = $details['runsheet_id'];
       unset($details['runsheet_id']);
       $task_id = $details['task_id'];
       unset($details['task_id']);
       //record runsheet update
       $new_vals = array(
            'updated_date'  =>  time(),
            'updated_by'    =>  Session::getUserId()
        );
        $db->updateDatabaseFields($this->table, $new_vals, $runsheet_id);
        $db->updateDatabaseFields($this->tasks_table, $details, $task_id);
        return true;
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

    public function completeTasks($data)
    {
       $db = Database::openConnection();
       //record runsheet update
       $new_vals = array(
            'updated_date'  =>  time(),
            'updated_by'    =>  Session::getUserId()
        );
        $db->updateDatabaseFields($this->table, $new_vals, $data['runsheet_id']);
        foreach($data['tasks'] as $task_id)
        {
            $task_vals = array(
                'completed' => 1
            );
            if(!empty($data['dd'][$task_id]['received']))
                $task_vals['received_by'] = $data['dd'][$task_id]['received'];
            if(!empty($data['dd'][$task_id]['tod']))
                $task_vals['time_of_drop'] = $data['dd'][$task_id]['tod'];
            $db->updateDatabaseFields($this->tasks_table, $task_vals, $task_id);
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

    private function getCompletedRunsheetsQuery($args)
    {
        $defaults = array(
            'from'          => strtotime('monday this week'),
            'to'            => time(),
            'client_id'     => 0,
            'customer_id'   => 0,
            'driver_id'     => 0,
        );
        $args = array_merge($defaults, $args);
        extract($args);
        //echo "<pre>",print_r($args),"</pre>";//die();
        $q = $this->getRunsheetQuery();
        $q .= "
            WHERE (rst.completed = 1 AND rs.updated_date >= $from AND rs.updated_date <= $to)
        ";

        if($client_id > 0)
            $q .= " AND( o.client_id = $client_id )";
        if($customer_id > 0)
            $q .= " AND( pj.customer_id = $customer_id )";
        if($driver_id > 0)
            $q .= " AND( rst.driver_id = $driver_id)";
        $q .= " ORDER BY rs.runsheet_day DESC";
        //echo $q; die();
        return $q;
    }

    private function getRunsheetQuery()
    {
        return "
            SELECT
                rs.runsheet_day, rs.created_date, rs.updated_date, rs.created_by, rs.updated_by,
                rst.*,
                d.name AS driver_name,
                pj.job_id AS job_number,pj.delivery_instructions AS job_delivery_instructions, pj.description, pj.ship_to AS job_shipto, pj.attention AS job_attention, pj.address AS job_address, pj.address_2 AS job_address2, pj.suburb AS job_suburb, pj.postcode AS job_postcode,
                pc.name AS customer_name,
                o.ship_to AS order_customer,o.client_order_id, o.order_number,o.instructions AS order_delivery_instructions, o.address AS order_address, o.address_2 AS order_address2, o.suburb AS order_suburb, o.postcode AS order_postcode,
                c.client_name AS order_client_name,
                i.name AS item_name, i.sku,
                sr.name AS FSG_contact, sr.phone AS FSG_contact_phone
            FROM
                {$this->table} rs
                JOIN {$this->tasks_table} rst ON rs.id = rst.runsheet_id
                LEFT JOIN production_jobs pj ON rst.job_id = pj.id
                LEFT JOIN sales_reps sr ON sr.id = pj.salesrep_id
                LEFT JOIN drivers d ON d.id = rst.driver_id
                LEFT JOIN `production_customers` pc ON pj.customer_id = pc.id
                LEFT JOIN orders o ON rst.order_id = o.id
                LEFT JOIN clients c ON o.client_id = c.id
                LEFT JOIN (SELECT GROUP_CONCAT(DISTINCT items.name SEPARATOR ', ') AS name, GROUP_CONCAT(DISTINCT items.sku SEPARATOR ', ') AS sku, oi.order_id FROM items JOIN orders_items oi ON oi.item_id = items.id GROUP BY oi.order_id) i ON i.order_id = o.id
        ";
    }
}
?>