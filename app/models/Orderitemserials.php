<?php
class Orderitemserials extends Model{
    public $table = "order_item_serials";

    public function getRecordedSerials($order_id = 0, $item_id = 0)
    {
        $db = Database::openConnection();
        return $db->queryData("SELECT * FROM {$this->table} WHERE order_id = $order_id AND item_id = $item_id");
    }

    public function insertData($post_data)
    {
        $db = Database::openConnection();
        foreach($post_data as $data)
        {
            $vals = array(
                'order_id'      => $data['order_id'],
                'item_id'       => $data['item_id'],
                'serial_number' => $data['serial_number']
            );
            if($data['serial_id'] > 0)
            {
                $db->updateDatabaseFields($this->table, $vals, $data['serial_id']);
            }
            else
            {
                $db->insertQuery($this->table, $vals);
            }
        }
        return true;
    }
}
?>