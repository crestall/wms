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

    public function getAllSwatches($client_id, $posted = 0, $state = "")
    {
        $db = Database::openConnection();
        $array = array();
        $posted_clause = "";
        if($posted > 0)
        {
            $posted_clause = "WHERE shipped = $posted";
        }
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