<?php
/**
 * View Inventory Implementation of the DataTablesSS Class.
 *
 *
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */
 class ViewInventory extends DataTablesSS
 {
    private static $return_array       = array();
    private static $table              = "items_locations";
    private static $locations_table    = "locations";
    private static $items_table        = "items";
    private static $columns            = array();
    private static $query              = "";
    private static $client_id          = 0;
    private static $active             = 1;

    private function __construct()
    {
        self::$columns = array(
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

    //public collection method
    public static function collectData( $request )
    {
        //the database object
        $db = Database::openConnection();
        // Build the SQL query string from the request
        $limit = self::limit( $request );
        $order = self::order( $request, self::$columns);
        $where = self::filter( $request, self::$columns );

        $query = self::createQuery();
        $query .= $where;
        $query .= " GROUP BY a.name ";
        $query .= $order;
        $query .= $limit;

        return $query;;






    }

    // Public Getters and Setters
    public static function getClientId()
    {
        return self::$client_id;
    }

    public static function setClientId($value)
    {
        self::$client_id = $value;
    }
    public static function getActive()
    {
        return self::$active;
    }

    public static function setActive($value)
    {
        self::$active = $value;
    }

    //private helper methods
    private static function createQuery()
    {
        return "
            SELECT
                IFNULL( SUM(a.qty),0) as on_hand,
                IFNULL( SUM(a.qc_count), 0) AS qc_count,
                IFNULL( SUM(b.allocated), 0) AS allocated,
                a.name ,a.sku, a.barcode, a.client_product_id, a.sku, a.item_id,
                GROUP_CONCAT(
                    IFNULL(a.location_id,0),',',
                    IFNULL(a.location,''),',',
                    IFNULL(a.qty,''),',',
                    IFNULL(a.qc_count,''),',',
                    IFNULL(b.allocated,''),','
                    SEPARATOR '|'
                ) AS locations
        ".self::from();
    }

    private static function from()
    {
        return "
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
                        i.client_id = ".self::$client_id." AND i.active = ".self::$active."
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
        ";
    }

    private static function queryDatabase($active = 1)
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
                        i.client_id = ".self::$client_id." AND i.active = $active
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