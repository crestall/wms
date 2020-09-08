<?php
class Jobstatus extends Model{
    public $table = "job_status";

    public function getSelectJobStatus($selected = false, $active = 1)
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        //$status = $db->queryData("SELECT id, name FROM {$this->table} WHERE active=$active ORDER BY name");
        $status = $db->queryData("SELECT js.id, js.name, ds.status_id AS `default` FROM `job_status` js LEFT JOIN default_production_job_status ds ON ds.status_id = js.id WHERE active=$active ORDER BY name");
        foreach($status as $s)
        {
            $label = ucwords($s['name']);
            $value = $s['id'];
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            elseif(!(empty($s['default'])))
            {
                $check = "selected='selected'";
            }
            else
            {
                $check = "";
            }
            $ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function getStatusName($id)
    {
        $db = Database::openConnection();
        return ucwords($db->queryValue($this->table, array('id' =>  $id), 'name'));
    }

    public function getStatusId($name)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('name' =>  $name));
    }

    public function addStatus($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'      =>  strtolower($data['name'])
        );
        return $db->insertQuery($this->table, $vals);
    }

    public function editStatus($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'      =>  strtolower($data['name'])
        );
        $vals['active'] = (isset($data['active']))? 1:0;
        $db->updateDatabaseFields($this->table, $vals, $data['id']);
        return true;
    }

    public function getStatus($active = -1)
    {
        $db = Database::openConnection();
        //$q = "SELECT * FROM {$this->table}";
        $q = "SELECT js.*, ds.status_id AS `default` FROM `job_status` js LEFT JOIN default_production_job_status ds ON ds.status_id = js.id";
        if($active >= 0)
        {
            $q .= " WHERE active = $active";
        }
        $q .= " ORDER BY name";
        return $db->queryData($q);
    }

    public function checkStatusNames($name, $current_name)
    {
        $db = Database::openConnection();
        $name = strtoupper($name);
        $current_name = strtoupper($current_name);
        $q = "SELECT name FROM {$this->table}";
        $rows = $db->queryData($q);
        $valid = 'true';
        foreach($rows as $row)
        {
        	if($name == strtoupper($row['name']) && $name != $current_name)
        	{
        		$valid = 'false';
        	}
        }
        return $valid;
    }
}
?>