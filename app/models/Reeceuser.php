<?php

 /**
  * Reeceuser Class
  *

  * @author     Mark Solly <mark.solly@fsg.com.au>

    FUNCTIONS

    addUser($data)
    addUpdateUsers(array $departments)
    getUserByEmail($email)
    getUserById($id) 

  */

class Reeceuser extends Model{
    public $table = "reece_departments";

    public function addUser($data)
    {
        $db = Database::openConnection();
        $db->insertQuery($this->table, $data);
        return true;
    }

    public function addUpdateUsers(array $users)
    {
        $db = Database::openConnection();
        foreach($users as $u)
        {
            if($updator = $db->queryIdByFieldNumber($this->table, 'email', $u['email']))
            {
                //update the table
                $db->updateDatabaseFields($this->table, $u, $updator);
            }
            else
            {
                //enter new value
                $db->insertQuery($this->table, $u);
            }
        }
        return true;
    }

    public function getUserByEmail($email)
    {
        $db = Database::openConnection();
        return $db->queryRow("SELECT * FROM {$this->table} WHERE email = :email", array('email' => $email));
    }

    public function getUserById($id)
    {
        $db = Database::openConnection();
        return $db->queryByID({$this->table}, $id);
    }

}
?>