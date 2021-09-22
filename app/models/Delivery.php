<?php
 /**
  * Delivery Class
  *

  * @author     Mark Solly <mark.solly@fsg.com.au>

    FUNCTIONS

    PUBLIC FUNCTIONS
    addDelivery($data)
    getAllDeliveries($client_id = 0)
    getClosedDeliveries($client_id = 0)
    getDeliveryStatusId($delivery_id)
    getOpenDeliveries($client_id = 0)
    markDeliveryDelivered($delivery_id)
    markDeliveryPicked($delivery_id)
    markDeliveryViewed($delivery_id)

    PRIVATE FUNCTIONs
    generateQuery()
    getStatusArray()
    getStatusId($status)
  */

class Delivery extends Model{
    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "deliveries";
    public $items_table = "deliveries_items";
    public $status_table = "delivery_status";
    public $urgency_table = "delivery_urgencies";
    public $entered_id;
    public $viewed_id;
    public $picked_id;
    public $onboard_id;
    public $delivered_id;
    public $status = array();

    public function __construct()
    {
        $this->entered_id   = $this->getStatusId('entered');
        $this->viewed_id    = $this->getStatusId('viewed');
        $this->picked_id    = $this->getStatusId('picked');
        $this->onboard_id   = $this->getStatusId('on board');
        $this->delivered_id = $this->getStatusId('delivered');
        $this->getStatusArray();
    }

    public function markDeliveryViewed($delivery_id)
    {
        $db = Database::openConnection();
        $cs_id = $this->getDeliveryStatusId($delivery_id);
        if($this->status[$cs_id]["stage"] < $this->status[$this->viewed_id]["stage"])
        {
            echo "<p>Will mark $delivery_id as viewed</p>";die();
            $db->updateDatabaseField($this->table, 'status_id', $this->viewed_id, $delivery_id);
        }
    }

    public function markDeliveryOnboard($delivery_id)
    {
        $db = Database::openConnection();
        $cs_id = $this->getDeliveryStatusId($delivery_id);
        if($this->status[$cs_id]["stage"] < $this->status[$this->onboard_id]["stage"])
            $db->updateDatabaseField($this->table, 'status_id', $this->onboard_id, $delivery_id);
    }

    public function markDeliveryDelivered($delivery_id)
    {
        $db = Database::openConnection();
        if($db->queryValue($this->table, ['id' => $delivery_id], "date_fulfilled") == 0)
            $db->updateDatabaseFields($this->table, [
                'date_fulfilled'    => time(),
                'status_id'         => $this->delivered_id
            ], $delivery_id);
    }

    public function markDeliveryPicked($delivery_id)
    {
        $db = Database::openConnection();
        $cs_id = $this->getDeliveryStatusId($delivery_id);
        if($this->status[$cs_id]["stage"] < $this->status[$this->picked_id]["stage"])
            $db->updateDatabaseField($this->table, 'status_id', $this->picked_id, $delivery_id);
    }

    public function getDeliveryStatusId($delivery_id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array($id => $delivery_id), "status_id");
    }

    public function addDelivery($data)
    {
        $db = Database::openConnection();
        $d_values = array(
            'client_id'     => $data['client_id'],
            'attention'     => $data['attention'],
            'date_entered'  => time(),
            'address'       => $data['delivery_address'],
            'suburb'        => $data['delivery_suburb'],
            'state'         => strtoupper($data['delivery_state']),
            'postcode'      => $data['delivery_postcode'],
            'urgency_id'    => $data['urgency']
        );
        if(!empty($data['address2'])) $d_values['address_2'] = $data['address2'];
        if(!empty($data['client_reference'])) $d_values['client_reference'] = $data['client_reference'];
        $delivery_id = $db->insertQuery($this->table, $d_values);
        foreach($data['items'] as $item_id => $locations)
        {
            foreach($locations as $location)
            {
                list($location_id, $qty) = explode('_', $location);
                $db->insertQuery($this->items_table,[
                    'deliveries_id'   => $delivery_id,
                    'item_id'       => $item_id,
                    'location_id'   => $location_id,
                    'qty'           => $qty
                ]);
            }
        }
        return $delivery_id;
    }

    public function getOpenDeliveries($client_id = 0)
    {
        $db = Database::openConnection();
        $q = $this->generateQuery()."
            WHERE d.status_id NOT IN ( {$this->onboard_id},{$this->delivered_id})
        ";
        if($client_id > 0)
            $q .= " AND d.client_id = $client_id";
        $q .= "
            GROUP BY
                d.id
            ORDER BY
                importance ASC, d.date_entered DESC
        ";
        return $db->queryData($q);
    }

    public function getClosedDeliveries($client_id = 0)
    {
        $db = Database::openConnection();
        $q = $this->generateQuery()."
            WHERE d.status_id IN ( {$this->onboard_id},{$this->delivered_id})
        ";
        if($client_id > 0)
            $q .= " AND d.client_id = $client_id";
        $q .= "
            GROUP BY
                d.id
            ORDER BY
                d.date_fulfilled DESC
        ";
        return $db->queryData($q);
    }

    public function getAllDeliveries($client_id = 0)
    {
        $db = Database::openConnection();
        $q = $this->generateQuery();
        if($client_id > 0)
            $q .= " WHERE d.client_id = $client_id";
        $q .= "
            GROUP BY
                d.id
            ORDER BY
                d.date_entered DESC
        ";
        return $db->queryData($q);
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

    private function generateQuery()
    {
        $q = "
            SELECT
                d.*,
                s.name AS status, s.stage, s.class AS status_class,
                (SELECT MAX(stage) FROM {$this->status_table}) AS total_stages,
                u.name AS delivery_window, u.rank AS importance, u.class AS delivery_window_class,
                GROUP_CONCAT(
                    i.item_id,'|',
                    items.name,'|',
                    items.sku,'|',
                    i.qty
                    SEPARATOR '~'
                ) AS items
            FROM
                {$this->table} d JOIN
                {$this->status_table} s ON d.status_id = s.id JOIN
                {$this->urgency_table} u ON d.urgency_id = u.id JOIN
                {$this->items_table} i ON i.deliveries_id = d.id JOIN
                items ON items.id = i.item_id
        ";
        return $q;
    }

}//end class
?>