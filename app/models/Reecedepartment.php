<?php

 /**
  * Reecedepartment Class
  *

  * @author     Mark Solly <mark.solly@fsg.com.au>

    FUNCTIONS

    addDepartment($data)

  */

class Reecedepartment extends Model{
    public $table = "reece_departments";

    public function addDepartment($data)
    {
        $db = Database::openConnection();
        $db->insertQuery($this->table, $data);
        return true;
    }


}
?>