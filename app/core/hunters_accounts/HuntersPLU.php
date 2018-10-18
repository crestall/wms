<?php
/**
 * Hunters PLU Location for the Hunters class.
 *
 *
 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class HuntersPLU extends Hunters
{
    public function init()
    {
        $this->CUSTOMER_CODE    = Config::get('HUNTERS_PLUCUSTOMER_CODE');
        $this->USER_NAME        = Config::get('HUNTERS_UNAME');
        $this->PWD              = Config::get('HUNTERS_PWD');
        $this->API_HOST         = Config::get('HUNTERS_HOST');


        $this->curl_options = array(
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_PROXY => false,
            CURLOPT_ENCODING => '',
            CURLOPT_VERBOSE => true,
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => array('Content-type: application/json', 'Authorization: Basic '.base64_encode($this->USER_NAME.':'.$this->PWD)),
            //CURLOPT_SSL_VERIFYHOST => false,
            //CURLOPT_SSL_VERIFYPEER => false
        );
    }
}//end class

?>