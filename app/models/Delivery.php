<?php
 /**
  * Delivery Class
  *

  * @author     Mark Solly <mark.solly@fsg.com.au>

    FUNCTIONS

  */

class Delivery extends Model{
    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "deliveries";
    public $items_table = "deliveries_items";
    public $status_table = "delivery_status";

    public function addDelivery($data)
    {
        $d_values = array(
            'client_id'     => $data['client_id'],
            'attention'     => $data['attention'],
            'date_entered'  => time(),
            'address'       => $data['address'],
            'suburb'        => $data['suburb'],
            'state'         => strtoupper($data['state']),
            'postcode'      => $data['postcode'],
            'urgency_id'    => $data['urgency']
        );
    }



}//end class
?>