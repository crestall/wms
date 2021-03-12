<?php

 /**
  * Customer Class
  *

  * @author     Mark Solly <mark.solly@fsg.com.au>
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

    public function addCustomer($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'      =>  $data['deliver_to'],
            'client_id' => $data['client_id']
        );
        if(!empty($data['company_name'])) $vals['company'] = $data['company_name'];
        if(!empty($data['tracking_email'])) $vals['email'] = $data['tracking_email'];
        if(!empty($data['contact_phone'])) $vals['phone'] = $data['contact_phone'];
        if(!empty($data['address'])) $vals['address'] = $data['address'];
        if(!empty($data['address2'])) $vals['address_2'] = $data['address2'];
        if(!empty($data['suburb'])) $vals['suburb'] = $data['suburb'];
        if(!empty($data['state'])) $vals['state'] = $data['state'];
        if(!empty($data['postcode'])) $vals['postcode'] = $data['postcode'];
        if(!empty($data['country'])) $vals['country'] = $data['country'];
        $id = $db->insertQuery($this->table, $vals);
        return $id;
    }

    public function editCustomer($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'          => $data['deliver_to'],
            'client_id'     => $data['client_id'],
            'email'         =>  null,
            'company'       =>  null,
            'phone'         =>  null,
            'address'       =>  null,
            'address_2'     =>  null,
            'suburb'        =>  null,
            'state'         =>  null,
            'postcode'      =>  null,
            'country'       =>  null
        );
        if(!empty($data['company_name'])) $vals['company'] = $data['company_name'];
        if(!empty($data['tracking_email'])) $vals['email'] = $data['tracking_email'];
        if(!empty($data['contact_phone'])) $vals['phone'] = $data['contact_phone'];
        if(!empty($data['address'])) $vals['address'] = $data['address'];
        if(!empty($data['address2'])) $vals['address_2'] = $data['address2'];
        if(!empty($data['suburb'])) $vals['suburb'] = $data['suburb'];
        if(!empty($data['state'])) $vals['state'] = $data['state'];
        if(!empty($data['postcode'])) $vals['postcode'] = $data['postcode'];
        if(!empty($data['country'])) $vals['country'] = $data['country'];
        $id = $db->updateDatabaseFields($this->table, $vals, $data['customer_id']);
        return $id;
    }
}
?>