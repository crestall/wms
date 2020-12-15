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

    private $output;
    private $order_items;
    private $orders_csv = array();

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

    public function collectOrders($file)
    {
        $tmp_handle = fopen('php://temp', 'r+');
        if (ftp_fget($this->CON_ID, $tmp_handle, $file, FTP_ASCII))
        {
            rewind($tmp_handle);
            while ($row = fgetcsv($tmp_handle))
            {
                //echo "<pre>",print_r($row),"</pre>";
                $this->orders_csv[] = $row;
            }
            $this->output = "=========================================================================================================".PHP_EOL;
            $this->output .= "IMPORTING BDS ORDERS ON ".date("jS M Y (D), g:i a (T)").PHP_EOL;
            $this->output .= "=========================================================================================================".PHP_EOL;
            $orders = $this->processOrders($this->orders_csv) ;
            if($orders = processOrders($this->orders_csv))
            {
                echo "<pre>",print_r($orders),"</pre>";die();
                //$this->addOnePlateOrders($orders);
            }
            Logger::logOrderImports('order_imports/oneplate', $this->output); //die();
        }
        else
        {
            Logger::log("FTP Could not open file", "Could not open ". $file);
            throw new Exception("Could not open ". $file);
        }
        fclose($tmp_handle);
    }

    private function processOrders($the_orders)
    {
        //echo "<pre>",print_r($the_orders),"</pre>"; die();
        /*
        [0] => Order_Number
        [1] => Shipment_Address_Company
        [2] => Shipment_Address_Name
        [3] => Shipment_Address1
        [4] => Shipment_Address2
        [5] => Shipment_Address_City
        [6] => Shipment_Address_State
        [7] => Shipment_AddressZIP_Code
        [8] => Shipment_Address_Country_Code
        [9] => Shipment_Address_Phone
        [10] => tracking_email
        [11] => ATL
        [12] => Delivery Instructions
        [13] => Express_Post
        [14] => Client Entry
        [15] => Item_1_sku
        [16] => Item_1_qty
        [17] => Item_1_id
        [18] => Item_2_sku
        [19] => Item_2_qty
        [20] => Item_2_id
        [21] => Item_3_sku
        [22] => Item_3_qty
        [23] => Item_3_id
        [24] => Item_4_sku
        [25] => Item_4_qty
        [26] => Item_4_id
        */
        if(count($the_orders) == 0)
            return false;
        $orders = array();
        if(!isset($the_orders[0]))
            $collected_orders[] = $the_orders;
        else
            $collected_orders = $the_orders;

        echo "THE ORDERS<pre>",print_r($the_orders),"</pre>";die();
        $skip_first = true;
        if(count($collected_orders) > 0)
        {
            $allocations = array();
            $orders_items = array();
            foreach($collected_orders as $o)
            {
                //echo "<pre>",print_r($row),"</pre>";continue;
                if($skip_first)
                {
                    $skip_first = false;
                    continue;
                }
            }
        }
        else
        {
            $this->output .= "=========================================================================================================".PHP_EOL;
            $this->output .= "No New Orders".PHP_EOL;
            $this->output .= "=========================================================================================================".PHP_EOL;
        }
        return false;
    }
} //end class
?>