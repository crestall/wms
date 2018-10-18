<?php

 /**
  * Logger class
  *
  * Used mainly to log failures, errors, exceptions, or any other malicious actions or attacks.
  *
  
  * @author     Mark Solly <mark.solly@3plplus.com.au>
  */

class Logger{

    /**
     * Constructor
     *
     */
    private function __construct(){}

    /**
     * log
     *
     * @access public
     * @static static method
     * @param  string  $header
     * @param  string  $message
     * @param  string  $filename
     * @param  string  $linenum
     */
    public static function log($header="", $message="", $filename="", $linenum="")
    {

        $logfile = APP . "logs/log_".date('Ymd').".txt";
        $date = date("d/m/Y G:i:s");
        $err = $date." | ".$filename." | ".$linenum." | ".$header. "\n";

        $message = is_array($message)? implode("\n", $message): $message;
        $err .= $message . "\n*******************************************************************\n\n";

        // log/write error to log file
        error_log($err, 3, $logfile);
    }

    public static function logOrderImports($file, $content)
    {
        $file .= "_".date('Ymd').'.txt';
        $logfile = APP . "logs/".$file;
        file_put_contents($logfile, $content, FILE_APPEND);
    }

    public static function logOrderFulfillment($file, $content)
    {
        $file .= date('Ymd').'.txt';
        $logfile = APP . "logs/".$file;
        file_put_contents($logfile, $content, FILE_APPEND);
    }

    public static function logReportsSent($file, $content)
    {
        $file .= date('Ymd').'.txt';
        $logfile = APP . "logs/".$file;
        file_put_contents($logfile, $content, FILE_APPEND);
    }
 }