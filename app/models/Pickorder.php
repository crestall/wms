<?php

 /**
  * PickOrder Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>
  */

class Pickorder extends Model{

    public $table = "picks_orders";

    public function savePickSummary($summary_scan)
    {
        $db = Database::openConnection();
        $ids_barcode = $this->getSummaryPickNumber();
        //die('time: '.time());
        $vals = array(
            'barcode'   =>  $ids_barcode,
            'order_ids' =>  serialize($summary_scan),
            'made'      =>  time()
        );
        $db->insertQuery($this->table, $vals);
        return $ids_barcode;
    }

    public function getSummaryPickNumber()
    {
        $db = Database::openConnection();;
        $pick_number = Utility::randomNumber(12);
        while($db->queryValue($this->table, array('barcode' => $pick_number)))
        {
            $pick_number = Utility::randomNumber(12);
        }
        return $pick_number;
    }

    public function getSummaryByBarcode($summary_number)
    {
        $db = Database::openConnection();
        /*
        $q = "
            SELECT
                *
            FROM
                picks_orders
            WHERE
                barcode = $summary_number
        ";
        return $q;
        return $db->queryRow($q);

         */
        return $db->queryRow("
            SELECT
                *
            FROM
                picks_orders
            WHERE
                barcode = :barcode",
            array(
                'barcode'     =>  $summary_number
            )
        );

    }

}

?>