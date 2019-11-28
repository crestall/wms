<?php

 /**
  * Location Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>


  FUNCTIONS

  addToLocation
  checkLocation($location)
  getAllLocations()
  getItemLocationId
  getItemStockInLocation
  getLocationId
  getLocationName
  getLocationUsage()
  getQCLocationsForItem($item_id)
  getSelectClientLocations
  getSelectDBLocations
  getSelectEmptyDBLocations
  getSelectEmptyLocations
  getSelectItemInLocations
  getSelectLocations
  getSelectNonEmptyLocations
  getSelectQCItemInLocations($item_id, $selected = false)
  isEmptyLocation
  isEmptyOfItem($id, $item_id)
  isOversize($id)
  subtractFromLocation
  updateLocation


  */

class Location extends Model{

    public $receiving_id;
    public $table = "locations";

    public function __construct()
    {
        $this->receiving_id = $this->getLocationId('receiving');
    }

    private function getReceivingId()
    {
        $db = Database::openConnection();
        //return ($db->queryValue($this->table, array('location' => 'Receiving')));
    }

    public function isOversize($id)
    {
        $db = Database::openConnection();
        $q = "
            SELECT * FROM clients_bays WHERE location_id = $id AND oversize = 1 AND date_removed = 0
        ";
        //die($q);
        $res = $db->queryRow($q);
        //echo "<pre>",print_r($res),"</pre>"; die();
        return ( !empty($res) );
    }

    public function getLocationUsage()
    {
        $db = Database::openConnection();
        $query = "
            SELECT l.id, l.location, cb.oversize, il.qty, i.name, i.sku, c.client_name
            FROM locations l
            JOIN items_locations il ON l.id = il.location_id
            JOIN items i ON i.id = il.item_id
            JOIN clients c ON i.client_id = c.id
            LEFT JOIN clients_bays cb ON cb.location_id = il.location_id AND cb.date_removed = 0 AND cb.client_id = c.id
            ORDER BY l.location
        ";

        return $db->queryData($query);
    }

    public function checkLocation($location)
    {
        $db = Database::openConnection();
        $location = strtoupper($location);
        $q = "SELECT location FROM {$this->table}";
        $rows = $db->queryData($q);
        $valid = 'true';
        foreach($rows as $row)
        {
        	if($location == strtoupper($row['location']))
        	{
        		$valid = 'false';
        	}
        }
        return $valid;
    }

    public function getAllLocations()
    {
        $db = Database::openConnection();

        return $db->queryData("SELECT * FROM locations ORDER BY location + 0");
        //return $db->queryData("SELECT * FROM locations WHERE SUBSTRING_INDEX(location, '.', 1) = '7'" );
    }

    public function updateQualityControlStatus($data)
    {
        $db = Database::openConnection();

        if(isset($data['qty_add']) && $data['qty_add'] > 0)
        {
            $db->query("UPDATE items_locations SET qc_count = qc_count + {$data['qty_add']} WHERE item_id = {$data['product_id']} AND location_id = {$data['add_to_location']}");
        }
        if(isset($data['qty_subtract']) && $data['qty_subtract'] > 0)
        {
            $db->query("UPDATE items_locations SET qc_count = qc_count - {$data['qty_subtract']} WHERE item_id = {$data['product_id']} AND location_id = {$data['subtract_from_location']}");
        }
        return true;
    }

    public function subtractFromLocation($data)
    {
        $db = Database::openConnection();
        //echo "<pre>",print_r($data),"</pre>"; //die();
        //subtract the stock
        $updater = $db->queryValue('items_locations', array('item_id' => $data['subtract_product_id'], 'location_id' => $data['subtract_from_location']));
        //die('updatr :'.$updater);
        //return;
        if(isset($data['qc_stock']))
        {
            $db->query("UPDATE items_locations SET qty = qty - {$data['qty_subtract']}, qc_count = qc_count - {$data['qty_subtract']} WHERE id = $updater");
        }
        else
        {
            $db->query("UPDATE items_locations SET qty = qty - {$data['qty_subtract']} WHERE id = $updater");
        }

        //record the movement
        $reference = (isset($data['reference']))? $data['reference']: "Admin Stock Update";
        $db->insertQuery('items_movement', array(
            'item_id'       =>  $data['subtract_product_id'],
            'location_id'   =>  $data['subtract_from_location'],
            'qty_out'       =>  $data['qty_subtract'],
            'reference'     =>  $reference,
            'reason_id'     =>  $data['reason_id'],
            'date'          =>  time(),
            'entered_by'    =>  Session::getUserId()
        ));
        $db->query("DELETE FROM items_locations WHERE qty <= 0 AND qc_count <= 0");

        return true;
    }

