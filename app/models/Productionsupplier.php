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

    public function addSupplier($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'          =>  strtolower($data['name']),
            'email'         =>  $data['email'],
            'contact'       =>  $data['contact']
        );
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
            'email'         =>  $data['email'],
            'contact'       =>  $data['contact'],
            'address'       =>  null,
            'address_2'     =>  null,
            'suburb'        =>  null,
            'state'         =>  null,
            'postcode'      =>  null,
            'country'       =>  'AU'
        );
        $vals['active'] = isset($data['active'])? 1 : 0;
        if(!empty($data['address'])) $vals['address'] = $data['address'];
        if(!empty($data['address2'])) $vals['address_2'] = $data['address2'];
        if(!empty($data['suburb'])) $vals['suburb'] = $data['suburb'];
        if(!empty($data['state'])) $vals['state'] = $data['state'];
        if(!empty($data['postcode'])) $vals['postcode'] = $data['postcode'];
        if(!empty($data['country'])) $vals['country'] = $data['country'];
        $id = $db->updateDatabaseFields($this->table, $vals, $data['supplier_id']);
        return $id;
    }
}
?>