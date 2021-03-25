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
    public $contacts_table = "production_contacts";

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

    public function getMultiSelectCustomers($selected = array())
    {
        $db = Database::openConnection();

        $ret_string = "";
        $q = "SELECT id, name FROM {$this->table} ORDER BY name";
        $reps = $db->queryData($q);
        foreach($reps as $r)
        {
            $label = ucwords($r['name']);
            $value = $r['id'];
            $ret_string .= "<option value='$value'";
            if(in_array($value, $selected))
            {
                $ret_string .= " selected";
            }
            $ret_string .= ">$label</option>";
        }
        return $ret_string;
    }

    public function getAllCustomers()
    {
        $db = Database::openConnection();
        $q = $this->generateQuery();
        $q .= " GROUP BY c.id ORDER BY c.name";
        return $db->queryData($q);
    }

    public function getCustomerById($id = 0)
    {
        $db = Database::openConnection();
        $q = $this->generateQuery();
        $q .= "WHERE c.id = $id";
        return $db->queryRow($q);
    }

    public function geCustomerIdByName($name)
    {
        $db = Database::openConnection();
        $q = "SELECT id FROM {$this->table} WHERE `name` LIKE :val LIMIT 1";
        $array = array('val' => '%'.$name.'%');
        $row = $db->queryRow($q, $array);
        return $row['id'];
    }

    public function addCustomer($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'          =>  $data['name']
        );
        if(!empty($data['phone'])) $vals['phone'] = $data['phone'];
        if(!empty($data['website'])) $vals['website'] = $data['website'];
        if(!empty($data['email'])) $vals['email'] = $data['email'];
        if(!empty($data['address'])) $vals['address'] = $data['address'];
        if(!empty($data['address2'])) $vals['address_2'] = $data['address2'];
        if(!empty($data['suburb'])) $vals['suburb'] = $data['suburb'];
        if(!empty($data['state'])) $vals['state'] = $data['state'];
        if(!empty($data['postcode'])) $vals['postcode'] = $data['postcode'];
        if(!empty($data['country'])) $vals['country'] = $data['country'];
        $id = $db->insertQuery($this->table, $vals);
        if(isset($data['contacts']) && is_array($data['contacts']))
        {
            foreach($data['contacts'] as $contact)
            {
                $contact['customer_id'] = $id;
                if(isset($contact['role']))
                    $contact['role'] = str_replace("|", "/", $contact['role']);
                $pcontact = new Productioncontact();
                $pcontact->addContact($contact);
            }
        }
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
            'country'       =>  null,
            'website'       =>  null
        );
        if(!empty($data['email'])) $vals['email'] = $data['email'];
        if(!empty($data['phone'])) $vals['phone'] = $data['phone'];
        if(!empty($data['address'])) $vals['address'] = $data['address'];
        if(!empty($data['address2'])) $vals['address_2'] = $data['address2'];
        if(!empty($data['suburb'])) $vals['suburb'] = $data['suburb'];
        if(!empty($data['state'])) $vals['state'] = $data['state'];
        if(!empty($data['postcode'])) $vals['postcode'] = $data['postcode'];
        if(!empty($data['country'])) $vals['country'] = $data['country'];
        if(!empty($data['website'])) $vals['website'] = $data['website'];
        $db->updateDatabaseFields($this->table, $vals, $data['customer_id']);
        if(isset($data['contacts']) && is_array($data['contacts']))
        {
            $pcontact = new Productioncontact();
            $pcontact->removeCustomerContacts($data['customer_id']);
            foreach($data['contacts'] as $contact)
            {
                $contact['customer_id'] = $data['customer_id'];
                if(isset($contact['role']))
                    $contact['role'] = str_replace("|", "/", $contact['role']);
                $pcontact->addContact($contact);
            }
        }
        return true;
    }

    public function getAutocompleteCustomer($data)
    {
        $db = Database::openConnection();
        $return_array = array();
        //echo "The request<pre>",print_r($data),"</pre>";die();
        $q = $data;
        $query = $this->generateQuery();
        $query .= "
            WHERE
                c.name LIKE :term
            GROUP BY
                c.id
        ";
        $array = array(
            'term'  => '%'.$q.'%'
        );
        $rows = $db->queryData($query, $array);
        foreach($rows as $row)
        {
            $row_array                  = array();
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
            $row_array['website']       = $row['website'];
            $row_array['contacts']      = $row['contacts'];

            array_push($return_array,$row_array);
        }
        return $return_array;
    }

    private function generateQuery()
    {
        return "
            SELECT
                c.*,
                GROUP_CONCAT(pc.id,',',IFNULL(pc.name,''),',',IFNULL(pc.email,''),',',IFNULL(pc.phone,''),',',IFNULL(pc.role,'') SEPARATOR '|') AS contacts
            FROM
                {$this->table} c LEFT JOIN
                {$this->contacts_table} pc ON c.id = pc.customer_id
        ";
    }
}
?>