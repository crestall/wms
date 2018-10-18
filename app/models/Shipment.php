<?php

 /**
  * Shipment Class
  *

    FUNCTIONS

  getExpectedShipments($client_id = false)
  getShipmentItemsString($id)

  * @author     Mark Solly <mark.solly@3plplus.com.au>
  */

class Shipment extends Model{

    /**
      * Table name for this & extending classes.
      *
      * @var string
      */

    public function getShipmentItemsString($id)
    {
        $items = $this->getShipmentItems($id);
        $is = "";
        foreach($items as $i)
        {
            $is .= $i['name']." (".$i['qty'].")<br/>";
        }
        $is = rtrim($is,",<br/>");
        return $is;
    }

    public function getShipmentItems($id)
    {
        $db = Database::openConnection();

        $iq = "
            SELECT si.*, i.name
            FROM shipments_items si JOIN items i ON i.id = si.item_id
            WHERE si.shipment_id = $id
        ";
        return $db->queryData($iq);
    }

    public function getExpectedShipments($client_id = false)
    {
        $db = Database::openConnection();

        $query = "SELECT * FROM ".$this->table." WHERE arrived = 0";
        if($client_id)
        {
            $query .= " AND client_id = $client_id";
        }
        $query .= " ORDER BY date_expected ASC";
        return $db->queryData($query);
    }
}