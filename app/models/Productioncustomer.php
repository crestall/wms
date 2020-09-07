<?php

/**
    * Production Customer Class
    *

    * @author     Mark Solly <mark.solly@fsg.com.au>

        FUNCTIONS

        addCustomer($data)
        editCustomer($data)
        getAllCustomers()
        getCustomerById($id = 0)
        getSelectCustomers($selected = false)

    */

class Productioncustomer extends Model{
    public $table = "production_customers";

    public function getSelectCustomers($selected = false)
    {
        $db = Database::openConnection();

        $check = "";
        $ret_string = "";
        $q = "SELECT id, name FROM {$this->table} ORDER BY name";
        $reps = $db->queryData($q);
        foreach($reps as $r)
        {
            $label = ucwords($r['name']);
            $value = $r['id'];
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function getAllCustomers()
    {
        $db = Database::openConnection();
        return $db->queryData("SELECT * FROM {$this->table} ORDER BY name");
    }

    public function getCustomerById($id = 0)
    {
        $db = Database::openConnection();
        return $db->queryById($this->table, $id);
    }

    public function addCustomer($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'          =>  $data['name']
        );
        if(!empty($data['phone'])) $vals['phone'] = $data['phone'];
        if(!empty($data['contact'])) $vals['contact'] = $data['contact'];
        if(!empty($data['email'])) $vals['email'] = $data['email'];
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
            'name'          =>  $data['name'],
            'email'         =>  null,
            'contact'       =>  null,
            'phone'         =>  null,
            'address'       =>  null,
            'address_2'     =>  null,
            'suburb'        =>  null,
            'state'         =>  null,
            'postcode'      =>  null,
            'country'       =>  null
        );
        if(!empty($data['contact'])) $vals['contact'] = $data['contact'];
        if(!empty($data['email'])) $vals['email'] = $data['email'];
        if(!empty($data['phone'])) $vals['phone'] = $data['phone'];
        if(!empty($data['address'])) $vals['address'] = $data['address'];
        if(!empty($data['address2'])) $vals['address_2'] = $data['address2'];
        if(!empty($data['suburb'])) $vals['suburb'] = $data['suburb'];
        if(!empty($data['state'])) $vals['state'] = $data['state'];
        if(!empty($data['postcode'])) $vals['postcode'] = $data['postcode'];
        if(!empty($data['country'])) $vals['country'] = $data['country'];
        $id = $db->updateDatabaseFields($this->table, $vals, $data['customer_id']);
        return $id;
    }

    public function getAutocompleteCustomer($data)
    {
        $db = Database::openConnection();
        $return_array = array();
        echo "The request<pre>",print_r($data),"</pre>";die();
        $q = $data["customer_name"];
        $q = "
            SELECT
                *
            FROM
                {$this->table}
            WHERE
                name LIKE :term
        ";
        $array = array(
            'term'  => '%'.$q.'%'
        );
        $rows = $db->queryData($query, $array);
        foreach($rows as $row)
        {
            $row_array              = array();
            $row_array['value']     = ucwords($row['name']);
            $row_array['contact']   = $row['contact'];
            $row_array['email']     = $row['email'];
            $row_array['phone']     = $row['phone'];
            $row_array['address']   = $row['address'];
            $row_array['address_2'] = $row['address_2'];
            $row_array['suburb']    = $row['suburb'];
            $row_array['state']     = $row['state'];
            $row_array['postcode']  = $row['postcode'];
            $row_array['country']   = $row['country'];

            array_push($return_array,$row_array);
        }
        return $return_array;
    }
}
?>