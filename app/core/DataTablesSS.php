<?php
 /**
 * The datatablesss class.
 *
 * The base class for DataTables Server Side Processing.
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class DataTablesSS{
    protected static $db_array = array();

    private function __construct(){}

    /**
     * Create the data output array for the DataTables rows
     *
     *  @param  array $columns Column information array
     *  @param  array $data    Data from the SQL get
     *  @return array          Formatted data in a row based format
     */
    protected static function dataOutput($columns, $data)
    {
        $out = array();
            //echo "DATA<pre>",print_r($data),"</pre>";
            $row = array();
            for ( $i=0, $ien=count($data) ; $i<$ien ; $i++ )
            {
                $row = array();
                for ( $j=0, $jen=count($columns) ; $j<$jen ; $j++ )
                {
                    $column = $columns[$j];
                    // Is there a formatter?
                    if ( isset( $column['formatter'] ) )
                    {
                        $row[ $column['dt'] ] = $column['formatter']( $data[$i] );
                    }
                    else
                    {
                        if(!empty($column['db']))
                        {
                            $row[ $column['dt'] ] = $data[$i][ $columns[$j]['db'] ];
                        }
                        else
                        {
                            $row[ $column['dt'] ] = "";
                        }
                    }
                }
                $out[] = $row;
            }
            //echo "OUT<pre>",print_r($out),"</pre>";
        //die();
        return $out;
    }

    /**
     * Paging
     *
     * Construct the LIMIT clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @return string SQL limit clause
     */
    protected static function limit ( $request )
    {
        $limit = '';
        if ( isset($request['start']) && $request['length'] != -1 )
        {
            $limit = " LIMIT ".intval($request['start']).", ".intval($request['length']);
        }
        return $limit;
    }

    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @return string SQL order by clause
     */
    protected static function order ( $request, $columns )
    {
        $order = '';
        if ( isset($request['order']) && count($request['order']) )
        {
            $orderBy = array();
            $dtColumns = self::pluck( $columns , 'dt' );
            for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ )
            {
                // Convert the column index into the column data property
                $columnIdx = intval($request['order'][$i]['column']);
                $requestColumn = $request['columns'][$columnIdx];
                $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                $column = $columns[ $columnIdx ];
                if ( $requestColumn['orderable'] == 'true' )
                {
                    $dir = $request['order'][$i]['dir'] === 'asc' ?
                        'ASC' :
                        'DESC';
                    $orderBy[] = '`'.$column['db'].'` '.$dir;
                }
            }
            if ( count( $orderBy ) ) {
                $order = ' ORDER BY '.implode(', ', $orderBy);
            }
        }
        return $order;
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
                        //$binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                        //$globalSearch[] = "`".$column['db']."` LIKE ".$binding;
                        $globalSearch[] = "`".$column['db']."` LIKE :gterm".$i;
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
                        //$binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                        //$columnSearch[] = "`".$column['db']."` LIKE ".$binding;
                        $columnSearch[] = "`".$column['db']."` LIKE :cterm".$i;
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

    /**
     * Pull a particular property from each assoc. array in a numeric array,
     * returning and array of the property values from each item.
     *
     *  @param  array  $a    Array to get data from
     *  @param  string $prop Property to read
     *  @return array        Array of property values
     */
    private static function pluck ( $a, $prop )
    {
        $out = array();
        for ( $i=0, $len=count($a) ; $i<$len ; $i++ )
        {
 			if ( empty($a[$i][$prop]) && $a[$i][$prop] !== 0 )
                continue;
            //removing the $out array index confuses the filter method in doing proper binding,
            //adding it ensures that the array data are mapped correctly
            $out[$i] = $a[$i][$prop];
        }
        return $out;
    }


    /**
     * Return a string from an array or a string
     *
     * @param  array|string $a Array to join
     * @param  string $join Glue for the concatenation
     * @return string Joined string
     */
    private static function _flatten ( $a, $join = ' AND ' )
    {
        if ( ! $a )
            return '';
        else if ( $a && is_array($a) )
            return implode( $join, $a );
        return $a;
    }
}
?>