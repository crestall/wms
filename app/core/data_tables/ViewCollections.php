<?php
/**
 * View Collections Implementation of the DataTablesSS Class.
 *
 *
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */
 class ViewCollections extends DataTablesSS
 {
    private static $return_array       = array();
    private static $table              = "collections";
    private static $items_table        = "items";
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
                'db' => 'name',
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
                    return $d."<br>".$image;
                }
            ),
            array( 'db' => 'sku',  'dt' => 1 ),
            array( 'db' => 'client_product_id', 'dt' => 2 ),
            array(
                'db'    => 'collection_items',
                'dt'    => 3,
                'formatter' => function( $d, $row ){
                    $cis = array();
                    $ret = "";
                    if( !empty($d) )
                    {
                        $cia = explode("|", $d);
                        foreach($cia as $ci)
                        {
                            list( $c['id'], $c['name'],$c['sku'], $c['qty']) = explode(",", $ci);
                            if(!empty($c['id']))
                                $cis[] = $c;
                        }
                        foreach($cis as $ind => $item)
                        {
                            ++$ind;
                            $ret .= $item['name']." (".$item['sku'].") - ".$item['qty'];
                            if($ind < count($cis))
                                $ret .= "<br>";
                        }
                    }
                    return $ret;
                }
            )
        );
        // Build the SQL query string from the request
        self::$client_id = $request['clientID'];
        self::$active = $request['active'];
        $limit = self::limit( $request );
        $order = self::order( $request, self::$columns);
        $having = self::havingFilter( $request, self::$columns );

        $query = self::createQuery();
        $query .= " GROUP BY i.id ";
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
                i.id,
                i.name,
                i.image,
                i.sku,
                i.client_product_id,
                GROUP_CONCAT(
                	li.id, '|',
                    li.name, '|',
                    li.sku, '|',
                    c.number
                    SEPARATOR '~'
                ) AS collection_items
            FROM
                ".self::$table." c JOIN ".self::$items_table." i ON c.item_id = i.id JOIN ".self::$items_table." li ON c.linked_item_id = li.id
            WHERE
                i.client_id = ".self::$client_id
        ;
    }

 }
?>