<?php
class Store extends Model{

    public function getAllStores($active = 1)
    {
        $db = Database::openConnection();
        return $db->queryData("SELECT * FROM {$this->table} WHERE active = $active ORDER BY name");
    }

    public function getStoreById($id = 0)
    {
        $db = Database::openConnection();
        return $db->queryById($this->table, $id);
    }

    public function addStore($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'chain_id'      =>  $data['chain_id'],
            'name'          =>  $data['name'],
            'address'       =>  $data['address'],
            'suburb'        =>  $data['suburb'],
            'state'         =>  $data['state'],
            'postcode'      =>  $data['postcode'],
            'store_number'  =>  $data['store_number']
        );
        if(!empty($data['address2'])) $vals['address_2'] = $data['address2'];
        if(!empty($data['phone'])) $vals['phone'] = $data['phone'];
        if(!empty($data['country'])) $vals['country'] = $data['country'];
        if(!empty($data['contact_name'])) $vals['contact_name'] = $data['contact_name'];
        if(!empty($data['contact_email'])) $vals['contact_email'] = $data['contact_email'];
        $id = $db->insertQuery($this->table, $vals);
        return $id;
    }

    public function editStore($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'chain_id'      =>  $data['chain_id'],
            'name'          =>  $data['name'],
            'contact_name'  =>  'Storeman',
            'contact_email' =>  null,
            'address'       =>  $data['address'],
            'address_2'     =>  null,
            'suburb'        =>  $data['suburb'],
            'state'         =>  $data['state'],
            'postcode'      =>  $data['postcode'],
            'country'       =>  'AU',
            'store_number'  =>  $data['store_number'],
            'phone'         =>  null
        );
        $vals['active'] = isset($data['active'])? 1 : 0;
        if(!empty($data['address2'])) $vals['address_2'] = $data['address2'];
        if(!empty($data['country'])) $vals['country'] = $data['country'];
        if(!empty($data['contact_name'])) $vals['contact_name'] = $data['contact_name'];
        if(!empty($data['contact_email'])) $vals['contact_email'] = $data['contact_email'];
        if(!empty($data['phone'])) $vals['phone'] = $data['phone'];
        $db->updateDatabaseFields($this->table, $vals, $data['store_id']);
        return true;
    }
}
?>