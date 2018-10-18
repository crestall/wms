<?php
class Barcodetype extends Model{
    public $table = "barcode_types";

    public function getSelectBarcodeType($selected = false)
    {
        $db = Database::openConnection();
        $return_string = "";
        $types = $db->queryData("SELECT * FROM {$this->table} ORDER BY id, name");
        foreach($types as $t)
        {
            $return_string .= "<option ";
            if($selected && $selected == $t['name'])
        	{
        		$return_string .= "selected='selected' ";
        	}
            $return_string .= ">{$t['name']}</option>";
        }
        return $return_string;
    }//end function
}
?>