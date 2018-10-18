<?php

 /**
  * Client Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>

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

    public function getClientId($name)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('client_name' => $name));
    }

    public function getEparcelClients()
    {
        $db = Database::openConnection();
        return $db->queryData("SELECT client_name, id, eparcel_location FROM clients WHERE api_key IS NOT NULL");
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
        if(!empty($data['carton_charge'])) $client_values['carton_charge'] = $data['carton_charge'];
        if(!empty($data['pallet_charge'])) $client_values['pallet_charge'] = $data['pallet_charge'];
        if(isset($data['image_name'])) $client_values['logo'] = $data['image_name'].".jpg";
        if(isset($data['has_reps'])) $client_values['has_reps'] = 1;
        $client_values['products_description'] = (!empty($data['products_description']))? $data['products_description']: null;
        $client_id = $db->insertQuery($this->table, $client_values);
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

    public function getClientInfo($clientId)
    {
        $db = Database::openConnection();
        $client = $db->queryById($this->table, $clientId);
        $client["id"]    = (int)$client["id"];
        return $client;
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
        $client_values['has_reps'] = (isset($data['has_reps']))? 1 : 0;
        $client_values['use_bubblewrap'] = (isset($data['use_bubblewrap']))? 1 : 0;
        if(!empty($data['contact_name'])) $client_values['contact_name'] = $data['contact_name'];
        if(!empty($data['carton_charge'])) $client_values['carton_charge'] = $data['carton_charge'];
        else $client_values['carton_charge'] = 5;
        if(!empty($data['pallet_charge'])) $client_values['pallet_charge'] = $data['pallet_charge'];
        else $client_values['pallet_charge'] = 5;
        if(isset($data['image_name'])) $client_values['logo'] = $data['image_name'].".jpg";
        elseif(isset($_POST['delete_logo'])) $client_values['logo'] = "default.png";
        $client_values['products_description'] = (!empty($data['products_description']))? $data['products_description']: null;
        $db->updatedatabaseFields($this->table, $client_values, $data['client_id']);

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
}
?>