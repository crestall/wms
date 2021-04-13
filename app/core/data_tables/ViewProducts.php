<?php
/**
 * View Products Implementation of the DataTablesSS Class.
 *
 *
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */
 class ViewProducts extends DataTablesSS
 {
    private static $return_array       = array();
    private static $table              = "items";
    private static $locations_table    = "locations";
    private static $columns            = array();
    private static $query              = "";
    private static $client_id          = 0;
    private static $active             = 1;

    private function __construct(){}

    //public collection methods
    public static function collectData( $request )
    {
        //the database object
        $db = Database::openConnection();
        //the columns setup
        self::$columns = array(
            array(
                'db' => 'id',
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
            array( 'db' => 'client_product_id', 'dt' => 2 ),
            array( 'db' => 'barcode',   'dt' => 3 ),
            array( 'db' => 'supplier', 'dt' => 4),
            array(
                'db' => '',
                'dt' => '5',
                'formatter' => function( $row ){
                    return $row['width']."X".$row['depth']."X".$row['height'];
                }
            ),
            array(
                'db' => 'weight',
                'dt' => '6',
                'formatter' => function( $d, $row ){
                    return $d."kg";
                }
            ),
            array(
                'db' => 'palletized',
                'dt' => '7',
                'formatter' => function( $d, $row ){
                    return ($d > 0)? "Yes":"No";
                }
            ),
            array(
                'db' => 'boxed_item',
                'dt' => '8',
                'formatter' => function( $d, $row ){
                    return ($d > 0)? "Yes":"No";
                }
            ),
            array(
                'db' => 'is_dangerous_good',
                'dt' => '9',
                'formatter' => function( $d, $row ){
                    return ($d > 0)? "Yes":"No";
                }
            ),
            array(
                'db' => 'is_pod',
                'dt' => '10',
                'formatter' => function( $d, $row ){
                    return ($d > 0)? "Yes":"No";
                }
            )
        );
        // Build the SQL query string from the request
        self::$client_id = $request['clientID'];
        self::$active = $request['active'];
        $limit = self::limit( $request );
        $order = self::order( $request, self::$columns);
        $where = self::whereFilter( $request, self::$columns );

        $query = self::createQuery();
        // Total Data Set length
        $resTotalLength = $db->queryData($query);
        $recordsTotal = count($resTotalLength);
        // Filtering
        $query .= $where;
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
        return "SELECT * FROM items WHERE `client_id` = ".self::$client_id." AND `active` = ".self::$active;
    }

 }
?>