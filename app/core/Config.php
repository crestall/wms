<?php

/**
 * Config class.
 * Gets a configuration value
 *
 
 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class Config{
    /**
     * Array of configurations
     *
     * @var array
     */
    private static $config = [];

    /**
     * Prefixes used to load specific configurations.
     *
     * @var array
     */
    private static $prefix = [
        'default'   => 'config',
        'js'        => 'javascript'
    ];

    public static function tester()
    {
        return "test";
    }

    /**
     * Get default configuration value(s)
     *
     * @param $key string
     * @return string|array|null
     */
    public static function get($key){
        return self::_get($key, self::$prefix['default']);
    }

    /**
     * Set or add a default configuration value
     *
     * @param $key string
     */
    public static function set($key, $value){
        self::_set($key, $value, self::$prefix['default']);
    }

    /**
     * Get javascript configuration value(s)
     *
     * @param $key string
     * @return string|array|null
     */
    public static function getJsConfig($key = ""){
        return self::_get($key, self::$prefix['js']);
    }

    /**
     * Set or add a javascript configuration value
     *
     * @param string $key
     * @param mixed  $value
     */
    public static function setJsConfig($key, $value){
        self::_set($key, $value, self::$prefix['js']);
    }

    /**
     * Get a configuration value(s)
     *
     * @param $key string
     * @param $source string
     * @return string|null
     * @throws Exception if configuration file doesn't exist
     */
    private static function _get($key, $source){

        if (!isset(self::$config[$source])) {
            echo "<p>Loading the config file into memory</p>";
            $config_file = APP . 'config/' . $source . '.php';

            if (!file_exists($config_file)) {
                throw new Exception("Configuration file " . $source . " doesn't exist");
            }

            self::$config[$source] = require $config_file . "";
        }

        if(empty($key))
        {
            echo "<p>Empty $key</p>";
            return self::$config[$source];
        }
        else if(isset(self::$config[$source][$key]))
        {
            echo "<p>$key already set</p>";
            return self::$config[$source][$key];
        }
        else
        {
            $db = Database::openConnection();
            if($sv = $db->queryValue('configuration', array('name' => $key), 'value'))
            {
                self::$config[$source][$key] = Encryption::decryptStringBase64($sv);
                echo "<p>Found $key - should return ".self::$config[$source][$key]."<p>";
                return self::$config[$source][$key] ;
            }
            else
            {
                echo "<p>Could not fine value for $key</p>";
            }
        }
        return null;
    }

    /**
     * Set or adds a configuration value
     *
     * @param $key string
     * @param $value string
     * @param $source string
     */
    private static function _set($key, $value, $source){

        // load configurations if not already loaded
        if (!isset(self::$config[$source])) {
            self::_get($key, $source);
        }

        if($key && $source){
            self::$config[$source][$key] = $value;
        }
    }
}
