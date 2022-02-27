<?php

 /**
  * Bookcover Class
  *

  * @author     Mark Solly <mark.solly@fsg.com.au>


  FUNCTIONS
  addCover($data)
  checkCoverNames($name, $current_name = "")
  editCover($data)
  getCoverId($name)
  getCovers($active = -1)
  */

class Bookcovers extends Model{

    public $table = "book_covers";

    public function __construct()
    {
        parent::__construct();
    }

    public function addCover($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'          =>  ucwords($data['name']),
            'qty'           =>  $data['qty']
        );
        return $db->insertQuery($this->table, $vals);
    }

    public function editCover($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'          =>  ucwords($data['name']),
            'qty'           =>  $data['qty']
        );
        //$vals['active'] = (isset($data['active']))? 1:0;
        $db->updateDatabaseFields($this->table, $vals, $data['id']);
    }

    public function getCovers($active = -1)
    {
        $db = Database::openConnection();
        $q = "SELECT * FROM {$this->table}";
        if($active >= 0)
        {
            $q .= " WHERE active = $active";
        }
        $q .= " ORDER BY name";
        return $db->queryData($q);
    }

    public function getCoverId($name)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('name' => $name));
    }

    public function checkCoverNames($name, $current_name = "")
    {
        $db = Database::openConnection();
        $name = strtoupper($name);
        $current_name = strtoupper($current_name);
        $q = "SELECT name FROM {$this->table}";
        $rows = $db->queryData($q);
        $valid = 'true';
        foreach($rows as $row)
        {
        	if($name == strtoupper($row['name']) && $current_name != strtoupper($row['name']))
        	{
        		$valid = 'false';
        	}
        }
        return $valid;
    }

}//end class
?>