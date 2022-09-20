<?php

 /**
  * Item Class
  *

    FUNCTIONS

  addItem($data)
  addPackingTypesForItem($types, $item_id)
  barcodeTaken($barcode, $current_barcode = false)
  boxBarcodeTaken($barcode, $current_barcode = false)
  breakPacks($post_data, $returns)
  checkBarcodes($barcode, $current_barcode)
  checkBoxBarcodes($barcode, $current_barcode)
  checkSkus($sku, $current_sku)
  clientItemAdd($data)
  editItem($data)
  getAllocatedStock($item_id, $fulfilled_id)
  getAllocatedStockForLocation($location_id)
  getAutocompleteItems($data, $fulfilled_id)
  getAvailableInLocation($item_id, $location_id)
  getAvailableLocationsForItem($item_id, $qty, $pallet = false, $allocation = true)
  getAvailableStock($item_id, $fulfilled_id)
  getBayUsage($item_id)
  getClientInventory($client_id, $active = 1)
  getAllClientsBayUsage()
  getItemByBarcode($barcode)
  getItemById($item_id)
  getItemBySku($sku)
  getItemByBoxBarcode($barcode)
  getItemsForClient($client_id, $active = 1)
  getItemForClientByBarcode($array)
  getLocationForItem($item_id, $location_id)
  getLocationsForItem($item_id)
  getLowStock($item_id)
  getPackItemDetails($item_id)
  getPackagingTypes()
  getPackingTypesForItem($item_id)
  getPalletCountSelect($item_id)
  getPreferredPickLocationId($item_id)
  getSelectCollectionItems($selected = false)
  getSelectPackitems($selected = false)
  getStockOnHand($item_id)
  getStockUnderQC($item_id)
  isCollection($item_id)
  isDoubleBayItem($item_id)
  isPalletItem($item_id)
  makePackes($data)
  moveStock($data)
  skuTaken($sku, $current_sku = false)
  updateCollection($items, $item_id)
  updatePackItem($items, $id),
  updateWarningLevel($data)

  * @author     Mark Solly <mark.solly@fsg.com.au>
  */

class Item extends Model{

    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "items";
    public $packaging_types = array();
    public $solar_client_ids = array(
        67  //TLJ Solar
    );

    public function __construct()
    {
        $this->getPackagingTypes();
    }

    public function getArccosInventory()
    {
        $db = Database::openConnection();

        $q = "
                SELECT
                    a.location_id,
                    IFNULL(a.qty,0) as qty,
                    IFNULL(a.qc_count, 0) AS qc_count,
                    IFNULL(b.allocated,0) AS allocated,
                    a.name,
                    a.sku,
                    a.location,
                    a.collection
                FROM
                (
                    SELECT
                        l.id AS location_id, SUM(il.qty) AS qty, SUM(il.qc_count) AS qc_count, i.id AS item_id, i.name, i.sku, l.location, i.collection
                    FROM
                        items i LEFT JOIN items_locations il ON i.id = il.item_id LEFT JOIN locations l ON il.location_id = l.id
                    WHERE
                        i.client_id = 87 AND i.active = 1 AND i.is_arccos = 1 AND i.collection = 0
                    GROUP BY
                        il.item_id
                ) a
                LEFT JOIN
                (
                    SELECT
                        COALESCE(SUM(oi.qty),0) AS allocated, oi.item_id, oi.location_id
                    FROM
                        orders_items oi JOIN orders o ON oi.order_id = o.id
                    WHERE
                        o.status_id != 4 AND o.cancelled = 0 AND o.client_id = 87
                    GROUP BY
                        oi.location_id, oi.item_id
                ) b
                ON a.item_id = b.item_id AND a.location_id = b.location_id
                ORDER BY a.name
        ";

        return $db->queryData($q);
    }

