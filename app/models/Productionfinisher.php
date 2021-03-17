<?php

/**
    * Prodution Finisher Class
    *

    * @author     Mark Solly <mark.solly@fsg.com.au>

        FUNCTIONS

        addFinisher($data)
        editFinisher($data)
        getAllFinishers($active = 1)
        getFinisherById($id = 0)
        getSelectFinishers($selected = false)

    */

class Productionfinisher extends Model{
    public $table = "production_finishers";
    public $contacts_table = "production_contacts";

    public function deactivateFinisher($finisher_id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'active', 0, $finisher_id);
        return true;
    }

    public function reactivateFinisher($finisher_id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'active', 1, $finisher_id);
        return true;
    }

    public function getSelectFinisherContacts($finisher_id = 0, $selected = false)
    {
        $db = Database::openConnection();

        $check = "";
        $ret_string = "";
        $q = "SELECT id, name FROM {$this->contacts_table} WHERE finisher_id = $finisher_id ORDER BY name";
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

    public function getSelectFinishers($selected = false)
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

    public function getMultiSelectFinishers($selected = array())
    {
        $db = Database::openConnection();

        $ret_string = "";
        $q = "SELECT id, name FROM {$this->table} WHERE active = 1 ORDER BY name";
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

    public function getAllFinishers($active = 1)
    {
        $db = Database::openConnection();
        $q = $this->generateQuery();
        $q .= " WHERE active = $active GROUP BY pf.id ORDER BY pf.name";
        return $db->queryData($q);
    }

    public function getFinisherById($id = 0)
    {
        $db = Database::openConnection();
        //return $db->queryById($this->table, $id);
        $q = $this->generateQuery();
        $q .= "WHERE pf.id = $id";
        //die($q);
        return $db->queryRow($q);
    }

    public function getFinisherIdByName($name)
    {
        $db = Database::openConnection();
        $q = "SELECT id FROM {$this->table} WHERE `name` LIKE :val LIMIT 1";
        $array = array('val' => '%'.$name.'%');
        $row = $db->queryRow($q, $array);
        return $row['id'];
    }

    public function addFinisher($data)
    {
        echo "productionfinisher<pre>",print_r($data),"</pre>";//die();
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
        if(isset($data['categories']) && is_array($data['categories']))
        {
            $fcat = new Finishercategories();
            $fcat->addFinisherCategories($data['categories'], $id);
        }
        if(isset($data['contacts']) && is_array($data['contacts']))
        {
            foreach($data['contacts'] as $contact)
            {
                $contact['finisher_id'] = $id;
                $pcontact = new Productioncontact();
                $pcontact->addContact($contact);
            }
        }
        return $id;
    }

    public function editFinisher($data)
    {
        //echo "editfinisher<pre>",print_r($data),"</pre>";die();
        $db = Database::openConnection();
        $vals = array(
            'name'          =>  strtolower($data['name']),
            'address'       =>  null,
            'address_2'     =>  null,
            'suburb'        =>  null,
            'state'         =>  null,
            'postcode'      =>  null,
            'country'       =>  null,
            'website'       =>  null,
            'phone'         => null,
            'email'         => mull
        );
        $vals['active'] = isset($data['active'])? 1 : 0;
        if(!empty($data['phone'])) $vals['phone'] = $data['phone'];
        if(!empty($data['website'])) $vals['website'] = $data['website'];
        if(!empty($data['email'])) $vals['email'] = $data['email'];
        if(!empty($data['address'])) $vals['address'] = $data['address'];
        if(!empty($data['address2'])) $vals['address_2'] = $data['address2'];
        if(!empty($data['suburb'])) $vals['suburb'] = $data['suburb'];
        if(!empty($data['state'])) $vals['state'] = $data['state'];
        if(!empty($data['postcode'])) $vals['postcode'] = $data['postcode'];
        if(!empty($data['country'])) $vals['country'] = $data['country'];
        $db->updateDatabaseFields($this->table, $vals, $data['finisher_id']);
        if(isset($data['categories']) && is_array($data['categories']))
        {
            $fcat = new Finishercategories();
            $fcat->addFinisherCategories($data['categories'], $data['finisher_id']);
        }
        else
        {
            $fcat = new Finishercategories();
            $fcat->removeFinisherCategories($data['finisher_id']);
        }
        if(isset($data['contacts']) && is_array($data['contacts']))
        {
            $pcontact = new Productioncontact();
            $pcontact->removeFinisherContacts($data['finisher_id']);
            foreach($data['contacts'] as $contact)
            {
                $contact['finisher_id'] = $data['finisher_id'];
                $pcontact->addContact($contact);
            }
        }
        return true;
    }

    public function getAutocompleteFinisher($data)
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
                name LIKE :term AND active = 1

        ";
        $array = array(
            'term'  => '%'.$q.'%'
        );
        $rows = $db->queryData($query, $array);
        foreach($rows as $row)
        {
            $row_array                          = array();
            $row_array['value']                 = ucwords($row['name']);
            $row_array['finisher_contact']      = $row['contact'];
            $row_array['finisher_email']        = $row['email'];
            $row_array['finisher_website']      = $row['website'];
            $row_array['finisher_phone']        = $row['phone'];
            $row_array['finisher_address']      = $row['address'];
            $row_array['finisher_address_2']    = $row['address_2'];
            $row_array['finisher_suburb']       = $row['suburb'];
            $row_array['finisher_state']        = $row['state'];
            $row_array['finisher_postcode']     = $row['postcode'];
            $row_array['finisher_country']      = $row['country'];
            $row_array['finisher_id']           = $row['id'];

            array_push($return_array,$row_array);
        }
        return $return_array;
    }

    private function generateQuery()
    {
        return "
            SELECT
                pf.*,
                GROUP_CONCAT(pc.id,',',IFNULL(pc.name,''),',',IFNULL(pc.email,''),',',IFNULL(pc.phone,''),',',IFNULL(pc.role,'') SEPARATOR '|') AS contacts
            FROM
                {$this->table} pf LEFT JOIN
                {$this->contacts_table} pc ON pf.id = pc.finisher_id
        ";
    }
}
?>