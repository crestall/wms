<?php
 /**
  * Inwardsgoods Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>

  FUNCTIONS

  getInwardsGoods($from, $to)
  getInwardsGoodsArray($from, $to)
  recordData($data)

  */
class Inwardsgoods extends Model{
    public $table = "inwards_goods";

    public function recordData($data)
    {
        $db = Database::openConnection();
        $db->insertQuery($this->table, $data);
        return true;
    }

    public function getInwardsGoods($from, $to)
    {
        $db = Database::openConnection();
        $query = "
            SELECT
                ig.*, c.client_name
            FROM
                inwards_goods ig JOIN clients c ON c.id = ig.client_id
            WHERE
                ig.date >= $from AND ig.date <= $to
            ORDER BY
                ig.date DESC
        ";

        return $db->queryData($query);
    }

    public function getInwardsGoodsArray($from, $to)
    {
        $db = Database::openConnection();
        $goods = $this->getInwardsGoods($from, $to);
        $return = array();
        foreach($goods as $g)
        {
            $eb = $db->queryValue('users', array('id' => $g['entered_by']), 'name');
            $row = array(
                'date'          => date('d/m/Y', $g['date']),
                'client_name'   => $g['client_name'],
                'pallets'       => $g['pallets'],
                'cartons'       => $g['cartons'],
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
                SUM(ig.pallets) AS pallets, SUM(ig.cartons) AS cartons, c.client_name, c.pallet_charge, c.carton_charge
            FROM
                inwards_goods ig JOIN clients c ON c.id = ig.client_id
            WHERE
                ig.date >= $from AND ig.date <= $to
            GROUP BY
                ig.client_id
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
                'cartons'       => $g['cartons']
            );
            $return[] = $row;
        }
        return $return;
    }
}
?>