<?php
 /**
  * Truckusage Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>

  FUNCTIONS

  recordData($data)

  */
class Truckusage extends Model{
    public $table = "truck_usage";

    public function recordData($data)
    {
        $db = Database::openConnection();
        $db->insertQuery($this->table, $data);
        return true;
    }

    public function getRunSheet($from, $to)
    {
        $db = Database::openConnection();
        $query = "
            SELECT
                c.client_name, DATE_FORMAT(FROM_UNIXTIME(tu.date), '%e %b %Y %T') AS 'date', o.order_number, tu.charge, tu.suburb, tu.entered_by
            FROM
                truck_usage tu join clients c on tu.client_id = c.id join orders o on o.id = tu.order_id
            WHERE
                date >= $from AND date <= $to
        ";

        return $db->queryData($query);
    }

    public function getRunSheetArray($from, $to)
    {
        $db = Database::openConnection();
        $runs = $this->getRunSheet($from, $to);
        $return = array();
        foreach($runs as $r)
        {
            $eb = $db->queryValue('users', array('id' => $r['entered_by']), 'name');
            $row = array(
                'date'          => $r['date'],
                'client_name'   => $r['client_name'],
                'order_number'  => $r['order_number'],
                'suburb'        => $r['suburb'],
                'charge'        => '$'.number_format($r['charge'],2),
                'entered_by'    => $eb
            );
            $return[] = $row;
        }
        return $return;
    }

}
?>