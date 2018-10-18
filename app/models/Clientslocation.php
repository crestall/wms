<?php
 /**
  * Clientslocation Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>


  FUNCTIONS

  addLocation($data)
  deleteAllocation($id)
  getCurrentLocations()


  */
class Clientslocation extends Model{
    public $table = "clients_locations";

    public function deleteAllocation($id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'date_removed', time(), $id);
        return true;
    }

    public function deleteAllocationByClientLocation($client_id, $location_id)
    {
        $db = Database::openConnection();
        $query = "
            UPDATE clients_locations SET date_removed = ".time()." WHERE client_id = $client_id AND location_id = $location_id AND date_removed = 0
        ";
        //die($query);
        $db->query($query);
        return true;
    }

    public function getCurrentLocations()
    {
        $db = Database::openConnection();
        return $db->queryData("SELECT cl.*, l.location FROM clients_locations cl JOIN locations l ON cl.location_id = l.id WHERE cl.date_removed = 0 ORDER BY l.location");
    }

    public function addLocation($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'location_id'   =>  $data['location'],
            'client_id'     =>  $data['client_id'],
            'date_added'    =>  time()
        );
        if(!empty($data['notes']))
            $vals['notes'] = $data['notes'];

        $db->insertQuery($this->table, $vals);
        return true;
    }
}
?>