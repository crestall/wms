<?php
class Orderitemserials extends Model{
    public $table = "order_item_serials";

    public function getRecordedSerials($order_id = 0, $item_id = 0)
    {
        $db = Database::openConnection();
        return $db->queryData("SELECT * FROM {$this->table} WHERE order_id = $order_id AND item_id = $item_id");
    }
}
?>