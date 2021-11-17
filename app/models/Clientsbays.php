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

    public function getSpaceUsage($from, $to, $client_id = 0)
    {
        $db = Database::openConnection();
        $excluded_location_ids = [
            2914,   //backorders
            2922    //collection items
        ];
        $q = "
        SELECT
            cb.id AS client_bay_id, cb.date_added, cb.date_removed, cb.oversize,
            dh.dh AS days_held,
            FROM_UNIXTIME(cb.date_added) AS DATE_ADDED,
            FROM_UNIXTIME(cb.date_removed) AS DATE_REMOVED,
            l.location, l.tray,
            c.client_name,
            FROM_UNIXTIME($from) AS DATE_FROM,
            FROM_UNIXTIME($to) AS DATE_TO,
            CAST(ROUND(
            CASE
            	cb.oversize
            WHEN
            	1
            THEN
            	csc.oversize * dh.dh / 7
            ELSE
            	csc.standard * dh.dh / 7
            END,2) AS DECIMAL(10,2)) AS storage_charge
        FROM
            clients_bays cb JOIN
            (
                SELECT
                    CASE
                        clients_bays.date_removed
                    WHEN
                        0
                    THEN
                        DATEDIFF(
                            FROM_UNIXTIME(1635771600),
                            FROM_UNIXTIME(clients_bays.date_added)
                        )
                    ELSE
                        DATEDIFF(
                            FROM_UNIXTIME(clients_bays.date_removed),
                            FROM_UNIXTIME(clients_bays.date_added)
                        )
                    END AS dh,
                    clients_bays.id
                FROM
                    clients_bays
                HAVING
                	dh > 0
            ) dh ON cb.id = dh.id JOIN
            locations l ON l.id = cb.location_id JOIN
            clients c ON cb.client_id = c.id JOIN
            client_storage_charges csc ON cb.client_id = csc.client_id
        WHERE
            c.delivery_client = 0 AND cb.location_id NOT IN(".implode(",",$excluded_location_ids).")";
        if($client_id > 0)
            $q .= " AND cb.client_id = $client_id ";
        $q .= "
        HAVING
            DATE(FROM_UNIXTIME(cb.date_added)) BETWEEN DATE_FROM AND DATE_TO
            AND (cb.date_removed = 0 OR DATE(FROM_UNIXTIME(cb.date_removed)) < DATE_TO)
        ORDER BY
            c.client_name
        ";
        //die($q);
        return $db->queryData($q);
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