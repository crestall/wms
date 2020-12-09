<?php

/**
    * Prodution Finisher Class
    *

    * @author     Mark Solly <mark.solly@fsg.com.au>

        FUNCTIONS

    */

class Productioncontact extends Model{
    public $table = "production_contacts";

    public function addFinisherContact($data, $id)
    {
        echo "productioncontact $id <pre>",print_r($data),"</pre>";die();
        $db = Database::openConnection();
        $id = $db->insertQuery($this->table, $data);
        return $id;
    }
}
?>