    public function clientEditItem($data)
    {
        //echo "Edit Item<pre>",print_r($data),"</pre>";die();
        foreach($data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $db = Database::openConnection();
        $item_values = array(
        	'name'	    =>	$name,
            'image'     =>  NULL,
            'is_pod'    => 0
        );
        if( isset($is_pod) ) $item_values['is_pod'] = 1;
        if(!empty($client_product_id)) $item_values['client_product_id'] = $client_product_id;
        if(!empty($image)) $item_values['image'] = $image;
        $db->updateDatabaseFields('items', $item_values, $item_id);
        return true;
    }

    public function recordData($data)
    {
        $db = Database::openConnection();
        $id = $db->insertQuery($this->table, $data);
        return $id;
    }

    public function getPalletCountSelect($item_id, $order_id = 0)
    {
        $db = Database::openConnection();
        if($this->isSolarItem($item_id))
        {
            $orders_table = "solar_orders";
            $items_table = "solar_orders_items";
        }
        else
        {
            $orders_table = "orders";
            $items_table = "orders_items";
        }
        $q = "
            SELECT DISTINCT ( a.available - IFNULL(b.qty, 0) ) AS available
            FROM
            (
                SELECT (il.qty - il.qc_count) AS available, il.location_id
                FROM items_locations il
                WHERE il.item_id = $item_id
            ) a
            LEFT JOIN
            (
                SELECT oi.qty, oi.location_id
                FROM $items_table oi JOIN $orders_table o ON oi.order_id = o.id
                WHERE o.status_id != 4
        ";
        if($order_id > 0)
        {
            $q .= " AND o.id != $order_id";
        }
        $q .="    ) b
            ON a.location_id = b.location_id
            ORDER BY available DESC
        ";
        return ($db->queryData($q));
    }

    private function getPackagingTypes()
    {
        $db = Database::openConnection();
        $types = $db->queryData("SELECT id, name FROM packing_types WHERE active = 1 ORDER BY name");
        foreach($types as $type)
        {
            $this->packaging_types[$type['id']] = $type['name'];
        }
    }

    public function getClientInventoryLocationCounts($client_id, $active = 1)
    {
        $db = Database::openConnection();
        $q = "
            SELECT
                IFNULL( SUM(a.qty),0) as on_hand,
                IFNULL( SUM(a.qc_count), 0) AS qc_count,
                IFNULL( SUM(b.allocated), 0) AS allocated,
                a.name, a.client_product_id, a.sku, a.barcode, a.item_id, a.pack_item, a.width, a.depth, a.height, a.weight, a.low_stock_warning, a.oversize, a.image,
                GROUP_CONCAT(
                    IFNULL(a.location_id,0),',',
                    IFNULL(a.location,''),',',
                    IFNULL(a.qty,''),',',
                    IFNULL(a.qc_count,''),',',
                    IFNULL(b.allocated,''),','
                    SEPARATOR '|'
                ) AS locations
            FROM
                (
                    SELECT
                        l.id AS location_id, il.qty, il.qc_count, i.client_product_id, i.id AS item_id,
                        i.name, i.sku, i.barcode, l.location, i.pack_item, i.width, i.depth, i.height, i.weight, i.low_stock_warning, l.oversize, i.image
                    FROM
                        items i LEFT JOIN
                        items_locations il ON i.id = il.item_id LEFT JOIN
                        locations l ON il.location_id = l.id
                    WHERE
                        i.client_id = $client_id AND i.active = $active
                ) a
                LEFT JOIN
                (
                    SELECT
                        COALESCE(SUM(oi.qty),0) AS allocated, oi.item_id, oi.location_id
                    FROM
                        orders_items oi JOIN
                        orders o ON oi.order_id = o.id JOIN
                        items i ON oi.item_id = i.id
                    WHERE
                        o.status_id != 4
                    GROUP BY
                    oi.location_id, oi.item_id
                ) b ON a.item_id = b.item_id AND a.location_id = b.location_id
            GROUP BY
                a.item_id
            ORDER BY
                a.name
        ";
        return $db->queryData($q);
    }

    public function getClientInventoryArray($client_id, $active = 1)
    {
        //Logger::logDataTablesCalls("datatables_test", "Starting getting the items at ".date("H:i:s"));
        $items = $this->getClientInventory($client_id, $active);
        //Logger::logDataTablesCalls("datatables_test", "Got the items at ".date("H:i:s"));
        $rows = array();
        $c = 1;
        //Logger::logDataTablesCalls("datatables_test", "Starting foreach items at ".date("H:i:s"));
        foreach($items as $i)
        {
            //if(is_null($i['location_id']))
                //continue;
            if(!isset($rows[$i['item_id']]))
            {
                $rows[$i['item_id']] = array(
                    'name'              => $i['name'],
                    'sku'               => $i['sku'],
                    'barcode'           => $i['barcode'],
                    'client_product_id' => $i['client_product_id'],
                    'weight'            => $i['weight'],
                    'depth'             => $i['depth'],
                    'width'             => $i['width'],
                    'height'            => $i['height'],
                    'low_stock_warning' => $i['low_stock_warning'],
                    'image'             => $i['image'],
                    'onhand'            => 0,
                    'allocated'         => 0,
                    'qc_count'          => 0,
                    'locations'         => array()
                );
            }
            $rows[$i['item_id']]['onhand'] += $i['qty'];
            $rows[$i['item_id']]['allocated'] += $i['allocated'];
            $rows[$i['item_id']]['qc_count'] += $i['qc_count'];
            $rows[$i['item_id']]['locations'][$i['location_id']]['name'] = $i['location'];
            $rows[$i['item_id']]['locations'][$i['location_id']]['onhand'] = $i['qty'];
            $rows[$i['item_id']]['locations'][$i['location_id']]['allocated'] = $i['allocated'];
            $rows[$i['item_id']]['locations'][$i['location_id']]['qc_count'] = $i['qc_count'];
            $rows[$i['item_id']]['locations'][$i['location_id']]['oversize'] = $i['oversize'];
            //Logger::logDataTablesCalls("datatables_test", date("H:i:s")." row $c done");
            ++$c;
        }
        //Logger::logDataTablesCalls("datatables_test", date("H:i:s")." will return all rows");
        return $rows;
    }

    public function getClientInventoryForCSV($client_id, $active = 1)
    {
        $db = Database::openConnection();

        $q = "
            SELECT
                IFNULL( SUM(a.qty),0) AS on_hand,
                IFNULL( SUM(a.qc_count), 0) AS qc_count,
                IFNULL( SUM(b.allocated), 0) AS allocated,
                ( IFNULL( SUM(a.qty),0) - IFNULL( SUM(a.qc_count), 0) - IFNULL( SUM(b.allocated), 0) ) AS available,
                a.name AS item_name,
                a.sku AS sku,
                a.barcode AS barcode,
                a.client_product_id AS client_product_id,
                a.item_id AS item_id,
                a.image,
                a.width,
                a.depth,
                a.weight,
                a.low_stock_warning,
                GROUP_CONCAT(
                    IFNULL(a.location_id,0),',',
                    IFNULL(a.location,''),',',
                    IFNULL(a.qty,''),',',
                    IFNULL(a.qc_count,''),',',
                    IFNULL(b.allocated,''),','
                    SEPARATOR '|'
                ) AS locations,
                (SELECT COUNT(*) FROM items_locations JOIN locations ON locations.id = items_locations.location_id WHERE item_id = a.item_id AND locations.tray = 0) AS bays,
                (SELECT COUNT(*) FROM items_locations JOIN locations ON locations.id = items_locations.location_id WHERE item_id = a.item_id AND locations.tray = 1) AS trays
            FROM
                (
                    SELECT
                        l.id AS location_id, il.qty, il.qc_count, i.client_product_id, i.id AS item_id,
                        i.name, i.sku, i.barcode, l.location, i.pack_item, i.width, i.depth, i.height, i.weight, i.low_stock_warning, l.oversize, i.image
                    FROM
                        items i LEFT JOIN
                        items_locations il ON i.id = il.item_id LEFT JOIN
                        locations l ON il.location_id = l.id
                    WHERE
                        i.client_id = $client_id AND i.active = $active
                ) a
                LEFT JOIN
                (
                    (SELECT
                    	COALESCE(SUM(oi.qty),0) AS allocated, oi.item_id, oi.location_id
                    FROM
                        orders_items oi JOIN
                        orders o ON oi.order_id = o.id
                    WHERE
                    	o.status_id != 4 AND o.cancelled = 0
                    GROUP BY
                    	oi.location_id, oi.item_id)
                    UNION ALL
                    (SELECT
                    	COALESCE(SUM(di.qty),0) AS allocated, di.item_id, di.location_id
                    FROM
                        deliveries_items di JOIN
                        deliveries d ON di.deliveries_id = d.id
                    WHERE
                    	d.status_id != 5
                    GROUP BY
                    	di.location_id, di.item_id)
                ) b ON a.item_id = b.item_id AND a.location_id = b.location_id
            GROUP BY a.item_id
        ";
        return $db->queryData($q); 
    }

    public function getClientInventory($client_id, $active = 1)
    {
        $db = Database::openConnection();
        if(in_array($client_id, $this->solar_client_ids))
        {
            $orders_table = "solar_orders";
            $items_table = "solar_orders_items";
        }
        else
        {
            $orders_table = "orders";
            $items_table = "orders_items";
        }
        $q = "  SELECT
                    a.location_id, IFNULL(a.qty,0) as qty, IFNULL(a.qc_count, 0) AS qc_count, IFNULL(b.allocated,0) AS allocated, a.name, a.client_product_id, a.sku, a.barcode, a.item_id, a.location, a.pack_item, a.width, a.depth, a.height, a.weight, a.low_stock_warning, a.oversize, a.image
                FROM
                (
                    SELECT
                        l.id AS location_id, il.qty, il.qc_count, i.client_product_id, i.id AS item_id, i.name, i.sku, i.barcode, l.location, i.pack_item, i.width, i.depth, i.height, i.weight, i.low_stock_warning, l.oversize, i.image
                    FROM
                        items i LEFT JOIN items_locations il ON i.id = il.item_id LEFT JOIN locations l ON il.location_id = l.id
                    WHERE
                        i.client_id = $client_id AND i.active = $active
                ) a
                LEFT JOIN
                (
                    SELECT
                        COALESCE(SUM(oi.qty),0) AS allocated, oi.item_id, oi.location_id
                    FROM
                        $items_table oi JOIN $orders_table o ON oi.order_id = o.id
                    WHERE
                        o.status_id != 4 AND o.cancelled = 0 AND o.client_id = $client_id
                    GROUP BY
                        oi.location_id, oi.item_id
                ) b
                ON a.item_id = b.item_id AND a.location_id = b.location_id
                ORDER BY a.name";
        //$q .= $this->constructQuery($orders_table, $items_table);
        //$q .= "ORDER BY a.name";
        return $db->queryData($q);
    }

    public function addStockToLocation($item_id, $location_id, $qty)
    {
        $db = Database::openConnection();
        if($updater = $db->queryValue('items_locations', array('item_id' => $item_id, 'location_id' => $location_id)))
        {
            $db->query("UPDATE items_locations SET qty = qty + $qty WHERE id = $updater");
        }
        else
        {
            $vals = array(
                'item_id'       =>  $item_id,
                'location_id'   =>  $location_id,
                'qty'           =>  $qty
            );
            $db->insertQuery('items_locations', $vals);
        }
    }

    public function moveStock($data, $reason_id)
    {
        $db = Database::openConnection();
        //echo "<pre>",print_r($data),"</pre>"; die();
        $data['reference'] = "Internal Stock Movement";
        $data['reason_id'] = $reason_id;
        $location = new Location();
        //remove the stock
        $data['subtract_product_id'] = $data['move_product_id'];
        $data['qty_subtract'] = $data['qty_move'];
        $data['subtract_from_location'] = $data['move_from_location'];
        $location->subtractFromLocation($data);
        //add the stock
        $data['add_product_id'] = $data['move_product_id'];
        $data['qty_add'] = $data['qty_move'];
        $data['add_to_location'] = $data['move_to_location'];
        $location->addToLocation($data);
        $this->cleanUpItemsLocations();
        return true;
    }

    public function updateWarningLevel($data)
    {
        $db = Database::openConnection();
        $val = ($data['value'] == 0)? NULL: $data['value'];
        $db->updateDatabaseField($this->table, 'low_stock_warning', $val, $data['product_id']);
        return true;
    }

    public function getBayUsage($item_id)
    {
        $db = Database::openConnection();
        $query = "SELECT COUNT(*) AS `usage` FROM items_locations il JOIN locations l ON l.id = il.location_id WHERE item_id = $item_id AND l.tray = 0";
        $r = $db->queryRow($query);
        return ($r['usage']);
    }

    public function getTrayUsage($item_id)
    {
        $db = Database::openConnection();
        $query = "SELECT COUNT(*) AS `usage` FROM items_locations il JOIN locations l ON l.id = il.location_id WHERE item_id = $item_id AND l.tray = 1";
        $r = $db->queryRow($query);
        return ($r['usage']);
    }

    public function getItemByBoxBarcode($barcode)
    {
        $db = Database::openConnection();

        return $db->queryRow("
            SELECT
                *
            FROM
                items
            WHERE
                box_barcode = :barcode",
            array(
                'barcode'     =>  $barcode
            )
        );
    }

    public function getItemByBarcode($barcode)
    {
        $db = Database::openConnection();

        return $db->queryRow("
            SELECT
                *
            FROM
                items
            WHERE
                barcode = :barcode",
            array(
                'barcode'     =>  $barcode
            )
        );
    }

    public function getItemForClientByBarcode($array, $active = 1)
    {
        $db = Database::openConnection();
        $q = "SELECT * FROM items WHERE (barcode = :barcode OR sku = :sku) AND client_id = :client_id";
        $a = array(
            'barcode'   =>  $array['barcode'],
            'sku'       =>  $array['barcode'],
            'client_id' =>  $array['client_id']
        );
        if($active >= 0)
        {
            $q .= " AND active = :active";
            $a['active'] = $active;
        }
        $item = $db->queryRow($q, $a);

        return $item;
    }

    public function getPodItemsForClientByBarcode($array)
    {
        $db = Database::openConnection();

        $q = "
            SELECT
                i.id as item_id, i.name, i.client_product_id, i.sku, i.barcode, i.image,
                oi.id AS order_item_id, oi.qty, oi.order_id, oi.pod_id,
                o.order_number, o.client_order_id
            FROM
                items i JOIN
                orders_items oi ON i.id = oi.item_id JOIN
                orders o ON oi.order_id = o.id
            WHERE
                (i.barcode = :barcode OR i.sku = :sku) AND
                i.is_pod = 1 AND
                i.active = 1 AND
                oi.pod_id = :pod_id
        ";
        $bindings = array(
            'barcode'   => $array['barcode'],
            'sku'       => $array['barcode'],
            'pod_id'    => $array['pod_invoice']
        );
        if($array['order_id'] > 0)
        {
            $q .= "
                AND oi.order_id = :order_id
            ";
            $bindings['order_id'] = $array['order_id'];
        }
        $items = $db->queryData($q, $bindings);

        return $items;
    }

    public function makePacks($data, $items)
    {
        $db = Database::openConnection();
        $pack_item_id = $data['add_product_id'];
        $make_to_location = $data['add_to_location'];
        $make_count = $data['make_count'];
        $item = $this->getItemById($pack_item_id);
        //make the packs
        if($updater = $db->queryValue('items_locations', array('location_id' => $make_to_location, 'item_id' => $pack_item_id)))
        {
            $db->query("UPDATE items_locations SET qty = qty + $make_count WHERE id = $updater");
        }
        else
        {
            $db->insertQuery('items_locations', array(
                'item_id'       =>  $pack_item_id,
                'location_id'   =>  $make_to_location,
                'qty'           =>  $make_count
            ));
        }
        //record the movement
        $db->insertQuery('items_movement', array(
            'item_id'       =>  $pack_item_id,
            'location_id'   =>  $make_to_location,
            'qty_in'        =>  $make_count,
            'reference'     =>  'Making '.$item['name'].' packs',
            'reason_id'     =>  28,
            'date'          =>  time(),
            'entered_by'    =>  Session::getUserId()
        ));
        //remove individual items from inventory
        foreach($items as $i)
        {
            $db->query("UPDATE items_locations SET qty = qty - {$i['qty']} WHERE item_id = {$i['item_id']} AND location_id = {$i['location_id']}");
            //record the movement
            $db->insertQuery('items_movement', array(
                'item_id'       =>  $i['item_id'],
                'location_id'   =>  $i['location_id'],
                'qty_out'       =>  $i['qty'],
                'reference'     =>  'Making '.$item['name'].' packs',
                'reason_id'     =>  28,
                'date'          =>  time(),
                'entered_by'    =>  Session::getUserId()
            ));
        }
        return true;
    }

    public function breakPacks($post_data, $returns)
    {
        $db = Database::openConnection();
        $pack_item_id = $post_data['break_product_id'];
        $break_count = $post_data['break_count'];
        //remove packs from inventory
        $item = $this->getItemById($pack_item_id);
        $ppl_id = $item['preferred_pick_location_id'];
        if($ppl_id > 0)
        {
            $count = $db->queryValue('items_locations', array('location_id' => $ppl_id, 'item_id' => $pack_item_id), 'qty');
            if($count < $break_count)
            {
                if($count > 0)
                {
                    $db->query("UPDATE items_locations SET qty = qty - $count WHERE location_id = $ppl_id AND item_id = $pack_item_id");
                    //record the movement
                    $db->insertQuery('items_movement', array(
                        'item_id'       =>  $pack_item_id,
                        'location_id'   =>  $ppl_id,
                        'qty_out'       =>  $count,
                        'reference'     =>  'Breaking '.$item['name'].' packs',
                        'reason_id'     =>  29,
                        'date'          =>  time(),
                        'entered_by'    =>  Session::getUserId()
                    ));
                    $break_count -= $count;
                }
            }
            else
            {
                $db->query("UPDATE items_locations SET qty = qty - $break_count WHERE location_id = $ppl_id AND item_id = $pack_item_id");
                //record the movement
                $db->insertQuery('items_movement', array(
                    'item_id'       =>  $pack_item_id,
                    'location_id'   =>  $ppl_id,
                    'qty_out'       =>  $break_count,
                    'reference'     =>  'Breaking '.$item['name'].' packs',
                    'reason_id'     =>  29,
                    'date'          =>  time(),
                    'entered_by'    =>  Session::getUserId()
                ));
                $break_count = 0;
            }
        }
        if($break_count > 0)
        {
            $locations = $this->getLocationsForItem($pack_item_id);
            foreach($locations as $l)
            {
                $l_qty = $l['qty'];
                $location_id = $l['location_id'];
                if($l_qty > 0)
                {
                    $remove = min($l_qty, $break_count);
                    $db->query("UPDATE items_locations SET qty = qty - $remove WHERE location_id = $location_id AND item_id = $pack_item_id");
                    //record the movement
                    $db->insertQuery('items_movement', array(
                        'item_id'       =>  $pack_item_id,
                        'location_id'   =>  $location_id,
                        'qty_out'       =>  $remove,
                        'reference'     =>  'Breaking '.$item['name'].' packs',
                        'reason_id'     =>  29,
                        'date'          =>  time(),
                        'entered_by'    =>  Session::getUserId()
                    ));
                    $break_count -= $remove;
                }
                if($break_count <= 0)
                {
                    break;
                }
            }
        }
        //add items to inventory
        foreach($returns as $ret)
        {
            if($updater = $db->queryValue('items_locations', array('location_id' => $ret['location_id'], 'item_id' => $ret['item_id'])))
            {
                $db->query("UPDATE items_locations SET qty = qty + {$ret['qty']} WHERE id = $updater");
            }
            else
            {
                $db->insertQuery('items_locations', array(
                    'item_id'       =>  $ret['item_id'],
                    'location_id'   =>  $ret['location_id'],
                    'qty'           =>  $ret['qty']
                ));
            }
            //record the movement
            $db->insertQuery('items_movement', array(
                'item_id'       =>  $ret['item_id'],
                'location_id'   =>  $ret['location_id'],
                'qty_in'        =>  $ret['qty'],
                'reference'     =>  'Breaking '.$item['name'].' packs',
                'reason_id'     =>  29,
                'date'          =>  time(),
                'entered_by'    =>  Session::getUserId()
            ));
        }

        $this->cleanUpItemsLocations();
        return true;
    }

    public function getAvailableInLocation($item_id, $location_id)
    {
        $db = database::openConnection();
        if($this->isSolarItem($item_id))
        {
            $orders_table = "solar_orders";
            $items_table = "solar_orders_items";
        }
        else
        {
            $orders_table = "orders";
            $items_table = "orders_items";
        }
        $res = $db->queryRow("
            SELECT
            (a.qty - IFNULL(SUM(b.qty),0) - IFNULL(SUM(c.qty),0)) AS available
            FROM
                (
                    SELECT
                        (qty - qc_count) as qty, item_id, location_id
                    FROM
                        items_locations
                    WHERE
                        item_id = $item_id AND location_id = $location_id
                ) a
                LEFT JOIN
                (
                    SELECT
                        qty, location_id, item_id, order_id
                    FROM
                        $items_table
                    WHERE
                        order_id NOT IN(SELECT id FROM $orders_table WHERE status_id = 4)
                ) b
                ON a.item_id = b.item_id AND a.location_id = b.location_id
                LEFT JOIN
                (
                    SELECT
                        qty, location_id, item_id, job_id
                    FROM
                        solar_service_jobs_items
                    WHERE
                        job_id NOT IN(SELECT id FROM solar_service_jobs WHERE status_id = 4)
                ) c
                ON a.item_id = c.item_id AND a.location_id = c.location_id
            GROUP BY
                a.location_id
        ");

        return $res['available'];
    }

    public function getPreferredPickLocationId($item_id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' => $item_id), "preferred_pick_location_id");
    }

    public function updatePackItem($items, $item_id)
    {
        $db = Database::openConnection();

        //delete previous entries
            $db->deleteQuery('pack_items', $item_id, 'item_id');
            //add the new ones
            foreach($items as $id => $array)
            {
                $number = $array['qty'];
                $values = array(
                    'item_id'           =>  $item_id,
                    'linked_item_id'    =>  $id,
                    'number'            =>  $number
                );
                $db->insertQuery('pack_items', $values);
            }
    }

    public function updateCollection($items, $item_id)
    {
        $db = Database::openConnection();

        //delete previous entries
            $db->deleteQuery('collections', $item_id, 'item_id');
            //add the new ones
            foreach($items as $id => $array)
            {
                $number = $array['qty'];
                $values = array(
                    'item_id'           =>  $item_id,
                    'linked_item_id'    =>  $id,
                    'number'            =>  $number
                );
                $db->insertQuery('collections', $values);
            }
    }

    public function getSelectPackitems($selected = false)
    {
        $db = Database::openConnection();
        $return_string = "";
        $items = $db->queryData("SELECT * FROM items WHERE pack_item = 1 ORDER BY name");
        foreach($items as $i)
        {
            $return_string .= "<option value='{$i['id']}'";
            if($selected && $selected == $i['id'])
        	{
        		$return_string .= "selected='selected' ";
        	}
            $return_string .= ">{$i['name']} ({$i['sku']})</option>";
        }
        return $return_string;
    }

    public function getSelectCollectionItems($selected = false)
    {
        $db = Database::openConnection();
        $return_string = "";
        $items = $db->queryData("
            SELECT
                i.id, i.name, i.sku
            FROM
                items i JOIN clients c ON i.client_id = c.id
            WHERE
                i.collection = 1 AND i.active = 1 AND c.active = 1
            ORDER BY
                i.name
        ");
        foreach($items as $i)
        {
            $return_string .= "<option value='{$i['id']}'";
            if($selected && $selected == $i['id'])
        	{
        		$return_string .= "selected='selected' ";
        	}
            $return_string .= ">{$i['name']} ({$i['sku']})</option>";
        }
        return $return_string;
    }

    public function getSelectCollectionItemsByClient($client_id = 0,$selected = false)
    {
        $db = Database::openConnection();
        $return_string = "";
        $items = $db->queryData("
            SELECT
                i.id, i.name, i.sku
            FROM
                items i JOIN clients c ON i.client_id = c.id
            WHERE
                i.collection = 1 AND i.active = 1 AND c.active = 1 AND i.client_id = $client_id
            ORDER BY
                i.name
        ");
        foreach($items as $i)
        {
            $return_string .= "<option value='{$i['id']}'";
            if($selected && $selected == $i['id'])
        	{
        		$return_string .= "selected='selected' ";
        	}
            $return_string .= ">{$i['name']} ({$i['sku']})</option>";
        }
        return $return_string;
    }

    public function getPackItemDetails($item_id)
    {
        $db = Database::openConnection();

        return $db->queryData("SELECT * FROM pack_items pi JOIN items i ON pi.linked_item_id = i.id WHERE item_id = $item_id");
    }

    public function getCollectionDetails($item_id)
    {
        $db = Database::openConnection();

        return $db->queryData("SELECT * FROM collections c JOIN items i ON c.linked_item_id = i.id WHERE item_id = $item_id ORDER BY i.name");
    }

    public function getAutocompleteAllItems($data, $fulfilled_id)
    {
        $db = Database::openConnection();
        $return_array = array();
        $q = $data["item"];
        $client_id = $data['clientid'];
        $query = "SELECT * FROM items WHERE active = 1 AND (name LIKE :term1 OR sku LIKE :term2  OR client_product_id LIKE :term3) AND client_id = $client_id ORDER BY name";
        $array = array(
            'term1' =>  '%'.$q.'%',
            'term2' =>  '%'.$q.'%',
            'term3' =>  '%'.$q.'%'
        );
        //echo $query;die();
        $rows = $db->queryData($query, $array);
        foreach($rows as $row)
        {
            $row_array['value'] = $row['name']." (".$row['sku'].")";
            if(!empty($row['publisher'])) $row_array['value'] = $row['name']." (".$row['publisher'].")";
            $row_array['sku'] = $row['sku'];
            $row_array['item_id'] = $row['id'];
            array_push($return_array,$row_array);
        }
        return $return_array;
    }

    public function getAutocompleteSolarItems($data, $fulfilled_id, $solar_type_id)
    {
        $return_array = array();
        if(in_array($data['clientid'], $this->solar_client_ids))
        {
            $the_items = $this->getAutocompleteItems($data, $fulfilled_id);
            foreach($the_items as $i)
            {
                if( ($i['solar_type_id'] == $solar_type_id) || (stripos($i['name'], "consumable") !== false) )
                    $return_array[] = $i;
            }
        }
        return $return_array;
    }

    public function getAutocompleteAllSolarItems($data, $fulfilled_id)
    {
        $db = Database::openConnection();
        $return_array = array();
        $q = $data["item"];
        $solar_type_id = $data['solar_type_id'];
        $query = "SELECT * FROM items WHERE active = 1 AND (name LIKE :term1 OR sku LIKE :term2) AND solar_type_id = $solar_type_id ORDER BY name";
        $array = array(
            'term1' =>  '%'.$q.'%',
            'term2' =>  '%'.$q.'%'
        );
        //echo $query;die();
        $rows = $db->queryData($query, $array);
        foreach($rows as $row)
        {
            $row_array['value'] = $row['name']." (".$row['sku'].")";
            $row_array['sku'] = $row['sku'];
            $row_array['item_id'] = $row['id'];
            array_push($return_array,$row_array);
        }
        return $return_array;
    }

    public function getSelectLocationAvailableCounts($item_id, $selected = false)
    {
        $db = Database::openConnection();
        $l_result = $db->queryRow("
            SELECT a.location, a.location_id, SUM(a.qty - IFNULL(b.allocated,0) - a.qc_count) as available,
            GROUP_CONCAT(
                DISTINCT IF( (a.qty - IFNULL(b.allocated,0) -  a.qc_count) > 0, (a.qty - IFNULL(b.allocated,0)  - a.qc_count), NULL ) ORDER BY (a.qty - IFNULL(b.allocated,0) - a.qc_count) DESC
            ) AS select_choices
            FROM
            (
                SELECT
                    l.location, l.id AS location_id, il.qty, il.qc_count, il.item_id, i.name
                FROM
                    items_locations il JOIN locations l ON il.location_id = l.id join items i on il.item_id = i.id
                WHERE
                    i.id = $item_id
            ) a
            LEFT JOIN
            (
                SELECT
                    COALESCE(SUM(oi.qty),0) AS allocated, oi.item_id, oi.location_id
                FROM
                    orders_items oi JOIN orders o ON oi.order_id = o.id
                WHERE
                    o.status_id != 4
                GROUP BY
                    oi.location_id, oi.item_id
            ) b
            ON a.item_id = b.item_id AND a.location_id = b.location_id
            ORDER BY name
        ");
        $choices = explode(",",$l_result['select_choices']);
        $return_string = "";
        foreach($choices as $i)
        {
            $return_string .= "<option ";
            if($selected && $selected == $i)
        	{
        		$return_string .= "selected='selected' ";
        	}
            $return_string .= ">$i</option>";
        }
        return $return_string;
    }

    public function getAutocompleteDeliveryItems($data)
    {
        //echo "The request<pre>",print_r($data),"</pre>";die();
        $db = Database::openConnection();
        $not_in = (!empty($data['exclude']))? " AND il.item_id NOT IN (".$data['exclude'].")" : "";
        $return_array = array();
        $q = $data["item"];
        $client_id = $data['clientid'];

        $query = "
            SELECT
                SUM(a.qty - IFNULL(b.allocated,0) - a.qc_count) as available, a.name, a.sku, a.item_id,
                GROUP_CONCAT(
                    IF( (a.qty - IFNULL(b.allocated,0) - a.qc_count) > 0, (a.qty - IFNULL(b.allocated,0) - a.qc_count), NULL ),'|' ,
                    a.location_id, '|'
                    ORDER BY (a.qty - IFNULL(b.allocated,0)  - a.qc_count) DESC
                    SEPARATOR '~'
                ) AS choices
            FROM
            (
                SELECT
                    l.id AS location_id, il.qty, il.qc_count, il.item_id, i.name, i.sku
                FROM
                    items_locations il JOIN locations l ON il.location_id = l.id join items i on il.item_id = i.id
                WHERE
                    i.active = 1 AND ( i.name LIKE :term1 OR sku LIKE :term2 ) AND i.client_id = $client_id
                    $not_in
            ) a
            LEFT JOIN
            (
                SELECT
                    COALESCE(SUM(di.qty),0) AS allocated, di.item_id, di.location_id
                FROM
                    deliveries_items di JOIN deliveries d ON di.deliveries_id = d.id
                WHERE
                    d.status_id != 5 AND d.cancelled = 0
                GROUP BY
                    di.location_id, di.item_id
            ) b
            ON a.item_id = b.item_id AND a.location_id = b.location_id
            group by a.item_id
            ORDER BY name
        ";
        //die($query);
        $array = array(
            'term1' =>  '%'.$q.'%',
            'term2' =>  '%'.$q.'%'
        );
        //echo $query;die();
        $rows = $db->queryData($query, $array);
        foreach($rows as $row)
        {
            if( empty($row['available']) ) continue;
            $row_array['value'] = $row['name']." (".$row['sku'].")";
            $row_array['sku'] = $row['sku'];
            $row_array['item_id'] = $row['item_id'];
            $row_array['total_available'] = $row['available'];
            $row_array['locations'] = $row['choices'];
            $row_array['name'] = $row['name'];
            array_push($return_array,$row_array);
        }
        //print_r($return_array);die();
        return $return_array;
    }

    public function getAutocompletePickupItems($data)
    {
        //echo "The request<pre>",print_r($data),"</pre>";die();
        $db = Database::openConnection();
        $return_array = array(
            array(
                'value' => 'Add a New Item',
                'item_id'   => -5,
                'client_product_id' => NULL
            )
        );
        $q = $data["item"];
        $client_id = $data['clientid'];

        $query = "
            SELECT
                id, name, sku, client_product_id, barcode
            FROM
                items
            WHERE
                palletized = 1 AND client_id = $client_id AND ( name LIKE :term1 OR sku LIKE :term2 OR client_product_id LIKE :term3 )
            ORDER BY name
        ";
        //die($query);
        $array = array(
            'term1' =>  '%'.$q.'%',
            'term2' =>  '%'.$q.'%',
            'term3' =>  '%'.$q.'%'
        );
        //echo $query;die();
        $rows = $db->queryData($query, $array);
        foreach($rows as $row)
        {
            $row_array['value'] = $row['name']." (".$row['sku'].")";
            $row_array['sku'] = $row['sku'];
            $row_array['item_id'] = $row['id'];
            $row_array['name'] = $row['name'];
            $row_array['client_product_id'] = $row['client_product_id'];
            array_push($return_array,$row_array);
        }
        //print_r($return_array);die();
        return $return_array;
    }

    public function getAutocompleteItems($data, $fulfilled_id)
    {
        //echo "The request<pre>",print_r($data),"</pre>";die();
        $db = Database::openConnection();

        if(in_array($data['clientid'], $this->solar_client_ids))
        {
            $orders_table = "solar_orders";
            $items_table = "solar_orders_items";
        }
        else
        {
            $orders_table = "orders";
            $items_table = "orders_items";
        }
        $return_array = array();
        $q = $data["item"];
        $client_id = $data['clientid'];
        //$query = "SELECT * FROM items WHERE active = 1 AND (name LIKE :term1 OR sku LIKE :term2) AND client_id = $client_id ORDER BY name";

        $query = "
            SELECT a.location, a.location_id, a.qty, a.qc_count, SUM(a.qty - IFNULL(b.allocated,0) - IFNULL(c.allocated,0) - a.qc_count) as available, a.name, a.sku, a.palletized, a.per_pallet, a.item_id, a.solar_type_id,
            GROUP_CONCAT(
                IF( (a.qty - IFNULL(b.allocated,0) - IFNULL(c.allocated,0) - a.qc_count) > 0, (a.qty - IFNULL(b.allocated,0) - IFNULL(c.allocated,0) - a.qc_count), NULL ) ORDER BY (a.qty - IFNULL(b.allocated,0) - IFNULL(c.allocated,0) - a.qc_count) DESC
            ) AS choices,
            GROUP_CONCAT(
                DISTINCT IF( (a.qty - IFNULL(b.allocated,0) - IFNULL(c.allocated,0) - a.qc_count) > 0, (a.qty - IFNULL(b.allocated,0) - IFNULL(c.allocated,0) - a.qc_count), NULL ) ORDER BY (a.qty - IFNULL(b.allocated,0) - IFNULL(c.allocated,0) - a.qc_count) DESC
            ) AS select_choices
            FROM
            (
                SELECT
                    l.location, l.id AS location_id, il.qty, il.qc_count, il.item_id, i.name, i.sku, i.palletized, i.per_pallet, i.solar_type_id
                FROM
                    items_locations il JOIN locations l ON il.location_id = l.id join items i on il.item_id = i.id
                WHERE
                    i.active = 1 AND (i.name LIKE :term1 OR sku LIKE :term2 ) AND i.client_id = $client_id
            ) a
            LEFT JOIN
            (
                SELECT
                    COALESCE(SUM(oi.qty),0) AS allocated, oi.item_id, oi.location_id
                FROM
                    $items_table oi JOIN $orders_table o ON oi.order_id = o.id join items i on oi.item_id = i.id
                WHERE
                    o.status_id != 4 AND o.cancelled = 0 AND (i.name LIKE :term3 OR sku LIKE :term4 ) AND i.client_id = $client_id
                GROUP BY
                    oi.location_id, oi.item_id
            ) b
            ON a.item_id = b.item_id AND a.location_id = b.location_id
            LEFT JOIN
            (
                SELECT
                    COALESCE(SUM(oi.qty),0) AS allocated, oi.item_id, oi.location_id
                FROM
                    solar_service_jobs_items oi JOIN solar_service_jobs o ON oi.job_id = o.id Join items i ON oi.item_id = i.id
                WHERE
                    o.status_id != 4
                GROUP BY
                    oi.location_id, oi.item_id
            ) c
            ON a.item_id = c.item_id AND a.location_id = c.location_id
            group by a.item_id
            ORDER BY name
        ";
        $array = array(
            'term1' =>  '%'.$q.'%',
            'term2' =>  '%'.$q.'%',
            'term3' =>  '%'.$q.'%',
            'term4' =>  '%'.$q.'%'
        );
        //echo $query;die();
        $rows = $db->queryData($query, $array);
        foreach($rows as $row)
        {
            if( empty($row['available']) && ( isset($data['checkavailable']) && $data['checkavailable'] )) continue;
            $row_array['value'] = $row['name']." (".$row['sku'].")";
            if(!empty($row['publisher'])) $row_array['value'] = $row['name']." (".$row['publisher'].")";
            $row_array['sku'] = $row['sku'];
            $row_array['item_id'] = $row['item_id'];
            $row_array['palletized'] = $row['palletized'];
            $row_array['per_pallet'] = $row['per_pallet'];
            $row_array['total_available'] = $row['available'];
            $row_array['max_values'] = $row['choices'];
            $row_array['select_values'] = $row['select_choices'];
            $row_array['name'] = $row['name'];
            $row_array['solar_type_id'] = $row['solar_type_id'];
            array_push($return_array,$row_array);
        }
        return $return_array;
    }

    public function clientItemAdd($data)
    {
        foreach($data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $upcount = 1;
        $sku = $client_product_id;
        while( $this->skuTaken($sku) )
        {
            $sku = $client_product_id."_".$upcount;
            ++$upcount;
        }
        $item_values = array(
            'name'              => $name,
            'client_product_id' => $client_product_id,
            'client_id'         => $client_id,
            'sku'               => $sku
        );
        if(isset($data['palletized'])) $item_values['palletized'] = $data['palletized'];
        $db = Database::openConnection();
        $id = $db->insertQuery('items', $item_values);
        return [
            'item_id'   => $id,
            'item_sku'  => $sku,
            'value'     => $name." (".$sku.")"
        ];
    }

    public function addItem($data)
    {
        //echo "The request<pre>",print_r($data),"</pre>";die();
        foreach($data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $db = Database::openConnection();
        $item_values = array(
        	'name'				            =>	$name,
            'sku'				            =>	$sku,
            'weight'			            =>	$weight,
            'width'				            =>	$width,
            'depth'				            =>	$depth,
            'height'			            =>	$height,
            'low_stock_warning'	            =>	$low_stock_warning,
            'trigger_point'	                =>	$trigger_point,
            'last_activity'		            =>	time(),
            'client_id'			            =>	$client_id,
            'preferred_pick_location_id'    =>  $preferred_pick_location_id,
            'palletized'                    =>  $palletized,
            'boxed_item'                    =>  $boxed_item,
            'is_lengths'                    =>  $is_lengths
        );
        $item_values['pack_item'] = (isset($pack_item))? 1 : 0;
        if(!empty($client_product_id)) $item_values['client_product_id'] = $client_product_id;
        //$item_values['boxed_item'] = (isset($boxed_item))? 1 : 0;
        $item_values['collection'] = (isset($collection))? 1 : 0;
        $item_values['per_pallet'] = (!empty($per_pallet))? $per_pallet : 0;
        $item_values['requires_bubblewrap'] = (isset($requires_bubblewrap))? 1 : 0;
        $item_values['is_pod'] = (isset($is_pod))? 1 : 0;
        if(isset($image_name)) $item_values['image'] = $image_name.".jpg";
        if(isset($external_image)) $item_values['image'] = $eximage;
        if(!empty($price)) $item_values['price'] = $price;
        if(isset($supplier)) $item_values['supplier'] = $supplier;
        if(isset($solar_type_id)) $item_values['solar_type_id'] = $solar_type_id;
        if(isset($small_satchel) && !empty($small_satchel)) $item_values['satchel_small'] = 1 / $small_satchel;
        if(isset($large_satchel) && !empty($large_satchel)) $item_values['satchel_large'] = 1 / $large_satchel;
        $item_values['double_bay'] = (isset($double_bay))? 1 : 0;
        if(!empty($barcode)) $item_values['barcode'] = $barcode;
        if(!empty($box_barcode)) $item_values['barcode'] = $box_barcode;
        //echo "The request<pre>",print_r($item_values),"</pre>";die();
        $id = $db->insertQuery('items', $item_values);
        return $id;
    }

    public function editItem($data)
    {
        //echo "Edit Item<pre>",print_r($data),"</pre>";die();
        foreach($data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $db = Database::openConnection();
        $item_values = array(
        	'name'				            =>	$name,
            'sku'				            =>	$sku,
            'weight'			            =>	$weight,
            'width'				            =>	$width,
            'depth'				            =>	$depth,
            'height'			            =>	$height,
            'low_stock_warning'	            =>	$low_stock_warning,
            'trigger_point'	                =>	$trigger_point,
            'last_activity'		            =>	time(),
            'palletized'                    =>  $palletized,
            'boxed_item'                    =>  $boxed_item,
            'is_lengths'                    =>  $is_lengths,
            'price'                         =>  0.00,
            'solar_type_id'                 =>  0,
            'barcode'                       =>  NULL,
            'client_product_id'             =>  NULL
        );
        if(!empty($supplier)) $item_values['supplier'] = $supplier;
        if(!empty($client_product_id)) $item_values['client_product_id'] = $client_product_id;
        //$item_values['active'] = (isset($active))? 1 : 0;
        //added '-deactivated' to SKU to maintain uniqueness
        if(isset($active))
        {
            $item_values['active'] = 1;
            $item_values['sku'] = str_replace("-deactivated", "",$sku);
        }
        else
        {
            $item_values['active'] = 0;
            $item_values['sku'] = $sku."-deactivated";
        }
        
        $item_values['requires_bubblewrap'] = (isset($requires_bubblewrap))? 1 : 0;
        $item_values['pack_item'] = (isset($pack_item))? 1 : 0;
        $item_values['collection'] = (isset($collection))? 1 : 0;
        $item_values['per_pallet'] = (isset($per_pallet))? $per_pallet : 0;
        $item_values['is_pod'] = (isset($is_pod))? 1 : 0;
        if(!isset($delete_image))
        {
            if(isset($image_name)) $item_values['image'] = $image_name.".jpg";
            if(isset($external_image)) $item_values['image'] = $eximage;
        }
        else
        {
            $item_values['image'] = null;
        }
        if(!empty($price)) $item_values['price'] = $price;
        $item_values['double_bay'] = (isset($double_bay))? 1 : 0;
        if(!empty($barcode)) $item_values['barcode'] = $barcode;
        if(!empty($box_barcode)) $item_values['barcode'] = $box_barcode;
        //echo "The request<pre>",print_r($item_values),"</pre>";die();
        if(isset($double_bay) && !$this->isDoubleBayItem($item_id))
        {
            //$client_id = $db->queryValue('items', array('id' => $id), 'client_id');
            $locations = $db->queryData("SELECT * FROM items_locations WHERE item_id = $item_id");
            //echo "<pre>",print_r($locations),"</pre>"; die();
            foreach($locations as $l)
            {
                $chosen_location = $db->queryValue('locations', array('id' => $l['location_id']), 'location');
                $next_location = substr($chosen_location, 0, -1)."b";
                $next_location_id = $db->queryValue('locations', array('location' => $next_location));
                if(!$db->queryValue('clients_locations', array('client_id' => $client_id, 'location_id' => $next_location_id, 'date_removed' => 0)))
                {
                    $values = array(
                        'location_id'   =>  $next_location_id,
                        'client_id'     =>  $client_id,
                        'date_added'    =>  time(),
                        'notes'         =>  'Double Bay Item'
                    );
                    $db->insertQuery('clients_locations', $values);
                }
            }
        }
        elseif(!isset($double_bay) && $this->isDoubleBayItem($item_id))
        {
            //$client_id = $db->queryValue('items', array('id' => $id), 'client_id');
            $locations = $db->queryData("SELECT * FROM items_locations WHERE item_id = $item_id");
            //echo "<pre>",print_r($locations),"</pre>"; die();
            foreach($locations as $l)
            {
                $chosen_location = $db->queryValue('locations', array('id' => $l['location_id']), 'location');
                $next_location = substr($chosen_location, 0, -1)."b";
                $next_location_id = $db->queryValue('locations', array('location' => $next_location));
                $db->query(
                    "UPDATE clients_locations
                    SET date_removed = ".time()."
                    WHERE client_id = $client_id AND location_id = $next_location_id"
                );
            }
        }
        $db->updateDatabaseFields('items', $item_values, $item_id);
        return true;
    }

    public function getStockOnHand($item_id)
	{
		$db = Database::openConnection();
		$ohq = $db->queryRow("SELECT SUM(qty) AS on_hand FROM items_locations WHERE item_id = $item_id GROUP BY item_id");
        $on_hand = (empty($ohq['on_hand']))? "0":$ohq['on_hand'];
		return $on_hand;
	}

    public function getStockUnderQC($item_id)
	{
		$db = Database::openConnection();
		$ohq = $db->queryRow("SELECT SUM(qc_count) AS under_qc FROM items_locations WHERE item_id = $item_id GROUP BY item_id");
        $under_qc = (empty($ohq['under_qc']))? "0":$ohq['under_qc'];
		return $under_qc;
	}

    public function getAllocatedStock($item_id, $fulfilled_id)
    {
        $db = Database::openConnection();
        $allocated = 0;
        $orders_table = "orders";
        $items_table = "orders_items";
        $backorder_location_id = 2914; // TODO: get this info dynamically

        $asq = $db->queryRow("
            SELECT
            	oi.item_id, i.name, sum(oi.qty) AS allocated
            FROM
            	$items_table oi JOIN $orders_table o ON oi.order_id = o.id Join items i ON oi.item_id = i.id
            WHERE
            	o.status_id != $fulfilled_id AND oi.item_id = $item_id AND oi.location_id != $backorder_location_id AND o.cancelled = 0
            GROUP BY
            	oi.item_id
        ");
        $allocated += (empty($asq['allocated']))? 0 : $asq['allocated'];
        return $allocated;
    }
    /*
    public function getAllocatedStockForLocation($location_id)
    {
        $db = Database::openConnection();
        $items_table = ($this->isSolarItem($item_id))? "solar_orders_items": "orders_items";
        $asq = $db->queryData("
            SELECT
            	oi.location_id, i.name, sum(oi.qty) AS allocated, oi.item_id, oi.id
            FROM
            	$items_table oi JOIN orders o ON oi.order_id = o.id Join items i ON oi.item_id = i.id
            WHERE
            	o.status_id != 4 AND oi.location_id = $location_id AND o.cancelled = 0
            GROUP BY
            	oi.item_id
        ");
        return (count($asq))? $asq : false;
    }
    */
    public function getAvailableLocationsForAutoselectItem($item_id)
    {
        $db = Database::openConnection();
        if($this->isSolarItem($item_id))
        {
            $orders_table = "solar_orders";
            $items_table = "solar_orders_items";
        }
        else
        {
            $orders_table = "orders";
            $items_table = "orders_items";
        }
        $item = $this->getItemById($item_id);
        $locations = $db->queryData("
            SELECT
                a.location, a.location_id, (a.in_location - IFNULL(b.allocated,0)) as available, IF(a.location_id = {$item['preferred_pick_location_id']}, 1, 0) as preferred
            FROM
            (
                SELECT
                    l.location, l.id AS location_id, (il.qty - il.qc_count) as in_location, il.id AS line_id, il.item_id
                FROM
                    items_locations il JOIN locations l ON il.location_id = l.id
                WHERE
                    il.item_id = $item_id AND (il.qty - il.qc_count) > 0
            ) a
            LEFT JOIN
            (
                SELECT
                    COALESCE(SUM(oi.qty),0) AS allocated, oi.item_id, oi.location_id
                FROM
                    $items_table oi JOIN $orders_table o ON oi.order_id = o.id Join items i ON oi.item_id = i.id
                WHERE
                    o.status_id != 4
                GROUP BY
                    oi.location_id, oi.item_id
            ) b
            ON a.item_id = b.item_id AND a.location_id = b.location_id
            LEFT JOIN
            (
                SELECT
                    COALESCE(SUM(oi.qty),0) AS allocated, oi.item_id, oi.location_id
                FROM
                    solar_service_jobs_items oi JOIN solar_service_jobs o ON oi.job_id = o.id Join items i ON oi.item_id = i.id
                WHERE
                    o.status_id != 4
                GROUP BY
                    oi.location_id, oi.item_id
            ) c
            ON a.item_id = c.item_id AND a.location_id = c.location_id
            ORDER BY available DESC
    	");
        return $locations;
    }

    public function getAvailableLocationsForItem($item_id, $pallet = false, $order_id = 0)
    {
        $db = Database::openConnection();
        $item = $this->getItemById($item_id);
        if($this->isSolarItem($item_id))
        {
            $orders_table = "solar_orders";
            $items_table = "solar_orders_items";
        }
        else
        {
            $orders_table = "orders";
            $items_table = "orders_items";
        }
        if($pallet)
        {
            $locations = $db->queryData("
                SELECT
                    a.location, a.location_id, (a.in_location - IFNULL(b.allocated,0)) as available, IF(a.location_id = {$item['preferred_pick_location_id']}, 1, 0) as preferred, TRIM( SUBSTRING_INDEX(a.location, '-', -1) ) AS sorter
                FROM
                (
                    SELECT
                        l.location, l.id AS location_id, (il.qty - il.qc_count) as in_location, il.id AS line_id, il.item_id
                    FROM
                        items_locations il JOIN locations l ON il.location_id = l.id
                    WHERE
                        il.item_id = $item_id
                ) a
                LEFT JOIN
                (
                    SELECT
                        COALESCE(SUM(oi.qty),0) AS allocated, oi.item_id, oi.location_id
                    FROM
                        $items_table oi JOIN $orders_table o ON oi.order_id = o.id Join items i ON oi.item_id = i.id
                    WHERE
                        o.status_id != 4 AND o.id != $order_id AND o.cancelled = 0 AND oi.item_id = $item_id
                    GROUP BY
                        oi.location_id, oi.item_id
                ) b
                ON a.item_id = b.item_id AND a.location_id = b.location_id
                ORDER BY
                    preferred DESC, sorter
            ");
        }
        else
        {
            $locations = $db->queryData("
                SELECT
                    a.location, a.location_id, (a.in_location - IFNULL(b.allocated,0) - IFNULL(c.allocated,0)) as available, IF(a.location_id = {$item['preferred_pick_location_id']}, 1, 0) as preferred, TRIM( SUBSTRING_INDEX(a.location, '-', -1) ) AS sorter
                FROM
                (
                    SELECT
                        l.location, l.id AS location_id, (il.qty - il.qc_count) as in_location, il.id AS line_id, il.item_id
                    FROM
                        items_locations il JOIN locations l ON il.location_id = l.id
                    WHERE
                        il.item_id = $item_id AND (il.qty - il.qc_count) > 0
                ) a
                LEFT JOIN
                (
                    SELECT
                        COALESCE(SUM(oi.qty),0) AS allocated, oi.item_id, oi.location_id
                    FROM
                        $items_table oi JOIN $orders_table o ON oi.order_id = o.id Join items i ON oi.item_id = i.id
                    WHERE
                        o.status_id != 4 AND o.id != $order_id AND o.cancelled = 0 AND oi.item_id = $item_id
                    GROUP BY
                        oi.location_id, oi.item_id
                ) b
                ON a.item_id = b.item_id AND a.location_id = b.location_id
                LEFT JOIN
                (
                    SELECT
                        COALESCE(SUM(oi.qty),0) AS allocated, oi.item_id, oi.location_id
                    FROM
                        solar_service_jobs_items oi JOIN solar_service_jobs o ON oi.job_id = o.id Join items i ON oi.item_id = i.id
                    WHERE
                        o.status_id != 4 AND o.id != $order_id
                    GROUP BY
                        oi.location_id, oi.item_id
                ) c
                ON a.item_id = c.item_id AND a.location_id = c.location_id
                ORDER BY
                    preferred DESC, sorter
        	");
        }

        return $locations;
    }

    public function getAvailableStock($item_id, $fulfilled_id)
    {
       $available = $this->getStockOnHand($item_id) - $this->getAllocatedStock($item_id, $fulfilled_id) - $this->getStockUnderQC($item_id);
       $available = ($available <= 0)? 0: $available;
       return $available;
    }

    public function skuTaken($sku, $current_sku = false)
    {
        $db = Database::openConnection();
        if($current_sku)
        {
            return ($db->fieldValueTaken($this->table, $sku, 'sku') && $sku != $current_sku);
        }
        return $db->fieldValueTaken($this->table, $sku, 'sku');
    }

    public function barcodeTaken($barcode, $current_barcode = false)
    {
        $db = Database::openConnection();
        if($current_barcode)
        {
            return ($db->fieldValueTaken($this->table, $barcode, 'barcode') && $barcode != $current_barcode);
        }
        return $db->fieldValueTaken($this->table, $barcode, 'barcode');
    }

    public function boxBarcodeTaken($barcode, $current_barcode = false)
    {
        $db = Database::openConnection();
        if($current_barcode)
        {
            return ($db->fieldValueTaken($this->table, $barcode, 'box_barcode') && $barcode != $current_barcode);
        }
        return $db->fieldValueTaken($this->table, $barcode, 'box_barcode');
    }

    public function checkSkus($sku, $current_sku)
    {
        $db = Database::openConnection();
        $sku = strtoupper($sku);
        $current_sku = strtoupper($current_sku);
        $q = "SELECT sku FROM items";
        $rows = $db->queryData($q);
        $valid = 'true';
        foreach($rows as $row)
        {
        	if($sku == strtoupper($row['sku']) && $sku != $current_sku)
        	{
        		$valid = 'false';
        	}
        }
        return $valid;
    }

    public function checkClientProductIds($client_product_id, $client_id)
    {
        $db = Database::openConnection();
        $client_product_id = strtoupper($client_product_id);
        $q = "SELECT client_product_id FROM items WHERE client_id = $client_id";
        $rows = $db->queryData($q);
        $valid = 'true';
        foreach($rows as $row)
        {
        	if($client_product_id == strtoupper($row['client_product_id']))
        	{
        		$valid = 'false';
        	}
        }
        return $valid;
    }

    public function checkBarcodes($barcode, $current_barcode)
    {
        $db = Database::openConnection();
        $barcode = strtoupper($barcode);
        $current_barcode = strtoupper($current_barcode);
        $q = "SELECT barcode FROM items";
        $rows = $db->queryData($q);
        $valid = 'true';
        foreach($rows as $row)
        {
        	if($barcode == strtoupper($row['barcode']) && $barcode != $current_barcode)
        	{
        		$valid = 'false';
        	}
        }
        return $valid;
    }

    public function checkBoxBarcodes($barcode, $current_barcode)
    {
        $db = Database::openConnection();
        $barcode = strtoupper($barcode);
        $current_barcode = strtoupper($current_barcode);
        $q = "SELECT box_barcode FROM items";
        $rows = $db->queryData($q);
        $valid = 'true';
        foreach($rows as $row)
        {
        	if($barcode == strtoupper($row['box_barcode']) && $barcode != $current_barcode)
        	{
        		$valid = 'false';
        	}
        }
        return $valid;
    }

    public function getItemsForClient($client_id, $active = 1)
    {
        $db = Database::openConnection();
        return $db->queryData("SELECT * FROM ".$this->table." WHERE client_id = $client_id AND active = $active ORDER BY name");
    }

    public function getItemById($item_id)
    {
        $db = Database::openConnection();
        return $db->queryRow("SELECT * FROM ".$this->table." WHERE id = $item_id");
    }

    public function getItemBySku($sku)
    {
        $db = Database::openConnection();
        return $db->queryRow("SELECT * FROM ".$this->table." WHERE sku = :sku", array('sku' => $sku));
    }

    public function getItemByClientProductId($cpi)
    {
        $db = Database::openConnection();
        return $db->queryRow("SELECT * FROM ".$this->table." WHERE client_product_id = :cpi AND active = 1", array('cpi' => $cpi));
    }

    public function getLowStock($item_id)
    {
       $item = $this->getItemById($item_id);
       return $item['low_stock_warning'];
    }

    public function getPackingTypesForItem($item_id)
    {
        $db = Database::openConnection();
        $types = $db->queryData("SELECT * FROM items_packing_types WHERE item_id = $item_id");
        $ret = array();
        foreach($types as $t)
        {
            $ret[$t['packing_type_id']] = $t['number'];
        }
        return $ret;
    }

    public function addPackingTypesForItem($types, $item_id)
    {
        $db = Database::openConnection();
        $db->deleteQuery('items_packing_types', $item_id, 'item_id');
        foreach($types as $t)
        {
            $vals = array(
                'packing_type_id'   =>  $t['id'],
                'number'            =>  $t['number'],
                'item_id'           =>  $item_id
            );
            $db->insertQuery('items_packing_types', $vals);
        }
        return true;
    }

    public function isDoubleBayItem($item_id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' => $item_id), 'double_bay') > 0;
    }

    public function isPalletItem($item_id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' => $item_id), 'palletized') > 0;
    }

    public function isPackItem($item_id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' => $item_id), 'pack_item') > 0;
    }

    public function isCollection($item_id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' => $item_id), 'collection') > 0;
    }

    public function getLocationsForItem($item_id)
    {
        $db = Database::openConnection();
        return $db->queryData("
            SELECT a.location, a.location_id, a.qty AS onhand, a.qc_count AS qc, IFNULL(b.allocated,0) as allocated, a.oversize, a.site
            FROM
            (
                SELECT
                    l.location, l.id AS location_id, il.qty, il.qc_count, il.item_id, cb.oversize, s.name AS site, s.is_default
                FROM
                    items_locations il JOIN
                	locations l ON il.location_id = l.id JOIN
                    sites s ON l.site_id = s.id JOIN
                	items i ON il.item_id = i.id JOIN
                	clients_bays cb ON il.location_id = cb.location_id AND cb.client_id = i.client_id
                WHERE
                    il.item_id = $item_id AND cb.date_removed = 0
            ) a
            LEFT JOIN
            (
                SELECT
                    COALESCE(SUM(oi.qty),0) AS allocated, oi.item_id, oi.location_id
                FROM
                    orders_items oi JOIN orders o ON oi.order_id = o.id Join items i ON oi.item_id = i.id
                WHERE
                    o.status_id != 4 AND o.cancelled = 0 AND oi.item_id = $item_id
                GROUP BY
                    oi.location_id, oi.item_id
            ) b
            ON a.item_id = b.item_id AND a.location_id = b.location_id
            ORDER BY
                a.is_default DESC, a.site, a.location
        ");
    }

    public function getLocationForItem($item_id, $location_id)
    {
        $db = Database::openConnection();
        if($this->isSolarItem($item_id))
        {
            $orders_table = "solar_orders";
            $items_table = "solar_orders_items";
        }
        else
        {
            $orders_table = "orders";
            $items_table = "orders_items";
        }
        return $db->queryRow("
            SELECT a.location, a.location_id, a.qty, a.qc_count, (IFNULL(b.allocated,0) + IFNULL(c.allocated,0)) as allocated, b.order_id, c.job_id
                FROM
                (
                    SELECT
                        l.location, l.id AS location_id, il.qty, il.qc_count, il.item_id
                    FROM
                        items_locations il JOIN locations l ON il.location_id = l.id
                    WHERE
                        il.item_id = $item_id AND il.location_id = $location_id
                ) a
                LEFT JOIN
                (
                    SELECT
                        COALESCE(SUM(oi.qty),0) AS allocated, oi.item_id, oi.location_id, oi.order_id
                    FROM
                        $items_table oi JOIN $orders_table o ON oi.order_id = o.id Join items i ON oi.item_id = i.id
                    WHERE
                        o.status_id != 4 AND o.cancelled = 0
                    GROUP BY
                        oi.location_id, oi.item_id
                ) b
                ON a.item_id = b.item_id AND a.location_id = b.location_id
                LEFT JOIN
                (
                    SELECT
                        COALESCE(SUM(oi.qty),0) AS allocated, oi.item_id, oi.location_id, oi.job_id
                    FROM
                        solar_service_jobs_items oi JOIN solar_service_jobs o ON oi.job_id = o.id Join items i ON oi.item_id = i.id
                    WHERE
                        o.status_id != 4
                    GROUP BY
                        oi.location_id, oi.item_id
                ) c
                ON a.item_id = c.item_id AND a.location_id = c.location_id
        ");
    }

    private function cleanUpItemsLocations()
    {
        $db = Database::openConnection();
        $db->query("DELETE FROM items_locations WHERE qty <= 0 AND qc_count <= 0");
    }

    private function isSolarItem($id)
    {
        $item = $this->getItemById($id);
        return in_array($item['client_id'], $this->solar_client_ids);
    }

    private function constructQuery($orders_table, $items_table)
    {
        return "
            LEFT JOIN
            (
                SELECT
                    COALESCE(SUM(oi.qty),0) AS allocated, oi.item_id, oi.location_id
                FROM
                    $items_table oi JOIN $orders_table o ON oi.order_id = o.id Join items i ON oi.item_id = i.id
                WHERE
                    o.status_id != 4
                GROUP BY
                    oi.location_id, oi.item_id
            ) b
            ON a.item_id = b.item_id AND a.location_id = b.location_id
        ";
    }
}