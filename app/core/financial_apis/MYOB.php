<?php
/**
 * The MYOB class.
 *
 * The base class for all MYOB account classes.
 * It provides reusable controller logic.
 * The extending classes can be used as part of the controller.
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */
 class MYOB{
    protected $controller;
    protected $USERNAME;
    protected $PASSWORD ;
    protected $URL;

    protected $CYPHER;
    protected $KEY;
    protected $TAG;


    public function __construct($controller, $url = "", $username = "", $password = "")
    {
        $this->controller = $controller;
        $this->URL = $url;
        $this->USERNAME = $username;
        $this->PASSWORD = $password;
    }


    public function callTask ($task, $params)
    {
        $params['task'] = $task;
        //$params = http_build_query($params);

        $c = curl_init($this->urlToController);
        curl_setopt ($c, CURLOPT_POSTFIELDS, $params);
        //echo  $this->username . ":" . $this->password;
        // setup the authentication
        curl_setopt($c, CURLOPT_USERPWD, $this->USERNAME . ":" . $this->PASSWORD);
        // Tell curl not to return headers, but do return the response
        curl_setopt($c, CURLOPT_HEADER, false);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);


        return curl_exec($c);

    }

    protected function encryptData($data) {}

    protected function decryptData($data) {}
 }
 ?>