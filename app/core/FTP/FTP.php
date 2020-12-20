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

    public function openConnection($dir = "")
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
            if(!empty($dir))
            {
                if(!@ftp_chdir($this->CON_ID, $dir))
                {
                    Logger::log("FTP Director Change Failed", "Could not change to ". $dir);
                    throw new Exception("Could not change to ". $dir);
                    return false;
                }
            }
            return true;
        }
    }

    /**
     * get a list of filenames in a directory
     *
     * @returns array
     */
    public function getFileNames()
    {
        return ftp_nlist($this->CON_ID, ".");
    }

    /**
     * get the size of a file
     *
     * returns size of file or -1 on error - this means the file is not found or it is a directory
     */
    public function getFileSize($file)
    {
        return ftp_size($this->CON_ID, $file);
    }

    /**
     * delete a file from the server
     *
     * @returns boolean
     */
    public function deleteFile($file)
    {
        return ftp_delete($this->CON_ID, $file);
    }

     /**
     * rename/move a file on the server
     *
     * @returns boolean
     */
    public function renameFile($old_name, $new_name)
    {
        return ftp_rename($this->CON_ID, $old_name, $new_name);
    }

    /**
    * Closes the current FTP connection
    *
    * @returns boolean
    */

    public function closeConnection()
    {
        return ftp_close($this->CON_ID);
    }

    /**
    * functions used by extending classes
    *
    */

    public function collectOrders($file){}
    private function processOrders($orders){}
    private function addOrders($orders){}

 }

 ?>