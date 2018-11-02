<?php
 /**
  * Newstock Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>

  FUNCTIONS

  recordData($data)

  */
class Newstock extends Model{
    public $table = "new_stock";

    public function recordData($data)
    {
        $db = Database::openConnection();
        $db->insertQuery($this->table, $data);
        return true;
    }
}
?>