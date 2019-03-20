<?php
 /**
  * Originorder Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>

    FUNCTIONS
    addOrder($data, $oitems)
    getCurrentOrders()

  */
  class Solarorder extends Order{

    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "solar_orders";

    public function __construct()
    {
        parent::__construct();
    }

    public function getSolarAllOrders($type_id, $fulfilled)
    {
        $db = Database::openConnection();
        $status_id = $this->fulfilled_id;
        $array = array();
        //echo "SELECT * FROM {$this->table} WHERE status_id != $status_id AND client_id NOT IN ({$this->excluded_clients}) ORDER BY date_ordered ASC"; die();
        if($fulfilled > 0)
        {
            $status_clause = "WHERE status_id = $status_id";
        }
        else
        {
            $status_clause = "WHERE status_id != $status_id";
        }
        $q = "SELECT * FROM {$this->table} $status_clause";
        if($type_id > 0)
        {
            $q .= " AND type_id = $type_id";
        }
        $q .= " ORDER BY date_ordered ASC";
        //die($q);
        return ($db->queryData($q, $array));
    }

    public function getCurrentOrders($store_order = 0)
    {
        $db = Database::openConnection();
        $q = "  select
                    count(*) as order_count, ot.name, o.client_id, o.type_id
                from
                    solar_orders o join solar_order_types ot on o.type_id = ot.id
                where
                    o.status_id != 4
                group by
                    o.type_id
                order by
                    ot.name";

        return $db->queryData($q);
    }

    public function getItemsForOrder($order_id, $picked = -1)
    {
        $db = Database::openConnection();
        $q = "
            SELECT i.*, oi.qty, oi.location_id, oi.item_id, oi.id AS line_id, il.qty AS location_qty
            FROM solar_orders_items oi JOIN items i ON oi.item_id = i.id LEFT JOIN items_locations il on oi.location_id = il.location_id AND il.item_id = i.id
            WHERE oi.order_id = $order_id
        ";
        if($picked === 1)
            $q .= " AND oi.picked = 1";
        elseif($picked === 0)
            $q .= " AND oi.picked = 0";
        //$q .= " GROUP BY i.id";
        $q .= " GROUP BY oi.location_id, i.id";
        $q .= " ORDER BY i.name";
        //die($q);
        return $db->queryData($q);
    }

    public function addOrder($data, $oitems)
    {
        //echo "<pre>",print_r($data),"</pre>"; die();
        $db = Database::openConnection();

        if(empty(Session::getUserId()))
        {
            $eb = (isset($data['entered_by']))? $data['entered_by'] : 0;
        }
        else
        {
            $eb = Session::getUserId();
        }
        $o_values = array(
            'work_order'    => $data['work_order'],
            'type_id'       => $data['type_id'],
            'client_id'     => $data['client_id'],
            'date_ordered'  => time(),
            'status_id'     => $this->ordered_id,
            'address'       => $data['address'],
            'suburb'        => $data['suburb'],
            'state'         => $data['state'],
            'postcode'      => $data['postcode'],
            'country'       => $data['country'],
            'entered_by'    => $eb
        );

        $order_id = $db->insertQuery($this->table, $o_values);
        //echo "<pre>",print_r($oitems),"</pre>"; die();
        foreach($oitems as $items)
        {
            if(!isset($items[0]))
                $the_items[] = $items;
            else
                $the_items = $items;
            foreach($the_items as $item):
                $item_id = $item['item_id'];
                foreach($item['locations'] as $il)
                {
                    /* */
                    $vals = array(
                        'item_id'       => $item_id,
                        'location_id'   => $il['location_id'],
                        'qty'           => $il['qty'],
                        'order_id'      => $order_id
                    );
                    $db->insertQuery('solar_orders_items', $vals);

                }
            endforeach;
        }
        //die();
        return $order_id;
    }


  }

?>