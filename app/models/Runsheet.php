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

    public function getAllRunsheets($printed = false, $completed = false)
    {
        $db = Database::openConnection();
        $q = "SELECT * FROM {$this->table}";
        if($completed)
        {
            $q .= " WHERE completed = 1";
        }
        else
        {
            $q .= " WHERE completed = 0";
        }
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
        return $db->queryById($this->table, $id);
    }

    public function getRunsheetForDay($day = 0)
    {
        $db = Database::openConnection();
        $q = "SELECT * FROM {$this->table} WHERE `day` = $day LIMIT 1";
        $row = $db->queryRow($q);
        return $row;
    }

    public function getRunsheetsForDriver($driver_id = 0)
    {
        $db = Database::openConnection();
        $q = "SELECT * FROM {$this->table} WHERE `driver_id` = $driver_id";
        return $db->queryData($q);
    }

    public function addRunsheet($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'runsheet_day'  =>  $data['runsheet_day'],
            'created_date'  =>  time(),
            'updated_date'  =>  time()
        );
        if(!empty($data['driver_id'])) $vals['driver_id'] = $data['driver_id'];
        $id = $db->insertQuery($this->table, $vals);
        return $id;
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