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

    private function __construct(){}

    //public collection method
    public static function collectData( $request )
    {
        //the database object
        $db = Database::openConnection();
        //the columns setup
        self::$columns = array(
            array(
                'db' => 'item_id',
                'dt' => 'DT_RowId',
                'formatter' => function( $d, $row ) {
                    // Technically a DOM id cannot start with an integer, so we prefix
                    // a string. This can also be useful if you have multiple tables
                    // to ensure that the id is unique with a different prefix
                    return 'row_'.$d;
                }
            ),
            array(
                'db' => 'item_name',
                'dt' => 0,
                'formatter' => function( $d, $row ){
                    $image = "";
                    if(preg_match('/https?/i', $row['image']))
                    {
                        $image = "<br><img src='".$row['image']."' class='img-thumbnail img-fluid'>";
                    }
                    elseif(!empty($row['image']))
                    {
                        $image = "<br><img src='/images/products/tn_".$row['image']."' class='img-fluid img-thumbnail'>";
                    }
                    return '
                      <a href="/products/edit-product/product='.$row['item_id'].'">'.$d.'</a>'.$image
                    ';
                }
            ),
            array( 'db' => 'sku',  'dt' => 1 ),
            array( 'db' => 'barcode',   'dt' => 2 ),
            array( 'db' => 'client_product_id', 'dt' => 3 ),
            array( 'db' => 'on_hand', 'dt' => 4 ),
            array( 'db' => 'allocated', 'dt'=> 5),
            array( 'db' => 'qc_count', 'dt'=> 6),
            array( 'db' => 'available', 'dt'=> 7),
            array(
                'db'    => 'locations',
                'dt'    => 8,
                'formatter' => function( $d, $row ){
                    $locations = array();
                    $ret = "";
                    if( !empty($d) )
                    {
                        $la = explode("|", $d);
                        foreach($la as $location)
                        {
                            list( $l['id'], $l['name'], $l['onhand'], $l['qc'], $l['allocated']) = explode(",", $location);
                            if(!empty($l['id']))
                                $locations[] = $l;
                        }
                        foreach($locations as $ind => $l)
                        {
                            ++$ind;
                            $ret .= $l['name']." (".$l['onhand'].")";
                            if(!empty($l['allocated']))
                                $ret .= " - ".$l['allocated']." allocated";
                            if(!empty($l['qc']))
                                $ret .= " and ".$l['qc']." unavailable";
                            if($ind < count($locations))
                                $ret .= "<br>";
                        }
                        return $ret;
                    }
                }
            ),
            array(
                'db' => '',
                'dt' => 9,
                'formatter' => function( $row ) {
                    return '
                        <p><a class="btn btn-outline-secondary" href="/inventory/add-subtract-stock/product='.$row['item_id'].'">Add/Subtract Stock</a></p>
                        <p><a class="btn btn-outline-secondary" href="/inventory/move-stock/product='.$row['item_id'].'">Move Stock</a></p>
                        <p><a class="btn btn-outline-secondary" href="/inventory/quality-control/product='.$row['item_id'].'">Quality Control</a>  </p>
                    ';
                }
            )
        );
        // Build the SQL query string from the request
        self::$client_id = $request['clientID'];
        $limit = self::limit( $request );
        $order = self::order( $request, self::$columns);
        $having = self::havingFilter( $request, self::$columns );

        $query = self::createQuery();
        $query .= " GROUP BY a.name ";
        // Total Data Set length
        $resTotalLength = $db->queryData($query);
        $recordsTotal = count($resTotalLength);
        // Filtering
        $query .= $having;
        // Data Set length after filtering
        $resFilterLength = $db->queryData($query, self::$db_array);
        $recordsFiltered = count($resFilterLength);
        // Order and limit for display
        $query .= $order;
        $query .= $limit;
        // Data for display
        $data = $db->queryData($query, self::$db_array);

        return array(
            "draw"            => isset ( $request['draw'] ) ?
                intval( $request['draw'] ) :
                0,
            "recordsTotal"    => intval( $recordsTotal ),
            "recordsFiltered" => intval( $recordsFiltered ),
            "data"            => self::dataOutput( self::$columns, $data )
        );
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
                IFNULL( SUM(a.qty),0) AS on_hand,
                IFNULL( SUM(a.qc_count), 0) AS qc_count,
                IFNULL( SUM(b.allocated), 0) AS allocated,
                ( IFNULL( SUM(a.qty),0) - IFNULL( SUM(a.qc_count), 0) - IFNULL( SUM(b.allocated), 0) ) AS available,
                a.name AS item_name,
                a.sku AS sku,
                a.barcode AS barcode,
                a.client_product_id AS client_product_id,
                a.item_id AS item_id,
                a.image,
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
 }
?>