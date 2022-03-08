<?php

 /**
  * Site Class
  *

  * @author     Mark Solly <mark.solly@fsg.com.au>


  FUNCTIONS

  addSite($data
  checkSite($site)
  deactivateSite($id)
  getAllSites()
  getSelectSites($selected = false)
  getSiteId($name)
  getSiteName($id)
  reactivateSite($id)
  updateSite($data)

  toDatabase($name)
  toDisplay($name)

  */

class Site extends Model{

    public $table = "sites";


    public function deactivateSite($id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'active', 0, $id);
        return true;
    }

    public function reactivateSite($id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'active', 1, $id);
        return true;
    }

    public function checkSite($site)
    {
        $db = Database::openConnection();
        $site = $this->toDatabase($site);
        $q = "SELECT name FROM {$this->table}";
        $rows = $db->queryData($q);
        $valid = 'true';
        foreach($rows as $row)
        {
        	if($site == $row['name'])
        	{
        		$valid = 'false';
        	}
        }
        return $valid;
    }

    public function getAllSites($active = -1)
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

    public function getSiteName($site_id)
    {
        $db = Database::openConnection();
        $res = $db->queryRow("SELECT name FROM {$this->table} WHERE id = $site_id");
        return $this->toDisplay($res['name']);
    }

    public function getSiteId($site)
    {
        $db = Database::openConnection();
        $site = $this->toDatabase($site);
        return $db->queryValue($this->table, array('name' => $site));
    }

    public function getSelectSites($selected = false)
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $q = "
            SELECT
                id, name
            FROM
                {$this->table}
            WHERE
                active = 1
            ORDER BY name";
        $sites = $db->queryData($q);
        foreach($sites as $s)
        {
            $label = $this->toDisplay($s['name']);
            $value = $s['id'];
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;

    }

    public function addSite($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'  =>  $this->toDatabase($data['name'])
        );
        $db->insertQuery($this->table, $vals);
        return true;
    }

    public function updateSite($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'  =>  $this->toDatabase($data['name'])
        );
        $db->updateDatabaseFields($this->table, $vals, $data['id']);
        return true;
    }

    private function toDatabase($name)
    {
        return strtolower(str_replace(" ","_",$name));
    }

    private function toDisplay($name)
    {
        return ucwords(str_replace("_"," ",$name));
    }
}

?>