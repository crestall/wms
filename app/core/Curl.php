<?php

 /**
  * Curl class
  *
  * Used to handle CURL request
  *
  
  * @author     Mark Solly <mark.solly@fsg.com.au>
  */

class Curl{

    private static $curl_options = array(
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_ENCODING        => "",
        CURLOPT_MAXREDIRS       => 10,
        CURLOPT_TIMEOUT         => 0,
        CURLOPT_FOLLOWLOCATION  => true,
        CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1
    );
    private static $headers = array();
    /**
     * Constructor
     *
     */
    private function __construct(){}

    public static function sendStandardPostRequest($url, array $data, $method = '')
    {
        self::$headers = array(
            'Content-Type: application/json',
            'Cache-Control: no-cache',
        );
        return self::sendPostRequest($url, $data, $method);
    }

    public static function sendSecurePOSTRequest($url, $data, $user, $pass, $method = '')
    {
        self::$headers = array(
            'Authorization: Basic '. base64_encode($user.":".$pass),
            'Content-Type: application/json',
            'Cache-Control: no-cache',
        );

        return self::sendPostRequest($url, $data, $method);
    }

    public static function sendStandardGetRequest($url, $data)
    {
        return self::sendGetRequest($url, $data);
    }

    public static function sendSecureGetRequest($url, $data, $user, $pass)
    {
        self::$headers = array(
            'Authorization: Basic '. base64_encode($user.":".$pass),
            'Content-Type: application/json',
            'Cache-Control: no-cache',
        );

        return self::sendGetRequest($url, $data);
    }

    private static function sendGetRequest($url, $data)
    {
        $ch = curl_init();
        $verbose = fopen('php://temp', 'w+');
        curl_setopt_array($ch, self::$curl_options);
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, self::$headers);

        $response = curl_exec($ch);
        if ($response === FALSE) {
            printf("cUrl error (#%d): %s<br>\n", curl_errno($ch),
                   htmlspecialchars(curl_error($ch)));
            rewind($verbose);
            $verboseLog = stream_get_contents($verbose);
            echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
            die();
        }
        curl_close($ch);
        return $response;
    }

    private static function sendPostRequest($url, $data, $method)
    {
        $ch = curl_init();

        if($method == 'form')
        {
            $fields_string = '';
            foreach($data as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
            rtrim($fields_string, '&');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        }
        else //json by default
        {
            $data_string = json_encode($data);
            self::$headers[] =  'Content-Length: ' . strlen($data_string);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        }
        curl_setopt_array($ch, self::$curl_options);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, self::$headers);

        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err)
        {
            Logger::log("CURL Error", $err." ".$url." ".print_r($data, true), __FILE__, __LINE__);
            return false;
        }
        else
        {
            return $result;
        }
    }
 }