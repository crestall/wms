<?php
/**
 * The FTP class.
 *
 * The base class for all FTP connections.
 * It provides reusable controller logic.
 * The extending classes can be used as part of the controller.
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */
 class FTP{
    public $connection_id;

    protected $controller;
    protected $USERNAME;
    protected $PASSWORD ;
    protected $URL;
    protected $CON_ID;

    public function __construct($controller, $url = "", $username = "", $password = "")
    {
        $this->controller = $controller;
        $this->URL = $url;
        $this->USERNAME = $username;
        $this->PASSWORD = $password;
    }

    /**
    * Opens and logs into an FTP connection
    *
    * @throws Exception if connection or login fail
    * @return boolean
    */

    public function openConnection()
    {
        if( !$this->CON_ID = ftp_connect($this->URL) )
        {
            Logger::log("FTP Connection Failed", "Could not open into ".$this->URL);
            throw new Exception('Could not open '.$this->URL);
            return false;
        }
        else
        {
            if( ! @ftp_login( $this->CON_ID, $this->USERNAME, $this->PASSWORD) )
            {
                Logger::log("FTP Loggin Failed", "Could not log into ".$this->URL." using ".$this->USERNAME);
                throw new Exception('Could not log in to  '.$this->URL);
                return false;
            }
            return true;
        }
    }

    /**
    * Closes the current FTP connection
    *
    * @return boolean
    */

    public function closeConnection()
    {
        return ftp_close($this->CON_ID);
    }
 }

 ?>