<?php

 /**
  * Orderreturn Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>

    FUNCTIONS

    getReturnedOrders($from, $to, $client_id)
    getReturnedOrdersArray($from, $to, $client_id)

  */

class Orderreturn extends Model{
    public $table = "returns";

    public function recordData($data)
    {
        $db = Database::openConnection();
        $db->insertQuery($this->table, $data);
        return true;
    }

    public function getReturnedOrders($from, $to, $client_id)
    {
        $db = Database::openConnection();
        $query = "
            SELECT r.*, i.name, o.order_number, o.client_order_id
            FROM returns r JOIN items i ON i.id = r.item_id JOIN orders o ON o.id = r.order_id
            WHERE r.client_id = :client_id AND date >= :from AND date <= :to ORDER BY date DESC
        ";
        $array = array(
            'client_id' => 	$client_id,
            'from'      =>  $from,
            'to'        =>  $to
        );
        return $db->queryData($query, $array);
    }

    public function getReturnedOrdersArray($from, $to, $client_id)
    {
        $db = Database::openConnection();
        $returns = $this->getReturnedOrders($from, $to, $client_id);
        $return = array();
        foreach($returns as $r)
        {
            $eb = $db->queryValue('users', array('id' => $r['entered_by']), 'name');
            $row = array(
                'return_date'           => date('d/m/Y', $r['date']),
                'item_name'             => $r['name'],
                'order_number'          => $r['order_number'],
                'client_order_number'   => $r['client_order_id'],
                'reason'                => $r['reason'],
                'entered_by'            => $eb
            );
            $return[] = $row;
        }
        return $return;
    }
}
?>