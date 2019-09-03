<?php

 /**
  * Order Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>

    FUNCTIONS

    addOrder($data, $oitems)
    addPackage($data)
    cancelOrders($ids)
    countItemForOrder($item_id, $order_id)
    courierAssigned($order_id)
    deletePackage($id)
    getAllOrders($client_id, $courier_id = -1, $fulfulled = 0, $store_order = -1)
    getAllOrdersByStatus($status_id)
    getClientActivity($from, $to, $clients = "")
    getCurrentOrders()
    getCurrentStoreOrders()
    getDispatchedOrders($from, $to, $client_id)
    getDispatchedOrdersArray($from, $to, $client_id)
    getEparcelSummaries($all = false)
    getHomePageOrders($client_id, $courier_id)
    getItemCountInOrder($order_id, $item_id)
    getItemCountForOrder($order_id)
    getItemsForOrder($order_id, $picked = -1)
    getItemsForOrderByConId($con_id, $client_id = false)
    getOrderByBarcode($barcode, $order_number)
    getOrderByConId($con_id)
    getOrderByOrderNumber($order_number)
    getOrderCountForSummary($summary_id)
    getOrderDetail($id)
    getOrderDispatchByConId($con_id)
    getOrdersForClient($client_id, $from, $to)
    getOrderTrends($from, $to, $client_id)
    getPackagesForOrder($id)
    getPickErrors($from, $to, $client_id = 0)
    getReturnedOrders($from, $to, $client_id)
    getReturnedOrdersArray($from, $to, $client_id)
    getSearchResults($args)
    getStatusId($status)
    getStatusName($status_id)
    getStatusses()
    getTopProducts($from, $to, $client_id)
    getUnfulfilledOrders($client_id, $courier_id, $store_order = -1)
    hasAssociatedPackage($id)
    recordDispatch($data, $recording)
    removeCourier($order_id)
    removeError($order_id)
    setSlipPrinted($id)
    updateItemsForOrder(array $order_items, $order_id)
    updateOrderAddress($data)
    updateOrderValue($field, $value, $id)
    updateOrderValues($values, $id)
    updateStatus($status_id, $id)

  */

class Order extends Model{

    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "orders";
    public $ordered_id;
    public $picked_id;
    public $packed_id;
    public $fulfilled_id;
    public $status = array();

    private $excluded_clients = "6, 14";
    private $vic_metro_postcodes = array(
        'min'   => 3000,
        'max'   => 3207
    );

    public function __construct()
    {
        $this->ordered_id   = $this->getStatusId('ordered');
        $this->picked_id    = $this->getStatusId('picked');
        $this->packed_id    = $this->getStatusId('packed');
        $this->fulfilled_id = $this->getStatusId('fulfilled');
        $this->getStatusses();
    }

    public function removeCourier($order_id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseFields($this->table, array(
            'courier_id'            => 0,
            'eparcel_shipment_id'   => NULL,
            'consignment_id'        => NULL,
            'total_cost'            => 0
        ), $order_id);
    }

    public function hasAssociatedPackage($id)
    {
        $db = Database::openConnection();
        return $db->queryValue('orders_packages', array('order_id' => $id));
    }

    public function isVicMetro($id)
    {
        $od = $this->getOrderDetail($id);
        return ($od['postcode'] >= $this->vic_metro_postcodes['min'] && $od['postcode'] <= $this->vic_metro_postcodes['max']);
    }

    public function getAddressStringForOrder($id)
    {
        $db = Database::openConnection();
        $ret_string = "";
        if(!empty($id))
        {
            //$address = $db->queryRow("SELECT * FROM addresses WHERE id = $id");
            $address = $db->queryRow("SELECT address, address_2, suburb, state, postcode, country FROM ".$this->table." WHERE id = $id");
            if(!empty($address))
            {
            	$ret_string = "<p>".$address['address'];
                if(!empty($address['address_2'])) $ret_string .= "<br/>".$address['address_2'];
                $ret_string .= "<br/>".$address['suburb'];
                $ret_string .= "<br/>".$address['state'];
                $ret_string .= "<br/>".$address['country'];
                $ret_string .= "<br/>".$address['postcode']."</p>";
            }
        }
        return $ret_string;
    }

