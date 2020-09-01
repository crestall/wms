<?php
class Jobstatus extends Model{
    public $table = "job_status";

    public function getSelectJobStatus($selected = false, $active = 1)
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $status = $db->queryData("SELECT id, name FROM {$this->table} WHERE active=$active ORDER BY name");
        foreach($status as $s)
        {
            $label = $s['name'];
            $value = $s['id'];
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function getStatusName($id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' =>  $id), 'name');
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
            'name'      =>  $data['name']
        );
        return $db->insertQuery($this->table, $vals);
    }

    public function editStatus($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'      =>  $data['name']
        );
        $vals['active'] = (isset($data['active']))? 1:0;
        $db->updateDatabaseFields($this->table, $vals, $data['id']);
        return true;
    }

    public function getStatus($active = -1)
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
}
?>