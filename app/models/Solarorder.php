<?php
 /**
  * Originorder Class
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>

    FUNCTIONS
    addOrder($data, $oitems)


  */
  class Solarorder extends Order{

    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "solar_orders";

    public function __construct()
    {
        parent::_construct();
    }

    public function addOrder($data, $oitems)
    {
        $db = Database::openConnection();

        if(empty(Session::getUserId()))
        {
            $eb = (isset($data['entered_by']))? $data['entered_by'] : 0;
        }
        else
        {
            $eb = Session::getUserId();
        }
        $o_values = array(
            'work_order'    => $data['work_order'],
            'type_id'       => $data['type_id'],
            'client_id'     => $data['client_id'],
            'date_ordered'  => time(),
            'status_id'     => $this->ordered_id,
            'address'       => $data['address'],
            'suburb'        => $data['suburb'],
            'state'         => $data['state'],
            'postcode'      => $data['postcode'],
            'country'       => $data['country'],
            'entered_by'    => $eb
        );

        $order_id = $db->insertQuery($this->table, $o_values);
        //echo "<pre>",print_r($oitems),"</pre>"; die();
        foreach($oitems as $items)
        {
            if(!isset($items[0]))
                $the_items[] = $items;
            else
                $the_items = $items;
            foreach($the_items as $item):
                $item_id = $item['item_id'];
                foreach($item['locations'] as $il)
                {
                    /* */
                    $vals = array(
                        'item_id'       => $item_id,
                        'location_id'   => $il['location_id'],
                        'qty'           => $il['qty'],
                        'order_id'      => $order_id
                    );
                    $db->insertQuery('origin_orders_items', $vals);

                }
            endforeach;
        }
        //die();
        return $order_id;
    }


  }

?>