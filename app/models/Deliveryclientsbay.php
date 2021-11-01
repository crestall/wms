<?php
 /**
  * Deliveryclientsbay Class
  *

  * @author     Mark Solly <mark.solly@fsg.com.au>


  FUNCTIONS

  getBayUsage($from, $to)
  stockAdded($client_id, $location_id)
  stockRemoved($client_id, $location_id, $product_id)

  */
class Deliveryclientsbay extends Model{
    public $table = "delivery_clients_bays";

    public function getCurrentBayUsage($client_id)
    {
        $db = Database::openConnection();
        $bays = $db->queryData("
            SELECT * FROM {$this->table} WHERE client_id = $client_id AND date_removed = 0;
        ");

        return $bays;
    }

    public function getBayUsage($from, $to)
    {

    }

    public function stockAdded($client_id, $location_id, $to_receiving = 0, $pallet_multiplier = 1, $is_oversize = false)
    {
        $db = Database::openConnection();
        $oversize = ($is_oversize)? 1 : 0;
        $not_oversize = ($is_oversize)? 0 : 1;
        //die('oversize '.$oversize);
        if($to_receiving)
        {
            $location = new Location();
            $location_id = $location->receiving_id;

            if($updater = $db->queryValue($this->table, array('client_id' => $client_id, 'location_id' => $location_id, 'date_removed'  =>  0)))
            {
                $db->query("UPDATE {$this->table} SET pallet_multiplier = pallet_multiplier + $pallet_multiplier WHERE id = $updater");
            }
            else
            {
                $db->insertQuery($this->table, array(
                    'client_id'         =>  $client_id,
                    'location_id'       =>  $location_id,
                    'date_added'        =>  time(),
                    'pallet_multiplier' =>  $pallet_multiplier
                ));
            }
        }
        else
        {
            $row = $db->queryRow("
                SELECT * FROM {$this->table} WHERE client_id = :client_id AND location_id = :location_id AND date_removed = 0
            ",
            array(
                'client_id'     => $client_id,
                'location_id'   => $location_id
            ));
            //echo "<pre>The row",print_r($row),"</pre>";die();
            //die("row count".count($row));
            if(isset($row['id']) && $row['oversize'] == $not_oversize)
            {
                $db->updateDatabaseField($this->table, 'date_removed', time(), $row['id']);
                $array = array(
                    'client_id'     =>  $client_id,
                    'location_id'   =>  $location_id,
                    'date_added'    =>  time(),
                    'oversize'      =>  $oversize
                );
                //echo "<pre>The row",print_r($array),"</pre>";die();
                $db->insertQuery($this->table, $array);
            }
            elseif( !isset($row['id']) )
            {
                $array = array(
                    'client_id'     =>  $client_id,
                    'location_id'   =>  $location_id,
                    'date_added'    =>  time(),
                    'oversize'      =>  $oversize
                );
                //echo "<pre>The row",print_r($array),"</pre>";die();
                $db->insertQuery($this->table, $array);
            }
        }
        return true;
    }

    public function stockRemoved($client_id, $location_id, $item_id)
    {
        $db = Database::openConnection();
        $this_row = $db->queryRow("
            SELECT *
            FROM {$this->table}
            WHERE date_removed = 0 AND client_id = $client_id AND location_id = $location_id AND item_id = $item_id
        ");
        $db->updateDatabaseField($this->table, 'date_removed', time(), $this_row['id']);
        return true;
    }

}
?>