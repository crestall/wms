<?php
class Configuration extends Model{
    public $table = "configuration";

    public function __construct(){ }

    public function addConfiguration($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'          => $data['name'],
            'value'         => $data['value'],
            'date_added'    => time()
        );
        return $db->insertQuery($this->table, $vals);
    }

    public function editConfiguration($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'          => $data['name'],
            'value'         => $data['value'],
            'date_modified' => time()
        );
        $db->updateDatabaseFields($this->table, $vals, $data['id']);
        return true;
    }

    public function getConfigurationName($id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' => $id), 'name');
    }

    public function getConfigurationValue($id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' => $id), 'value');
    }

    public function getConfigurationValueByName($name)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('name' => $name), 'value');
    }

    public function getConfigurations()
    {
        $db = Database::openConnection();
        $q = "SELECT * FROM {$this->table} ORDER BY name";
        return $db->queryData($q);
    }

    public function getConfigurationId($name)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('name' => $name));
    }
}
?>