<?php

 /**
  * Environment class.
  * Gets an environment variable from $_SERVER
  *
  
  * @author     Mark Solly <mark.solly@3plplus.com.au>
  */

class Environment{

    /**
     * Constructor
     *
     */
    private function __construct(){}

    /**
     * Gets an environment variable from $_SERVER, $_ENV, or using getenv()
     *
     * @param $key string
     * @return string|null
     */
    public static function get($key){

        $val = null;
        if (isset($_SERVER[$key])) {
            $val = $_SERVER[$key];
        } elseif (isset($_ENV[$key])) {
            $val = $_ENV[$key];
        } elseif (getenv($key) !== false) {
            $val = getenv($key);
        }

        return $val;
    }

}