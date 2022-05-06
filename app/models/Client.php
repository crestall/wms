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
        if(isset($data['pick_pack'])) $client_values['pick_pack'] = 1;
        $client_values['can_adjust'] = (!isset($data['can_adjust']))? 0 : 1;
        $client_values['products_description'] = (!empty($data['products_description']))? $data['products_description']: null;

        $charges_values = array(
            'standard_truck'        => $data['standard_truck'],
            'urgent_truck'          => $data['urgent_truck'],
            'standard_ute'          => $data['standard_ute'],
            'urgent_ute'            => $data['urgent_ute'],
            'standard_bay'          => $data['standard_bay'],
            'oversize_bay'          => $data['oversize_bay'],
            '40GP_loose'            => $data['40GP_loose'],
            '20GP_loose'            => $data['20GP_loose'],
            '40GP_palletised'       => $data['40GP_palletised'],
            '20GP_palletised'       => $data['20GP_palletised'],
            'max_loose_40GP'        => $data['max_loose_40GP'],
            'max_loose_20GP'        => $data['max_loose_20GP'],
            'additional_loose'      => $data['additional_loose'],
            'repalletising'         => $data['repalletising'],
            'shrinkwrap'            => $data['shrinkwrap'],
            'service_fee'           => $data['service_fee'],
            'manual_order_entry'    => $data['manual_order_entry'],
            'pallet_in'             => $data['pallet_in'],
            'pallet_out'            => $data['pallet_in'],
            'carton_in'             => $data['carton_in'],
            'carton_out'            => $data['carton_out']
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
        $client_values['pick_pack'] = (isset($data['pick_pack']))? 1 : 0;
        $client_values['use_bubblewrap'] = (isset($data['use_bubblewrap']))? 1 : 0;
        $client_values['can_adjust'] = (!isset($data['can_adjust']))? 0 : 1;
        if(!empty($data['contact_name'])) $client_values['contact_name'] = $data['contact_name'];
        if(isset($data['image_name'])) $client_values['logo'] = $data['image_name'].".jpg";
        elseif(isset($_POST['delete_logo'])) $client_values['logo'] = "default.png";
        $client_values['products_description'] = (!empty($data['products_description']))? $data['products_description']: null;
        $db->updatedatabaseFields($this->table, $client_values, $data['client_id']);
        $charges_values = array(
            'standard_truck'        => $data['standard_truck'],
            'urgent_truck'          => $data['urgent_truck'],
            'standard_ute'          => $data['standard_ute'],
            'urgent_ute'            => $data['urgent_ute'],
            'standard_bay'          => $data['standard_bay'],
            'oversize_bay'          => $data['oversize_bay'],
            '40GP_loose'            => $data['40GP_loose'],
            '20GP_loose'            => $data['20GP_loose'],
            '40GP_palletised'       => $data['40GP_palletised'],
            '20GP_palletised'       => $data['20GP_palletised'],
            'max_loose_40GP'        => $data['max_loose_40GP'],
            'max_loose_20GP'        => $data['max_loose_20GP'],
            'additional_loose'      => $data['additional_loose'],
            'repalletising'         => $data['repalletising'],
            'shrinkwrap'            => $data['shrinkwrap'],
            'service_fee'           => $data['service_fee'],
            'manual_order_entry'    => $data['manual_order_entry'],
            'pallet_in'             => $data['pallet_in'],
            'pallet_out'            => $data['pallet_out'],
            'carton_in'             => $data['carton_in'],
            'carton_out'            => $data['carton_out']
        );
        //echo "<pre>",print_r($charges_values),"</pre>"; die();
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
        $q = "SELECT id, client_name FROM clients WHERE active = 1 AND pick_pack = 1";
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

    public function getPPClientDeliveryHandlingCharges($client_id, $from, $to)
    {
        $db = Database::openConnection();
        $q = "
            SELECT
                cd.client_id, cd.client_name,
                GROUP_CONCAT(
                    IFNULL(dhc.eparcel_count,0),'|',
                    IFNULL(dhc.eparcel_charge,0)
                    SEPARATOR '~'
                ) AS eparcel,
                GROUP_CONCAT(
                    IFNULL(dhc.international_count,0),'|',
                    IFNULL(dhc.international_charge,0) SEPARATOR '~'
                ) AS eparcel_international,
                GROUP_CONCAT(
                    IFNULL(dhc.eparcel_express_count,0),'|',
                    IFNULL(dhc.eparcel_express_charge,0) SEPARATOR '~'
                ) AS eparcel_express,
                GROUP_CONCAT(
                    IFNULL(dhc.dfe_count,0),'|',
                    IFNULL(dhc.dfe_charge,0)
                    SEPARATOR '~'
                ) AS direct_freight_express,
                GROUP_CONCAT(
                    IFNULL(dhc.fsg_count,0),'|',
                    IFNULL(dhc.fsg_charge,0)
                    SEPARATOR '~'
                ) AS FSG_delivery,
                GROUP_CONCAT(
                    IFNULL(dhc.total_orders,0),'|',
                    IFNULL(dhc.handling_charge,0)
                    SEPARATOR '~'
                ) AS handling_charge,
                GROUP_CONCAT(
                    IFNULL(icc.total_collections,0),'|',
                    IFNULL(icc.charge,0)
                    SEPARATOR '~'
                ) AS collections_charge
            FROM
                (
                    SELECT
                        clients.id AS client_id,
                        clients.client_name
                    FROM
                        clients
                    WHERE
                        pick_pack = 1 AND active = 1
                )cd LEFT JOIN
                (
                    SELECT
                        client_id,
                        SUM(CASE WHEN courier_id = 1 AND country = 'AU'  THEN 1 ELSE 0 END) AS eparcel_count,
                        SUM(CASE WHEN courier_id = 1 AND country = 'AU' THEN postage_charge ELSE 0 END) AS eparcel_charge,
        	            SUM(CASE WHEN courier_id = 1 AND country != 'AU'  THEN 1 ELSE 0 END) AS international_count,
        	            SUM(CASE WHEN courier_id = 1 AND country != 'AU' THEN postage_charge ELSE 0 END) AS international_charge,
                        SUM(CASE WHEN courier_id = 7 THEN 1 ELSE 0 END) AS eparcel_express_count,
                        SUM(CASE WHEN courier_id = 7 THEN postage_charge ELSE 0 END) AS eparcel_express_charge,
                        SUM(CASE WHEN courier_id = 11 THEN 1 ELSE 0 END) AS dfe_count,
                        SUM(CASE WHEN courier_id = 11 THEN postage_charge ELSE 0 END) AS dfe_charge,
                        SUM(CASE WHEN (courier_id = 4 OR courier_id = 8) THEN 1 ELSE 0 END) AS fsg_count,
                        SUM(CASE WHEN (courier_id = 4 OR courier_id = 8) THEN postage_charge ELSE 0 END) AS fsg_charge,
                        SUM(CASE WHEN status_id = 4 THEN 1 ELSE 0 END) AS total_orders,
                        SUM(CASE WHEN status_id = 4 THEN handling_charge ELSE 0 END) AS handling_charge
                    FROM
                        orders
                    WHERE
                        date_fulfilled BETWEEN $from AND $to
                    GROUP BY
                        client_id
                )dhc ON dhc.client_id = cd.client_id LEFT JOIN
                (
                    SELECT
                        client_id,
                        COUNT(*) AS total_collections,
                        SUM(charge) AS charge
                    FROM
                        items_collections
                    WHERE
                        date_entered BETWEEN $from AND $to
                    GROUP BY
                        client_id
                )icc ON icc.client_id = cd.client_id
            WHERE
                cd.client_id = $client_id
        ";
        //die($q);
        return $db->queryRow($q);
    }

    public function getPPClientStorageCharges($client_id, $from, $to)
    {
        $db = Database::openConnection();
        $q = "
            SELECT
                cd.client_id, cd.client_name,
                GROUP_CONCAT(
                    IFNULL(cb.standard_bays,0),'|',
                    cc.standard_bay,'|',
                    IFNULL(cb.standard_bays,0) * cc.standard_bay
                    SEPARATOR '~'
                ) AS standard_bays,
                GROUP_CONCAT(
                    IFNULL(cb.oversize_bays,0),'|',
                    cc.oversize_bay,'|',
                    IFNULL(cb.oversize_bays,0) * cc.oversize_bay
                    SEPARATOR '~'
                ) AS oversize_bays
            FROM
                (
                    SELECT
                        id AS client_id,
                        client_name
                    FROM
                        clients
                    WHERE
                        pick_pack = 1 AND active = 1
                )cd JOIN
                (
                    SELECT *
                    FROM client_charges
                )cc ON cd.client_id = cc.client_id LEFT JOIN
                (
                    SELECT
                        client_id,
                        date_added, date_removed,
                        SUM(CASE WHEN oversize = 1 THEN pallet_multiplier ELSE 0 END) AS oversize_bays,
                        SUM(CASE WHEN oversize = 0 THEN pallet_multiplier ELSE 0 END) AS standard_bays
                    FROM
                        clients_bays
                    WHERE
                        date_added < $to AND
                        (date_removed > $from OR date_removed = 0)
                    GROUP BY
                        client_id
                )cb ON cb.client_id = cd.client_id
            WHERE
                cd.client_id = $client_id
            GROUP BY
                cd.client_id
        ";
         //die($q);
        return $db->queryRow($q);
    }

    public function getPPClientGeneralCharges($client_id, $from, $to)
    {
        $db = Database::openConnection();
        $q = "
            SELECT
                cd.client_id, cd.client_name,
                GROUP_CONCAT(
                    1, '|',
                    FORMAT( cc.service_fee /(13/3),2), '|',
                    FORMAT( cc.service_fee /(13/3),2)
                    SEPARATOR '~'
                ) AS service_fee,
                GROUP_CONCAT(
                    IFNULL(rs.repalletise_count, 0),'|',
                    cc.repalletising, '|',
                    cc.repalletising * IFNULL(rs.repalletise_count, 0)
                    SEPARATOR '~'
                ) AS repalletising_inventory,
                GROUP_CONCAT(
                    IFNULL(rs.shrinkwrap_count, 0),'|',
                    cc.shrinkwrap, '|',
                    cc.shrinkwrap * IFNULL(rs.shrinkwrap_count, 0)
                    SEPARATOR '~'
                ) AS shrinkwrapping_pallets,
                GROUP_CONCAT(
                    IFNULL(gi.pallets_in, 0),'|',
                    cc.pallet_in, '|',
                    cc.pallet_in * IFNULL(gi.pallets_in, 0)
                    SEPARATOR '~'
                ) AS pallets_received,
                GROUP_CONCAT(
                    IFNULL(gi.cartons_in, 0),'|',
                    cc.carton_in, '|',
                    cc.carton_in * IFNULL(gi.cartons_in, 0)
                    SEPARATOR '~'
                ) AS cartons_received
            FROM
                (
                    SELECT
                        clients.id AS client_id,
                        clients.client_name
                    FROM
                        clients
                    WHERE
                        pick_pack = 1 AND active = 1
                )cd JOIN
                (
                    SELECT
                        *
                    FROM
                        client_charges
                )cc ON cc.client_id = cd.client_id LEFT JOIN
                (
                    SELECT
                        client_id,
                        COALESCE(SUM(repalletise_count),0) AS repalletise_count,
                        COALESCE(SUM(shrinkwrap_count),0) AS shrinkwrap_count
                    FROM
                        repalletise_shrinkwrap
                    WHERE
                        date > $from AND date < $to
                    GROUP BY
                        client_id
                )rs ON rs.client_id = cd.client_id LEFT JOIN
                (
                    SELECT
                        client_id,
                        COALESCE(SUM(pallets),0) AS pallets_in,
                        COALESCE(SUM(cartons),0) AS cartons_in
                    FROM
                        inwards_goods
                    WHERE
                        date > $from AND date < $to
                    GROUP BY
                        client_id
                ) gi ON gi.client_id = cd.client_id
            WHERE
                cd.client_id = $client_id
        ";
        //die($q);
        return $db->queryRow($q);
    }

    public function getClientContainerUnloadingCharges($client_id, $from, $to)
    {
        $db = Database::openConnection();
        $q = "
            SELECT
                cd.client_id,
                cd.client_name,
                GROUP_CONCAT(
                    IFNULL(loose_20foot_container_count,0),'|',
                    cc.20GP_loose,'|',
                    IFNULL(loose_20foot_container_count,0) * cc.20GP_loose
                    SEPARATOR '~'
                ) AS 20GP_loose_unpack,
                GROUP_CONCAT(
                    IFNULL(loose_40foot_container_count,0),'|',
                    cc.40GP_loose,'|',
                    IFNULL(loose_40foot_container_count,0) * cc.40GP_loose
                    SEPARATOR '~'
                ) AS 40GP_loose_unpack,
                GROUP_CONCAT(
                    IFNULL(palletised_20foot_container_count,0),'|',
                    cc.20GP_palletised,'|',
                    IFNULL(palletised_20foot_container_count,0) * cc.20GP_palletised
                    SEPARATOR '~'
                ) AS 20GP_palletised_unpack,
                GROUP_CONCAT(
                    IFNULL(palletised_40foot_container_count,0),'|',
                    cc.40GP_palletised,'|',
                    FORMAT(IFNULL(palletised_40foot_container_count,0) * cc.40GP_palletised,2)
                    SEPARATOR '~'
                ) AS 40GP_palletised_unpack,
                GROUP_CONCAT(
                    IFNULL(extra_loose_items,0),'|',
                    cc.additional_loose,'|',
                    FORMAT(IFNULL(extra_loose_items,0) * cc.additional_loose,2)
                    SEPARATOR '~'
                ) AS loose_items_over_allowance
            FROM
                (
                    SELECT
                        id AS client_id,
                        client_name
                    FROM
                        clients
                    WHERE
                        active = 1
                ) cd JOIN
                (
                    SELECT *
                    FROM client_charges
                ) cc ON cd.client_id = cc.client_id LEFT JOIN
                (
                    SELECT
                        client_id,
                        COALESCE(SUM(CASE WHEN container_size = '20 Foot' AND load_type = 'Loose' THEN 1 ELSE 0 END),0) AS loose_20foot_container_count,
                        COALESCE(SUM(CASE WHEN container_size = '20 Foot' AND load_type = 'Palletised' THEN 1 ELSE 0 END),0) AS palletised_20foot_container_count,
                        COALESCE(SUM(CASE WHEN container_size = '40 Foot' AND load_type = 'Palletised' THEN 1 ELSE 0 END),0) AS palletised_40foot_container_count,
                        COALESCE(SUM(CASE WHEN container_size = '40 Foot' AND load_type = 'Loose' THEN 1 ELSE 0 END),0) AS loose_40foot_container_count,
                        SUM(
                        CASE
                        WHEN
                            load_type = 'Loose'
                        THEN
                            CASE
                            WHEN
                                container_size = '40 Foot'
                            THEN
                                CASE
                                WHEN
                                    item_count > 1250
                                THEN
                                    item_count - 1250
                                ELSE
                                    0
                                END
                            ELSE
                                CASE
                                WHEN
                                    item_count > 800
                                THEN
                                    item_count - 800
                                ELSE
                                    0
                                END
                            END
                        ELSE
                            0
                        END) AS extra_loose_items
                    FROM
                        unloaded_containers
                    WHERE
                    	date between $from AND $to
                    GROUP BY
                        client_id
                ) uc ON uc.client_id = cd.client_id
            WHERE
                cd.client_id = $client_id
        ";
        //die($q);
        return $db->queryRow($q);
    }

    public function getDeliveryClientGeneralCharges($client_id, $from, $to)
    {
        $db = Database::openConnection();
        $q = "
            SELECT
                cd.client_id,cd.client_name,
                GROUP_CONCAT(
                    1, '|',
                    cc.service_fee, '|',
                    cc.service_fee
                    SEPARATOR '~'
                ) AS service_fee,
                GROUP_CONCAT(
                    IFNULL(rs.repalletise_count, 0),'|',
                    cc.repalletising, '|',
                    cc.repalletising * IFNULL(rs.repalletise_count, 0)
                    SEPARATOR '~'
                ) AS repalletising_inventory,
                GROUP_CONCAT(
                    IFNULL(rs.shrinkwrap_count, 0),'|',
                    cc.shrinkwrap, '|',
                    cc.shrinkwrap * IFNULL(rs.shrinkwrap_count, 0)
                    SEPARATOR '~'
                ) AS shrinkwrapping_pallets ,
                GROUP_CONCAT(
                    IFNULL(med.manual_deliveries,0) + IFNULL(mep.manual_pickups,0),'|',
                    cc.manual_order_entry,'|',
                    cc.manual_order_entry * (IFNULL(med.manual_deliveries,0) + IFNULL(mep.manual_pickups,0))
                    SEPARATOR '~'
                ) AS manual_job_entry,
                GROUP_CONCAT(
                    IFNULL(pr.pallets_received,0),'|',
                    cc.pallet_in,'|',
                    cc.pallet_in * (IFNULL(pr.pallets_received,0))
                    SEPARATOR '~'
                ) AS pallets_received,
                GROUP_CONCAT(
                    IFNULL(pd.pallets_dispatched,0),'|',
                    cc.pallet_out,'|',
                    cc.pallet_out * (IFNULL(pd.pallets_dispatched,0))
                    SEPARATOR '~'
                ) AS pallets_dispatched
            FROM
            (
                SELECT
                    clients.id AS client_id,
                    clients.client_name
                FROM
                    clients
                WHERE
                    delivery_client = 1
            )cd JOIN
            (
                SELECT
                    *
                FROM
                    client_charges
            )cc ON cc.client_id = cd.client_id LEFT JOIN
            (
                SELECT
                    client_id,
                    COALESCE(SUM(repalletise_count),0) AS repalletise_count,
                    COALESCE(SUM(shrinkwrap_count),0) AS shrinkwrap_count
                FROM
                    repalletise_shrinkwrap
                WHERE
                    date > $from AND date < $to
                GROUP BY
                    client_id
            )rs ON rs.client_id = cd.client_id LEFT JOIN
            (
                SELECT
                    client_id,
                    COALESCE(SUM(manually_entered),0) AS manual_deliveries
                FROM
                    deliveries
                WHERE
                    date_fulfilled > $from AND date_fulfilled < $to
                GROUP BY
                    client_id
            )med ON med.client_id = cd.client_id LEFT JOIN
            (
                SELECT
                    client_id,
                    COALESCE(SUM(manually_entered),0) AS manual_pickups
                FROM
                    deliveries
                WHERE
                    date_fulfilled > $from AND date_fulfilled < $to
                GROUP BY
                    client_id
            )mep ON mep.client_id = cd.client_id LEFT JOIN
            (
                SELECT
                    d.client_id,
                    COUNT(*) AS pallets_dispatched
                FROM
                    deliveries_items di JOIN
                    deliveries d ON d.id = di.deliveries_id
                WHERE
                    d.date_fulfilled > $from AND d.date_fulfilled < $to
                GROUP BY
                    d.client_id
            )pd ON pd.client_id = cd.client_id LEFT JOIN
            (
                SELECT
                    p.client_id,
                    COUNT(*) AS pallets_received
                FROM
                    pickups_items pi JOIN
                    pickups p ON p.id = pi.pickups_id
                WHERE
                    p.date_fulfilled > $from AND p.date_fulfilled < $to
                GROUP BY
                    p.client_id
            )pr ON pr.client_id = cd.client_id
            WHERE
                cd.client_id = $client_id
        ";
        //die($q);
        return $db->queryRow($q);
    }

    public function getDeliveryClientDeliveryCharges($client_id, $from, $to)
    {
        $db = Database::openConnection();
        $q = "
            SELECT
                cd.client_id,cd.client_name,
                GROUP_CONCAT(
                    IFNULL(d.standard_truck_count, 0),'|',
                    cc.standard_truck,'|',
                    IFNULL(d.standard_truck_cost, 0)
                    SEPARATOR '~'
                ) AS standard_truck_deliveries,
                GROUP_CONCAT(
                    IFNULL(d.standard_ute_count, 0),'|',
                    cc.standard_ute,'|',
                    IFNULL(d.standard_ute_cost, 0)
                    SEPARATOR '~'
                ) AS standard_ute_deliveries,
                GROUP_CONCAT(
                    IFNULL(d.urgent_truck_count, 0),'|',
                    cc.urgent_truck, '|',
                    IFNULL(d.urgent_truck_cost, 0)
                    SEPARATOR '~'
                ) AS urgent_truck_deliveries,
                GROUP_CONCAT(
                    IFNULL(d.urgent_ute_count, 0),'|',
                    cc.urgent_ute, '|',
                    IFNULL(d.urgent_ute_cost, 0)
                    SEPARATOR '~'
                ) AS urgent_ute_deliveries,
                GROUP_CONCAT(
                    IFNULL(p.standard_truck_count, 0),'|',
                    cc.standard_truck,'|',
                    IFNULL(p.standard_truck_cost, 0)
                    SEPARATOR '~'
                ) AS standard_truck_pickups,
                GROUP_CONCAT(
                    IFNULL(p.standard_ute_count, 0),'|',
                    cc.standard_ute,'|',
                    IFNULL(p.standard_ute_cost, 0)
                    SEPARATOR '~'
                ) AS standard_ute_pickups,
                GROUP_CONCAT(
                    IFNULL(p.urgent_truck_count, 0),'|',
                    cc.urgent_truck, '|',
                    IFNULL(p.urgent_truck_cost, 0)
                    SEPARATOR '~'
                ) AS urgent_truck_pickups,
                GROUP_CONCAT(
                    IFNULL(p.urgent_ute_count, 0),'|',
                    cc.urgent_ute, '|',
                    IFNULL(p.urgent_ute_cost, 0)
                    SEPARATOR '~'
                ) AS urgent_ute_pickups
            FROM
                (SELECT
                    clients.id AS client_id, clients.client_name
                FROM
                    clients
                )cd LEFT JOIN
                (SELECT
                    client_id,
                    SUM(CASE WHEN (vehicle_type = 'truck' AND urgency_id = 3) OR vehicle_type = 'client_supplied'THEN 1 ELSE 0 END) AS standard_truck_count,
                    SUM(CASE WHEN vehicle_type = 'ute' AND urgency_id = 3 THEN 1 ELSE 0 END) AS standard_ute_count,
                    SUM(CASE WHEN vehicle_type = 'truck' AND urgency_id < 3 THEN 1 ELSE 0 END) AS urgent_truck_count,
                    SUM(CASE WHEN vehicle_type = 'ute' AND urgency_id < 3 THEN 1 ELSE 0 END) AS urgent_ute_count,
                    SUM(CASE WHEN (vehicle_type = 'truck' AND urgency_id = 3) OR vehicle_type = 'client_supplied' THEN shipping_charge ELSE 0 END) AS standard_truck_cost,
                    SUM(CASE WHEN vehicle_type = 'ute' AND urgency_id = 3 THEN shipping_charge ELSE 0 END) AS standard_ute_cost,
                    SUM(CASE WHEN vehicle_type = 'truck' AND urgency_id < 3 THEN shipping_charge ELSE 0 END) AS urgent_truck_cost,
                    SUM(CASE WHEN vehicle_type = 'ute' AND urgency_id < 3 THEN shipping_charge ELSE 0 END) AS urgent_ute_cost
                FROM
                    deliveries
                WHERE
                    date_fulfilled > $from AND date_fulfilled < $to
                GROUP BY
                    client_id
                )d ON d.client_id = cd.client_id LEFT JOIN
                (SELECT
                    client_id,
                    SUM(CASE WHEN (vehicle_type = 'truck' AND urgency_id = 3) OR vehicle_type = 'client_supplied' THEN 1 ELSE 0 END) AS standard_truck_count,
                    SUM(CASE WHEN vehicle_type = 'ute' AND urgency_id = 3 THEN 1 ELSE 0 END) AS standard_ute_count,
                    SUM(CASE WHEN vehicle_type = 'truck' AND urgency_id < 3 THEN 1 ELSE 0 END) AS urgent_truck_count,
                    SUM(CASE WHEN vehicle_type = 'ute' AND urgency_id < 3 THEN 1 ELSE 0 END) AS urgent_ute_count,
                    SUM(CASE WHEN (vehicle_type = 'truck' AND urgency_id = 3) OR vehicle_type = 'client_supplied' THEN shipping_charge ELSE 0 END) AS standard_truck_cost,
                    SUM(CASE WHEN vehicle_type = 'ute' AND urgency_id = 3 THEN shipping_charge ELSE 0 END) AS standard_ute_cost,
                    SUM(CASE WHEN vehicle_type = 'truck' AND urgency_id < 3 THEN shipping_charge ELSE 0 END) AS urgent_truck_cost,
                    SUM(CASE WHEN vehicle_type = 'ute' AND urgency_id < 3 THEN shipping_charge ELSE 0 END) AS urgent_ute_cost
                FROM
                    pickups
                WHERE
                    date_fulfilled > $from AND date_fulfilled < $to
                GROUP BY
                    client_id
                )p ON cd.client_id = p.client_id JOIN
                (
                    SELECT * FROM client_charges
                )cc ON cc.client_id = cd.client_id
            WHERE
                cd.client_id = $client_id
        ";
        //die($q);
        return $db->queryRow($q);
    }

    public function getDeliveryClientStorageCharges($client_id, $from, $to)
    {
        $db = Database::openConnection();
        $q = "
            SELECT
                client_id,
                client_name,
                GROUP_CONCAT(
                    IFNULL(standard_bay_days,0),' bays/week','|',
                    standard_bay,'|',
                    standard_charge
                    SEPARATOR '~'
                ) AS standard_bay_charge,
                GROUP_CONCAT(
                    IFNULL(oversize_bay_days,0),' bays/week','|',
                    oversize_bay,'|',
                    oversize_charge
                    SEPARATOR '~'
                ) AS oversize_bay_charge
            FROM
                (SELECT
                    cd.client_id,
                 	cd.client_name,
                    FORMAT( CEILING(SUM(cb.standard)/7) ,0 ) AS standard_bay_days,
                    FORMAT( CEILING(SUM(cb.oversize)/7) ,0 ) AS oversize_bay_days,
                 	FORMAT( CEILING(SUM(cb.standard)/7) * cc.standard_bay ,2 ) AS standard_charge,
                 	FORMAT( CEILING(SUM(cb.oversize)/7) * cc.oversize_bay ,2 ) AS oversize_charge,
                 	cc.oversize_bay,
                 	cc.standard_bay
                FROM
                    (
                        SELECT
                            id AS client_id,
                            client_name
                        FROM
                            clients
                        WHERE
                            delivery_client = 1 AND active = 1
                    )cd JOIN
                    (
                        SELECT *
                        FROM client_charges
                    ) cc ON cd.client_id = cc.client_id LEFT JOIN
                    (SELECT
                        client_id,
                        CASE
                        WHEN size = 'standard'
                        THEN
                            CASE
                            WHEN date_removed = 0
                            THEN
                                CASE
                                    WHEN date_added < $from
                                    THEN
                                        DATEDIFF( FROM_UNIXTIME($to), FROM_UNIXTIME($from) )
                                    ELSE
                                        CASE
                                        WHEN date_added < $to
                                        THEN
                                            DATEDIFF( FROM_UNIXTIME($to), FROM_UNIXTIME(date_added) )
                                        END
                                    END
                            ELSE
                                CASE
                                WHEN date_added < $from
                                THEN
                                    CASE
                                    WHEN date_removed > $to
                                    THEN
                                        DATEDIFF( FROM_UNIXTIME($to), FROM_UNIXTIME($from) )
                                    ELSE
                                        DATEDIFF( FROM_UNIXTIME(date_removed), FROM_UNIXTIME($from) )
                                    END
                                ELSE
                                    CASE
                                    WHEN date_removed > $to
                                    THEN
                                        DATEDIFF( FROM_UNIXTIME($to), FROM_UNIXTIME(date_added ) )
                                    ELSE
                                        ABS(DATEDIFF( FROM_UNIXTIME(date_removed), FROM_UNIXTIME(date_added) ) )
                                    END
                                END
                            END
                        ELSE
                            0
                        END AS standard
                        ,
                        CASE
                        WHEN size = 'oversize' OR size = 'double-oversize'
                        THEN
                            CASE
                            WHEN date_removed = 0
                            THEN
                                CASE
                                WHEN date_added < $from
                                THEN
                                    DATEDIFF( FROM_UNIXTIME($to), FROM_UNIXTIME($from) )
                                ELSE
                                    DATEDIFF( FROM_UNIXTIME($to), FROM_UNIXTIME(date_added) )
                                END
                            ELSE
                                CASE
                                WHEN date_added < $from
                                THEN
                                    CASE
                                    WHEN date_removed > $to
                                    THEN
                                        DATEDIFF( FROM_UNIXTIME($to), FROM_UNIXTIME($from) )
                                    ELSE
                                        DATEDIFF( FROM_UNIXTIME(date_removed), FROM_UNIXTIME($from) )
                                    END
                                ELSE
                                    CASE
                                    WHEN date_removed > $to
                                    THEN
                                        DATEDIFF( FROM_UNIXTIME($to), FROM_UNIXTIME(date_added ) )
                                    ELSE
                                        DATEDIFF( FROM_UNIXTIME(date_removed), FROM_UNIXTIME(date_added) )
                                    END
                                END
                            END
                        ELSE
                            0
                        END as oversize
                    FROM
                        delivery_clients_bays
                    HAVING
                    	standard >= 0 AND oversize >= 0
                ) cb ON cb.client_id = cc.client_id
                GROUP BY client_id
            )t
            WHERE client_id = $client_id
            GROUP BY client_id
        ";
        //die($q);
        return $db->queryRow($q);
    }
}
?>