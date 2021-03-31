<?php
 /**
 * The datatablesss class.
 *
 * The base class for DataTables Server Side Processing.
 * It provides reusable controller logic.
 * The extending classes can be used as part of the controller.
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class DataTablesSS{
    /**
    * controller
    *
    * @var Controller
    */
    protected $controller;

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
    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
        $this->request    = $controller->request;
    }

    /**
     * Create the data output array for the DataTables rows
     *
     *  @param  array $columns Column information array
     *  @param  array $data    Data from the SQL get
     *  @return array          Formatted data in a row based format
     */
    protected function dataOutput($columns, $data)
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

    //getters and setters
    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}
?>