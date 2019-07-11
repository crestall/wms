<?php
 /**
  * Solarservicejob Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>

    FUNCTIONS
    addJob($data, $oitems)
    getCurrentJobs()

  */
  class Solarservicejob extends Model{

    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "solar_service_jobs";
    public $items_table = "solar_service_jobs_items";
    public $order_type_table = "solar_order_types";

    public function __construct()
    {
        parent::__construct();
    }

    public function cancelJobs($ids)
    {
        $db = Database::openConnection();
        foreach($ids as $id)
        {
            $db->deleteQuery($this->table, $id);
            $db->deleteQuery($this->items_table, $id, 'order_id');
        }
    }

    public function getAllServiceJobs($type_id, $fulfilled)
    {
        $db = Database::openConnection();
        $order = new Order();
        $status_id = $order->fulfilled_id;
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
        $q .= " ORDER BY date DESC";
        //die($q);
        return ($db->queryData($q, $array));
    }

    public function getCurrentServiceJobs()
    {
        $db = Database::openConnection();
        $q = "  select
                    count(*) as job_count, ot.name, o.client_id, o.type_id
                from
                    {$this->table} o join {$this->order_type_table} ot on o.type_id = ot.id
                where
                    o.status_id != 4
                group by
                    o.type_id
                order by
                    ot.name";

        return $db->queryData($q);
    }

    public function getItemsForJob($job_id, $picked = -1)
    {
        $db = Database::openConnection();
        $q = "
            SELECT i.*, oi.qty, oi.location_id, oi.item_id, oi.id AS line_id, il.qty AS location_qty
            FROM {$this->items_table} oi JOIN items i ON oi.item_id = i.id LEFT JOIN items_locations il on oi.location_id = il.location_id AND il.item_id = i.id
            WHERE oi.order_id = $job_id
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

    public function addJob($data, $oitems)
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
            'type_id'       => $data['job_type'],
            'client_id'     => $data['client_id'],
            'date_entered'  => time(),
            'job_date'      => $data['date_value'],
            'status_id'     => 1,
            'address'       => $data['address'],
            'suburb'        => $data['suburb'],
            'state'         => $data['state'],
            'postcode'      => $data['postcode'],
            'country'       => $data['country'],
            'entered_by'    => $eb,
            'team_id'       => $data['team_id']
        );
        if(!empty($data['customer_name']))
            $o_values['customer_name'] = $data['customer_name'];
        if(!empty($data['address2']))
            $o_values['address_2'] = $data['address2'];
        $o_values['battery'] = (isset($data['battery']))? 1:0;

        $job_id = $db->insertQuery($this->table, $o_values);
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
                        'job_id'        => $job_id
                    );
                    $db->insertQuery($this->items_table, $vals);

                }
            endforeach;
        }
        //die();
        return $job_id;
    }

    public function getAddressStringForJob($id)
    {
        $db = Database::openConnection();
        $ret_string = "";
        if(!empty($id))
        {
            //$address = $db->queryRow("SELECT * FROM addresses WHERE id = $id");
            $address = $db->queryRow("SELECT address, address_2, suburb, state, postcode, country, customer_name FROM ".$this->table." WHERE id = $id");
            if(!empty($address))
            {
                $ret_string = "<p>";
                if(!empty($address['customer_name'])) $ret_string .= $address['customer_name']."<br/>";
            	$ret_string .= $address['address'];
                if(!empty($address['address_2'])) $ret_string .= "<br/>".$address['address_2'];
                $ret_string .= "<br/>".$address['suburb'];
                $ret_string .= "<br/>".$address['state'];
                $ret_string .= "<br/>".$address['country'];
                $ret_string .= "<br/>".$address['postcode']."</p>";
            }
        }
        return $ret_string;
    }

  }

?>