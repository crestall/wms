<?php
/**
    * Prodution DeliveryDocketSender Class
    *

    * @author     Mark Solly <mark.solly@fsg.com.au>

        FUNCTIONS

    */
class Deliverydocketsender extends Model{
    public $table = "delivery_docket_senders";

    public function getSelectSender($selected = false)
    {
        $db = Database::openConnection();
        $return_string = "";
        $senders = $db->queryData("SELECT * FROM {$this->table} ORDER BY is_default DESC, name");
        foreach($senders as $s)
        {
            $label = $s['name'];
            $value = $s['id'];
            $return_string .= "<option value=$value ";
            if($selected && $selected == $s['id'])
        	{
        		$return_string .= "selected='selected' ";
        	}
            $return_string .= ">$label</option>";
        }
        return $return_string;
    }//end function

    public function addSender($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'      =>  ucwords($data['name'])
        );
        return $db->insertQuery($this->table, $vals);
    }

    public function editSender($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'      =>  ucwords($data['name']),
            'address'   =>  null
        );
        if(!empty($data['address'])) $vals['address'] = $data['address'];
        if(isset($data['is_default']))
        {
            $db->query("UPDATE {$this->table} SET is_default = 0");
            $vals['is_default'] = 1;
        }
        if(!isset($data['delete_image']))
        {
            if(isset($data['image_name'])) $vals['logo'] = $data['image_name'].".jpg";
        }
        else
        {
            $vals['logo'] = null;
        }
        $db->updateDatabaseFields($this->table, $vals, $data['id']);
        return true;
    }

    public function getSenderById($id = 0)
    {
        $db = Database::openConnection();
        return $db->queryByID($this->table, $id);
    }
}
?>