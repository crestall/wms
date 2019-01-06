<?php

 /**
  * Pickface Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>


  FUNCTIONS

  getAllPickfaces()
  getSelectPickfaces
  updatePickface


  */

class Pickface extends Model{

    public $table = "pickfaces";

    public function getAllPickfaces()
    {
        $db = Database::openConnection();
        $q = "
            SELECT pf.*, l.location
            FROM pickfaces pf JOIN locations l ON pf.location_id = l.id
            ORDER BY l.location + 0
        ";
        return $db->queryData($q);
    }


    public function addPickface($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'location_id'  =>  $data['location_id'],
            'count'        =>  $data['count']
        );
        $db->insertQuery('pickfaces', $vals);
        return true;
    }

    public function updatePickface($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'location_id'  =>  $data['location_id'],
            'count'        =>  $data['count']
        );
        $db->updateDatabaseFields($this->table, $vals, $data['id']);
        return true;
    }
}

?>