<?php

 /**
  * Itemscollection Class
  *

  * @author     Mark Solly <mark.solly@fsg.com.au>

    FUNCTIONS

    getAddressString($id, $prefix = "")
    getCurrentPickups()
    getPickup($id)
    getPickupByNumber($pickup_number)
    getPickupNumber()
    getPickups($client_id = 0)
    recordData($data)

  */
class Itemcollection extends Model{
    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "items_collections";
    public $status_table = "items_collections_status";
    public $booked_id;
    public $complete_id;
    public $cancelled_id;
    public $status = array();

    public function __construct()
    {
        $this->booked_id      = $this->getStatusId('booked');
        $this->complete_id    = $this->getStatusId('complete');
        $this->cancelled_id   = $this->getStatusId('cancelled');
        $this->getStatusArray();
    }

    public function addItemCollection($data)
    {
        echo "<pre>",print_r($data),"</pre>"; die(); 
    }

    public function cancelItemCollection($id)
    {
        $status_id = $this->getStatusId("cancelled");
        $this->updateStatusId($status_id, $id);
    }

    public function getItemsCollectionNumber()
    {
        $db = Database::openConnection();
        $items_collection_number = Utility::ean13_check_digit(Utility::randomNumber(12));
        while($db->queryValue($this->table, array('items_collection_number' => $items_collection_number)))
        {
            $items_collection_number = Utility::ean13_check_digit(Utility::randomNumber(12));
        }
        return $items_collection_number;
    }

    private function getStatusId($status)
    {
        $db = Database::openConnection();
        return ($db->queryValue($this->status_table, array('name' => $status)));
    }

    private function getStatusArray()
    {
        $db = Database::openConnection();
        $statusses = $db->queryData("SELECT id, name FROM {$this->status_table}");
        foreach($statusses as $status)
        {
            $this->status[$status['id']] = $status['name'];
        }
    }

    private function updateStatusId($status_id, $item_collection_id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'status_id', $status_id , $item_collection_id);
    }

}//end class

?>