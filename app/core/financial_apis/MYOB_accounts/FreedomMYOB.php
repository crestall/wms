<?php
/**
 * Freedom implimentation of the MYOB class.
 *
 *
 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class FreedomMYOB extends MYOB
{
    private $client_id = 7;

    public function init()
    {
        //Client Specific Credentials
        $this->URL = 'https://coventrypress.com.au/api/';
        $this->USERNAME = 'mark';
        $this->PASSWORD = 'szqwj1QdSuVZ8dThwl';
        //Client Specific Encryption Details
        $this->CYPHER = "AES-256-CBC";
        $this->KEY = Config::get('FREEDOM_MYOB_KEY');
        $this->TAG = "Coventry Press";
    }

    protected function encryptData($data)
    {
        $ivlen = openssl_cipher_iv_length($this->CYPHER);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $encrypted = openssl_encrypt($data, $this->CYPHER, $this->KEY, $options=0, $iv);
        $iv = base64_encode($iv);
        return $iv."\r\n".$encrypted;
    }

    protected function decryptData($data)
    {
        global $cipher, $key, $tag;
        list($iv, $encrypted) = explode("\r\n",$data);
        $iv = base64_decode($iv);
        $decrypted = openssl_decrypt($encrypted, $this->CYPHER, $this->KEY, $options=0, $iv);
        return $decrypted;
    }
}//end class

?>