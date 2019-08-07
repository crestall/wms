<?php

 /**
  * Swatch Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>

    FUNCTIONS


  */

class Swatch extends Model{

    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "swatches";

    public function __construct()
    {

    }

    public function addSwatch($data)
    {
        $db = Database::openConnection();

        $values = array(
            'client_id'     => $data['client_id'],
            'name'          => $data['name'],
            'date'          => time(),
            'address'       => $data['address'],
            'suburb'        => $data['suburb'],
            'state'         => $data['state'],
            'postcode'      => $data['postcode']
        );
        if(!empty($data['email']))
            $values['email'] = $data['email'];
        if($data['errors'] > 0)
        {
            $values['errors'] = 1;
            $values['error_string'] = $data['error_string'];
        }
        $request_id = $db->insertQuery($this->table, $values);
        return $request_id;
    }

    public function getAllSwatches($client_id, $posted = 0, $state = "")
    {
        $db = Database::openConnection();
        $array = array();
        $posted_clause = "WHERE shipped = $posted";
        $q = "SELECT * FROM {$this->table} $posted_clause";
        if($client_id > 0)
        {
            $q .= " AND client_id = $client_id";
        }
        if(!empty($state))
        {
            $q .= " AND state = :state";
            $array['state'] = $state;
        }
        $q .= " ORDER BY errors DESC, date ASC";
        //die($q);
        return ($db->queryData($q, $array));
    }

    public function getSwatchDetail($id)
    {
        $db = Database::openConnection();
        $swatch = $db->queryById($this->table, $id);
        return (empty($swatch))? false : $swatch;
    }

    public function updateSwatchAddress($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'          =>  $data['ship_to'],
            'address'		=>	$data['address'],
            'address_2'     =>  null,
            'suburb'		=>	$data['suburb'],
            'state'		    =>	$data['state'],
            'postcode'	    =>	$data['postcode'],
            'country'       =>  $data['country']
        );
        if(isset($data['address2'])) $vals['address_2'] = $data['address2'];
        $db->updatedatabaseFields($this->table, $vals, $data['order_id']);
        return true;
    }

    public function dispatchSwatch($id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'shipped', 1, $swatch_id);
    }

    public function removeError($swatch_id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseFields($this->table, array('errors' => 0, 'error_string' => NULL), $swatch_id);
    }

    public function cancelRequests($ids)
    {
        $db = Database::openConnection();
        foreach($ids as $id)
        {
            $db->deleteQuery('swatches', $id);
        }
    }

}