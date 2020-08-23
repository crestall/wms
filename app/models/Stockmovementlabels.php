<?php
class Stockmovementlabels extends Model{
    public $table = "stock_movement_labels";

    public function getLabelName($id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' =>  $id), 'name');
    }

    public function getLabelId($name)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('name' =>  $name));
    }

    public function getMovementLabels($active = -1)
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

    public function getSelectStockMovementLabels($selected = false)
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

    public function addLabel($label)
    {
        $db = Database::openConnection();
        $db->insertQuery($this->table, array(
            'name'      => $label,
            'locked'    => 0,
            'active'    => 0
        ));
    }

    public function updateLabel($data)
    {
       $db = Database::openConnection();
       $vals = array(
        "name"  => $data['reason']
       );
       if(isset($data['locked']) & $data['locked'] > 0) $vals['locked'] = 1;
       if(isset($data['active']) & $data['active'] > 0) $vals['active'] = 1;

       $db->updateDatabaseFields($this->table, $vals, $data['id']);
    }
}
?>