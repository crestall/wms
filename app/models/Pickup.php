<?php

 /**
  * Pickup Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>

    FUNCTIONS

    getAddressString($id, $prefix = "")
    getCurrentPickups()
    getPickup($id)
    getPickupByNumber($pickup_number)
    getPickupNumber()
    getPickups($client_id = 0)
    recordData($data)

  */

class Pickup extends Model{

    public function getPickup($id)
    {
        $db = Database::openConnection();
        return $db->queryByID($this->table, $id);
    }

    public function getPickupByNumber($pickup_number)
    {
        $db = Database::openConnection();
        return $db->queryRow("SELECT * FROM pickups WHERE pickup_number = :pickup_number", array(
            "pickup_number" => $pickup_number
        ));
    }

    public function recordData($data)
    {
        $db = Database::openConnection();
        $db->insertQuery($this->table, $data);
        return true;
    }

    public function getCurrentPickups()
    {
       $db = Database::openConnection();
       $q = "  select
                    count(*) as pickup_count, c.client_name, pu.client_id
                from
                    pickups pu join clients c on pu.client_id = c.id
                where
                    pu.date_completed = 0 and c.active = 1
                group by
                    pu.client_id
                order by
                    c.client_name";

        return $db->queryData($q);
    }

    public function getAddressString($id, $prefix = "")
    {
		$db = Database::openConnection();
		$ret_string = "";
        $address = $db->queryRow("SELECT {$prefix}address, {$prefix}address_2, {$prefix}suburb, {$prefix}postcode FROM {$this->table} WHERE id = $id");
        if(!empty($address))
        {
            $ret_string = "<p>".$address[$prefix.'address'];
            if(!empty($address[$prefix.'address_2'])) $ret_string .= "<br/>".$address[$prefix.'address_2'];
            $ret_string .= "<br/>".$address[$prefix.'suburb'];
            $ret_string .= "<br/>VIC";
            $ret_string .= "<br/>AU";
            $ret_string .= "<br/>".$address[$prefix.'postcode']."</p>";
        }
        return $ret_string;
    }

    public function getPickups($client_id = 0)
    {
        $db = Database::openConnection();
        $q = "SELECT * FROM {$this->table} WHERE date_completed = 0";
        if($client_id > 0)
        {
            $q .= " AND client_id = $client_id";
        }
        $q .= " ORDER BY client_id, date ASC";
        //die($q);
        return ($db->queryData($q));
    }

    public function getPickupNumber()
    {
        $db = Database::openConnection();
        $pickup_number = Utility::ean13_check_digit(Utility::randomNumber(12));
        while($db->queryValue($this->table, array('pickup_number' => $pickup_number)))
        {
            $pickup_number = Utility::ean13_check_digit(Utility::randomNumber(12));
        }
        return $pickup_number;
    }

    public function updatePickupValues($values, $id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseFields($this->table, $values, $id);
        return true;
    }

    public function cancelPickup($id)
    {
        $db = Database::openConnection();
        $db->deleteQuery($this->table, $id);
    }

}
?>