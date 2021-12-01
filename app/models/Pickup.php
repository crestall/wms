<?php
 /**
  * Pickup Class
  *

  * @author     Mark Solly <mark.solly@fsg.com.au>

    FUNCTIONS

    PUBLIC FUNCTIONS
    addPickup($data)
    getAllPickups($client_id = 0)
    getClosedPickups($client_id = 0)
    getSelectStatus($selected = false)
    getOpenPickupCount()
    getOpenPickups($client_id = 0)
    getPickupDetails($delivery_id)
    getPickupStatusId($delivery_id)
    getSearchResults($args)
    markPickupVehicleAssigned($pickup_id)
    markPickupComplete($pickup_id)
    markPickupViewed($pickup_id)
    updateFieldValue($field, $value, $id)

    PRIVATE FUNCTIONs
    generateQuery()
    getStatusArray()
    getStatusId($status)
  */

class Pickup extends Model{
    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "pickups";
    public $items_table = "pickups_items";
    public $status_table = "pickup_status";
    public $urgency_table = "delivery_urgencies";
    public $entered_id;
    public $viewed_id;
    public $vehicleassigned_id;
    public $complete_id;
    public $status = array();

    public function __construct()
    {
        $this->entered_id   = $this->getStatusId('entered');
        $this->viewed_id    = $this->getStatusId('viewed');
        $this->vehicleassigned_id    = $this->getStatusId('vehicle assigned');
        $this->complete_id = $this->getStatusId('complete');
        $this->getStatusArray();
    }

