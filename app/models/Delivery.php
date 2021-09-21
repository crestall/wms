<?php
 /**
  * Delivery Class
  *

  * @author     Mark Solly <mark.solly@fsg.com.au>

    FUNCTIONS

    addDelivery($data)

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

    public function __construct()
    {
        $this->entered_id   = $this->getStatusId('entered');
        $this->viewed_id    = $this->getStatusId('viewed');
        $this->picked_id    = $this->getStatusId('picked');
        $this->onboard_id   = $this->getStatusId('on board');
        $this->delivered    = $this->getStatusId('delivered');
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
        $q = $this->generateQuery($client_id)."
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
        $q = $this->generateQuery($client_id)."
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
        $q = $this->generateQuery($client_id);
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

    private function generateQuery($client_id = 0)
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