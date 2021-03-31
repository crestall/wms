<?php
/**
 * View Inventory Implementation of the DataTablesSS Class.
 *
 *
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */
 class ViewInventory extends DataTablesSS
 {
    private $return_array       = array();
    private $table              = "items_locations";
    private $locations_table    = "locations";
    private $items_table        = "items";
    private $columns            = array();
    private $client_id;

    public function init()
    {
        $this->columns = array(
            array( 'db' => 'name', 'dt' => 0 ),
            array( 'db' => 'sku',  'dt' => 1 ),
            array( 'db' => 'barcode',   'dt' => 2 ),
            array( 'db' => 'client_product_id', 'dt' => 3 ),
            array( 'db' => 'on_hand', 'dt' => 4 ),
            array( 'db' => 'allocated', 'dt'=> 5),
            array( 'db' => 'qc_count', 'dt'=> 6),
            array( 'db' => 'available', 'dt'=> 7),
            array( 'db' => 'locations', 'dt'=> 8),
            array( 'db' => '', 'dt' => 9)
        );
    }

    private function queryDatabase($client_id, $active = 1)
    {
        $db = Database::openConnection();
        $q = "
            SELECT
                IFNULL( SUM(a.qty),0) as on_hand,
                IFNULL( SUM(a.qc_count), 0) AS qc_count,
                IFNULL( SUM(b.allocated), 0) AS allocated,
                a.name, a.client_product_id, a.sku, a.barcode, a.item_id, a.pack_item, a.width, a.depth, a.height, a.weight, a.low_stock_warning, a.oversize, a.image,
                GROUP_CONCAT(
                    IFNULL(a.location_id,0),',',
                    IFNULL(a.location,''),',',
                    IFNULL(a.qty,''),',',
                    IFNULL(a.qc_count,''),',',
                    IFNULL(b.allocated,''),','
                    SEPARATOR '|'
                ) AS locations
            FROM
                (
                    SELECT
                        l.id AS location_id, il.qty, il.qc_count, i.client_product_id, i.id AS item_id,
                        i.name, i.sku, i.barcode, l.location, i.pack_item, i.width, i.depth, i.height, i.weight, i.low_stock_warning, l.oversize, i.image
                    FROM
                        items i LEFT JOIN
                        items_locations il ON i.id = il.item_id LEFT JOIN
                        locations l ON il.location_id = l.id
                    WHERE
                        i.client_id = $client_id AND i.active = $active
                ) a
                LEFT JOIN
                (
                    SELECT
                        COALESCE(SUM(oi.qty),0) AS allocated, oi.item_id, oi.location_id
                    FROM
                        orders_items oi JOIN
                        orders o ON oi.order_id = o.id JOIN
                        items i ON oi.item_id = i.id
                    WHERE
                        o.status_id != 4
                    GROUP BY
                    oi.location_id, oi.item_id
                ) b ON a.item_id = b.item_id AND a.location_id = b.location_id
            GROUP BY
                a.item_id
            ORDER BY
                a.name
        ";
        return $db->queryData($q);
    }
 }
?>