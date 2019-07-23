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

    public function getSelectTeam($selected = false)
    {
        $db = Database::openConnection();

        $check = "";
        $ret_string = "";
        $q = "SELECT id, name FROM {$this->table} WHERE active = 1";
        $q .= " ORDER BY name";
        $teams = $db->queryData($q);
        foreach($teams as $t)
        {
            $label = $t['name'];
            $value = $t['id'];
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function getSelectTeamLeaders($selected = false)
    {
        $db = Database::openConnection();

        $check = "";
        $ret_string = "";
        $q = "SELECT id, name FROM users WHERE active = 1 AND solar_team_leader = 1";
        $q .= " ORDER BY name";
        $teams = $db->queryData($q);
        foreach($teams as $t)
        {
            $label = $t['name'];
            $value = $t['id'];
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function getTeamName($id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' => $id), 'name');
    }

    public function getAllTeams($active = 1)
    {
        $db = Database::openConnection();
        return $db->queryData("SELECT * FROM {$this->table} WHERE active = $active ORDER BY name");
    }

    public function getTeamById($id = 0)
    {
        $db = Database::openConnection();
        return $db->queryById($this->table, $id);
    }

    public function addTeam($data)
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

    public function editTeam($data)
    {
        //echo "<pre>",print_r($data),"</pre>"; die();
        $db = Database::openConnection();
        $vals = array(
            'name'          =>  $data['name'],
            'comments'      =>  null,
        );
        $vals['active'] = isset($data['active'])? 1 : 0;
        if(!empty($data['comments'])) $vals['comments'] = $data['comments'];
        $id = $db->updateDatabaseFields($this->table, $vals, $data['team_id']);
        return $id;
    }
}
?>