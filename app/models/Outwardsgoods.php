<?php
 /**
  * Outwardsgoods Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>

  FUNCTIONS

  recordData($data)

  */
class Outwardsgoods extends Model{
    public $table = "outwards_goods";

    public function recordData($data)
    {
        $db = Database::openConnection();
        $db->insertQuery($this->table, $data);
        return true;
    }

     public function getOutwardsGoods($from, $to)
    {
        $db = Database::openConnection();
        $query = "
            SELECT
                og.*, c.client_name
            FROM
                outwards_goods og JOIN clients c ON c.id = og.client_id
            WHERE
                og.date >= $from AND og.date <= $to
            ORDER BY
                og.date DESC
        ";

        return $db->queryData($query);
    }

    public function getOutwardsGoodsArray($from, $to)
    {
        $db = Database::openConnection();
        $goods = $this->getOutwardsGoods($from, $to);
        $return = array();
        foreach($goods as $g)
        {
            $eb = $db->queryValue('users', array('id' => $g['entered_by']), 'name');
            $row = array(
                'date'          => date('d/m/Y', $g['date']),
                'client_name'   => $g['client_name'],
                'pallets'       => $g['pallets'],
                'cartons'       => $g['cartons'],
                'satchels'      => $g['satchels'],
                'entered_by'    => $eb
            );
            $return[] = $row;
        }
        return $return;
    }

    public function getSummary($from, $to)
    {
        $db = Database::openConnection();
        $query = "
                SELECT
                    SUM(og.pallets) AS pallets, SUM(og.cartons) AS cartons, SUM(og.satchels) AS satchels, c.client_name, c.pallet_charge, c.carton_charge
                FROM
                    outwards_goods og JOIN clients c ON c.id = og.client_id
                WHERE
                    og.date >= $from AND og.date <= $to
                GROUP BY
                    og.client_id
                ORDER BY
                    c.client_name
        ";

        return $db->queryData($query);
    }

    public function getSummaryArray($from, $to)
    {
        $goods = $this->getSummary($from, $to);
        $return = array();
        foreach($goods as $g)
        {
            $row = array(
                'client_name'   => $g['client_name'],
                'pallets'       => $g['pallets'],
                'cartons'       => $g['cartons'],
                'satchels'      => $g['satchels']
            );
            $return[] = $row;
        }
        return $return;
    }
}
?>