    public function addToLocation($data)
    {
        $db = Database::openConnection();
        //echo "<pre>",print_r($data),"</pre>"; die();
        //add the stock
        if($updater = $db->queryValue('items_locations', array('item_id' => $data['add_product_id'], 'location_id' => $data['add_to_location'])))
        {
            if(isset($data['qc_stock']))
            {
                $db->query("UPDATE items_locations SET qty = qty + {$data['qty_add']}, qc_count = qc_count + {$data['qty_add']} WHERE id = $updater");
            }
            else
            {
                $db->query("UPDATE items_locations SET qty = qty + {$data['qty_add']} WHERE id = $updater");
            }
        }
        else
        {
            $vals = array(
                'item_id'       =>  $data['add_product_id'],
                'location_id'   =>  $data['add_to_location'],
                'qty'           =>  $data['qty_add']
            );
            if(isset($data['under_qc']))
            {
                $vals['qc_count'] = $data['qty_add'];
            }
            $db->insertQuery('items_locations', $vals);
        }
        //record the movement
        $reference = isset($data['reference'])? $data['reference'] : "Admin Stock Update";
        $db->insertQuery('items_movement', array(
            'item_id'       =>  $data['add_product_id'],
            'location_id'   =>  $data['add_to_location'],
            'qty_in'        =>  $data['qty_add'],
            'reference'     =>  $reference,
            'reason_id'     =>  $data['reason_id'],
            'date'          =>  time(),
            'entered_by'    =>  Session::getUserId()
        ));
        return true;
    }

    public function getLocationName($location_id)
    {
        $db = Database::openConnection();
        //return $db->queryValue($this->table, array('id' => $location_id), 'location');
        $res = $db->queryRow("SELECT location FROM {$this->table} WHERE id = $location_id");
        return $res['location'];
    }

