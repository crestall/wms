<?php
/**
 * View Inventory Implementation of the DataTablesSS Class.
 *
 *
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */
 class ViewInventory extends DataTablesSS
 {
    private $return_array       = array();
    private $table              = "items_locations";
    private $locations_table    = "locations";
    private $items_table        = "items";
    private $columns            = array();

    public function init()
    {
        $this->columns = array(
            array( 'db' => 'name', 'dt' => 0 ),
            array( 'db' => 'sku',  'dt' => 1 ),
            array( 'db' => 'barcode',   'dt' => 2 ),
            array( 'db' => 'client_product_id', 'dt' => 3 ),
            array( 'db' => 'on_hand', 'dt' => 4 ),
            array( 'db' => 'allocated', 'dt'=> 5),
            array( 'db' => 'qc_count', 'dt'=> 6),
            array( 'db' => 'available', 'dt'=> 7),
            array( 'db' => 'locations', 'dt'=> 8),
            array( 'db' => '', 'dt' => 9)
        );
    }
 }
?>