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
}
?>