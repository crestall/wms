<?php
/**
 * BDS implimentation of the FTP class.
 *
 *
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class BdsFTP extends FTP
{
    private $client_id = 86;

    private $return_array = array(
        'orders_created'        => 0,
        'invoices_processed'    => 0,
        'import_error'          => false,
        'error'                 => false,
        'error_count'           => 0,
        'error_string'          => '',
        'import_error_string'   => '',
        'import_message'        => ''
    );

    public function init()
    {
        //Client Specific Credentials
        $this->URL = 'ftp.bahai.org.au';
        $this->USERNAME = 'bdsorders';
        $this->PASSWORD = 'mN**s735a';
    }

    public function processOrders($file)
    {
        $tmp_handle = fopen('php://temp', 'r+');
        if (ftp_fget($this->CON_ID, $tmp_handle, $file, FTP_ASCII))
        {
            rewind($tmp_handle);
            while ($row = fgetcsv($tmp_handle)) {
                echo "<pre>",print_r($row),"</pre>";
            }
        }
        fclose($tmp_handle);
    }
} //end class
?>