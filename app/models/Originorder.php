<?php
 /**
  * Originorder Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>

    FUNCTIONS


  */
  class Originorder extends Order{

    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "origin_orders";

    public function __construct()
    {
        parent::_construct();
    }

  }

?>