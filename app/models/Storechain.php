<?php
class Storechain extends Model{
    public $table = "store_chains";

    public function getSelectStoreChains($selected = false)
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $types = $db->queryData("SELECT id, name FROM {$this->table} ORDER BY name");
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

    public function getChainName($id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' =>  $id), 'name');
    }

    public function getChainId($name)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('name' =>  $name));
    }

    public function addChain($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'      =>  $data['name']
        );
        return $db->insertQuery($this->table, $vals);
    }

    public function editChain($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'      =>  $data['name']
        );
        $vals['active'] = (isset($data['active']))? 1:0;
        $db->updateDatabaseFields($this->table, $vals, $data['id']);
        return true;
    }

    public function getChains($active = -1)
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