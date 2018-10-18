<?php
class Packingtype extends Model{
    public $table = "packing_types";

    public function getSelectPackingTypesMultiple(array $selected = array())
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $types = $db->queryData("SELECT id, name, multiples FROM {$this->table} ORDER BY name");
        foreach($types as $t)
        {
            $label = $t['name'];
            $value = $t['id'];
            if(count($selected))
            {
                $check = (in_array($value, $selected))? "selected='selected'" : "";
            }
            $ret_string .= "<option data-multiples='{$t['multiples']}' $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function getTypeName($id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' =>  $id), 'name');
    }

    public function getTypeId($name)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('name' =>  $name));
    }

    public function getSelectPackingTypes($selected = false)
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $types = $db->queryData("SELECT id, name FROM {$this->table} WHERE active = 1 ORDER BY name");
        foreach($types as $t)
        {
            $label = $t['name'];
            $value = $t['id'];
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function getPackingTypes($active = -1)
    {
        $db = Database::openConnection();
        $q = "SELECT * FROM ".$this->table;
        if($active >= 0)
        {
            $q .= " WHERE active = $active";
        }
        $q .= " ORDER BY name";
        return $db->queryData($q);
    }

    public function addType($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'      =>  $data['name'],
            'width'     =>  $data['width'],
            'depth'     =>  $data['depth'],
            'height'    =>  $data['height']
        );
        $vals['multiples'] = (isset($data['multiples']))? 1:0;
        return $db->insertQuery($this->table, $vals);
    }

    public function editType($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'      =>  $data['name'],
            'width'     =>  $data['width'],
            'depth'     =>  $data['depth'],
            'height'    =>  $data['height']
        );
        $vals['active'] = (isset($data['active']))? 1:0;
        $vals['multiples'] = (isset($data['multiples']))? 1:0;
        $db->updateDatabaseFields($this->table, $vals, $data['id']);
    }

    public function isMultiple($type_id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' => $type_id), 'multiples') > 0;
    }
}
?>