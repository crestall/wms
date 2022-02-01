<?php

 /**
  * Client Class
  *

  * @author     Mark Solly <mark.solly@fsg.com.au>

  FUNCTIONS

  addClient($data)
  getAllClients($active = 1)
  getClientCourierRef($client_id)
  getClientId($name)
  getClientInfo($clientId)
  getClientName($client_id)
  getEparcelClass($client_id)
  getEparcelClients()
  getProductsDescription($id)
  getSelectClients($selected = false, $exclude = '')
  getSelectSalesRepClients($selected = false, $exclude = '')
  updateClientInfo($data)

  */

class Client extends Model{

    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "clients";
    //public $delivery_charges_table = "client_delivery_charges";
    //public $storage_charges_table = "client_storage_charges";
    public $charges_table = "client_charges";
    public $solar_client_id;

    public function __construct()
    {
        $this->solar_client_id = $this->getClientId('TLJ Solar Pty Ltd');
    }

    public function getClientId($name)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('client_name' => $name));
    }

    public function canAdjustAllocations($client_id = 0)
    {
        if($client_id == 0)
            return false;
        $db = Database::openConnection();
        return ( $db->queryValue($this->table, array('id' => $client_id), 'can_adjust') > 0 );
    }

    public function getEparcelClients()
    {
        $db = Database::openConnection();
        return $db->queryData("SELECT client_name, id, eparcel_location FROM clients WHERE api_key IS NOT NULL AND `active` = 1");
    }

    public function addClient($data)
    {
        //echo "The request<pre>",print_r($data),"</pre>";//die();
        $db = Database::openConnection();
        $client_values = array(
            'client_name'		=>	$data['client_name'],
            'billing_email'		=>	$data['billing_email'],
            'sales_email'		=>	$data['sales_email'],
            'inventory_email'	=>	$data['inventory_email'],
            'deliveries_email'  =>  $data['deliveries_email'],
            'sales_contact'		=>	$data['sales_contact'],
            'inventory_contact'	=>	$data['inventory_contact'],
            'deliveries_contact'=>  $data['deliveries_contact'],
            'ref_1'				=>	$data['ref_1'],
            'address'	        =>	$data['address'],
            'suburb'	        =>	$data['suburb'],
            'state'		        =>	$data['state'],
            'postcode'	        =>	$data['postcode'],
            'country'           =>  $data['country']
        );
        //echo "The request<pre>",print_r($client_values),"</pre>";die();
        if(!empty($data['contact_name'])) $client_values['contact_name'] = $data['contact_name'];
        if(isset($data['image_name'])) $client_values['logo'] = $data['image_name'].".jpg";
        if(isset($data['production_client'])) $client_values['production_client'] = 1;
        if(isset($data['delivery_client'])) $client_values['delivery_client'] = 1;
        $client_values['can_adjust'] = (!isset($data['can_adjust']))? 0 : 1;
        $client_values['products_description'] = (!empty($data['products_description']))? $data['products_description']: null;

        $charges_values = array(
            'standard_truck'    => $data['standard_truck'],
            'urgent_truck'      => $data['urgent_truck'],
            'standard_ute'      => $data['standard_ute'],
            'urgent_ute'        => $data['urgent_ute'],
            'standard_bay'      => $data['standard_bay'],
            'oversize_bay'      => $data['oversize_bay'],
            '40GP_loose'        => $data['40GP_loose'],
            '20GP_loose'        => $data['20GP_loose'],
            '40GP_palletised'   => $data['40GP_palletised'],
            '20GP_palletised'   => $data['20GP_palletised'],
            'max_loose_40GP'    => $data['max_loose_40GP'],
            'max_loose_20GP'    => $data['max_loose_20GP'],
            'additional_loose'  => $data['additional_loose'],
            'repalletising'     => $data['repalletising'],
            'shrinkwrap'        => $data['shrinkwrap'],
            'service_fee'       => $data['service_fee']
        );
        //
        //echo "CLIENT VALUES<pre>",print_r($client_values),"</pre>";die();
        $client_id = $db->insertQuery($this->table, $client_values);
        $charges_values['client_id'] = $client_id;
        //echo "CHARGES VALUES<pre>",print_r($charges_values),"</pre>";  die();
        $db->insertQuery($this->charges_table, $charges_values);
        return $client_id;
    }

    public function getEparcelClass($client_id)
    {
        $db = Database::openConnection();
        $location = $db->queryValue($this->table, array('id' => $client_id), 'eparcel_location');
        if(empty($location))
        {
            return 'Eparcel';
        }
        return $location.'Eparcel';
    }

    public function getAllClients($active = 1)
    {
        $db = Database::openConnection();
        $query = "SELECT * FROM {$this->table} WHERE active = $active ORDER BY client_name";
        return($db->queryData($query));
    }

    public function getClientInfo($client_id)
    {
        $db = Database::openConnection();
        $q = "SELECT * FROM {$this->table} c JOIN {$this->charges_table} cc ON c.id = cc.client_id WHERE c.id = $client_id";
        return ($db->queryRow($q));
    }

    public function getProductsDescription($id)
    {
        $client = $this->getClientInfo($id);
        return $client['products_description'];
    }

    public function getClientName($client_id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' => $client_id), 'client_name');
    }

    public function getClientCourierRef($client_id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' => $client_id), 'ref_1');
    }

    public function updateClientInfo($data)
    {
        //echo "The request<pre>",print_r($data),"</pre>";die();
        $db = Database::openConnection();
        $client_values = array(
            'client_name'		=>	$data['client_name'],
            'billing_email'		=>	$data['billing_email'],
            'sales_email'		=>	$data['sales_email'],
            'inventory_email'	=>	$data['inventory_email'],
            'deliveries_email'  =>  $data['deliveries_email'],
            'sales_contact'		=>	$data['sales_contact'],
            'inventory_contact'	=>	$data['inventory_contact'],
            'deliveries_contact'=>  $data['deliveries_contact'],
            'ref_1'				=>	$data['ref_1'],
            'address'	        =>	$data['address'],
            'suburb'	        =>	$data['suburb'],
            'state'		        =>	$data['state'],
            'postcode'	        =>	$data['postcode'],
            'country'           =>  $data['country']
        );
        $client_values['active'] = (isset($data['active']))? 1 : 0;
        $client_values['production_client'] = (isset($data['production_client']))? 1 : 0;
        $client_values['delivery_client'] = (isset($data['delivery_client']))? 1 : 0;
        $client_values['use_bubblewrap'] = (isset($data['use_bubblewrap']))? 1 : 0;
        $client_values['can_adjust'] = (!isset($data['can_adjust']))? 0 : 1;
        if(!empty($data['contact_name'])) $client_values['contact_name'] = $data['contact_name'];
        if(isset($data['image_name'])) $client_values['logo'] = $data['image_name'].".jpg";
        elseif(isset($_POST['delete_logo'])) $client_values['logo'] = "default.png";
        $client_values['products_description'] = (!empty($data['products_description']))? $data['products_description']: null;
        $db->updatedatabaseFields($this->table, $client_values, $data['client_id']);
        $charges_values = array(
            'standard_truck'    => $data['standard_truck'],
            'urgent_truck'      => $data['urgent_truck'],
            'standard_ute'      => $data['standard_ute'],
            'urgent_ute'        => $data['urgent_ute'],
            'standard_bay'      => $data['standard_bay'],
            'oversize_bay'      => $data['oversize_bay'],
            '40GP_loose'        => $data['40GP_loose'],
            '20GP_loose'        => $data['20GP_loose'],
            '40GP_palletised'   => $data['40GP_palletised'],
            '20GP_palletised'   => $data['20GP_palletised'],
            'max_loose_40GP'    => $data['max_loose_40GP'],
            'max_loose_20GP'    => $data['max_loose_20GP'],
            'additional_loose'  => $data['additional_loose'],
            'repalletising'     => $data['repalletising'],
            'shrinkwrap'        => $data['shrinkwrap'],
            'service_fee'       => $data['service_fee']
        );
        $db->updatedatabaseFields($this->charges_table, $charges_values, $data['charges_id']);
        return true;
    }

    public function getSelectClients($selected = false, $exclude = '')
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $q = "SELECT id, client_name FROM clients WHERE active = 1";
        if(strlen($exclude))
        {
            $q .= " AND id NOT IN($exclude)";
        }
        $q .= " ORDER BY client_name";
        $clients = $db->queryData($q);
        foreach($clients as $c)
        {
            $label = $c['client_name'];
            $value = $c['id'];
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option value='$value' $check >$label</option>";
        }
        return $ret_string;
    }

    public function getSelectPPClients($selected = false, $exclude = '')
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $q = "SELECT id, client_name FROM clients WHERE active = 1 AND delivery_client = 0";
        if(strlen($exclude))
        {
            $q .= " AND id NOT IN($exclude)";
        }
        $q .= " ORDER BY client_name";
        $clients = $db->queryData($q);
        foreach($clients as $c)
        {
            $label = $c['client_name'];
            $value = $c['id'];
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option value='$value' $check >$label</option>";
        }
        return $ret_string;
    }

    public function getSelectSalesRepClients($selected = false, $exclude = '')
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $q = "SELECT id, client_name FROM clients WHERE active = 1 AND has_reps = 1";
        if(strlen($exclude))
        {
            $q .= " AND id NOT IN($exclude)";
        }
        $q .= " ORDER BY client_name";
        $clients = $db->queryData($q);
        foreach($clients as $c)
        {
            $label = $c['client_name'];
            $value = $c['id'];
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function getSelectDeliveryClients($selected = false, $exclude = '')
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $q = "SELECT id, client_name FROM clients WHERE active = 1 AND delivery_client = 1";
        if(strlen($exclude))
        {
            $q .= " AND id NOT IN($exclude)";
        }
        $q .= " ORDER BY client_name";
        $clients = $db->queryData($q);
        foreach($clients as $c)
        {
            $label = $c['client_name'];
            $value = $c['id'];
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function getSelectProductionClients($selected = false, $exclude = '')
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $q = "SELECT id, client_name FROM clients WHERE active = 1 AND production_client = 1";
        if(strlen($exclude))
        {
            $q .= " AND id NOT IN($exclude)";
        }
        $q .= " ORDER BY client_name";
        $clients = $db->queryData($q);
        foreach($clients as $c)
        {
            $label = $c['client_name'];
            $value = $c['id'];
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function isProductionClient($client_id = 0)
    {
        if($client_id == 0)
            return false;
        $db = Database::openConnection();
        return ( $db->queryValue($this->table, array('id' => $client_id), 'production_client') > 0 );
    }

    public function isDeliveryClient($client_id = 0)
    {
        if($client_id == 0)
            return false;
        $db = Database::openConnection();
        return ( $db->queryValue($this->table, array('id' => $client_id), 'delivery_client') > 0 );
    }

    public function getClientDeliveryCharges($client_id, $from, $to)
    {
        $db = Database::openConnection();
        //$client_info = $this->getClientInfo($client_id);
        $q =  "
            SELECT
                d.client_id,d.client_name,
                GROUP_CONCAT(
                    s.standard_bay_days,' days','|',
                    c.standard_bay,' per week','|',
                    s.standard_storage_charge
                    SEPARATOR '~'
                ) AS standard_bay_storage,
                GROUP_CONCAT(
                    s.oversize_bay_days, ' days','|',
                    c.oversize_bay,' per week','|',
                    s.oversize_storage_charge
                    SEPARATOR '~'
                ) AS oversize_bay_storage,
                GROUP_CONCAT(
                    IFNULL(d.standard_truck_count, 0),'|',
                    c.standard_truck,'|',
                    IFNULL(d.standard_truck_cost, 0)
                    SEPARATOR '~'
                ) AS standard_truck_deliveries,
                GROUP_CONCAT(
                    IFNULL(d.standard_ute_count, 0),'|',
                    c.standard_ute,'|',
                    IFNULL(d.standard_ute_cost, 0)
                    SEPARATOR '~'
                ) AS standard_ute_deliveries,
                GROUP_CONCAT(
                    IFNULL(d.urgent_truck_count, 0),'|',
                    c.urgent_truck, '|',
                    IFNULL(d.urgent_truck_cost, 0)
                    SEPARATOR '~'
                ) AS urgent_truck_deliveries,
                GROUP_CONCAT(
                    IFNULL(d.urgent_ute_count, 0),'|',
                    c.urgent_ute, '|',
                    IFNULL(d.urgent_ute_cost, 0)
                    SEPARATOR '~'
                ) AS urgent_ute_deliveries,
                GROUP_CONCAT(
                    IFNULL(p.standard_truck_count, 0),'|',
                    c.standard_truck,'|',
                    IFNULL(p.standard_truck_cost, 0)
                    SEPARATOR '~'
                ) AS standard_truck_pickups,
                GROUP_CONCAT(
                    IFNULL(p.standard_ute_count, 0),'|',
                    c.standard_ute,'|',
                    IFNULL(p.standard_ute_cost, 0)
                    SEPARATOR '~'
                ) AS standard_ute_pickups,
                GROUP_CONCAT(
                    IFNULL(p.urgent_truck_count, 0),'|',
                    c.urgent_truck, '|',
                    IFNULL(p.urgent_truck_cost, 0)
                    SEPARATOR '~'
                ) AS urgent_truck_pickups,
                GROUP_CONCAT(
                    IFNULL(p.urgent_ute_count, 0),'|',
                    c.urgent_ute, '|',
                    IFNULL(p.urgent_ute_cost, 0)
                    SEPARATOR '~'
                ) AS urgent_ute_pickups
            FROM
                (
                    SELECT
                        dh.*,
                        CAST(ROUND(dh.standard_bay_days * client_charges.standard_bay / 7,2) AS DECIMAL(10,2)) AS standard_storage_charge,
                        CAST(ROUND(dh.oversize_bay_days * client_charges.oversize_bay / 7,2) AS DECIMAL(10,2)) AS oversize_storage_charge
                    FROM
                        (SELECT
                            client_id,
                            SUM(CASE WHEN size = 'standard' THEN
                                CASE
                                WHEN
                                    date_removed = 0
                                THEN
                                    CASE
                                    WHEN
                                        date_added < $from
                                    THEN
                                        DATEDIFF(
                                            FROM_UNIXTIME($to),
                                            FROM_UNIXTIME($from)
                                        )
                                    ELSE
                                        DATEDIFF(
                                            FROM_UNIXTIME($to),
                                            FROM_UNIXTIME(date_added)
                                        )
                                    END
                                ELSE
                                    CASE
                                    WHEN
                                        date_added < $from
                                    THEN
                                        CASE
                                        WHEN
                                            date_removed > $to
                                        THEN
                                            DATEDIFF(
                                                FROM_UNIXTIME($to),
                                                FROM_UNIXTIME($from)
                                            )
                                        ELSE
                                            DATEDIFF(
                                                FROM_UNIXTIME(date_removed),
                                                FROM_UNIXTIME($from)
                                            )
                                        END
                                    ELSE
                                        CASE
                                        WHEN
                                            date_removed > $to
                                        THEN
                                            DATEDIFF(
                                                FROM_UNIXTIME($to),
                                                FROM_UNIXTIME(date_added )
                                            )
                                        ELSE
                                            DATEDIFF(
                                                FROM_UNIXTIME(date_removed),
                                                FROM_UNIXTIME(date_added)
                                            )
                                        END
                                    END
                                END
                            ELSE 0 END) AS standard_bay_days,
                            SUM(CASE WHEN size = 'oversize' OR size = 'double-oversize' THEN
                                CASE
                                WHEN
                                    date_removed = 0
                                THEN
                                    CASE
                                    WHEN
                                        date_added < $from
                                    THEN
                                        DATEDIFF(
                                            FROM_UNIXTIME($to),
                                            FROM_UNIXTIME($from)
                                        )
                                    ELSE
                                        DATEDIFF(
                                            FROM_UNIXTIME($to),
                                            FROM_UNIXTIME(date_added)
                                        )
                                    END
                                ELSE
                                    CASE
                                    WHEN
                                        date_added < $from
                                    THEN
                                        CASE
                                        WHEN
                                            date_removed > $to
                                        THEN
                                            DATEDIFF(
                                                FROM_UNIXTIME($to),
                                                FROM_UNIXTIME($from)
                                            )
                                        ELSE
                                            DATEDIFF(
                                                FROM_UNIXTIME(date_removed),
                                                FROM_UNIXTIME($from)
                                            )
                                        END
                                    ELSE
                                        CASE
                                        WHEN
                                            date_removed > $to
                                        THEN
                                            DATEDIFF(
                                                FROM_UNIXTIME($to),
                                                FROM_UNIXTIME(date_added )
                                            )
                                        ELSE
                                            DATEDIFF(
                                                FROM_UNIXTIME(date_removed),
                                                FROM_UNIXTIME(date_added)
                                            )
                                        END
                                    END
                                END
                            ELSE 0 END) AS oversize_bay_days
                        FROM
                            delivery_clients_bays
                        WHERE
                            date_added < $to
                        GROUP BY
                            client_id
                        )dh JOIN
                        client_charges ON dh.client_id = client_charges.client_id
                )s JOIN
                (
                    SELECT
                        deliveries.client_id,
                        clients.client_name,
                        SUM(CASE WHEN vehicle_type = 'truck' AND urgency_id = 3 THEN 1 ELSE 0 END) AS standard_truck_count,
                        SUM(CASE WHEN vehicle_type = 'ute' AND urgency_id = 3 THEN 1 ELSE 0 END) AS standard_ute_count,
                        SUM(CASE WHEN vehicle_type = 'truck' AND urgency_id < 3 THEN 1 ELSE 0 END) AS urgent_truck_count,
                        SUM(CASE WHEN vehicle_type = 'ute' AND urgency_id < 3 THEN 1 ELSE 0 END) AS urgent_ute_count,
                        SUM(CASE WHEN vehicle_type = 'truck' AND urgency_id = 3 THEN shipping_charge ELSE 0 END) AS standard_truck_cost,
                        SUM(CASE WHEN vehicle_type = 'ute' AND urgency_id = 3 THEN shipping_charge ELSE 0 END) AS standard_ute_cost,
                        SUM(CASE WHEN vehicle_type = 'truck' AND urgency_id < 3 THEN shipping_charge ELSE 0 END) AS urgent_truck_cost,
                        SUM(CASE WHEN vehicle_type = 'ute' AND urgency_id < 3 THEN shipping_charge ELSE 0 END) AS urgent_ute_cost
                    FROM
                        deliveries JOIN
                        clients ON deliveries.client_id = clients.id
                    WHERE
                        deliveries.date_fulfilled > $from AND deliveries.date_fulfilled < $to
                    GROUP BY
                        deliveries.client_id
                )d ON d.client_id = s.client_id LEFT JOIN
                (
                    SELECT
                        client_id,
                        SUM(CASE WHEN vehicle_type = 'truck' AND urgency_id = 3 THEN 1 ELSE 0 END) AS standard_truck_count,
                        SUM(CASE WHEN vehicle_type = 'ute' AND urgency_id = 3 THEN 1 ELSE 0 END) AS standard_ute_count,
                        SUM(CASE WHEN vehicle_type = 'truck' AND urgency_id < 3 THEN 1 ELSE 0 END) AS urgent_truck_count,
                        SUM(CASE WHEN vehicle_type = 'ute' AND urgency_id < 3 THEN 1 ELSE 0 END) AS urgent_ute_count,
                        SUM(CASE WHEN vehicle_type = 'truck' AND urgency_id = 3 THEN shipping_charge ELSE 0 END) AS standard_truck_cost,
                        SUM(CASE WHEN vehicle_type = 'ute' AND urgency_id = 3 THEN shipping_charge ELSE 0 END) AS standard_ute_cost,
                        SUM(CASE WHEN vehicle_type = 'truck' AND urgency_id < 3 THEN shipping_charge ELSE 0 END) AS urgent_truck_cost,
                        SUM(CASE WHEN vehicle_type = 'ute' AND urgency_id < 3 THEN shipping_charge ELSE 0 END) AS urgent_ute_cost
                    FROM
                        pickups
                    WHERE
                        date_fulfilled > $from AND date_fulfilled < $to
                    GROUP BY
                        client_id
                )p ON s.client_id = p.client_id JOIN
                (
                    SELECT * FROM client_charges
                )c ON s.client_id = c.client_id
            WHERE
                d.client_id = $client_id
        ";
        die($q);
        $charges = $db->queryRow($q);
        //$charges['service_fee'] = $client_info['service_fee'];

        return $charges;
    }
}
?>