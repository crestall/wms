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
}
?>