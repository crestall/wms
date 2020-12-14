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
        $this->URL = 'ftp.bahai.org.au/bdsorders';
        $this->USERNAME = 'bdsorders';
        $this->PASSWORD = 'mN**s735a';
    }
} //end class
?>