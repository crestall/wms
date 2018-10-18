<?php

 /**
  * Customer Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>
  */

class Customer extends Model{

    public function getCustomerInfo($c_id)
    {
        $db = Database::openConnection();
        $customer = $db->queryById($this->table, $c_id);
        if(empty($customer))
        {
            throw new Exception("Customer ID " .  $c_id . " doesn't exists");
        }
        $customer["id"]    = (int)$customer["id"];
        return $customer;
    }

    public function getCustomerName($c_id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' => $c_id), 'name');
    }

    public function getCustomerEmail($c_id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' => $c_id), 'email');
    }
}
?>