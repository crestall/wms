<?php
/**
 * View Direct Freight Bookings Implementation of the DataTablesSS Class.
 *
 *
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */
class ViewDFBookings extends DataTablesSS
{
    private static $return_array       = array();
    private static $table              = "df_bookings";
    private static $columns            = array();
    private static $query              = "";

    private function __construct(){}

    //public collection methods
    public static function collectData( $request )
    {
        //the database object
        $db = Database::openConnection();
        //the columns setup
        self::$columns = [
            [
                'db' => 'id',
                'dt' => 'DT_RowId',
                'formatter' => function( $d, $row ) {
                    return 'row_'.$d;
                }
            ],
            [
                'db' => 'date_shipped',
                'dt' => 0,
                'formatter' => function( $d, $row ) {
                    return date("d/m/Y", strtotime($d));
                }
            ],
            [
                'db' => '',
                'dt' => 1,
                'formatter' => function( $row ) {
                    $rs = "<p class='font-weight-bold'>".$row['receiver_name'];
                    if($row['receiver_contact_name'] != $row['receiver_name'])
                        $rs .= "<br>".$row['receiver_contact_name'];
                    $rs .= "</p><p>";
                    $rs .= $row['address'];
                    if(!empty($row['address_2']))
                        $rs .= "<br>".$row['address_2'];
                    $rs .= "<br>".$row['suburb'];
                    $rs .= "<br>".$row['state'];
                    $rs .= "<br>".$row['postcode'];
                    return $rs;
                }
            ],
            [ 'db' => 'consignment_id', 'dt' => 2 ],
            [
                'db' => 'postage_charge',
                'dt' => 3,
                'formatter' => function( $d ){
                    return "$".$d;
                }
            ],
            [
                'db' => 'other_charges',
                'dt' => 4,
                'formatter' => function( $d ){
                    return "$".$d;
                }
            ],
            [
                'db' => 'fuel_levee',
                'dt' => 5,
                'formatter' => function( $d ){
                    return "$".$d;
                }
            ],
            [
                'db' => '',
                'dt' => 6,
                'formatter' => function( $row ){
                    $total = $row['other_charges'] + $row['postage_charge'] +$row['fuel_levee'];
                    return "$".$total;
                }
            ],
            [
                'db' => '',
                'dt' => 7,
                'formatter' => function( $row ) {
                    return "<p><button class='btn btn-outline-fsg btn-sm track_booking' data-bookingid='{$row['id']}'>Track Delivery</button></p>";
                }
            ]
        ];
        // Build the SQL query string from the request
        $limit = self::limit( $request );
        $order = self::order( $request, self::$columns);
        $having = self::havingFilter( $request, self::$columns );

        $query = self::createQuery();
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

    //private helper methods
    private static function createQuery()
    {
        return "
            SELECT * FROM ".self::$table."
        ";
    }
}