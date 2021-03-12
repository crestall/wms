<?php

 /**
  * Customer Class
  *

  * @author     Mark Solly <mark.solly@fsg.com.au>
  */

class Customer extends Model{

    public function getAutocompleteCustomers($term, $client_id)
    {
        $db = Database::openConnection();
        $q = strtoupper($term);
        if (!$q) return;

        $rows = $db->queryData("
            SELECT * FROM {$this->table} WHERE ((`name` LIKE :term1) OR (`company` LIKE :term2)) AND `client_id` = $client_id
            ",
            array(
                'term1' => '%'.$q.'%',
                'term2' => '%'.$q.'%'
            )
        );
        //echo "SELECT * FROM {$this->table} WHERE ((`name` LIKE :term1) OR (`company` LIKE :term2)) AND `client_id` = $client_id";
        //print_r($rows);die();
        $return_array = array();
        foreach($rows as $row)
        {
            $row_array                  = array();
            $row_array['label']         = ucwords($row['name'])."-".$row['suburb'];
            $row_array['value']         = ucwords($row['name']);
            $row_array['email']         = $row['email'];
            $row_array['phone']         = $row['phone'];
            $row_array['address']       = $row['address'];
            $row_array['address_2']     = $row['address_2'];
            $row_array['suburb']        = $row['suburb'];
            $row_array['state']         = $row['state'];
            $row_array['postcode']      = $row['postcode'];
            $row_array['country']       = $row['country'];
            $row_array['customer_id']   = $row['id'];

            array_push($return_array,$row_array);
        }
        return $return_array;
    }

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