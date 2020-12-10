<?php

/**
    * Prodution Contact Class
    *

    * @author     Mark Solly <mark.solly@fsg.com.au>

        FUNCTIONS

    */

class Productioncontact extends Model{
    public $table = "production_contacts";

    public function addContact($data)
    {
        //echo "productioncontact <pre>",print_r($data),"</pre>";die();
        $db = Database::openConnection();
        $id = $db->insertQuery($this->table, $data);
        return $id;
    }

    public function updateContact($data)
    {
        //echo "productioncontact <pre>",print_r($data),"</pre>";die();
        $db = Database::openConnection();
        if(empty($data['contact_id']))
        {
            unset($data['contact_id']);
            $id = $db->insertQuery($this->table, $data);
        }
        else
        {
            $id = $data['contact_id'];
            unset($data['contact_id']);
            $db->updateDatabaseFields($this->table, $data, $id);
        }
        return $id;
    }
}
?>