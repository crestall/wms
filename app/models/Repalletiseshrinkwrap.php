<?php

 /**
  * Repalletiseshrinkwrap Class
  *

  * @author     Mark Solly <mark.solly@fsg.com.au>


  FUNCTIONS

  addData($data)
  getDetails()

  */

class Repalletiseshrinkwrap extends Model{

    public $table = "repalletise_shrinkwrap";

    public function addData($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'client_id'     =>  $data['client_id'],
            'date'          =>  $data['date_value'],
            'entered_by'    =>  Session::getUserId()
        );
        if(!empty($data['repalletise_count'])) $vals['repalletise_count'] = $data['repalletise_count'];
        if(!empty($data['shrinkwrap_count'])) $vals['shrinkwrap_count'] = $data['shrinkwrap_count'];
        if(!empty($data['notes'])) $vals['notes'] = $data['notes'];
        $db->insertQuery($this->table, $vals);
        return true;
    }

    public function getDetails($client_id = 0, $from = 0, $to = false, $entered_by = false)
    {
        $db = Database::openConnection();
        $q = "SELECT * FROM {$this->table} WHERE date > $from";
        if($client_id > 0)
            $q .= " AND client_id = $client_id";
        if($to !== false)
            $q .= " AND date < $to";
        if($entered_by !== false)
            $q .= " AND entered_by = $entered_by";

        $q .= " ORDER BY date DESC";
        return $db->queryData($q);
    }
}

?>