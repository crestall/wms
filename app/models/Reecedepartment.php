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

    public function addUpdateDepartments(array $departments)
    {
        $db = Database::openConnection();
        foreach($departments as $d)
        {
            if($updator = $db->queryIdByFieldNumber($this->table, 'reece_id', $d['reece_id']))
            {
                //update the table
                $db->updateDatabaseFields($this->table, $d, $updator);
            }
            else
            {
                //enter new value
                $db->insertQuery($this->table, $d);
            }
        }
        return true;
    }


}
?>