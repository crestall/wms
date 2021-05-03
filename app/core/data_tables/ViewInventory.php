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
    
    //public collection methods
    public static function collectDataForClient( $request )
    {
        //the database object
        $db = Database::openConnection();
        //the columns setup
        self::$columns = array(
            array(
                'db' => 'item_id',
                'dt' => 'DT_RowId',
                'formatter' => function( $d, $row ) {
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
                            <a href="/products/client-product-edit/product='.$row['item_id'].'">'.$d.$image.'</a>'
                        ;
                }
            ),
            array( 'db' => 'sku',  'dt' => 1 ),
            array( 'db' => 'client_product_id', 'dt' => 2 ),
            array( 'db' => 'barcode',   'dt' => 3 ),
            array(
                'db' => '',
                'dt' => 4,
                'formatter' => function($row){
                    $details = "";
                    if(!empty($row['width'])) $details .= "Width: ".$row['width']."cm<br/>";
                    if(!empty($row['depth'])) $details .= "Depth: ".$row['depth']."cm<br/>";
                    if(!empty($row['height'])) $details .= "Height: ".$row['height']."cm<br/>";
                    if(!empty($row['weight'])) $details .= "Weight: ".$row['weight']."kg";
                    return $details;
                }
            ),
            array( 'db' => 'on_hand', 'dt' => 5 ),
            array( 'db' => 'allocated', 'dt'=> 6),
            array( 'db' => 'qc_count', 'dt'=> 7),
            array( 'db' => 'available', 'dt'=> 8),
            array(
                'db' => '',
                'dt' => 9,
                'formatter' => function($row){
                    $location_string = ($row['bays'] > 0)? $row['bays']." Full Pallet Bays<br/>" : "";
                    $location_string .= ($row['trays'] > 0)? $row['trays']." Tray Spaces (9 per pallet bay)" : "";
                    $location_string = rtrim($location_string, "<br/>");
                    return $location_string;
                }
            ),
            array(
                'db' => '',
                'dt' => 10,
                'formatter' => function($row){
                    return "
                        <p><input type='text' class='form-control number ml-auto' id='lowstock_{$row['item_id']}' name='lowstock_{$row['item_id']}' value='{$row['low_stock_warning']}' style=1max-width: 80px1 /></p>
                        <p class='text-right'><button class='btn btn-outline-secondary btn-sm update_product' data-productid='{$row['item_id']}'>Update</button> </p>
                        <div class='errorbox' style='display:none;' id='error_{$row['item_id']}'>Only input whole, positive numbers please</div>
                        <div class='feedbackbox' style='display:none;' id='feedback_{$row['item_id']}'>Product warning level updated</div>
                    ";
                }
            )
        );
        // Build the SQL query string from the request
        self::$client_id = $request['clientID'];
        $limit = self::limit( $request );
        $order = self::order( $request, self::$columns);
        $having = self::havingFilter( $request, self::$columns );
        $query = self::createQuery();
        $query .= " GROUP BY a.item_id ";
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

    public static function collectDataForWarehouse( $request )
    {
        //the database object
        $db = Database::openConnection();
        //the columns setup
        self::$columns = array(
            array(
                'db' => 'item_id',
                'dt' => 'DT_RowId',
                'formatter' => function( $d, $row ) {
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
                    ;
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
        $query .= " GROUP BY a.item_id ";
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
                a.width,
                a.depth,
                a.weight,
                a.low_stock_warning,
                GROUP_CONCAT(
                    IFNULL(a.location_id,0),',',
                    IFNULL(a.location,''),',',
                    IFNULL(a.qty,''),',',
                    IFNULL(a.qc_count,''),',',
                    IFNULL(b.allocated,''),','
                    SEPARATOR '|'
                ) AS locations,
                (SELECT COUNT(*) FROM items_locations JOIN locations ON locations.id = items_locations.location_id WHERE item_id = a.item_id AND locations.tray = 0) AS bays,
                (SELECT COUNT(*) FROM items_locations JOIN locations ON locations.id = items_locations.location_id WHERE item_id = a.item_id AND locations.tray = 1) AS trays
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