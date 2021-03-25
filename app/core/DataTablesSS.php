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
    * @param array      $config user-provided config
    */
    public function __construct(Controller $controller, array $config = [])
    {
        $this->controller = $controller;
        $this->request    = $controller->request;
        $this->config     = array_merge($this->config, $config);
    }
}
?>