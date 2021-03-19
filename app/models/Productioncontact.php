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

    public function removeFinisherContacts($finisher_id)
    {
        $db = Database::openConnection();
        $db->deleteQuery($this->table, $finisher_id, 'finisher_id');
    }

    public function removeCustomerContacts($customer_id)
    {
        $db = Database::openConnection();
        $db->deleteQuery($this->table, $customer_id, 'customer_id');
    }

    public function getCustomerContactIDs($customer_id, $first = false)
    {
        $db = Database::openConnection();
        if($first)
        {
            return $db->queryValue($this->table, array('customer_id' => $customer_id));
        }
    }

    public function getCustomerContacts($customer_id)
    {
        $db = Database::openConnection();
        $q = "SELECT * FROM {$this->table} WHERE customer_id = :customer_id";
        return $db->queryData($q, ['customer_id' => $customer_id]);
    }

    public function getFinisherContacts($finisher_id)
    {
        $db = Database::openConnection();
        $q = "SELECT * FROM {$this->table} WHERE finisher_id = :finisher_id";
        return $db->queryData($q, ['finisher_id' => $finisher_id]);
    }
}
?>