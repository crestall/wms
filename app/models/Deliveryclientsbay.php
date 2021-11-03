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

    public function stockAdded($data)
    {
        $db = Database::openConnection();
        $db->insertQuery($this->table, array(
            'client_id'     => $data['client_id'],
            'location_id'   => $data['location_id'],
            'item_id'       => $data['item_id'],
            'size'          => $data['size'],
            'date_added'    => time()
        ));
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