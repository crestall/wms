<?php
 /**
  * Eparcelorder Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>

  FUNCTIONS

  addSummary($response, $id)
  setAsPrinted($id)

  */
class Eparcelorder extends Model{
    public $table = "eparcel_orders";

    public function addSummary($response, $id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'order_summary', $response, $id);
        return true;
    }

    public function setAsPrinted($id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'printed', 1, $id);
        return true;
    }

    public function getSummary($id)
    {
        $db = Database::openConnection();
        return $db->queryById($this->table, $id);
    }

}
?>