<?php

/**
    * Solarteam Class
    *

    * @author     Mark Solly <mark.solly@3plplus.com.au>

        FUNCTIONS

        addRep($data)
        editRep($data)
        getAllReps($active = 1)
        getRepById($id = 0)
        getSelectSalesReps($selected = false, $client_id = 0)

    */

class Solarteam extends Model{
    public $table = "solar_teams";

    public function getSelectSalesReps($selected = false, $client_id = 0)
    {
        $db = Database::openConnection();

        $check = "";
        $ret_string = "";
        $q = "SELECT id, name FROM {$this->table} WHERE active = 1";
        if($client_id > 0)
        {
            $q .= " AND client_id = $client_id";
        }
        $q .= " ORDER BY name";
        $reps = $db->queryData($q);
        foreach($reps as $r)
        {
            $label = $r['name'];
            $value = $r['id'];
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function getAllReps($active = 1)
    {
        $db = Database::openConnection();
        return $db->queryData("SELECT * FROM {$this->table} WHERE active = $active ORDER BY name");
    }

    public function getRepById($id = 0)
    {
        $db = Database::openConnection();
        return $db->queryById($this->table, $id);
    }

    public function addRep($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'          =>  $data['name']
        );
        if(!empty($data['comments'])) $vals['comments'] = $data['comments'];
        if(!empty($data['team_leader_id'])) $vals['team_leader_id'] = $data['team_leader_id'];
        $id = $db->insertQuery($this->table, $vals);
        return $id;
    }

    public function editRep($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'client_id'     =>  $data['client_id'],
            'name'          =>  $data['name'],
            'email'         =>  $data['email'],
            'phone'         =>  $data['phone'],
            'tfn'           =>  null,
            'abn'           =>  null,
            'comments'      =>  null,
            'address'       =>  null,
            'address_2'     =>  null,
            'suburb'        =>  null,
            'state'         =>  null,
            'postcode'      =>  null,
            'country'       =>  'AU'
        );
        $vals['active'] = isset($data['active'])? 1 : 0;
        if(!empty($data['tfn'])) $vals['tfn'] = $data['tfn'];
        if(!empty($data['abn'])) $vals['abn'] = $data['abn'];
        if(!empty($data['comments'])) $vals['comments'] = $data['comments'];
        if(!empty($data['address'])) $vals['address'] = $data['address'];
        if(!empty($data['address2'])) $vals['address_2'] = $data['address2'];
        if(!empty($data['suburb'])) $vals['suburb'] = $data['suburb'];
        if(!empty($data['state'])) $vals['state'] = $data['state'];
        if(!empty($data['postcode'])) $vals['postcode'] = $data['postcode'];
        if(!empty($data['country'])) $vals['country'] = $data['country'];
        $id = $db->updateDatabaseFields($this->table, $vals, $data['rep_id']);
        return $id;
    }
}
?>