<?php
/**
 * The Xero class.
 *
 * The base class for all Xero account classes.
 * It provides reusable controller logic.
 * The extending classes can be used as part of the controller.
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class Xero
{
    public $controller;

    protected $table = "xero_authorization";
    protected $output;
    protected $return_array = array(
        'import_count'          => 0,
        'imported_orders'       => array(),
        'error_orders'          => array(),
        'import_error'          => false,
        'error'                 => false,
        'error_count'           => 0,
        'error_string'          => '',
        'import_error_string'   => ''
    );
    protected $ua;
    protected $order_items;

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
    }

    protected function tokenExpired($expire_time)
    {
        return(time() > $expire_time);
    }

}//end class
?>