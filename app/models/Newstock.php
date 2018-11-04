<?php
 /**
  * Newstock Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>

  FUNCTIONS

  isRegistered($item_id)
  needsMailing($item_id)
  recordData($data)
  updateInCount($line_id, $qty)
  updateMailed($line_id)
  updateRecorded($line_id)

  */
class Newstock extends Model{
    public $table = "new_stock";

    public function recordData($data)
    {
        $db = Database::openConnection();
        $db->insertQuery($this->table, $data);
        return true;
    }

    public function getInputsForClient($client_id)
    {
        $db = Database::openConnection();
        $q = "SELECT ns.*, i.name, i.sku FROM {$this->table} ns JOIN items i ON ns.item_id = i.id WHERE ns.client_id = $client_id AND ns.recorded = 1 AND ns.mail_sent = 0";
        return $db->queryData($q);
    }

    public function isRegistered($item_id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array(
            'item_id'   => $item_id,
            'mail_sent' => 0,
            'recorded'  => 0
        ));
    }

    public function needsMailing($item_id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array(
            'item_id'   => $item_id,
            'mail_sent' => 0,
            'recorded'  => 1
        ));
    }

    public function updateRecorded($line_id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'recorded', 1, $line_id);
        return true;
    }

    public function updateMailed($line_id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'mail_sent', 1, $line_id);
        return true;
    }

    public function updateInCount($line_id, $qty)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'qty_added', $qty, $line_id);
        return true;
    }
}
?>