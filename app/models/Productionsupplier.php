<?php

/**
    * Prodution Supplier Class
    *

    * @author     Mark Solly <mark.solly@fsg.com.au>

        FUNCTIONS

        addSupplier($data)
        editSupplier($data)
        getAllSuppliers($active = 1)
        getSupplierById($id = 0)
        getSelectSuppliers($selected = false)

    */

class Productionsupplier extends Model{
    public $table = "production_suppliers";

    public function getSelectSuppliers($selected = false)
    {
        $db = Database::openConnection();

        $check = "";
        $ret_string = "";
        $q = "SELECT id, name FROM {$this->table} WHERE active = 1 ORDER BY name";
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

    public function getAllSuppliers($active = 1)
    {
        $db = Database::openConnection();
        return $db->queryData("SELECT * FROM {$this->table} WHERE active = $active ORDER BY name");
    }

    public function getSupplierById($id = 0)
    {
        $db = Database::openConnection();
        return $db->queryById($this->table, $id);
    }

    public function getSupplierIdByName($name)
    {
        $db = Database::openConnection();
        $q = "SELECT id FROM {$this->table} WHERE `name` LIKE :val LIMIT 1";
        $array = array('val' => '%'.$name.'%');
        $row = $db->queryRow($q, $array);
        return $row['id'];
    }

    public function addSupplier($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'          =>  $data['name']
        );
        if(!empty($data['email'])) $vals['email'] = $data['email'];
        if(!empty($data['contact'])) $vals['contact'] = $data['contact'];
        if(!empty($data['phone'])) $vals['phone'] = $data['phone'];
        if(!empty($data['address'])) $vals['address'] = $data['address'];
        if(!empty($data['address2'])) $vals['address_2'] = $data['address2'];
        if(!empty($data['suburb'])) $vals['suburb'] = $data['suburb'];
        if(!empty($data['state'])) $vals['state'] = $data['state'];
        if(!empty($data['postcode'])) $vals['postcode'] = $data['postcode'];
        if(!empty($data['country'])) $vals['country'] = $data['country'];
        $id = $db->insertQuery($this->table, $vals);
        return $id;
    }

    public function editSupplier($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'          =>  strtolower($data['name']),
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
        $vals['active'] = isset($data['active'])? 1 : 0;
        if(!empty($data['email'])) $vals['email'] = $data['email'];
        if(!empty($data['contact'])) $vals['contact'] = $data['contact'];
        if(!empty($data['phone'])) $vals['phone'] = $data['phone'];
        if(!empty($data['address'])) $vals['address'] = $data['address'];
        if(!empty($data['address2'])) $vals['address_2'] = $data['address2'];
        if(!empty($data['suburb'])) $vals['suburb'] = $data['suburb'];
        if(!empty($data['state'])) $vals['state'] = $data['state'];
        if(!empty($data['postcode'])) $vals['postcode'] = $data['postcode'];
        if(!empty($data['country'])) $vals['country'] = $data['country'];
        $id = $db->updateDatabaseFields($this->table, $vals, $data['supplier_id']);
        return $id;
    }

    public function getAutocompleteSupplier($data)
    {
        $db = Database::openConnection();
        $return_array = array();
        //echo "The request<pre>",print_r($data),"</pre>";die();
        $q = $data;
        $query = "
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
            $row_array                  = array();
            $row_array['value']         = ucwords($row['name']);
            $row_array['contact']       = $row['contact'];
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
}
?>