    public function getItemCountInOrder($order_id, $item_id)
    {
        $db = Database::openConnection();
        $check = $db->queryRow("
           SELECT SUM(qty) AS count FROM orders_items WHERE order_id = $order_id AND item_id = $item_id
        ");

        return (empty($check['count']))? 0 : $check['count'];
    }

    public function getItemsForOrderByConId($con_id, $client_id = false)
    {
        $db = Database::openConnection();
        $q = "
            SELECT
                oi.*, i.name
            FROM
                orders_items oi JOIN items i ON oi.item_id = i.id
            WHERE
                oi.order_id = (SELECT id FROM orders WHERE consignment_id = :con_id
        ";
        if($client_id)
        {
            $q .= " AND client_id = $client_id";
        }
        $q .= ")";
        return $db->queryData($q, array('con_id' => $con_id));
    }

    public function updateOrderValue($field, $value, $id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, $field, $value, $id);
        return true;
    }

    public function courierAssigned($order_id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' => $order_id), "courier_id") > 0;
    }

    public function addOrder($data, $oitems)
    {
        $db = Database::openConnection();
        $order_number = $this->getOrderNumber();
        $ref_1 = $db->queryValue('clients', array('id' => $data['client_id']), 'ref_1');
        if(isset($data['signature_req']))
        {
            $instructions = (!empty($data['delivery_instructions']))? $data['delivery_instructions']: "";
        }
        else
        {
            $instructions = (!empty($data['delivery_instructions']))? $data['delivery_instructions']: "Leave in a safe place out of the weather";
        }
        if(empty(Session::getUserId()))
        {
            $eb = (isset($data['entered_by']))? $data['entered_by'] : 0;
        }
        else
        {
            $eb = Session::getUserId();
        }
        $o_values = array(
            'order_number'  => $order_number,
            'client_id'     => $data['client_id'],
            'ship_to'       => $data['deliver_to'],
            'date_ordered'  => time(),
            'status_id'     => $this->ordered_id,
            'ref_1'         => $ref_1,
            'instructions'  => $instructions,
            'address'       => $data['address'],
            'suburb'        => $data['suburb'],
            'state'         => $data['state'],
            'postcode'      => $data['postcode'],
            'country'       => $data['country'],
            'entered_by'    => $eb
        );
        if(!empty($data['client_order_id']))
            $o_values['client_order_id'] = $data['client_order_id'];
        if(!empty($data['company_name']))
            $o_values['company_name'] = $data['company_name'];
        if(!empty($data['tracking_email']))
            $o_values['tracking_email'] = $data['tracking_email'];
        if(isset($data['express_post']))
            $o_values['eparcel_express'] = 1;
        if(isset($data['satchels']))
            $o_values['satchels'] = $data['satchels'];
        if(isset($data['date_ordered']))
            $o_values['date_ordered'] = $data['date_ordered'];
        if(isset($data['weight']))
            $o_values['weight'] = $data['weight'];
        if(isset($data['signature_req']))
            $o_values['signature_req'] = 1;
        if(!empty($data['customer_order_id']))
            $o_values['customer_order_id'] = $data['customer_order_id'];
        if(!empty($data['3pl_comments']))
            $o_values['3pl_comments'] = $data['3pl_comments'];
        if(!empty($data['address2']))
            $o_values['address_2'] = $data['address2'];
        if(!empty($data['uploaded_file']))
            $o_values['uploaded_file'] = $data['uploaded_file'];
        if(!empty($data['contact_phone']))
            $o_values['contact_phone'] = $data['contact_phone'];
        if(!empty($data['errors']))
            $o_values['errors'] = $data['errors'];
        if(!empty($data['error_string']))
            $o_values['error_string'] = $data['error_string'];
        if(isset($data['b2b']))
            $o_values['store_order'] = 1;
        $order_id = $db->insertQuery('orders', $o_values);
        //echo "<pre>",print_r($oitems),"</pre>"; die();
        foreach($oitems as $items)
        {
            //$items = (array)$items;
            //echo "<pre>",print_r($items),"</pre>"; //die();
            if(!isset($items[0]))
                $the_items[] = $items;
            else
                $the_items = $items;
            //echo "<pre>",print_r($the_items),"</pre>"; //die();
            foreach($the_items as $item):
                //echo "The Item<pre>",print_r($item),"</pre>"; //die();
                $item_id = $item['item_id'];
                //echo "<pre>",print_r($item),"</pre>";
                foreach($item['locations'] as $il)
                {
                    /* */
                    $vals = array(
                        'item_id'       => $item_id,
                        'location_id'   => $il['location_id'],
                        'qty'           => $il['qty'],
                        'order_id'      => $order_id
                    );
                    $db->insertQuery('orders_items', $vals);

                }
                if(!empty($item['order_error_string']))
                {
                    $db->query("UPDATE orders SET pick_notices = IFNULL(CONCAT(pick_notices, '{$item['order_error_string']}'), '{$item['order_error_string']}') WHERE id = $order_id");
                    //echo "UPDATE orders SET 3pl_comments = IFNULL(CONCAT(3pl_comments, '{$item['order_error_string']}'), '{$item['order_error_string']}') WHERE id = $order_id"; die();
                }
            endforeach;
        }
        //die();
        return $order_number;
    }

    public function getDispatchedOrders($from, $to, $client_id)
    {
        $db = Database::openConnection();
        $query = "
        SELECT
            o.*, od.scanned_by, od.date, od.parcels_scanned, op.packed_by, op.date AS packed_date
        FROM
            orders o LEFT JOIN order_dispatch od ON o.consignment_id = od.consignment_id LEFT JOIN order_packing op ON op.order_id = o.id
        WHERE
            client_id = :client_id AND status_id = :status_id AND date_fulfilled >= :from AND date_fulfilled <= :to ORDER BY date_fulfilled DESC";
        $array = array(
            'client_id' => 	$client_id,
            'status_id' =>  $this->fulfilled_id,
            'to'        =>  $to,
            'from'      =>  $from
        );
        return $db->queryData($query, $array);
    }

    public function getDispatchedOrdersArray($from, $to, $client_id)
    {
        $db = Database::openConnection();
        $orders = $this->getDispatchedOrders($from, $to, $client_id);
        $return = array();
        foreach($orders as $co)
        {
            $ad = array(
                'address'   =>  $co['address'],
                'address_2' =>  $co['address_2'],
                'suburb'    =>  $co['suburb'],
                'state'     =>  $co['state'],
                'postcode'  =>  $co['postcode'],
                'country'   =>  $co['country']
            );

            $packages = $this->getPackagesForOrder($co['id']);


            $address = Utility::formatAddressWeb($ad);
            $shipped_to = "";
            if(!empty($co['company_name'])) $shipped_to .= $co['company_name']."<br/>";
            if(!empty($co['ship_to'])) $shipped_to .= $co['ship_to']."<br/>";
            $shipped_to .= $address;
            $products = $this->getItemsCountForOrder($co['id']);
            $order_items = $this->getItemsForOrder($co['id']);
            //$num_items = count($products);
            //$parcels = Packaging::getPackingForOrder($co,$order_items,$packages);
            $parcels = array();
            $eb = $db->queryValue('users', array('id' => $co['entered_by']), 'name');
            if(empty($eb))
            {
                $eb = "Automatically Imported";
            }
            $num_items = 0;
            $items = "";
            $csv_items = array();
            foreach($products as $p)
            {
                $items .= $p['name']." (".$p['qty']."),<br/>";
                $num_items += $p['qty'];
                $pallet = ($p['palletized'] && $p['qty'] == $p['per_pallet'])? 1 : "";
                $csv_items[] = array(
                    'name'      =>  $p['name'],
                    'qty'       =>  $p['qty'],
                    'pallet'    =>  $pallet
                );
            }
            $items = rtrim($items, ",<br/>");
            $courier = $db->queryValue('couriers', array('id' => $co['courier_id']), 'name');
            if($courier == "Local")
            {
                $courier = $co['courier_name'];
            }
            $charge = "$".number_format($co['total_cost'], 2);
            if( $client_id == 6 )
            {
                //big bottle
                if($co['date_fulfilled'] < 1523232000) //9th April 2018
                {
                    if( strpos(strtolower($co['3pl_comments']), 'replacement cap') !== false ||  $co['store_order'])
                        $charge = "$".number_format($co['total_cost'], 2);
                    else
                        $charge = "$".number_format( Utility::getBBCharge( $co['country'], $co['state'], $num_items, $co['eparcel_express'] == 1 ), 2 );
                }
            }
            $dd = $pb = "";
            $shrink_wrap = (empty($co['shrink_wrap']))? 0 : 1;
            $bubble_wrap = (empty($co['bubble_wrap']))? 0 : 1;
            $has_shrink_wrap = (empty($co['shrink_wrap']))? "No" : "Yes";
            $has_bubble_wrap = (empty($co['bubble_wrap']))? "No" : "Yes";
            $pallets = (empty($co['pallets']))? 0 : $co['pallets'];
            if(!empty($co['scanned_by'])):
                $by = $db->queryValue('users', array('id' => $co['scanned_by']), 'name');
                $dd = $co['parcels_scanned']." packages out of ".$co['labels']." scanned out of warehouse by<br/><strong>".$by."</strong><br/>at <strong>".date('h:iA', $co['date'])."</strong><br/>on <strong>".date('d/m/Y', $co['date'])."</strong>";
            endif;
            if(!empty($co['packed_by'])):
                $by = $db->queryValue('users', array('id' => $co['packed_by']), 'name');
                $pb = "Packed by <strong>".$by."</strong> on ".date('d/m/Y', $co['packed_date']);
            endif;
            $row = array(
                'date_ordered'          => date('d-m-Y', $co['date_ordered']),
                'entered_by'            => $eb,
                'date_fulfilled'        => date('d-m-Y', $co['date_fulfilled']),
                'order_number'          => $co['order_number'],
                'client_order_number'   => $co['client_order_id'],
                'shipped_to'            => $shipped_to,
                'country'               => $co['country'],
                'items'                 => $items,
                'total_items'           => $num_items,
                'courier'               => $courier,
                'charge'                => $charge,
                'consignment_id'        => $co['consignment_id'],
                'bubble_wrap'           => $bubble_wrap,
                'shrink_wrap'           => $shrink_wrap,
                'has_bubble_wrap'       => $has_bubble_wrap,
                'has_shrink_wrap'       => $has_shrink_wrap,
                'charge_code'           => $co['charge_code'],
                'pallets'               => $co['pallets'],
                'comments'              => $co['3pl_comments'],
                'id'                    => $co['id'],
                'packed_by'             => $pb,
                'dispatched_by'         => $dd,
                'store_order'           => $co['store_order'],
                'csv_items'             => $csv_items,
                'cartons'               => max(count($packages), $co['labels']),
                'parcels'               => $parcels,
                'weight'                => $co['weight']
            );
            $return[] = $row;
        }
        return $return;
    }

    public function getSearchResults($args)
    {
        extract($args);
        $db = Database::openConnection();

        $query = "
            SELECT o.*, eo.manifest_id
            FROM orders o LEFT JOIN eparcel_orders eo ON eo.id = o.eparcel_order_id
            WHERE
                (o.consignment_id LIKE :term1 OR o.ship_to LIKE :term2 OR o.address LIKE :term3 OR o.address_2 LIKE :term4 OR o.address_3 LIKE :term5 OR o.suburb LIKE :term6 OR o.order_number LIKE :term7 OR o.client_order_id LIKE :term8 OR eo.manifest_id LIKE :term9)
                AND
                (o.date_ordered < :to)
        ";
        $date_to_value = ($date_to_value == 0)? $date_to_value = time(): $date_to_value;
        $array = array(
            'term1' =>  '%'.$term.'%',
            'term2' =>  '%'.$term.'%',
            'term3' =>  '%'.$term.'%',
            'term4' =>  '%'.$term.'%',
            'term5' =>  '%'.$term.'%',
            'term6' =>  '%'.$term.'%',
            'term7' =>  '%'.$term.'%',
            'term8' =>  '%'.$term.'%',
            'term9' =>  '%'.$term.'%',
            'to'    =>  $date_to_value
        );

        if($date_from_value > 0)
        {
            $query .= " AND (o.date_ordered > :from)";
            $array['from'] = $date_from_value;
        }
        if($client_id > 0)
        {
            $query .= " AND (o.client_id = :client_id)";
            $array['client_id'] = $client_id;
        }
        if($courier_id > 0)
        {
            $query .= " AND (o.courier_id = :courier_id)";
            $array['courier_id'] = $courier_id;
        }
        //print_r($array);
        //die($query);
        return $orders = $db->queryData($query, $array);
    }

    public function getOrderCountForSummary($summary_id)
    {
        $db = Database::openConnection();
        return $db->countData('orders', array('eparcel_order_id' => $summary_id));
    }

    public function getEparcelSummaries($all = false)
    {
        $db = Database::openConnection();
        $seven_ago = strtotime("7 days ago");
        $dates = ($all)? "" : "AND create_date >= $seven_ago";
        $q = "SELECT * FROM eparcel_orders WHERE order_summary IS NOT NULL $dates ORDER BY create_date DESC";
        return $db->queryData($q);
    }

    public function getOrderByOrderNumber($order_number)
    {
        $db = Database::openConnection();

        return $db->queryRow("
            SELECT
                *
            FROM
                orders
            WHERE
                order_number = :order_no",
            array(
                'order_no'    =>  $order_number
            )
        );
    }

    public function getOrderByBarcode($barcode, $order_number = false)
    {
        $db = Database::openConnection();
        if(!$order_number)
            $order_number = $barcode;

        return $db->queryRow("
            SELECT
                *
            FROM
                orders
            WHERE
                order_number = :order_no
                OR order_number = :barcode",
            array(
                'order_no'    =>  $order_number,
                'barcode'     =>  $barcode
            )
        );
    }

    public function recordDispatch($data, $recording)
    {
        $db = Database::openConnection();
        if(empty($recording))
        {
            $vals = array(
                'consignment_id'    =>  $data['consignment_id'],
                'scanned_by'        =>  Session::getUserId(),
                'date'              =>  time()
            );
            $db->insertQuery('order_dispatch', $vals);
        }
        else
        {
            $db->query("UPDATE order_dispatch SET parcels_scanned = parcels_scanned + 1 WHERE id = ".$recording['id']);
        }

        return true;
    }

    public function getOrderDispatchByConId($con_id)
    {
        $db = Database::openConnection();
        return $db->queryRow("SELECT * FROM order_dispatch WHERE consignment_id = :con_id", array('con_id' => $con_id));
    }

    public function getOrderByConId($con_id)
    {
        $db = Database::openConnection();
        return $db->queryRow("SELECT * FROM orders WHERE consignment_id = :con_id", array('con_id' => $con_id));
    }

    public function getOrderByShipmentId($shipment_id)
    {
        $db = Database::openConnection();
        return $db->queryRow("SELECT * FROM orders WHERE eparcel_shipment_id = :shipment_id", array('shipment_id' => $shipment_id));
    }

    public function removeError($order_id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseFields($this->table, array('errors' => 0, 'error_string' => NULL), $order_id);
    }

    public function updateItemsForOrder(array $order_items, $order_id)
    {
        //echo "<pre>",print_r($order_items),"</pre>";
        $db = Database::openConnection();
        //delete the old ones
        $db->deleteQuery('orders_items', $order_id, 'order_id');
        //update with new data
        foreach($order_items as $oi)
        {
            //echo "<pre>",print_r($oi['locations']),"</pre>";die();
            foreach($oi['locations'] as $oil)
            {
                $vals = array(
                    'order_id'      =>  $order_id,
                    'item_id'       =>  $oi['item_id'],
                    'location_id'   =>  $oil['location_id'],
                    'qty'           =>  $oil['qty']
                );
                $db->insertQuery('orders_items', $vals);
            }
        }
        return true;
    }

    public function getPackagesForOrder($id)
    {
        $db = Database::openConnection();
        return $db->queryData("SELECT * FROM orders_packages WHERE order_id = $id");
    }

    public function addPackage($data)
    {
        $db = Database::openConnection();
        $values = array(
            'order_id'  =>  $data['order_id'],
            'width'     =>  $data['width'],
            'height'    =>  $data['height'],
            'depth'     =>  $data['depth'],
            'weight'    =>  $data['weight']
        );
        return $db->insertQuery("orders_packages", $values);
    }

    public function deletePackage($id)
    {
        $db = Database::openConnection();
        $db->deleteQuery('orders_packages', $id);
    }

    public function cancelOrders($ids)
    {
        $db = Database::openConnection();
        foreach($ids as $id)
        {
            $db->deleteQuery('orders', $id);
            $db->deleteQuery('orders_items', $id, 'order_id');
        }
    }

    public function countItemForOrder($item_id, $order_id)
    {
        $db = Database::openConnection();
        $res = $db->queryRow("SELECT SUM(qty) AS qty FROM orders_items WHERE item_id = $item_id AND order_id = $order_id");
        return (int)$res['qty'];
    }

    public function getStatusses()
    {
        $db = Database::openConnection();
        $statusses = $db->queryData("SELECT id, name FROM order_status ORDER BY name");
        foreach($statusses as $status)
        {
            $this->status[$status['id']] = $status['name'];
        }
    }

    public function getStatusId($status)
    {
        $db = Database::openConnection();
        return ($db->queryValue('order_status', array('name' => $status)));
    }

    public function getStatusName($status_id)
    {
        $db = Database::openConnection();
        return ($db->queryValue('order_status', array('id' => $status_id), 'name'));
    }

    public function getItemCountForOrder($order_id)
    {
        $db = Database::openConnection();
        $cq = $db->queryRow("SELECT SUM(qty) AS sum FROM orders_items WHERE order_id = :order_id GROUP BY order_id", array('order_id' => $order_id));
        return($cq['sum']);
    }

    public function updateOrderValues($values, $id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseFields($this->table, $values, $id);
        return true;
    }

    public function getItemsForOrderNoLocations($order_id)
    {
        $db = Database::openConnection();
        $q = "
            SELECT i.name, i.client_id, i.id AS item_id, i.sku, i.barcode, SUM(oi.qty) AS qty
            FROM orders_items oi
            JOIN items i ON oi.item_id = i.id
            WHERE oi.order_id = $order_id
            GROUP BY oi.item_id
            ORDER BY i.name
        ";
        return $db->queryData($q);
    }

    public function getItemsForOrder($order_id, $picked = -1)
    {
        $db = Database::openConnection();
        $q = "
            SELECT i.*, oi.qty, oi.location_id, oi.item_id, oi.id AS line_id, il.qty AS location_qty
            FROM orders_items oi JOIN items i ON oi.item_id = i.id LEFT JOIN items_locations il on oi.location_id = il.location_id AND il.item_id = i.id
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

    public function getItemsCountForOrder($order_id, $picked = -1)
    {
        $db = Database::openConnection();
        $q = "
            SELECT i.*, SUM(oi.qty) AS qty
            FROM orders_items oi JOIN items i ON oi.item_id = i.id
            WHERE oi.order_id = $order_id
            GROUP BY i.id
        ";
        if($picked === 1)
            $q .= " AND oi.picked = 1";
        elseif($picked === 0)
            $q .= " AND oi.picked = 0";
        $q .= " ORDER BY i.name";
        return $db->queryData($q);
    }

    public function getOrderDetail($id)
    {
        $db = Database::openConnection();
        $order = $db->queryById($this->table, $id);
        return (empty($order))? false : $order;
    }

    public function getAllOrdersByStatus($status_id)
    {
        $db = Database::openConnection();
        return ($db->queryData("SELECT * FROM {$this->table} WHERE status_id = $status_id ORDER BY date_ordered ASC"));
    }

    public function getHomePageOrders($client_id, $courier_id)
    {
        $db = Database::openConnection();
        $status_id = $this->fulfilled_id;
        //echo "SELECT * FROM {$this->table} WHERE status_id != $status_id AND client_id NOT IN ({$this->excluded_clients}) ORDER BY date_ordered ASC"; die();
        $q = "SELECT * FROM {$this->table} WHERE status_id != $status_id AND client_id NOT IN ({$this->excluded_clients})";
        if($client_id > 0)
        {
            $q .= " AND client_id = $client_id";
        }
        if($courier_id >= 0)
        {
            $q .= " AND courier_id = $courier_id";
        }
        $q .= " ORDER BY date_ordered ASC";
        return ($db->queryData($q));
    }

    public function getAllOrders($client_id, $courier_id = -1, $fulfilled = 0, $store_order = -1, $state = "")
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
        if($client_id > 0)
        {
            $q .= " AND client_id = $client_id";
        }
        if($courier_id >= 0)
        {
            $q .= " AND courier_id = $courier_id";
        }
        if($store_order >= 0)
        {
            $q .= " AND store_order = $store_order";
        }
        if(!empty($state))
        {
            $q .= " AND state = :state";
            $array['state'] = $state;
        }
        $q .= " ORDER BY errors DESC, client_id, courier_id ASC, country, consignment_id, date_ordered ASC";
        //die($q);
        return ($db->queryData($q, $array));
    }

    public function getUnfulfilledOrders($client_id, $courier_id, $store_order = -1)
    {
        $db = Database::openConnection();
        $status_id = $this->fulfilled_id;
        //echo "SELECT * FROM {$this->table} WHERE status_id != $status_id AND client_id NOT IN ({$this->excluded_clients}) ORDER BY date_ordered ASC"; die();
        $q = "SELECT * FROM {$this->table} WHERE status_id != $status_id";
        if($client_id > 0)
        {
            $q .= " AND client_id = $client_id";
        }
        if($courier_id >= 0)
        {
            $q .= " AND courier_id = $courier_id";
        }
        if($store_order >= 0)
        {
            $q .= " AND store_order = $store_order";
        }
        $q .= " ORDER BY client_id, courier_id ASC, country, date_ordered ASC";
        //die($q);
        return ($db->queryData($q));
    }

    public function getOrderTrends($from, $to, $client_id)
    {
        $db = Database::openConnection();
        $query1 = "
            SELECT
                count(*) as total_orders, UNIX_TIMESTAMP(FROM_DAYS(TO_DAYS(DATE_FORMAT(FROM_UNIXTIME(date_fulfilled), '%Y-%m-%d')) - MOD( TO_DAYS( DATE_FORMAT(FROM_UNIXTIME(date_fulfilled), '%Y-%m-%d') ) -7, 7 ))) + 6*24*60*60 AS friday
            FROM
                orders
            WHERE
                date_fulfilled >= $from AND date_fulfilled <= $to
        ";


        if($client_id > 0)
            $query1 .= " AND client_id = ".$client_id;
        $query1 .= "  GROUP BY
                UNIX_TIMESTAMP(FROM_DAYS(TO_DAYS(DATE_FORMAT(FROM_UNIXTIME(date_fulfilled), '%Y-%m-%d')) - MOD( TO_DAYS( DATE_FORMAT(FROM_UNIXTIME(date_fulfilled), '%Y-%m-%d') ) -7, 7 )))";
        //echo $query1; die();

        $orders = $db->queryData($query1);

        $return_array = array(
            array(
                'Week Ending',
                'Total Orders'
            )
        );

        foreach($orders as $o)
        {
            $row_array = array();
            $row_array[0] = date("d/m/y", $o['friday']);
            $row_array[1] = $o['total_orders'];

            $return_array[] = $row_array;
        }

        return $return_array;
    }

    public function getClientActivity($from, $to, $clients = "")
    {
        $db = Database::openConnection();
        $query = "
            SELECT
                DATE(from_unixtime(date_fulfilled)) AS date_fulfilled, COUNT(*) AS orders, client_id
            FROM
                orders o join clients c on c.id = o.client_id
        ";

        $date_clause = " WHERE date_fulfilled > 0";
        $client_clause = "";

        if(isset($from) || isset($to))
        {
            $date_clause = " WHERE";
            if(isset($from))
            {
                $date_clause .= " date_fulfilled > {$from}";
            }
            if(isset($to))
            {
                if(isset($from))
                {
                    $date_clause .= " AND ";
                }
                $date_clause .= " date_fulfilled < {$to}";
            }
        }
        $client_query = "SELECT id, client_name FROM clients WHERE active = 1";
        if(!empty($clients))
        {
            $clients = rtrim($clients, ",");
            $client_clause = " AND client_id IN($clients)";
            $client_query .= " AND id IN($clients)";
        }
        $query .= $date_clause.$client_clause;
        $query .= "
            GROUP BY
                client_id, DATE(from_unixtime(date_fulfilled))
            ORDER BY
                DATE(from_unixtime(date_fulfilled))

        ";

        $clients = $db->queryData($client_query);
        //echo $query; //die();
        $orders = $db->queryData($query);

        $keys = array('Date');
        $i = 1;
        foreach($clients as $c)
        {
            $keys[$i] = $c['client_name'];
            ++$i;
        }

        $return_array = array(
            $keys
        );

        $date = "";
        foreach($orders as $o)
        {
            if($o['date_fulfilled'] != $date)
            {
                if(!empty($date))
                {
                    $return_array[] = $row_array;
                }
                $row_array = array();
                $row_array[0] = date("d/m/Y", strtotime($o['date_fulfilled']));
                $i = 1;
                foreach($clients as $c)
                {
                    $row_array[$i] = 0;
                    ++$i;
                }
            }
            $i = 1;
            foreach($clients as $c)
            {
                if($o['client_id'] == $c['id'])
                {
                    $row_array[$i] = $o['orders'];
                }
                ++$i;
            }

            $date = $o['date_fulfilled'];
        }
        $return_array[] = $row_array;
        return $return_array;
    }

    public function getPickErrors($from, $to, $client_id = 0)
    {
        $from += 24*60*60;
        $to += 24*60*60;
        $db = Database::openConnection();
        $query1 = "
            SELECT
                count(*) as total_orders, UNIX_TIMESTAMP(FROM_DAYS(TO_DAYS(DATE_FORMAT(FROM_UNIXTIME(date_fulfilled), '%Y-%m-%d')) - MOD( TO_DAYS( DATE_FORMAT(FROM_UNIXTIME(date_fulfilled), '%Y-%m-%d') ) -7, 7 ))) + 6*24*60*60 AS friday
            FROM
                orders
            WHERE
                date_fulfilled >= $from AND date_fulfilled <= $to
        ";


        if($client_id > 0)
            $query1 .= " AND client_id = ".$client_id;
        $query1 .= "  GROUP BY
                UNIX_TIMESTAMP(FROM_DAYS(TO_DAYS(DATE_FORMAT(FROM_UNIXTIME(date_fulfilled), '%Y-%m-%d')) - MOD( TO_DAYS( DATE_FORMAT(FROM_UNIXTIME(date_fulfilled), '%Y-%m-%d') ) -7, 7 )))";
        //echo $query1;

        $orders = $db->queryData($query1);

        $return_array = array(
            array(
                'Week Ending',
                'Total Orders'
            )
        );

        foreach($orders as $o)
        {
            $row_array = array();
            $row_array[0] = date("d/m/y", $o['friday']);
            $row_array[1] = $o['total_orders'];
            $return_array[] = $row_array;
        }
        return $return_array;
    }

    public function getTopProducts($from, $to, $client_id)
    {
        $db = Database::openConnection();
        $query1 = "
            SELECT
                SUM(oi.qty) as total_items,
                i.name
            FROM
                orders o JOIN orders_items oi ON o.id = oi.order_id JOIN items i ON i.id = oi.item_id
            WHERE
                o.date_fulfilled >= $from AND o.date_fulfilled <= $to
        ";


        if($client_id > 0)
            $query1 .= " AND o.client_id = ".$client_id;
        $query1 .= "
            GROUP BY
                oi.item_id
            ORDER BY
                total_items DESC
            LIMIT
                0,10
            ";
        //echo $query1;

        $items = $db->queryData($query1);

        $return_array = array(
            array(
                'Item',
                'Number Ordered'
            )
        );

        foreach($items as $i)
        {
            $row_array = array();
            $row_array[0] = $i['name'];
            $row_array[1] = (int)$i['total_items'];

            $return_array[] = $row_array;
        }
        return $return_array;
    }

    public function getCurrentOrders($store_order = 0)
    {
        $db = Database::openConnection();
        $q = "  select
                    count(*) as order_count, c.client_name, o.client_id
                from
                    orders o join clients c on o.client_id = c.id
                where
                    o.status_id != {$this->fulfilled_id} and c.active = 1 and o.store_order = $store_order
                group by
                    o.client_id
                order by
                    c.client_name";

        return $db->queryData($q);
    }

    public function getCurrentStoreOrders()
    {
        return $this->getCurrentOrders(1);
    }

    public function getOrdersForClient($client_id, $from, $to)
    {
        $db = Database::openConnection();
        return $db->queryData("SELECT * FROM orders WHERE client_id = $client_id AND date_ordered >= $from AND date_ordered <= $to ORDER BY date_ordered DESC");
    }

    public function setSlipPrinted($id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table,'slip_printed', 1, $id);
    }

    public function updateStatus($status_id, $id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table,'status_id', $status_id, $id);
    }

    public function updateOrderAddress($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'ship_to'       =>  $data['ship_to'],
            'company_name'  =>  null,
            'address'		=>	$data['address'],
            'address_2'     =>  null,
            'suburb'		=>	$data['suburb'],
            'state'		    =>	$data['state'],
            'postcode'	    =>	$data['postcode'],
            'country'       =>  $data['country']
        );
        if(isset($data['company'])) $vals['company_name'] = $data['company'];
        if(isset($data['address2'])) $vals['address_2'] = $data['address2'];
        $db->updatedatabaseFields($this->table, $vals, $data['order_id']);
        return true;
    }

    public function getOrderNumber()
    {
        $db = Database::openConnection();
        $order_number = Utility::ean13_check_digit(Utility::randomNumber(12));
        while($db->queryValue('orders', array('order_number' => $order_number)))
        {
            $order_number = Utility::ean13_check_digit(Utility::randomNumber(12));
        }
        return $order_number;
    }
}