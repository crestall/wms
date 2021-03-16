<?php

 /**
  * Logger class
  *
  * Used mainly to log failures, errors, exceptions, or any other malicious actions or attacks.
  *
  
  * @author     Mark Solly <mark.solly@fsg.com.au>
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
    // for the dispatch reports
    public static function logReportsSent($file, $content)
    {
        $file .= date('Ymd').'.txt';
        $logfile = APP . "logs/".$file;
        file_put_contents($logfile, $content, FILE_APPEND);
    }
    // for the production emails
    public static function logRemindersSent($file, $content)
    {
        $file .= date('Ymd').'.txt';
        $logfile = APP . "logs/".$file;
        file_put_contents($logfile, $content, FILE_APPEND);
    }
    // for the database error tracking
    public static function logDatabaseActivity($file, $content)
    {
        $file .= date('Ymd').'.txt';
        $logfile = APP . "logs/".$file;
        file_put_contents($logfile, $content, FILE_APPEND);
    }
    // testing time between calls
    public static function logDataTablesCalls($file, $content)
    {
        //if(SITE_LIVE)
            return;
        $file .= date('Ymd').'.txt';
        $logfile = APP . "logs/datatables/".$file;
        $message = $content . PHP_EOL."*********************************************".PHP_EOL;
        file_put_contents($logfile, $message, FILE_APPEND);
    }
 }