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
    public static function collectData( $request, $client_id )
    {
        //the database object
        $db = Database::openConnection();
        //the columns setup
        self::$columns = array(
            array( 'db' => 'name', 'dt' => 0 ),
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
                'formatter' => function( $d ){
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
                'formatter' => function() {
                    return '<a href="more_log.php?more=1">MORE</a>';
                }
            )
        );
        // Build the SQL query string from the request
        self::$client_id = $client_id;
        $limit = self::limit( $request );
        $order = self::order( $request, self::$columns);
        $where = self::filter( $request, self::$columns );

        $query = self::createQuery();
        $query .= " GROUP BY a.name ";
        //PUT THE HAVING CLAUSE HERE ??????????
        $query .= $where;
        $query .= $order;
        $query .= $limit;

        //return $query;;
        // Main query to actually get the data
        $data = $db->queryData($query, self::$db_array);
        //echo "<pre>",print_r($data),"</pre>";die();

        // Data set length after filtering
        $resFilterLength = $db->queryRow("SELECT count(*)".self::from().$where, self::$db_array);
        //echo "<pre>",print_r($resFilterLength),"</pre>";die();
        $recordsFiltered = $resFilterLength['count(*)'];

        // Total data set length
        $resTotalLength = $db->queryRow("SELECT count(*)".self::from());
        $recordsTotal = $resTotalLength['count(*)'];

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
                IFNULL( SUM(a.qty),0) as on_hand,
                IFNULL( SUM(a.qc_count), 0) AS qc_count,
                IFNULL( SUM(b.allocated), 0) AS allocated,
                ( IFNULL( SUM(a.qty),0) - IFNULL( SUM(a.qc_count), 0) - IFNULL( SUM(b.allocated), 0) ) AS available,
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

    /**
     * Searching / Filtering
     *
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here performance on large
     * databases would be very poor
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL where clause
     */
    static function filter ( $request, $columns  )
    {
        $globalSearch = array();
        $columnSearch = array();
        $dtColumns = self::pluck( $columns, 'dt' );
        if ( isset($request['search']) && $request['search']['value'] != '' )
        {
            $str = $request['search']['value'];
            for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ )
            {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                $column = $columns[ $columnIdx ];
                if ( $requestColumn['searchable'] == 'true' )
                {
                    if(!empty($column['db']))
                    {
                        $globalSearch[] = "`".$column['db']."` HAVING :gterm".$i;
                        self::$db_array['gterm'.$i] = "%".$str."%";
                    }
                }
            }
        }
        // Individual column filtering
        if ( isset( $request['columns'] ) )
        {
            for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ )
            {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                $column = $columns[ $columnIdx ];
                $str = $requestColumn['search']['value'];

                if ( $requestColumn['searchable'] == 'true' && $str != '' )
                {
                    if(!empty($column['db'])){
                        $columnSearch[] = "`".$column['db']."` HAVING :cterm".$i;
                        self::$db_array['cterm'.$i] = "%".$str."%";
                    }
                }
            }
        }
        // Combine the filters into a single string
        $where = '';
        if ( count( $globalSearch ) ) {
            $where = '('.implode(' OR ', $globalSearch).')';
        }
        if ( count( $columnSearch ) ) {
            $where = $where === '' ?
                implode(' AND ', $columnSearch) :
                $where .' AND '. implode(' AND ', $columnSearch);
        }
        if ( $where !== '' ) {
            $where = 'WHERE '.$where;
        }
        return $where;
    }
 }
?>