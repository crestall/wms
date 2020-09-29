<?php
class Driver extends Model{

    public function __construct()
    {
        parent::__construct();
    }

    public function addDriver($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'      =>  $data['name']
        );
        if(isset($data['phone']))
        {
            $vals['phone'] = $data['phone'];
        }
        return $db->insertQuery($this->table, $vals);
    }

    public function editDriver($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'      =>  $data['name']
        );
        $vals['active'] = (isset($data['active']))? 1:0;
        $vals['phone'] = (isset($data['phone']))? $data['phone']:NULL;
        $db->updateDatabaseFields($this->table, $vals, $data['id']);
        return true;
    }

    public function getSelectDrivers( $selected = false )
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $query = "SELECT id, name FROM {$this->table} WHERE active = 1";
        $query .= " ORDER BY name";
        $drivers = $db->queryData($query);
        foreach($drivers as $c)
        {
            $label = ucwords($c['name']);
            $value = $c['id'];
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function getDriverName($id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' => $id), 'name');
    }

    public function getDrivers($active = -1)
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

    public function getDriverId($name)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('name' => $name));
    }
}
?>