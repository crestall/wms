<?php

 /**
  * Warehouse Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>


  FUNCTIONS

  addWarehouse
  editWarehouse
  getAllWarehouses($active = 1)
  getSelectAllWarehouses
  getSelectWarehouses
  getWarehouseId($name)
  getWarehouseName($id)
  isDefaultWarehouse($id)
  makeDefault($id)
  */

class Location extends Model{

   public $table = "warehouses";

    public function __construct()
    {

    }

    public function getAllWarehouses($active = 1)
    {
        $db = Database::openConnection();

        return $db->queryData("SELECT * FROM {$this->table} WHERE active = $active ORDER BY name");
    }

    public function getWarehouseName($id)
    {
        $db = Database::openConnection();
        $res = $db->queryRow("SELECT name FROM {$this->table} WHERE id = $id");
        return $res['name'];
    }

    public function getWarehouseId($name)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('name' => $name));
    }

    public function getSelectWarehouses($selected = false, $active = 1)
    {
        return $this->getSelectAllWarehouses($selected, $active);
    }

    public function getSelectAllWarehouses($selected = false, $active = -1)
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $q = "
            SELECT
                id, name
            FROM
                {$this->table}";
        if($active >= 0)
        {
            $q .= " WHERE active = $active ";
        }
        $q .= " ORDER BY name ";
        $warehouses = $db->queryData($q);
        foreach($warehouses as $l)
        {
            $label = $l['name'];
            $value = $l['id'];
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;

    }

    public function isDefaultWarehouse($id)
    {
        if($id == 0)
        {
            return false;
        }
        $db = Database::openConnection();
        $q = "SELECT * FROM {$this->table} WHERE active = 1";
        $warehouse = $db->queryRow($q);
        return ($warehouse['is_default'] > 0);
    }


    public function editWarehouse($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'      =>  $data['name'],
            'active'    =>  0,
        );
        if(isset($data['active']))
            $vals['active'] = 1;
        $db->updateDatabaseFields($this->table, $vals, $data['id']);
        if(isset($data['is_default']))
            $this->makeDefault($data['id']);
        return true;
    }

    public function makeDefault($id)
    {
        $db = Database::openConnection();
        $db->query("UPDATE {$this->table} SET is_default = 0");
        $db->updateDatabaseField($this->table, 'is_default', 1, $id);
        return true;
    }

    public function addWarehouse($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'      =>  $data['name'],
            'active'    =>  1,
        );
        if(!isset($data['active']))
            $vals['active'] = 1;
        $warehouse_id = $db->insertQuery($this->table, $vals);
        if(isset($data['is_default']))
            $this->makeDefault($warehouse_id);
        return $warehouse_id;
    }
}

?>