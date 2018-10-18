<?php

 /**
  * Clientreporstsent Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>

    FUNCTIONS

    getAddressString($id, $prefix = "")
    getPickup($id)
    getPickups($client_id = 0)
    recordData($data)

  */

class Clientreportssent extends Model{

    public $table = "client_reports_sent";

    public function recordData($data)
    {
        $db = Database::openConnection();
        $db->insertQuery($this->table, $data);
        return true;
    }

    public function getTodaysReports()
    {
        $db = Database::openConnection();
        $today = mktime(0, 0, 0);
        return $db->queryData("SELECT crs.*, c.client_name FROM client_reports_sent crs JOIN clients c on crs.client_id = c.id WHERE date > $today ORDER BY client_id");
    }

    public function updatePickupValues($values, $id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseFields($this->table, $values, $id);
        return true;
    }

}
?>