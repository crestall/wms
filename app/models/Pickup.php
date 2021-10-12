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
    getOpenPickupCount()
    getOpenPickups($client_id = 0)
    getPickupDetails($delivery_id)
    getPickupStatusId($delivery_id)
    markPickupAssigned($pickup_id)
    markPickupComplete($pickup_id)
    markPickupViewed($pickup_id)

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
    public $assigned_id;
    public $complete_id;
    public $status = array();

    public function __construct()
    {
        $this->entered_id   = $this->getStatusId('entered');
        $this->viewed_id    = $this->getStatusId('viewed');
        $this->assigned_id    = $this->getStatusId('assigned');
        $this->complete_id = $this->getStatusId('complete');
        $this->getStatusArray();
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

    public function markPickupAssigned($pickup_id)
    {
        $db = Database::openConnection();
        $cs_id = $this->getPickupStatusId($pickup_id);
        //$cs_id = $db->queryValue($this->table, ['id' => $delivery_id], 'status_id');
        if($this->status[$cs_id]["stage"] < $this->status[$this->onboard_id]["stage"])
            $db->updateDatabaseField($this->table, 'status_id', $this->onboard_id, $pickup_id);
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
        return $db->queryData($q);
    }

    public function getClosedPickups($client_id = 0)
    {
        $db = Database::openConnection();
        $q = $this->generateQuery()."
            WHERE p.status_id = {$this->complete_id}
        ";
        if($client_id > 0)
            $q .= " AND p.client_id = $client_id";
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
                    count(*) as pickup_count, c.client_name, p.client_id
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
                u.name AS pickup_window, u.rank AS importance, u.class AS pickup_window_class,
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