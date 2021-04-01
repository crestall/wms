<?php
 /**
 * The datatablesss class.
 *
 * The base class for DataTables Server Side Processing.
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class DataTablesSS{
    /**
    * request
    *
    * @var Request
    */
    protected $request;

    /**
    * Default configurations data
    *
    * @var array
    */
    protected $config = [];

     /**
    * Constructor
    *
    * @param Controller $controller
    */
    public function __construct()
    {}

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
        for ( $i=0, $ien=count($data) ; $i<$ien ; $i++ )
        {
            $row = array();
            for ( $j=0, $jen=count($columns) ; $j<$jen ; $j++ )
            {
                $column = $columns[$j];
                // Is there a formatter?
                if ( isset( $column['formatter'] ) )
                {
                    if(empty($column['db']))
                    {
                        $row[ $column['dt'] ] = $column['formatter']( $data[$i] );
                    }
                    else
                    {
                        $row[ $column['dt'] ] = $column['formatter']( $data[$i][ $column['db'] ], $data[$i] );
                    }
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
        return $out;
    }
}
?>