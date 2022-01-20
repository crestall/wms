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
        //echo "The request<pre>",print_r($client_values),"</pre>";die();
        if(!empty($data['contact_name'])) $client_values['contact_name'] = $data['contact_name'];
        if(isset($data['image_name'])) $client_values['logo'] = $data['image_name'].".jpg";
        if(isset($data['production_client'])) $client_values['production_client'] = 1;
        if(isset($data['delivery_client'])) $client_values['delivery_client'] = 1;
        $client_values['can_adjust'] = (!isset($data['can_adjust']))? 0 : 1;
        $client_values['products_description'] = (!empty($data['products_description']))? $data['products_description']: null;
        echo "<pre>",print_r($client_values),"</pre>";die();
        $client_id = $db->insertQuery($this->table, $client_values);
        $truck_delivery_charge_values = [
            'client_id'     => $client_id,
            'vehicle_type'  => 'truck'
        ];
        $ute_delivery_charge_values = [
            'client_id'     => $client_id,
            'vehicle_type'  => 'ute'
        ];
        if(!empty($data['truck_standard_charge'])) $truck_delivery_charge_values['standard_charge'] = $data['truck_standard_charge'];
        if(!empty($data['truck_urgent_charge'])) $truck_delivery_charge_values['urgent_charge'] = $data['truck_urgent_charge'];
        if(!empty($data['ute_standard_charge'])) $ute_delivery_charge_values['standard_charge'] = $data['ute_standard_charge'];
        if(!empty($data['ute_urgent_charge'])) $ute_delivery_charge_values['urgent_charge'] = $data['ute_urgent_charge'];
        $db->insertQuery($this->delivery_charges_table, $truck_delivery_charge_values);
        $db->insertQuery($this->delivery_charges_table, $ute_delivery_charge_values);
        $storage_charges = [
            'client_id' => $client_id
        ];
        if(!empty($data['standard_storage_charge'])) $storage_charges['standard'] = $data['standard_storage_charge'];
        if(!empty($data['oversize_storage_charge'])) $storage_charges['oversize'] = $data['oversize_storage_charge'];
        if(!empty($data['pickface_storage_charge'])) $storage_charges['pickface'] = $data['pickface_storage_charge'];
        $db->insertQuery($this->storage_charges_table, $storage_charges);
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
        if(!empty($data['ute_charge'])) $client_values['ute_charge'] = $data['ute_charge'];
        if(isset($data['image_name'])) $client_values['logo'] = $data['image_name'].".jpg";
        elseif(isset($_POST['delete_logo'])) $client_values['logo'] = "default.png";
        $client_values['products_description'] = (!empty($data['products_description']))? $data['products_description']: null;
        $db->updatedatabaseFields($this->table, $client_values, $data['client_id']);
        $truck_values = [
            'standard_charge'   => 0,
            'urgent_charge'     => 0
        ];
        $ute_values = [
            'standard_charge'   => 0,
            'urgent_charge'     => 0
        ];
        if(!empty($data['truck_standard_charge'])) $truck_values['standard_charge'] = $data['truck_standard_charge'];
        if(!empty($data['truck_urgent_charge'])) $truck_values['urgent_charge'] = $data['truck_urgent_charge'];
        if(!empty($data['ute_urgent_charge'])) $ute_values['urgent_charge'] = $data['ute_urgent_charge'];
        if(!empty($data['ute_standard_charge'])) $ute_values['standard_charge'] = $data['ute_standard_charge'];
        $db->updatedatabaseFields($this->delivery_charges_table, $truck_values, $data['tc_line_id']);
        $db->updatedatabaseFields($this->delivery_charges_table, $ute_values, $data['uc_line_id']);
        $sc_values = [
            'standard'  => 0,
            'oversize'  => 0,
            'pickface'  => 0
        ];
        if(!empty($data['standard_storage_charge'])) $sc_values['standard'] = $data['standard_storage_charge'];
        if(!empty($data['oversize_storage_charge'])) $sc_values['oversize'] = $data['oversize_storage_charge'];
        if(!empty($data['pickface_storage_charge'])) $sc_values['pickface'] = $data['pickface_storage_charge'];
        $db->updatedatabaseFields($this->storage_charges_table, $sc_values, $data['sc_line_id']); 
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
}
?>