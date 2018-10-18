<?php
class Huntersgoodstype extends Model{
    public $table = "hunters_goods_types";

    public function getSelectHuntersPackageType($selected = false)
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $types = $db->queryData("SELECT id, description FROM {$this->table} ORDER BY description");
        foreach($types as $t)
        {
            $label = $t['description'];
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