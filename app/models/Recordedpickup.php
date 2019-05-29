<?php

 /**
  * Recordedpickup Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>

    FUNCTIONS

    getAddressString($id, $prefix = "")
    getPickup($id)
    getPickups($client_id = 0)
    recordData($data)

  */

class Recordedpickup extends Model{

    public $table = "recorded_pickups";

    public function getPickup($id)
    {
        $db = Database::openConnection();
        return $db->queryByID($this->table, $id);
    }

    public function recordData($data)
    {
        $db = Database::openConnection();
        $db->insertQuery($this->table, $data);
        return true;
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

    public function getPickups($from, $to, $client_id)
    {
        $db = Database::openConnection();

        $query = "
        SELECT
            *
        FROM
            {$this->table}
        WHERE
            client_id = :client_id AND date_recorded >= :from AND date_recorded <= :to ORDER BY date_recorded DESC";
        $array = array(
            'client_id' => 	$client_id,
            'to'        =>  $to,
            'from'      =>  $from
        );
        return $db->queryData($query, $array);
    }

    public function updatePickupValues($values, $id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseFields($this->table, $values, $id);
        return true;
    }

}
?>