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
        $q .= " ORDER BY date ASC";
        //die($q);
        return ($db->queryData($q, $array));
    }

}