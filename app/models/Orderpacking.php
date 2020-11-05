<?php

 /**
  * Orderpacking Class
  *

  * @author     Mark Solly <mark.solly@fsg.com.au>

    FUNCTIONS

    getReturnedOrders($from, $to, $client_id)
    getReturnedOrdersArray($from, $to, $client_id)

  */

class Orderpacking extends Model{
    public $table = "order_packing";

    public function recordData($data)
    {
        $db = Database::openConnection();
        $db->insertQuery($this->table, $data);
        return true;
    }
}
?>