<?php
 /**
  * Clientsbays Class
  *

  * @author     Mark Solly <mark.solly@fsg.com.au>


  FUNCTIONS

  getBayUsage($from, $to)
  stockAdded($client_id, $location_id)
  stockRemoved($client_id, $location_id, $product_id)

  */
class Clientsbays extends Model{
    public $table = "clients_bays";

    public function getCurrentBayUsage($client_id)
    {
        $db = Database::openConnection();
        $bays = $db->queryData("
            SELECT * FROM {$this->table} WHERE client_id = $client_id AND date_removed = 0;
        ");

        return $bays;
    }

    public function getBayUsage($from, $to)
    {
        $db = Database::openConnection();
        $daydif = ($to - $from)/60/60/24;

        $friday = strtotime("last saturday", $from);
        if($daydif < 14)
        {
            $gap = "+ 1 day";
            $friday = $from;
        }
        elseif($daydif < 60)
        {
            $gap = "+ 7 days";
        }
        else
        {
            $gap =  "+ 1 month";
        }
        $fridays = array();
        while($friday < $to)
        {
            $fridays[] = array(
                'stamp'     =>  $friday,
                'string'    =>  date("d-M-y", $friday)
            );
            $friday = strtotime($gap, $friday);
        }

        $data = array();
        foreach($fridays as $i => $f)
        {
            $qfrom = isset($fridays[$i - 1])? $fridays[$i - 1]['stamp'] : $from;
            $qto = $f['stamp'];
            $bquery = "SELECT client_id, count(*) AS bays FROM clients_bays WHERE date_added < $qto AND (date_removed > $qto OR date_removed = 0) AND trays = 0 GROUP BY client_id";
            $bquery = "
                SELECT
                    client_id, count(*) AS bays
                FROM
                    clients_bays cb join locations l on cb.location_id = l.id
                WHERE
                    date_added < $qto AND (date_removed > $qto OR date_removed = 0) and tray = 0 AND l.active = 1
                GROUP BY
                    client_id
            ";
            //echo "<p>$bquery</p>";
            $reps = $db->queryData($bquery);
            foreach($reps as $rep)
            {
                $client_name = $db->queryValue('clients', array('id' => $rep['client_id']), 'client_name');
                $data[$client_name][$f['string']] = $rep['bays'];
            }
            $tquery = "
                SELECT
                    client_id, count(*) AS bays
                FROM
                    clients_bays cb join locations l on cb.location_id = l.id
                WHERE
                    date_added < $qto AND (date_removed > $qto OR date_removed = 0) and tray = 1 AND l.active = 1
                GROUP BY
                    client_id
            ";
            $treps = $db->queryData($tquery);
            foreach($treps as $trep)
            {
                $client_name = $db->queryValue('clients', array('id' => $trep['client_id']), 'client_name');
                if(isset($data[$client_name][$f['string']] ))
                    $data[$client_name][$f['string']] += $trep['bays']/9;
                else
                    $data[$client_name][$f['string']] = $trep['bays']/9;
            }
            $lquery = "SELECT client_id, count(*) AS bays FROM clients_locations WHERE date_added < $qto AND (date_removed > $qto OR date_removed = 0) GROUP BY client_id";
            //echo "<p>$lquery</p>";
            $lreps = $db->queryData($lquery);
            foreach($lreps as $lrep)
            {
                $client_name = $db->queryValue('clients', array('id' => $lrep['client_id']), 'client_name');
                if(isset($data[$client_name][$f['string']] ))
                    $data[$client_name][$f['string']] += $lrep['bays'];
                else
                    $data[$client_name][$f['string']] = $lrep['bays'];
            }
        }
        ksort($data);
        return array(
            'data'      => $data,
            'fridays'   => $fridays
        );
    }

    public function stockAdded($client_id, $location_id, $to_receiving = 0, $pallet_multiplier = 1, $is_oversize = false)
    {
        $db = Database::openConnection();
        $oversize = ($is_oversize)? 1 : 0;
        $not_oversize = ($is_oversize)? 0 : 1;
        //die('oversize '.$oversize);
        if($to_receiving)
        {
            $location = new Location();
            $location_id = $location->receiving_id;

            if($updater = $db->queryValue($this->table, array('client_id' => $client_id, 'location_id' => $location_id, 'date_removed'  =>  0)))
            {
                $db->query("UPDATE {$this->table} SET pallet_multiplier = pallet_multiplier + $pallet_multiplier WHERE id = $updater");
            }
            else
            {
                $db->insertQuery($this->table, array(
                    'client_id'         =>  $client_id,
                    'location_id'       =>  $location_id,
                    'date_added'        =>  time(),
                    'pallet_multiplier' =>  $pallet_multiplier
                ));
            }
        }
        else
        {
            $row = $db->queryRow("
                SELECT * FROM {$this->table} WHERE client_id = :client_id AND location_id = :location_id AND date_removed = 0
            ",
            array(
                'client_id'     => $client_id,
                'location_id'   => $location_id
            ));
            //echo "<pre>The row",print_r($row),"</pre>";die();
            //die("row count".count($row));
            if(isset($row['id']) && $row['oversize'] == $not_oversize)
            {
                $db->updateDatabaseField($this->table, 'date_removed', time(), $row['id']);
                $array = array(
                    'client_id'     =>  $client_id,
                    'location_id'   =>  $location_id,
                    'date_added'    =>  time(),
                    'oversize'      =>  $oversize
                );
                //echo "<pre>The row",print_r($array),"</pre>";die();
                $db->insertQuery($this->table, $array);
            }
            elseif( !isset($row['id']) )
            {
                $array = array(
                    'client_id'     =>  $client_id,
                    'location_id'   =>  $location_id,
                    'date_added'    =>  time(),
                    'oversize'      =>  $oversize
                );
                //echo "<pre>The row",print_r($array),"</pre>";die();
                $db->insertQuery($this->table, $array);
            }
        }
        return true;
    }

    public function stockRemoved($client_id, $location_id, $product_id, $remove_oversize = false)
    {
        $db = Database::openConnection();
        $location = new Location();
        if($location_id == $location->receiving_id)
        {
            $this_row = $db->queryRow("SELECT * FROM {$this->table} WHERE date_removed = 0 AND client_id = $client_id AND location_id = $location_id ");
            $pallet_multiplier = $this_row['pallet_multiplier'] - 1;
            if($pallet_multiplier < 1)
                $pallet_multiplier = 1;

            $db->updateDatabaseField($this->table, 'date_removed', time(), $this_row['id']);

            if($db->queryValue('items_locations', array(
                'item_id'       =>  $product_id,
                'location_id'   =>  $location_id
            )))
            {
                $db->insertQuery($this->table, array(
                    'client_id'         =>  $client_id,
                    'location_id'       =>  $location_id,
                    'date_added'        =>  time(),
                    'pallet_multiplier' =>  $pallet_multiplier
                ));
            }
        }
        else
        {
            /*
            if(!$db->queryValue('items_locations', array(
                'item_id'       =>  $product_id,
                'location_id'   =>  $location_id
            )))
            {
                $db->query("
                    UPDATE {$this->table}
                    SET date_removed = ".time()."
                    WHERE date_removed = 0 AND client_id = $client_id AND location_id = $location_id
                ");
            }
            */
            $locations = $db->queryData("
                SELECT il.* FROM items_locations il JOIN items i ON il.item_id = i.id WHERE i.client_id = $client_id AND il.location_id = $location_id
            ");
            //echo "<pre>The row",print_r($locations),"</pre>";die();
            if(count($locations))
            {
                //do oversize update
                $row = $db->queryRow("
                    SELECT * FROM {$this->table} WHERE client_id = :client_id AND location_id = :location_id AND date_removed = 0
                ",
                array(
                    'client_id'     => $client_id,
                    'location_id'   => $location_id
                ));
                if(isset($row['oversize']) && $row['oversize'] == 1 && $remove_oversize)
                {
                    $db->updateDatabaseField($this->table, 'date_removed', time(), $row['id']);
                    $array = array(
                        'client_id'     =>  $client_id,
                        'location_id'   =>  $location_id,
                        'date_added'    =>  time(),
                        'oversize'      =>  0
                    );
                    //echo "<pre>The row",print_r($array),"</pre>";die();
                    $db->insertQuery($this->table, $array);
                }
            }
            else
            {
                //remove allocation
                $db->query("
                    UPDATE {$this->table}
                    SET date_removed = ".time()."
                    WHERE date_removed = 0 AND client_id = $client_id AND location_id = $location_id
                ");
            }
        }

        return true;
    }

}
?>