    public function getLocationId($name)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('location' => $name));
    }

    public function getSelectAllLocations($selected = false)
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $q = "
            SELECT
                id, location
            FROM
                locations
            WHERE
                selectable = 1
            ORDER BY location";
        $locations = $db->queryData($q);
        foreach($locations as $l)
        {
            $label = $l['location'];
            $value = $l['id'];
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;

    }

    public function getSelectLocations($selected = false, $item_id = false)
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";

        $q = "
            SELECT
                id, location
            FROM
                locations
            WHERE
                selectable = 1
                AND
                CASE WHEN multi_sku = 0
                THEN id NOT IN (SELECT location_id FROM clients_locations WHERE date_removed = 0 UNION SELECT location_id FROM items_locations)
                ELSE id NOT IN (SELECT location_id FROM clients_locations WHERE date_removed = 0)
                END";

        if( $item_id )
        {
            $q .= " OR ( id IN (SELECT location_id FROM items_locations WHERE item_id = $item_id) AND (selectable = 1) )";
        }
        $q .= " ORDER BY location";
        //echo $q;die();
        $locations = $db->queryData($q);
        foreach($locations as $l)
        {
            $label = $l['location'];
            $value = $l['id'];
            if($selected)
			{
				$check = ($value == $selected)? "selected='selected'" : "";
			}
			$ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function getSelectDBLocations($selected = false, $item_id = false)
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $array = array();

        $q = "
            SELECT id, location
            FROM locations
            WHERE
            (
                selectable = 1
            )
            AND
            (
                CASE WHEN multi_sku = 0
                THEN id NOT IN (SELECT location_id FROM clients_locations WHERE date_removed = 0 UNION SELECT location_id FROM items_locations)
                ELSE id NOT IN (SELECT location_id FROM clients_locations WHERE date_removed = 0)
                END
            )
            AND
            (
                SUBSTRING(location, -1) = 'a'
            )
        ";
        if( $item_id )
        {
            $q .= " OR
                    (
                        id IN (SELECT location_id FROM items_locations WHERE item_id = :item_id) AND (selectable = 1)
                    )";
            $array['item_id'] = $item_id;
        }
        $q .= "
            ORDER BY
                location
        ";
        //echo $q;die();
        $locations = $db->queryData($q, $array);
        /* */
        foreach($locations as $l)
        {
            $next_location_id = 0;
            $label = $l['location'];
            $value = $l['id'];
            $next_location = substr($label, 0, -1)."b";
            $next_location_id = $db->queryValue('locations', array('location' => $next_location));
            if( !$this->isEmptyLocation($next_location_id) ) continue;
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option $check value='$value'>$label</option>";
        }
        //die();
        return $ret_string;

    }


    /*
    public function getSelectDBLocations($selected = false, $item_id = false)
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $array = array();

        $q = "
            SELECT
                l1.id, l1.location
            FROM
                (
                    SELECT id, location
                    FROM locations
                    WHERE
                    (
                        CASE WHEN multi_sku = 0
                        THEN id NOT IN (SELECT location_id FROM clients_locations WHERE date_removed = 0 UNION SELECT location_id FROM items_locations)
                        ELSE id NOT IN (SELECT location_id FROM clients_locations WHERE date_removed = 0)
                        END
                    )
                    AND
                    (
                        SUBSTRING(location, -1) = 'a'
                    )
        ";
        if( $item_id )
        {
            $q .= " OR
                    (
                         id IN (SELECT location_id FROM items_locations WHERE item_id = :item_id)
                    )";
            $array['item_id'] = $item_id;
        }
        $q .= " ) l1
                LEFT JOIN
                (
                    SELECT id, location
                    FROM locations
                    WHERE
                    (
                        CASE WHEN multi_sku = 0
                        THEN id NOT IN (SELECT location_id FROM clients_locations WHERE date_removed = 0 UNION SELECT location_id FROM items_locations)
                        ELSE id NOT IN (SELECT location_id FROM clients_locations WHERE date_removed = 0)
                        END
                    )
                    AND
                    (
                        SUBSTRING(location, -1) = 'b'
                    )
                ) l2
                ON l2.location = CONCAT(SUBSTRING(l1.location,1,LENGTH(l1.location) - 1), 'b')
            WHERE
                l2.id IS NOT NULL
            ORDER BY
                l1.location
        ";
        //echo $q;die();
        $locations = $db->queryData($q, $array);
        foreach($locations as $l)
        {
            $label = $l['location'];
            $value = $l['id'];
            if($selected)
			{
				$check = ($value == $selected)? "selected='selected'" : "";
			}
			$ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }
    */
    public function getSelectClientLocations($selected = false)
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $q = "SELECT id, location FROM locations WHERE id NOT IN (SELECT location_id FROM clients_locations WHERE date_removed = 0 UNION SELECT location_id FROM items_locations)  AND (selectable = 1) ORDER BY location";
        $locations = $db->queryData($q);
        foreach($locations as $l)
        {
            $label = $l['location'];
            $value = $l['id'];
            if($selected)
			{
				$check = ($value == $selected)? "selected='selected'" : "";
			}
			$ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function getSelectEmptyLocations($selected = false)
    {
       $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $q = "SELECT id, location FROM locations WHERE id NOT IN (SELECT location_id FROM clients_locations WHERE date_removed IS NULL UNION SELECT location_id FROM items_locations) AND (selectable = 1) ORDER BY location";
        $locations = $db->queryData($q);
        foreach($locations as $l)
        {
            $label = $l['location'];
            $value = $l['id'];
            if($selected)
			{
				$check = ($value == $selected)? "selected='selected'" : "";
			}
			$ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function getSelectEmptyDBLocations($selected = false)
    {
       $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $q = "SELECT id, location FROM locations WHERE id NOT IN (SELECT location_id FROM clients_locations WHERE date_removed IS NULL UNION SELECT location_id FROM items_locations) AND SUBSTRING(location, -1) = 'a'  AND (selectable = 1) ORDER BY location";
        $locations = $db->queryData($q);
        foreach($locations as $l)
        {
            $next_location_id = 0;
            $label = $l['location'];
            $value = $l['id'];
            $next_location = substr($label, 0, -1)."b";
            $next_location_id = $db->queryValue('locations', array('location' => $next_location));
            if( !$this->isEmptyLocation($next_location_id) ) continue;
            if($selected)
			{
				$check = ($value == $selected)? "selected='selected'" : "";
			}
			$ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function getSelectNonEmptyLocations($selected = false)
    {
       $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $q = "SELECT id, location FROM locations WHERE id NOT IN (SELECT location_id FROM clients_locations WHERE date_removed IS NULL) AND id IN (SELECT location_id FROM items_locations) AND (selectable = 1) ORDER BY location";
        $locations = $db->queryData($q);
        foreach($locations as $l)
        {
            $label = $l['location'];
            $value = $l['id'];
            if($selected)
			{
				$check = ($value == $selected)? "selected='selected'" : "";
			}
			$ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function getSelectNonEmptyUnallocatedLocations($selected = false)
    {
       $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $q = "  SELECT
                    id, location
                FROM
                    locations
                WHERE
                    id NOT IN (SELECT location_id FROM clients_locations WHERE date_removed IS NULL)
                    AND id IN (SELECT location_id FROM items_locations)
                    AND (selectable = 1)
                ORDER BY location";
        $locations = $db->queryData($q);
        foreach($locations as $l)
        {
            $label = $l['location'];
            $value = $l['id'];
            $c = $db->queryRow("SELECT count(*) AS count FROM orders_items oi JOIN orders o ON oi.order_id = o.id WHERE o.status_id != 4 AND oi.location_id = $value");
            //echo "SELECT count(*) AS count FROM orders_items oi JOIN orders o ON oi.order_id = o.id WHERE o.status_id != 4 AND oi.location_id = $value";
            if($c['count'] == 0)
            {
                if($selected)
                {
                    $check = ($value == $selected)? "selected='selected'" : "";
                }
                $ret_string .= "<option $check value='$value'>$label</option>";
            }
        }
        return $ret_string;
    }

    public function isEmptyLocation($id)
    {
        if($id == 0)
        {
            return false;
        }
        $db = Database::openConnection();
        $q = "
            SELECT * FROM locations l
            LEFT JOIN items_locations il ON l.id = il.location_id
            LEFT JOIN clients_locations cl ON cl.location_id = l.id AND cl.date_removed = 0
            WHERE l.id = $id
        ";
        $locations = $db->queryRow($q);
        if($locations['multi_sku'])
            return true;
        return is_null($locations['client_id']);
    }

    public function isEmptyOfItem($id, $item_id)
    {
        $db = Database::openConnection();
        $q = "
            SELECT * FROM items_locations WHERE item_id = $item_id AND location_id = $id
        ";
        //die($q);
        $res = $db->queryRow($q);
        //echo "<pre>",print_r($res),"</pre>"; die();
        return ( empty($res) );
    }

    public function getEmptyLocations()
    {
        $db = Database::openConnection();
        $q = "
          SELECT id, location
          FROM locations
          WHERE id NOT IN (SELECT location_id FROM clients_locations WHERE date_removed = 0 UNION SELECT location_id FROM items_locations) AND tray = 0
          ORDER BY location + 0
        ";
        return $db->queryData($q);
    }

    public function getSelectItemInLocations($item_id, $selected = false, $add_ppl = false, $qty = 0)
    {
        $db = Database::openConnection();
        $qi = "";
        if($add_ppl)
        {
            $ppl_id = $db->queryValue("items", array('id' => $item_id), "preferred_pick_location_id");
            if($ppl_id > 0)
                $qi = " OR (l.id = $ppl_id) ";
        }
        $location_array = array();
        $locations = $db->queryData("
            SELECT l.location, l.id
            FROM items_locations il JOIN locations l ON il.location_id = l.id
            WHERE (il.item_id = $item_id AND (qty - qc_count) >= $qty) $qi
            GROUP BY l.id
            ORDER BY l.location");
        $check = "";
        $ret_string = "";
        foreach($locations as $l)
    	{
            $label = $l['location'];
            $value = $l['id'];
            if($selected)
			{
				$check = ($value == $selected)? "selected='selected'" : "";
			}
			$ret_string .= "<option $check value='$value'>$label</option>";
    	}
        return $ret_string;
    }

    public function getSelectQCItemInLocations($item_id, $selected = false)
    {
        $db = Database::openConnection();
        $location_array = array();
        $locations = $db->queryData("SELECT l.location, l.id FROM items_locations il JOIN locations l ON il.location_id = l.id WHERE il.item_id = $item_id AND il.qc_count > 0");
        $check = "";
        $ret_string = "";
        foreach($locations as $l)
    	{
            $label = $l['location'];
            $value = $l['id'];
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option $check value='$value'>$label</option>";
    	}
        return $ret_string;
    }

    public function getItemLocationId(array $params)
    {
        $db = Database::openConnection();
        return $db->queryValue('items_locations', $params);
    }

    public function getItemStockInLocation($item_id, $location_id)
    {
        $db = Database::openConnection();
        $pc = $db->queryRow("SELECT (qty - qc_count) as available FROM items_locations WHERE location_id = $location_id AND item_id = $item_id");
        return $pc['available'];
    }

    public function getQCLocationsForItem($item_id)
    {
        $db = Database::openConnection();
        return $db->queryData("SELECT qc_count FROM items_locations WHERE item_id = $item_id AND qc_count > 0");
    }

    public function addLocation($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'location'  =>  $data['location']
        );
        if(isset($data['multisku']))
            $vals['multi_sku'] = 1;
        if(isset($data['trays']))
            $vals['tray'] = 1;
        $db->insertQuery('locations', $vals);
        return true;
    }

    public function updateLocation($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'location'  =>  $data['location'],
            'multi_sku' =>  0,
            'tray'      =>  0
        );
        if($data['multisku'] == "true")
            $vals['multi_sku'] = 1;
        if($data['tray'] == "true")
            $vals['tray'] = 1;
        $db->updateDatabaseFields($this->table, $vals, $data['id']);
        return true;
    }
}

?>