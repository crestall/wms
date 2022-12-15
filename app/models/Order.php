<?php

 /**
  * Order Class
  *

  * @author     Mark Solly <mark.solly@fsg.com.au>

    FUNCTIONS

    addOrder($data, $oitems)
    addPackage($data)
    addressMatch($order_id, $match_id)
    cancelOrders($ids)
    countItemForOrder($item_id, $order_id)
    courierAssigned($order_id)
    consolidateOrders($id, $intoid)
    deletePackage($id)
    getAllOrders($client_id, $courier_id = -1, $fulfulled = 0, $store_order = -1)
    getAllOrdersByStatus($status_id)
    getBackorders($client_id = 0)
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
    getOrderNumber($id);
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

    public function getSelectStatuses($selected = false)
    {
        $check = "";
        $ret_string = "";
        foreach($this->status as $value => $label )
        {
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function getOrderNumberForOrder($id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' => $id), 'order_number');
    }

    public function updateOrderItemsLocations($line_id, $new_location_id, $pod = false)
    {
        $db = Database::openConnection();
        if($pod)
            $db->updateDatabaseFields('orders_items', array('location_id' => $new_location_id, 'pod_id' => NULL), $line_id);
        else
            $db->updateDatabaseField('orders_items', 'location_id', $new_location_id, $line_id);

        return true;
    }

    public function getPODIdSelect($selected = false)
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $pods = $db->queryData("
            SELECT DISTINCT
                oi.pod_id
            FROM
                orders_items oi
            WHERE
                oi.pod_id IS NOT NULL AND
                oi.pod_id != ''
            ORDER BY
                oi.id
        ");
        foreach($pods as $p)
        {
            if($selected)
            {
                $check = ($p['pod_id'] == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option $check>{$p['pod_id']}</option>";
        }
        return $ret_string;
    }

    public function getPODIdForOrder($order_id)
    {
        $db = Database::openConnection();
        return $db->queryValue('orders_items', array('order_id' => $order_id, 'location_id' => 2914), 'pod_id');
    }

    public function getPODItemsForPODId($pod_id, $order_id = 0)
    {
        $db = Database::openConnection();
        $q = "
            SELECT
                *
            FROM
                orders_items oi
            WHERE
                oi.pod_id = :pod_id
        ";
        if($order_id > 0)
            $q .= " AND oi.order_id = $order_id";
        $array = array('pod_id' => $pod_id);
        return $db->queryData($q, $array);
    }

    public function getBackorders($client_id = 0)
    {
        $db = Database::openConnection();
        $q = "SELECT * FROM {$this->table} WHERE backorder_items = 1 AND cancelled = 0";
        if($client_id > 0)
        {
            $q .= " AND client_id = $client_id";
        }
        $q .= " ORDER BY date_ordered ASC";
        return $db->queryData($q);
    }

    public function isBackorder($order_id = 0)
    {
        $db = Database::openConnection();
        if($order_id == 0)
            return false;
        //return ( $db->queryValue($this->table, array('id' => $order_id), 'backorder_items') == 1 );   Check the actual order items instead
        $l = new location();
        $orders = $db->queryData("SELECT id FROM `orders_items` WHERE `location_id` = {$l->backorders_id} AND `order_id` = $order_id");
        return ( count($orders) > 0 );
    }

    public function consolidateOrders($old_id, $new_id)
    {
        $db = Database::openConnection();
        $array = array(
            'new_id'    => $new_id,
            'old_id'    => $old_id
        );
        //update orders_items table
        $q = "UPDATE orders_items SET order_id = :new_id WHERE order_id = :old_id";
        $db->queryData($q, $array);
        //update orders_packages table
        $q = "UPDATE orders_packages SET order_id = :new_id WHERE order_id = :old_id";
        $db->queryData($q, $array);
        //update order_item_serials table
        $q = "UPDATE order_item_serials SET order_id = :new_id WHERE order_id = :old_id";
        $db->queryData($q, $array);

    }

    public function addressMatch($address_array, $match_id)
    {
        $db = Database::openConnection();
        $address_array['id'] = $match_id;
        return $db->countData($this->table, $address_array);
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

    public function countAssociatedPackage($id)
    {
        $db = Database::openConnection();
        $q = "SELECT SUM(count) AS packages FROM orders_packages WHERE order_id = $id";
        $res = $db->queryRow($q);
        $pc = (empty($res['packages']))? 0 : $res['packages'];
        return $pc;
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
        $ref_2 = $order_number;
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
            'ref_2'         => $order_number,
            'instructions'  => $instructions,
            'address'       => $data['address'],
            'suburb'        => $data['suburb'],
            'state'         => strtoupper($data['state']),
            'postcode'      => $data['postcode'],
            'country'       => strtoupper($data['country']),
            'entered_by'    => $eb
        );
        if(isset($data['backorder_items']))
            $o_values['backorder_items'] = 1;
        if(isset($data['is_woocommerce']))
            $o_values['is_woocommerce'] = 1;
        if(isset($data['is_shopify']))
            $o_values['is_shopify'] = 1;
        if(isset($data['is_ebay']))
            $o_values['is_ebay'] = 1;
        if(isset($data['is_voicecaddy']))
            $o_values['is_voicecaddy'] = 1;
        if(isset($data['is_homecoursegolf']))
            $o_values['is_homecoursegolf'] = 1;
        if(isset($data['is_superspeedgolf']))
            $o_values['is_superspeedgolf'] = 1;
        if(isset($data['is_marketplacer']))
            $o_values['is_marketplacer'] = 1;
        if(isset($data['is_buzzbee']))
            $o_values['is_buzzbee'] = 1;
        if(isset($data['is_rukket']))
            $o_values['is_rukket'] = 1;
        if(isset($data['is_arccosgolf']))
            $o_values['is_arccosgolf'] = 1;
        if(isset($data['pickup']))
            $o_values['pickup'] = 1;
        if(isset($data['shopify_id']))
            $o_values['shopify_id'] = $data['shopify_id'];
        if(isset($data['ebay_id']))
            $o_values['ebay_id'] = $data['ebay_id'];
        if(!empty($data['marketplacer_id']))
            $o_values['marketplacer_id'] = $data['marketplacer_id'];
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
        if(!empty($data['customer_id']))
            $o_values['customer_id'] = $data['customer_id'];
        if(!empty($data['error_string']))
            $o_values['error_string'] = $data['error_string'];
        if(isset($data['b2b']))
            $o_values['store_order'] = 1;
        /* try{
            $order_id = $db->insertQuery('orders', $o_values);
        } catch(Exception $e){
            echo "<pre>",print_r($oitems),"</pre>"; die();
        }*/
        $order_id = $db->insertQuery('orders', $o_values);
        //echo "<pre>",print_r($oitems),"</pre>"; //die();
        $the_items = array();
        foreach($oitems as $items)
        {
            //$items = (array)$items;
            echo "<pre>",print_r($items),"</pre>"; die();
            if(!isset($items[0]))
                $the_items[] = $items;
            else
                $the_items = $items;
            //echo "The Items<pre>",print_r($the_items),"</pre>";
            if(count($the_items)):
                foreach($the_items as $item):
                    if(count($item)):
                        //echo "The Item<pre>",print_r($item),"</pre>"; //die();
                        $item_id = $item['item_id'];
                        //echo "<p>{$data['deliver_to']} $order_id</p>Item<pre>",print_r($item),"</pre>";
                        /* */
                        foreach($item['locations'] as $il)
                        {
                            $vals = array(
                                'item_id'               => $item_id,
                                'location_id'           => $il['location_id'],
                                'qty'                   => $il['qty'],
                                'order_id'              => $order_id,
                                'client_order_item_id'  => $il['client_order_item_id'],
                                'shopify_line_item_id'  => $il['shopify_line_item_id'],
                                'ebay_line_item_id'     => $il['ebay_line_item_id'],
                                'pod_id'                => $il['pod_id']
                            );
                            if(!empty($il['pod_id']))
                                $vals['pod_id'] = $il['pod_id'];
                            $db->insertQuery('orders_items', $vals);
                        }
                        if(isset($item['collection_item']))
                        {
                            $cvals = array(
                                'item_id'               => $item['collection_item']['item_id'],
                                'location_id'           => $item['collection_item']['location_id'],
                                'qty'                   => $item['collection_item']['qty'],
                                'order_id'              => $order_id,
                                'client_order_item_id'  => $item['collection_item']['client_order_item_id'],
                                'shopify_line_item_id'  => $item['collection_item']['shopify_line_item_id'],
                                'ebay_line_item_id'     => $item['collection_item']['ebay_line_item_id'],
                                'is_kit'                => 1
                            );
                            if(!empty($item['collection_item']['pod_id']))
                                $cvals['pod_id'] = $item['collection_item']['pod_id'];
                            $db->insertQuery('orders_items', $cvals);
                        }
                        if(!empty($item['order_error_string']))
                        {
                            $db->query("UPDATE orders SET pick_notices = IFNULL(CONCAT(pick_notices, '{$item['order_error_string']}'), '{$item['order_error_string']}') WHERE id = $order_id");
                            //echo "UPDATE orders SET 3pl_comments = IFNULL(CONCAT(3pl_comments, '{$item['order_error_string']}'), '{$item['order_error_string']}') WHERE id = $order_id"; die();
                        }
                    endif;
                endforeach;
            endif;
        }
        //die();
        return $order_number;
    }

    public function getDispatchedOrders($from, $to, $client_id, $is_arccosgolf = false)
    {
        $db = Database::openConnection();
        $query = "
        SELECT
            o.*, od.scanned_by, od.date, od.parcels_scanned, op.packed_by, op.date AS packed_date
        FROM
            orders o LEFT JOIN order_dispatch od ON o.consignment_id = od.consignment_id LEFT JOIN order_packing op ON op.order_id = o.id
        WHERE
            client_id = :client_id AND status_id = :status_id AND date_fulfilled >= :from AND date_fulfilled <= :to
        ";
        if($is_arccosgolf !== false)
            $query .= " AND is_arccosgolf > 0 ";
        $query .= "
        ORDER BY date_fulfilled DESC";
        $array = array(
            'client_id' => 	$client_id,
            'status_id' =>  $this->fulfilled_id,
            'to'        =>  $to,
            'from'      =>  $from
        );
        return $db->queryData($query, $array);
    }

    public function getUnFTPedOrdersArray($client_id)
    {
        $db = Database::openConnection();
        $cmodel = new Courier();
        $query = "
            SELECT
                o.*
            FROM
                orders o
            WHERE
                client_id = :client_id AND status_id = :status_id AND ftp_uploaded = 0
            ORDER BY
                date_fulfilled DESC
        ";
        $array = array(
            'client_id' => 	$client_id,
            'status_id' =>  $this->fulfilled_id
        );
        /*
        $query = "
            SELECT
                o.*
            FROM
                orders o
            WHERE
                client_id = :client_id AND id = 88034
            ORDER BY
                date_fulfilled DESC
        ";
        $array = array(
            'client_id' => 	$client_id
        );
        */
        $orders = $db->queryData($query, $array);
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

            $address = Utility::formatAddressWeb($ad);
            $shipped_to = "";
            if(!empty($co['company_name'])) $shipped_to .= $co['company_name']."<br/>";
            if(!empty($co['ship_to'])) $shipped_to .= $co['ship_to']."<br/>";
            $shipped_to .= $address;

            $products = $this->getItemsCountForOrder($co['id']);

            echo "PRODUCTS<pre>",print_r($products),"</pre>";

            $eb = $db->queryValue('users', array('id' => $co['entered_by']), 'name');
            if(empty($eb))
            {
                $eb = "Automatically Imported";
            }
            $num_items = 0;
            $items = "";
            $csv_items = array();
            $ignore_cpoii = array();
            foreach($products as $p)
            {
                if($p['is_kit'] == 1)
                {
                    $ignore_cpoii[] = $p['client_order_item_id'];
                    $items .= $p['name']." (".$p['qty']."),<br/>";
                    $num_items += $p['qty'];
                    $csv_items[] = array(
                        'name'  =>  $p['name'],
                        'qty'   =>  $p['qty'],
                        'cpid'  =>  $p['client_product_id'],
                        'coiid' =>  $p['client_order_item_id'],
                        'sku'   =>  $p['sku']
                    );
                }
                else
                {
                    if(in_array($p['client_order_item_id'], $ignore_cpoii))
                        continue;
                    $items .= $p['name']." (".$p['qty']."),<br/>";
                    $num_items += $p['qty'];
                    $csv_items[] = array(
                        'name'  =>  $p['name'],
                        'qty'   =>  $p['qty'],
                        'cpid'  =>  $p['client_product_id'],
                        'coiid' =>  $p['client_order_item_id'],
                        'sku'   =>  $p['sku']
                    );
                }
            }
            $items = rtrim($items, ",<br/>");
            $courier = $db->queryValue('couriers', array('id' => $co['courier_id']), 'name');
            if($courier == "Local")
            {
                $courier = $co['courier_name'];
            }
            if($co['courier_id'] == $cmodel->directFreightId)
            {
                $tracking_url = "https://directfreight.com.au";
            }
            elseif( $co['courier_id'] == $cmodel->eParcelId || $co['courier_id'] == $cmodel->eParcelExpressId )
            {
                $tracking_url = "https://auspost.com.au/parcels-mail/track.html#/track?id={$co['consignment_id']}";
            }
            else
            {
                $tracking_url = "No tracking URL for this courier";
            }
            $handling_charge = number_format($co['handling_charge'], 2);
            $postage_charge = number_format($co['postage_charge'], 2);
            $gstex_charge = number_format( ($co['postage_charge'] + $co['handling_charge']), 2);
            $gst = number_format($co['gst'], 2);
            $gstinc_charge = number_format($co['total_cost'], 2);
            $dd = $pb = "";
            $shrink_wrap = (empty($co['shrink_wrap']))? 0 : 1;
            $bubble_wrap = (empty($co['bubble_wrap']))? 0 : 1;
            $has_shrink_wrap = (empty($co['shrink_wrap']))? "No" : "Yes";
            $has_bubble_wrap = (empty($co['bubble_wrap']))? "No" : "Yes";
            $pallets = (empty($co['pallets']))? 0 : $co['pallets'];
            $row = array(
                'order_id'              => $co['id'],
                'date_ordered'          => date('d-m-Y', $co['date_ordered']),
                'entered_by'            => $eb,
                'date_fulfilled'        => date('d-m-Y', $co['date_fulfilled']),
                'order_number'          => $co['order_number'],
                'client_order_number'   => $co['client_order_id'],
                'shipped_to'            => $shipped_to,
                'country'               => $co['country'],
                'items'                 => $items,
                'total_items'           => $num_items,
                'handling_charge'       => $handling_charge,
                'postage_charge'        => $postage_charge,
                'total_exgst'           => $gstex_charge,
                'gst'                   => $gst,
                'total_gstinc'          => $gstinc_charge,
                'courier'               => $courier,
                'tracking_url'          => $tracking_url,
                'consignment_id'        => $co['consignment_id'],
                'csv_items'             => $csv_items
            );
            $return[] = $row;
        }
        return $return;

    }

    public function isKitOrder($id)
    {
        $db = Database::openConnection();
        return $db->queryValue("orders_items", array(
            'is_kit'    => 1,
            'order_id'  => $id
        ));

    }

    public function getDispatchedOrdersArray($from, $to, $client_id, $is_arccosgolf = false )
    {
        $db = Database::openConnection();
        $orders = $this->getDispatchedOrders($from, $to, $client_id, $is_arccosgolf);
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
            $parcels = Packaging::getPackingForOrder($co,$order_items,$packages);
            //$parcels = array();
            $weight = $co['weight'];
            if( count($parcels) )
            {
                $weight = 0;
                foreach($parcels as $p)
                {
                    $weight += $p['weight'];
                }

            }
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
            $total_charge = "$".number_format($co['total_cost'], 2);
            $total_fee = "$".number_format($co['total_cost'] - $co['gst'], 2);
            $handling_charge = "$".number_format($co['handling_charge'], 2);
            $postage_charge = "$".number_format($co['postage_charge'], 2);
            $gst = "$".number_format($co['gst'], 2);
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
                'handling_charge'       => $handling_charge,
                'postage_charge'        => $postage_charge,
                'gst'                   => $gst,
                'total_charge'          => $total_charge,
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
                'weight'                => $weight,
                'uploaded_file'         => $co['uploaded_file'],
                'client_id'             => $co['client_id'],
                'total_fee'             => $total_fee
            );
            $return[] = $row;
        }
        return $return;
    }

    public function getUndispatchedOrdersWithSerials($from, $to, $client_id)
    {
        $db = Database::openConnection();
        $query = "
        SELECT
            o.*
        FROM
            orders o JOIN order_item_serials ois ON o.id = ois.order_id
        WHERE
            client_id = :client_id AND status_id != :status_id AND date_ordered >= :from AND date_ordered <= :to
        GROUP BY
            o.id
        ORDER BY
            date_ordered DESC";
        $array = array(
            'client_id' => 	$client_id,
            'status_id' =>  $this->fulfilled_id,
            'to'        =>  $to,
            'from'      =>  $from
        );
        //echo $query;print_r($array);
        return $db->queryData($query, $array);
    }

    public function getUndispatchedOrdersWithSerialsArray($from, $to, $client_id)
    {
        $db = Database::openConnection();
        $orders = $this->getUndispatchedOrdersWithSerials($from, $to, $client_id);
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

            $address = Utility::formatAddressWeb($ad);
            $shipped_to = "";
            if(!empty($co['company_name'])) $shipped_to .= $co['company_name']."<br/>";
            if(!empty($co['ship_to'])) $shipped_to .= $co['ship_to']."<br/>";
            $shipped_to .= $address;
            $products = $this->getItemsWithSerialForOrder($co['id']);
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
                $items .= $p['name']." (".$p['sku'].") - ".$p['serial_number'].",<br/>";

                $csv_items[] = array(
                    'name'          =>  $p['name'],
                    'sku'           =>  $p['sku'],
                    'serial_number' =>  $p['serial_number']
                );
            }
            $items = rtrim($items, ",<br/>");

            $row = array(
                'date_ordered'          => date('d-m-Y', $co['date_ordered']),
                'entered_by'            => $eb,
                'order_number'          => $co['order_number'],
                'client_order_number'   => $co['client_order_id'],
                'customer_order_number' => $co['customer_order_id'],
                'shipped_to'            => $shipped_to,
                'country'               => $co['country'],
                'items'                 => $items,
                'id'                    => $co['id']

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
                o.cancelled = 0
                AND
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

    public function getEparcelSummaries($from)
    {
        $db = Database::openConnection();
        $to = $from + 7*24*60*60;
        $q = "SELECT * FROM eparcel_orders WHERE order_summary IS NOT NULL AND create_date >= $from AND create_date <= $to ORDER BY create_date DESC";
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
        $db = Database::openConnection();
        //This methods deletes them all and adds new ones in
            //echo "<pre>",print_r($order_items),"</pre>";
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
        /*//This method updates the system
            foreach($order_items as $oi)
            {
                if($updater = $db->queryValue('orders_items', array('order_id' => $order_id, 'item_id' => $oi['item_id'])))
            }
        */
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
            'weight'    =>  $data['weight'],
            'count'     =>  $data['count'],
            'pallet'    =>  0
        );
        if(isset($data['pallet']))
            $values['pallet'] = 1;
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
            //$db->deleteQuery('orders', $id);
            //$db->deleteQuery('orders_items', $id, 'order_id');
            $db->updateDatabaseFields($this->table,[
                'cancelled'     => 1,
                'cancelled_at'  => time(),
                'cancelled_by'  => Session::getUserId()
            ], $id);
        }
    }

    public function fillBackorders($ids, $backorder_location_id)
    {
        $db = Database::openConnection();
        $item = new Item();
        foreach($ids as $id)
        {
            //get items for this order in the backorders location
            $bois = $db->queryData("SELECT * FROM orders_items WHERE order_id = $id AND location_id = $backorder_location_id");
            foreach($bois as $boi)
            {
                //The required number of items
                $required = $boi['qty'];
                //find location(s) for this item
                $locations = $item->getLocationsForItem($boi['item_id']);
                foreach($locations as $l)
                {
                    $available = $l['qty'] - $l['qc_count'] - $l['allocated'];
                    if($available <= 0)
                        continue;
                    if($available < $required)
                    {
                        //insert what can fit
                        $db->insertQuery('orders_items', array(
                            'order_id'              =>  $id,
                            'item_id'               =>  $boi['item_id'],
                            'client_order_item_id'  =>  $boi['client_order_item_id'],
                            'location_id'           =>  $l['location_id'],
                            'qty'                   =>  $available
                        ));
                        $required -= $available;
                    }
                    else
                    {
                        //insert all that are required
                        $db->insertQuery('orders_items', array(
                            'order_id'              =>  $id,
                            'item_id'               =>  $boi['item_id'],
                            'client_order_item_id'  =>  $boi['client_order_item_id'],
                            'location_id'           =>  $l['location_id'],
                            'qty'                   =>  $required
                        ));
                    }
                }
                $db->deleteQuery('orders_items', $boi['id']);
            }
            $this->updateOrderValue('backorder_items', 0, $id);
        }

    }

    public function removeBackorderStatus($order_id)
    {
        $db = Database::openConnection();

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

    public function getCurrentStatus($order_id)
    {
        $db = Database::openConnection();
        return ($db->queryValue('orders', array('id' => $order_id), 'status_id'));
    }

    public function getStatusName($status_id)
    {
        $db = Database::openConnection();
        return ($db->queryValue('order_status', array('id' => $status_id), 'name'));
    }

    public function getItemCountForOrder($order_id)
    {
        $db = Database::openConnection();
        $cq = $db->queryRow("SELECT COALESCE(SUM(qty), 0) AS sum FROM orders_items WHERE order_id = :order_id AND is_kit = 0", array('order_id' => $order_id));
        return ($cq['sum']);
    }

    public function getSKUCountForOrder($order_id)
    {
        $db = Database::openConnection();
        $cq = $db->queryRow("SELECT COUNT(item_id) AS skus FROM orders_items WHERE order_id = :order_id AND is_kit = 0", array('order_id' => $order_id));
        return($cq['skus']);
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
            SELECT i.name, i.client_id, i.id AS item_id, i.sku, i.barcode, SUM(oi.qty) AS qty, oi.id as line_id, oi.order_id
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
            SELECT i.*, SUM(oi.qty) AS qty, oi.location_id, oi.item_id, oi.id AS line_id, oi.pod_id, oi.shopify_line_item_id, oi.ebay_line_item_id, il.qty AS location_qty, l.location
            FROM orders_items oi JOIN items i ON oi.item_id = i.id LEFT JOIN items_locations il on oi.location_id = il.location_id AND il.item_id = i.id JOIN locations l ON oi.location_id = l.id
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

    public function orderHasDangerousGoods($order_id)
    {
        $items = $this->getItemsForOrder($order_id);
        $contains_dangerous_goods = false;
        foreach($items as $i)
        {
            if($i['is_dangerous_good'] == 1)
            {
                $contains_dangerous_goods = true;
                break;
            }
        }
        return $contains_dangerous_goods;
    }

    public function getBackorderItemsForOrder($order_id)
    {
        $db = Database::openConnection();
        $location = new Location();
        return $db->queryData("
            SELECT
                i.*, SUM(oi.qty) AS required,
                oi.location_id,
                oi.item_id,
                oi.id AS line_id,
                oi.pod_id,
                l.location
            FROM
                orders_items oi
                JOIN items i ON oi.item_id = i.id
                LEFT JOIN items_locations il on il.item_id = i.id
                JOIN locations l ON oi.location_id = l.id
            WHERE
                oi.order_id = $order_id AND oi.location_id = {$location->backorders_id}
            GROUP BY
                oi.location_id, i.id
            ORDER BY
                i.name
        ");
    }

    public function getKitsandItemsForOrder($order_id, $is_kit)
    {
        $db = Database::openConnection();
        $q = "
            SELECT
                i.*, SUM(oi.qty) AS qty, oi.client_order_item_id
            FROM
                orders_items oi JOIN
                items i ON oi.item_id = i.id
            WHERE
                oi.order_id = $order_id
        ";
        if($is_kit)
        {
            $q .= "
                    AND
                    i.id NOT IN(
                        SELECT orders_items.item_id FROM orders_items WHERE orders_items.client_order_item_id != oi.client_order_item_id AND orders_items.is_kit != 1
                    )
            ";
        }
        else
        {
            $q .= "
                AND
                i.id IN(
                    SELECT orders_items.item_id FROM orders_items WHERE orders_items.client_order_item_id != oi.client_order_item_id AND orders_items.is_kit != 1
                )
            ";
        }
        $q .= "
            GROUP BY
                i.id
            ORDER BY
                i.name
        ";
        return $db->queryData($q);
    }

    public function getItemsCountForOrder($order_id, $picked = -1)
    {
        $db = Database::openConnection();
        $q = "
            SELECT i.*, SUM(oi.qty) AS qty, oi.client_order_item_id, oi.is_kit
            FROM orders_items oi JOIN items i ON oi.item_id = i.id
            WHERE oi.order_id = $order_id
            GROUP BY oi.client_order_item_id, i.id
        ";
        if($picked === 1)
            $q .= " AND oi.picked = 1";
        elseif($picked === 0)
            $q .= " AND oi.picked = 0";
        $q .= " ORDER BY oi.is_kit DESC, i.name";
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

    public function getProductionOrders($client_id, $status_id, $from, $to)
    {
        $db = Database::openConnection();
        $q = "SELECT o.*, c.client_name FROM orders o LEFT JOIN clients c ON o.client_id = c.id";
        $ws = " WHERE c.production_client = 1";
        if($client_id != 0)
        {
           $ws .= (strlen($ws) > 0)? " AND client_id = $client_id" : " WHERE client_id = $client_id";
        }
        if($status_id != 0)
        {
           $ws .= (strlen($ws) > 0)? " AND status_id = $status_id" : " WHERE status_id = $status_id";
        }
        $ws .= (strlen($ws) > 0)? " AND date_ordered >= $from" : " WHERE date_ordered >= $from";
        $ws .= (strlen($ws) > 0)? " AND date_ordered <= $to" : " WHERE date_ordered <= $to";
        $q .= $ws." ORDER BY date_ordered DESC";
        //die($q);
        return $db->queryData($q);
    }

    public function getUnfulfilledProductionOrders($client_id, $courier_id = -1, $state = "")
    {
        $db = Database::openConnection();
        $status_id = $this->fulfilled_id;
        $array = array();
        $q = "
            SELECT
                o.*, c.client_name
            FROM
                orders o LEFT JOIN clients c ON o.client_id = c.id
            WHERE
                c.production_client = 1 AND
                status_id != $status_id
        ";
        if($client_id > 0)
        {
            $q .= " AND client_id = $client_id";
        }
        if($courier_id >= 0)
        {
            $q .= " AND courier_id = $courier_id";
        }
        if(!empty($state))
        {
            $q .= " AND o.state = :state";
            $array['state'] = $state;
        }
        $q .= " ORDER BY errors DESC, client_id, courier_id ASC, country, consignment_id, date_ordered ASC";
        //echo "the array<pre>",print_r($array),"</pre>";
        //echo $q;die();
        return ($db->queryData($q, $array));
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
        //$q = "SELECT * FROM {$this->table} $status_clause";
        $q = "
            SELECT
                o.*,
                IFNULL(rs.id, 0) AS runsheet_id, IFNULL(rs.printed, 0) AS printed, rs.runsheet_day, IFNULL(rs.runsheet_completed, 0) AS runsheet_completed, rs.driver_id
            FROM
                orders o LEFT JOIN
                (SELECT runsheets.id, runsheet_tasks.printed, runsheet_tasks.order_id, runsheets.runsheet_day, runsheet_tasks.driver_id, runsheet_tasks.completed AS runsheet_completed FROM runsheets JOIN runsheet_tasks ON runsheets.id = runsheet_tasks.runsheet_id JOIN orders ON runsheet_tasks.order_id = orders.id) rs ON rs.order_id = o.id
            $status_clause
        ";
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
        $q .= " AND backorder_items = 0 AND cancelled = 0";
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

    public function getDailyOrderTrends($from, $to, $client_id = 0)
    {
        $db = Database::openConnection();
        $db->query("
            CREATE TEMPORARY TABLE date_list (id date Primary Key);
        ");
        $db->query("
            CALL filldates(DATE(timestamp(current_date) - INTERVAL 6 MONTH),DATE(timestamp(current_date) + INTERVAL 1 DAY));
        ");
        $query = "
            SELECT
                a.date AS TODAY,
                a.total_orders,
                ROUND(AVG(b.total_orders), 1) AS order_average
            FROM
            (
                SELECT
                    count(o.date_fulfilled) AS total_orders,
                    DATE(timestamp(current_date) - INTERVAL 3 MONTH) AS START_DAY,
                    DATE(timestamp(current_date)) AS END_DAY,
                    date_list.id AS date
                FROM
                    date_list LEFT JOIN
                    orders o ON DATE(FROM_UNIXTIME(o.date_fulfilled)) = date_list.id
                WHERE
                    WEEKDAY(date_list.id) < 5
                GROUP BY
                    date_list.id
                HAVING
                    date >= START_DAY AND date <= END_DAY
            )a JOIN
            (
                SELECT
                    count(o.date_fulfilled) AS total_orders,
                    DATE(timestamp(current_date) - INTERVAL 6 MONTH) AS START_DAY,
                    DATE(timestamp(current_date)) AS END_DAY,
                    date_list.id AS date
                FROM
                    date_list LEFT JOIN
                    orders o ON DATE(FROM_UNIXTIME(o.date_fulfilled)) = date_list.id
                WHERE
                    WEEKDAY(date_list.id) < 5
                GROUP BY
                    date_list.id
                HAVING
                    date >= START_DAY AND date <= END_DAY
            )b ON b.date BETWEEN (a.date - INTERVAL 3 MONTH) AND  a.date
            GROUP BY
                a.date
            ORDER BY
                a.date ASC
        ";
        $orders = $db->queryData($query);

        $return_array = array(
            array(
                'Date',
                'Total Orders Per Day',
                '3 Month Daily Average'
            )
        );

        foreach($orders as $o)
        {
            $row_array = array();
            $row_array[0] = $o['TODAY'];
            $row_array[1] = (int)$o['total_orders'];
            $row_array[2] = (float)$o['order_average'];
            $return_array[] = $row_array;
        }
        //print_r($return_array);
        return $return_array;
    }


    public function getWeeklyOrderTrends($from, $to, $client_id = 0)
    {
        $db = Database::openConnection();
        $db->query("
            CREATE TEMPORARY TABLE yw (id int Primary Key);
        ");
        $db->query("
            CALL fillyearweek(DATE(timestamp(current_date) - INTERVAL 6 MONTH),DATE(timestamp(current_date) + INTERVAL 1 DAY));
        ");
        $q = "
            SELECT
                a.MONDAY,
                a.total_orders,
                ROUND(AVG(b.total_orders), 1) AS order_average
            FROM
            (
                SELECT
                    count(o.date_fulfilled) AS total_orders,
                    STR_TO_DATE(  CONCAT(yw.id,' Monday'), '%X%V %W') AS MONDAY,
                    YEARWEEK(timestamp(current_date) - INTERVAL 3 MONTH) AS START_WEEK,
                    YEARWEEK(timestamp(current_date) + INTERVAL 1 DAY) AS END_WEEK,
                    yw.id AS year_week
                FROM
                    yw LEFT JOIN
                    orders o ON YEARWEEK(FROM_UNIXTIME(o.date_fulfilled)) = yw.id
        ";
        if($client_id > 0)
            $q .= "
                WHERE
                	o.client_id = 7
            ";
        $q .= "
                GROUP BY
                    yw.id
                HAVING
                    year_week >= START_WEEK AND year_week <= END_WEEK
            )a JOIN
            (
                SELECT
                    count(o.date_fulfilled) AS total_orders,
                    YEARWEEK(timestamp(current_date) - INTERVAL 6 MONTH) AS START_WEEK,
                    YEARWEEK(timestamp(current_date) + INTERVAL 1 DAY) AS END_WEEK,
                    yw.id AS year_week
                FROM
                    yw LEFT JOIN
                    orders o ON YEARWEEK(FROM_UNIXTIME(o.date_fulfilled)) = yw.id
        ";
        if($client_id > 0)
            $q .= "
                WHERE
                	o.client_id = 7
            ";
        $q .= "
                GROUP BY
                    yw.id
                HAVING
                    year_week >= START_WEEK AND year_week <= END_WEEK
            )b ON b.year_week BETWEEN YEARWEEK(STR_TO_DATE(  CONCAT(a.year_week,' Monday'), '%X%V %W') - INTERVAL 3 MONTH) AND  a.year_week
            GROUP BY
                a.year_week
            ORDER BY
                a.MONDAY ASC
        ";
        $orders = $db->queryData($q);
        $return_array = array(
            array(
                'Week Beginning',
                'Total Orders Per Week',
                '3 Month Weekly Average'
            )
        );

        foreach($orders as $o)
        {
            $row_array = array();
            $row_array[0] = $o['MONDAY'];
            $row_array[1] = (int)$o['total_orders'];
            $row_array[2] = (float)$o['order_average'];
            $return_array[] = $row_array;
        }
        //print_r($return_array);
        return $return_array;
    }

    public function getClientActivity($from, $to)
    {
        //$from += 24*60*60;
        //$to += 24*60*60;
        $from = strtotime('yesterday', strtotime('-3 months'));
        $to = strtotime("tomorrow");
        $db = Database::openConnection();
        $query1 = "
            SELECT
                count(*) as total_orders,
                o.client_id,
                c.client_name,
                o.date_fulfilled,
                DATE(FROM_UNIXTIME(o.date_fulfilled)) AS 'date_index'
            FROM
                orders o JOIN clients c ON o.client_id = c.id
            WHERE
                o.date_fulfilled >= $from AND o.date_fulfilled <= $to AND c.active = 1
            GROUP BY
                DATE(FROM_UNIXTIME(o.date_fulfilled)), o.client_id
            ORDER BY
                date_index, o.client_id
        ";
        //die($query1);
        $orders = $db->queryData($query1);
        //print_r($orders); die();
        $query2 = "
            SELECT
                count(*) as total_orders,
                o.client_id,
                c.client_name,
                o.date_fulfilled,
                DATE(FROM_UNIXTIME(o.date_fulfilled)) AS 'date_index'
            FROM
                solar_orders o JOIN clients c ON o.client_id = c.id
            WHERE
                o.date_fulfilled >= $from AND o.date_fulfilled <= $to AND c.active = 1
            GROUP BY
                DATE(FROM_UNIXTIME(o.date_fulfilled)), o.client_id
            ORDER BY
                date_index, o.client_id
        ";
        $solar_orders = $db->queryData($query2);
        //print_r($solar_orders);
        $query3 = "
            SELECT
                count(*) as total_orders,
                o.client_id,
                c.client_name,
                o.date_completed,
                DATE(FROM_UNIXTIME(o.date_completed)) AS 'date_index'
            FROM
                solar_service_jobs o JOIN clients c ON o.client_id = c.id
            WHERE
                o.date_completed >= $from AND o.date_completed <= $to AND c.active = 1
            GROUP BY
                DATE(FROM_UNIXTIME(o.date_completed)), o.client_id
            ORDER BY
                date_index, o.client_id
        ";
        $solar_service_jobs = $db->queryData($query3);
        //print_r($solar_service_jobs);
        $clients = $db->queryData("SELECT id, client_name FROM clients WHERE active = 1 AND pick_pack = 1 ORDER BY client_name");
        $return_array = array();
        $array = array('Date');
        foreach($clients as $c)
        {
            $array[] = $c['client_name'];
        }
        $return_array[] = $array;
        $day_array = array();
        foreach($clients as $c)
        {
            foreach($orders as $o)
            {
                if(!isset($day_array[$o['date_index']]))
                    $day_array[$o['date_index']] = array();
                if(!isset($day_array[$o['date_index']][$c['id']]))
                {
                    $day_array[$o['date_index']][$c['id']] = 0;
                    if($c['id'] == $o['client_id'])
                        $day_array[$o['date_index']][$c['id']] += $o['total_orders'];
                }
                elseif($c['id'] == $o['client_id'])
                    $day_array[$o['date_index']][$c['id']] += $o['total_orders'];
            }
            foreach($solar_orders as $o)
            {
                if(!isset($day_array[$o['date_index']]))
                    $day_array[$o['date_index']] = array();
                if(!isset($day_array[$o['date_index']][$c['id']]))
                {
                    $day_array[$o['date_index']][$c['id']] = 0;
                    if($c['id'] == $o['client_id'])
                        $day_array[$o['date_index']][$c['id']] += $o['total_orders'];
                }
                elseif($c['id'] == $o['client_id'])
                    $day_array[$o['date_index']][$c['id']] += $o['total_orders'];
            }
            foreach($solar_service_jobs as $o)
            {
                if(!isset($day_array[$o['date_index']]))
                    $day_array[$o['date_index']] = array();
                if(!isset($day_array[$o['date_index']][$c['id']]))
                {
                    $day_array[$o['date_index']][$c['id']] = 0;
                    if($c['id'] == $o['client_id'])
                        $day_array[$o['date_index']][$c['id']] += $o['total_orders'];
                }
                elseif($c['id'] == $o['client_id'])
                    $day_array[$o['date_index']][$c['id']] += $o['total_orders'];
            }
        }
        //print_r($day_array);
        foreach($day_array as $date => $orders)
        {
            $a = array($date);
            foreach($orders as $cid => $to)
            {
                $a[] = $to;
            }
            $return_array[] = $a;
        }
        //print_r($return_array); die();
        return $return_array;
    }

    public function getPickErrors($from, $to, $client_id = 0)
    {
        $from = strtotime('yesterday', strtotime('-3 months'));
        $to = strtotime("tomorrow", strtotime('this Friday'));
        $db = Database::openConnection();

        $query1 = "
            SELECT
                (a.total_orders) AS total_orders,
                a.friday
                FROM
                (
                    SELECT
                        count(*) as total_orders,
                        MAX(`date_fulfilled`) AS 'friday',
                        date_fulfilled
                    FROM
                        orders
                    WHERE
                        date_fulfilled >= $from AND date_fulfilled <= $to
                    GROUP BY
                        UNIX_TIMESTAMP(FROM_DAYS(TO_DAYS(DATE_FORMAT(FROM_UNIXTIME(date_fulfilled), '%Y-%m-%d')) - MOD( TO_DAYS( DATE_FORMAT(FROM_UNIXTIME(date_fulfilled), '%Y-%m-%d') ) -7, 7 )))
                ) a
                WHERE
                    a.date_fulfilled >= $from AND a.date_fulfilled <= $to
        ";


        //if($client_id > 0)
            //$query1 .= " AND client_id = ".$client_id;
        $query1 .= "  GROUP BY
                    UNIX_TIMESTAMP(FROM_DAYS(TO_DAYS(DATE_FORMAT(FROM_UNIXTIME(a.date_fulfilled), '%Y-%m-%d')) - MOD( TO_DAYS( DATE_FORMAT(FROM_UNIXTIME(a.date_fulfilled), '%Y-%m-%d') ) -7, 7 )))";
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
        $from = strtotime('yesterday', strtotime('-3 months'));
        $to = strtotime("tomorrow");
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
                    count(*) as order_count, c.client_name, o.client_id, c.logo
                from
                    orders o join clients c on o.client_id = c.id
                where
                    o.status_id != {$this->fulfilled_id} and c.active = 1 and o.store_order = $store_order and o.backorder_items = 0 AND o.cancelled = 0
                group by
                    o.client_id
                order by
                    c.client_name";

        return $db->queryData($q);
    }

    public function getCurrentProductionOrders()
    {
        $db = Database::openConnection();
        $q = "  select
                    count(*) as order_count, c.client_name, o.client_id
                from
                    orders o join clients c on o.client_id = c.id
                where
                    o.status_id != {$this->fulfilled_id} and c.active = 1 and c.production_client = 1 AND o.cancelled = 0
                group by
                    o.client_id
                order by
                    c.client_name";

        return $db->queryData($q);
    }

    public function getCurrentBackorderOrders()
    {
        $db = Database::openConnection();
        $q = "  select
                    count(*) as order_count, c.client_name, o.client_id, c.logo
                from
                    orders o join clients c on o.client_id = c.id
                where
                    o.status_id != {$this->fulfilled_id} and c.active = 1 and o.backorder_items = 1
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

    public function getOrdersForClient($client_id, $from, $to, $dispatched = -1)
    {
        $db = Database::openConnection();
        $q = "
            SELECT o.*, c.name AS cname
            FROM orders o LEFT JOIN couriers c ON o.courier_id = c.id
            WHERE
                client_id = $client_id AND
                date_ordered >= $from AND
                date_ordered <= $to AND
                cancelled = 0";
        if($dispatched >= 0)
        {
            $q .= ($dispatched == 1)? " AND status_id = 4 " : " AND status_id != 4 ";
        }
        $q .= " ORDER BY date_ordered DESC
        ";


        return $db->queryData($q);
    }

    public function setSlipPrinted($id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table,'slip_printed', 1, $id);
    }

    public function updateStatus($status_id, $id)
    {
        $db = Database::openConnection();
        if($this->getCurrentStatus($id) != $this->fulfilled_id)
        {
            $db->updateDatabaseField($this->table,'status_id', $status_id, $id);
        }
    }

    public function updateFTPUploaded($order_id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table,'ftp_uploaded', 1, $order_id);
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

    public function getItemsWithSerialForOrder($id)
    {
        $db = Database::openConnection();

        $q = "
            SELECT
                ois.serial_number, i.name, i.sku
            FROM
                order_item_serials ois JOIN items i ON ois.item_id = i.id
            WHERE
                ois.order_id = $id
            ORDER BY
                i.name
        ";
        return $db->queryData($q);
    }
}