    public function getWeeklyPickupCountsForChart($client_id = 0)
    {
        $db = Database::openConnection();
        $db->query("
            CREATE TEMPORARY TABLE year_week (id int Primary Key);
        ");
        $db->query(Utility::insertYearWeekQuery());
        $pickups = $db->queryData("
            SELECT
                a.MONDAY,
                a.TOTAL_PICKUPS,
                ROUND(AVG(b.total_pickups), 1) AS pickup_average
            FROM
            (
                SELECT
                	count(p.date_entered) AS TOTAL_PICKUPS,
                    STR_TO_DATE(  CONCAT(yw.id,' Monday'), '%X%V %W') AS MONDAY,
                    YEARWEEK(timestamp(current_date) - INTERVAL 3 MONTH) AS START_WEEK,
                    YEARWEEK(timestamp(current_date) + INTERVAL 1 DAY) AS END_WEEK,
                    yw.id AS year_week
                FROM
                    `year_week` yw LEFT JOIN
                    pickups p ON YEARWEEK(FROM_UNIXTIME(p.date_entered)) = yw.id AND p.client_id = $client_id
                GROUP BY
                    yw.id
                HAVING
                    year_week >= START_WEEK AND year_week <= END_WEEK
                ORDER BY
                    MONDAY ASC
            )a JOIN
            (
                SELECT
                    COUNT(p.date_entered) AS total_pickups,
                    YEARWEEK(timestamp(current_date) - INTERVAL 6 MONTH) AS START_WEEK,
                    YEARWEEK(timestamp(current_date) + INTERVAL 1 DAY) AS END_WEEK,
                    yw.id AS year_week
                FROM
                    `year_week` yw LEFT JOIN
                    pickups p ON YEARWEEK(FROM_UNIXTIME(p.date_entered)) = yw.id AND p.client_id = $client_id
                GROUP BY
                    yw.id
                HAVING
                    year_week >= START_WEEK AND year_week <= END_WEEK
            )b ON b.year_week BETWEEN YEARWEEK(STR_TO_DATE(  CONCAT(a.year_week,' Monday'), '%X%V %W') - INTERVAL 3 MONTH) AND  a.year_week
            GROUP BY
                a.year_week
            ORDER BY
                MONDAY ASC
        ");
        //echo "<pre>",print_r($deliveries),"</pre>";die();
        $return_array = array(
            array(
                'Week Beginning',
                'Total Pickups Per Week',
                '3 Month Weekly Average'
            )
        );
        foreach($pickups as $p)
        {
            $row_array = array();
            $row_array[0] = $p['MONDAY'];
            $row_array[1] = (int)$p['TOTAL_PICKUPS'];
            $row_array[2] = (float)$p['pickup_average'];
            $return_array[] = $row_array;
        }
        return $return_array;
    }

    public function getSearchResults($args)
    {
        extract($args);
        $db = Database::openConnection();
        $q = $this->generateQuery();
        $q .= "
            WHERE
        ";
        $array = array();
        $date_to_value = ($date_to_value == 0)? time(): $date_to_value;
        $q .= "(p.date_entered < :to)";
        $array['to'] = $date_to_value;
        if($date_from_value > 0)
        {
            $q .= " AND (p.date_entered > :from)";
            $array['from'] = $date_from_value;
        }
        if($client_id > 0)
        {
            $q .= " AND (p.client_id = :client_id)";
            $array['client_id'] = $client_id;
        }
        if($status_id > 0)
        {
            $q .= " AND (p.status_id = :status_id)";
            $array['status_id'] = $status_id;
        }
        if($urgency_id > 0)
        {
            $q .= " AND (p.urgency_id = :urgency_id)";
            $array['urgency_id'] = $urgency_id;
        }
        $q .= "
            GROUP BY
                p.id
        ";
        if(!empty($term))
        {
            $q .= " HAVING (
                pickup_number LIKE :term1 OR
                client_reference LIKE :term2 OR
                requested_by_name LIKE :term3 OR
                address LIKE :term4 OR
                address_2 LIKE :term5 OR
                suburb LIKE :term6 OR
                postcode LIKE :term7 OR
                vehicle_type LIKE :term8 OR
                pickup_window LIKE :term9 OR
                items LIKE :term10
            )";
            for($i = 1; $i <= 10; ++$i)
            {
                $array['term'.$i] = "%".$term."%";
            }
        }
        $q .="
            ORDER BY
                importance ASC, p.date_entered DESC
        ";
        return $db->queryData($q, $array);
    }

    public function markPickupViewed($pickup_id)
    {
        $db = Database::openConnection();
        //echo "<pre>",print_r($this->status),"</pre>";
        $cs_id = $this->getPickupStatusId($pickup_id);
        //$cs_id = $db->queryValue($this->table, ['id' => $pickup_id], 'status_id');
        if($this->status[$cs_id]["stage"] < $this->status[$this->viewed_id]["stage"])
        {
            $db->updateDatabaseField($this->table, 'status_id', $this->viewed_id, $pickup_id);
        }
    }

    public function markPickupVehicleAssigned($pickup_id)
    {
        $db = Database::openConnection();
        $cs_id = $this->getPickupStatusId($pickup_id);
        //$cs_id = $db->queryValue($this->table, ['id' => $delivery_id], 'status_id');
        if($this->status[$cs_id]["stage"] < $this->status[$this->vehicleassigned_id]["stage"])
            $db->updateDatabaseField($this->table, 'status_id', $this->vehicleassigned_id, $pickup_id);
    }

    public function markPickupComplete($pickup_id)
    {
        $db = Database::openConnection();
        if($db->queryValue($this->table, ['id' => $pickup_id], "date_completed") == 0)
            $db->updateDatabaseFields($this->table, [
                'date_completed'    => time(),
                'status_id'         => $this->complete_id
            ], $pickup_id);
    }

    public function getPickupStatusId($pickup_id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' => $pickup_id), "status_id");
    }

    public function getPickupCharge($pickup_id)
    {
        $db = Database::openConnection();
        $pickup = $this->getPickupDetails($pickup_id);
        return $db->queryValue("client_delivery_charges", ['client_id' => $pickup['client_id'], 'vehicle_type' => $pickup['vehicle_type']],$pickup['charge_level'].'_charge');
    }

    public function addPickup($data)
    {
        $db = Database::openConnection();
        $p_values = array(
            'client_id'     => $data['client_id'],
            'requested_by'  => $data['requested_by'],
            'date_entered'  => time(),
            'address'       => $data['pickup_address'],
            'suburb'        => $data['pickup_suburb'],
            'state'         => strtoupper($data['pickup_state']),
            'postcode'      => $data['pickup_postcode'],
            'urgency_id'    => $data['urgency']
        );
        if(!empty($data['pickup_address2'])) $p_values['address_2'] = $data['pickup_address2'];
        if(!empty($data['client_reference'])) $p_values['client_reference'] = $data['client_reference'];
        $pickup_id = $db->insertQuery($this->table, $p_values);
        foreach($data['pickup_items'] as $item_id => $pallet_count)
        {
            $db->insertQuery($this->items_table,[
                'pickups_id'    => $pickup_id,
                'item_id'       => $item_id,
                'pallets'       => $pallet_count
            ]);
        }
        return $pickup_id;
    }

    public function getOpenPickups($client_id = 0)
    {
        $db = Database::openConnection();
        $q = $this->generateQuery()."
            WHERE p.status_id != {$this->complete_id}
        ";
        if($client_id > 0)
            $q .= " AND p.client_id = $client_id";
        $q .= "
            GROUP BY
                p.id
            ORDER BY
                importance ASC, p.date_entered DESC
        ";
        //die ($q);
        return $db->queryData($q);
    }

    public function getClosedPickups($client_id = 0, $from = 0, $to = 0)
    {
        $db = Database::openConnection();
        $q = $this->generateQuery()."
            WHERE p.status_id = {$this->complete_id}
        ";
        if($client_id > 0)
            $q .= " AND p.client_id = $client_id";
        if($from > 0)
            $q .= " AND p.date_completed >= $from";
        if($to > 0)
            $q .= " AND p.date_completed <= $to";
        $q .= "
            GROUP BY
                p.id
            ORDER BY
                p.date_completed DESC
        ";
        return $db->queryData($q);
    }

    public function getAllPickups($client_id = 0)
    {
        $db = Database::openConnection();
        $q = $this->generateQuery();
        if($client_id > 0)
            $q .= " WHERE p.client_id = $client_id";
        $q .= "
            GROUP BY
                p.id
            ORDER BY
                p.date_entered DESC
        ";
        return $db->queryData($q);
    }

    public function getSelectStatus($selected = false)
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $q = "SELECT id, name FROM {$this->status_table} ORDER BY stage";
        $status = $db->queryData($q);
        foreach($status as $s)
        {
            $label = ucwords($s['name']);
            $value = $s['id'];
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function getPickupDetails($delivery_id)
    {
        $db = Database::openConnection();
        $q = $this->generateQuery()."
            WHERE p.id = $delivery_id
            GROUP BY p.id
        ";
        //die($q);
        return $db->queryRow($q);
    }

    private function getStatusId($status)
    {
        $db = Database::openConnection();
        return ($db->queryValue($this->status_table, array('name' => $status)));
    }

    private function getStatusArray()
    {
        $db = Database::openConnection();
        $statusses = $db->queryData("SELECT id, name, stage FROM {$this->status_table} ORDER BY stage");
        foreach($statusses as $status)
        {
            $this->status[$status['id']] = array(
                'id'    => $status['name'],
                'stage' => $status['stage']
            );
        }
    }

    public function getOpenPickupCount()
    {
        $db = Database::openConnection();
        $q = "  select
                    count(*) as pickup_count, c.client_name, p.client_id, c.logo
                from
                    pickups p JOIN clients c on p.client_id = c.id
                where
                    p.status_id != {$this->complete_id} and c.active = 1
                group by
                    p.client_id
                order by
                    c.client_name";

        return $db->queryData($q);
    }

    public function updateFieldValue($field, $value, $id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, $field, $value, $id);
    }

    private function generateQuery()
    {
        $q = "
            SELECT
                p.*, (23589 + p.id) AS pickup_number,
                users.name AS requested_by_name,
                c.client_name,
                s.name AS status, s.stage, s.class AS status_class,
                (SELECT MAX(stage) FROM {$this->status_table}) AS total_stages,
                u.name AS pickup_window, u.rank AS importance, u.class AS pickup_window_class, u.charge_level,
                GROUP_CONCAT(
                    i.item_id,'|',
                    items.name,'|',
                    items.sku,'|',
                    i.pallets
                    SEPARATOR '~'
                ) AS items
            FROM
                {$this->table} p JOIN
                clients c ON p.client_id = c.id JOIN
                {$this->status_table} s ON p.status_id = s.id JOIN
                {$this->urgency_table} u ON p.urgency_id = u.id JOIN
                {$this->items_table} i ON i.pickups_id = p.id JOIN
                users ON users.id = p.requested_by JOIN
                items ON items.id = i.item_id
        ";
        return $q;
    }

}//end class
?>