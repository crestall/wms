<?php
class Courier extends Model{
    public $eParcelId;
    public $eParcelExpressId;
    public $huntersId;
    public $huntersPluId;
    public $huntersPalId;
    public $threePlTruckId;
    public $localId;
    public $vicLocalId;
    public $directFreightId;
    public $cometLocalId;
    public $sydneyCometId;

    public function __construct()
    {
        parent::__construct();
        $this->eParcelId = $this->getCourierId('eParcel');
        $this->eParcelExpressId = $this->getCourierId('eParcel Express');
        $this->huntersId = $this->getCourierId('Hunters Small');
        $this->huntersPluId = $this->getCourierId('Hunters Bulk');
        $this->huntersPalId = $this->getCourierId('Hunters Pallet');
        $this->threePlTruckId = $this->getCourierId('3PL Truck');
        $this->localId = $this->getCourierId('Local');
        $this->vicLocalId = $this->getCourierId('Vic Local');
        $this->directFreightId = $this->getCourierId('Direct Freight');
        $this->cometLocalId = $this->getCourierId('Comet White Glove');
        $this->sydneyCometId = $this->getCourierId('Sydney Comet');
    }

    public function addCourier($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'      =>  $data['name']
        );
        if(isset($data['table_name']))
        {
            $vals['table_name'] = $data['table_name'];
        }
        return $db->insertQuery($this->table, $vals);
    }

    public function editCourier($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'      =>  $data['name']
        );
        $vals['active'] = (isset($data['active']))? 1:0;
        $vals['table_name'] = (isset($data['table_name']))? $data['table_name']:NULL;
        $db->updateDatabaseFields($this->table, $vals, $data['id']);
        return true;
    }

    public function getSelectCouriers( $selected = false, $choose_none = true, $include_local = true, $exclude = array() )
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        if($choose_none)
        {
            $ret_string = "<option value='0'";
            if( $selected === "0" )
            {
                $ret_string .= " selected='selected'";
            }
            $ret_string .= ">None Selected</option>";
        }
        $query = "SELECT id, name FROM {$this->table} WHERE active = 1";
        if(!$include_local)
            $query .= " AND name != 'Local'";
        foreach($exclude as $ex)
        {
            $query .= " AND name != '$ex'";
        }
        $query .= " ORDER BY name";
        $couriers = $db->queryData($query);
        foreach($couriers as $c)
        {
            $label = $c['name'];
            $value = $c['id'];
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function getCourierName($id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' => $id), 'name');
    }

    public function getCourierNameForOrder($id, $order_id = 0)
    {
        $db = Database::openConnection();
        if($id == $this->threePlTruckId)
            return "3PL Truck";
        elseif($id == $this->localId)
            return $db->queryValue('orders', array('id' => $order_id), 'courier_name');
        else
            return $db->queryValue($this->table, array('id' => $id), 'name');
    }

    public function getCouriers($active = -1)
    {
        $db = Database::openConnection();
        $q = "SELECT * FROM {$this->table}";
        if($active >= 0)
        {
            $q .= " WHERE active = $active";
        }
        $q .= " ORDER BY name";
        return $db->queryData($q);
    }

    public function getTruckId()
    {
        return $this->getCourierId('3PL Truck');
    }

    public function getLocalId()
    {
        return $this->getCourierId('Local');
    }

    public function getVicLocalId()
    {
        return $this->getCourierId('Vic Local');
    }

    public function getCourierId($name)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('name' => $name));
    }
}
?>