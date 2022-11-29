<?php

/**
 * Allocations class.
 *
 * Manages allocation of items and locations to orders

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class Allocations{

    public $controller;

    private $backorder_clients = array(
        86      //BDS
    );

    public function __construct(Controller $controller){
        $this->controller = $controller;
    }

    public function createOrderItemsArray(array $items, $order_id = 0, $store_order = false)
    {
        $oi_values = array();
        $allocations = array();
        $oi_index = 0;
        ///echo "ITEMS in Alloctions<pre>",print_r($items),"</pre>"; //die();
        $import_error = false;
        foreach($items as $oid => $order_items)
        {
            $values = array();
            $import_error_string = "";
            $item_error = false;
            $item_backorder = false;
            //$item_error_string = "<ul>";
            $order_error_string = "";
            $import_error = false;
            foreach($order_items as $details)
            {
                $i_id = $details['id'];
                $item_error_string = "<ul>";
                $item_backorder_string = "<ul>";
                $import_error_string = "<ul>";
                $client_order_item_id = (isset($details['client_item_id']))? $details['client_item_id'] : NULL;
                $shopify_line_item_id = (isset($details['shopify_line_item_id']))? $details['shopify_line_item_id'] : 0;
                $ebay_line_item_id = (isset($details['ebay_line_item_id']))? $details['ebay_line_item_id'] : 0;
                $marketplacer_line_item_id =  (isset($details['marketplacer_line_item_id']))? $details['marketplacer_line_item_id'] : 0;
                $pod_id = (isset($details['pod_id']))? $details['pod_id'] : NULL;
                $item = $this->controller->item->getItemById($i_id);
                if(filter_var($details['qty'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1))) === false)
                {
                    $import_error = true;
                    $import_error_string .= "<li>Only whole positive numbers can be selected for pick quantities.</li>";
                }
                else
                {
                    if($item['collection'] > 0)
                    {
                        $collection_items = $this->controller->item->getCollectionDetails($i_id);
                        $collection_item = array(
                            'item_id'                   =>  $i_id,
                            'location_id'               =>  0,
                            'qty'                       =>  $details['qty'],
                            'client_order_item_id'      =>  $client_order_item_id,
                            'shopify_line_item_id'      =>  $shopify_line_item_id,
                            'ebay_line_item_id'         =>  $ebay_line_item_id,
                            'marketplacer_line_item_id' => $marketplacer_line_item_id,
                            'pod_id'                    =>  $pod_id,
                            'is_kit'                    =>  1
                        );
                        $add_collection = true;
                    }
                    else
                    {
                        $item['number'] = 1;
                        $collection_items = array(
                            $item
                        );
                        $collection_item = array();
                        $add_collection = false;
                    }
                    foreach($collection_items as $ci)
                    {
                        //echo "Allocations<pre>",print_r($allocations),"</pre>";//continue;
                        $pick_count = $left = $ci['number'] * $details['qty'];
                        $item_name = $ci['name'];
                        $item_sku = $ci['sku'];
                        $id = (isset($ci['linked_item_id']))? $ci['linked_item_id'] : $ci['id'];

                        $f_locations = array();
                        $backorder_items = false;

                        if(!isset($allocations[$id])) $allocations[$id] = 0;

                        $a = $this->controller->item->getAvailableStock($id, $this->controller->order->fulfilled_id);
                        $allo = $allocations[$id];
                        $total_available = $this->controller->item->getAvailableStock($id, $this->controller->order->fulfilled_id) - $allocations[$id];

                        $item_error_string .= "<p>Total available for $item_name is $a minus $allo</p>";

                        if($order_id > 0)
                        {
                            $total_available += $this->controller->order->countItemForOrder($id, $order_id);
                        }
                        if( $total_available < $pick_count)
                        {
                            if( in_array($ci['client_id'], $this->backorder_clients) && $item['is_pod'] == 1 )
                            {
                                $allocations[$id] += $total_available; // reserve available for this order
                                //$left = $total_available;

                                //only individual items can be put on backorder
                                $locations = $this->controller->item->getAvailableLocationsForItem($id, false, $order_id);
                                foreach($locations as $l)
                                {
                                    if(!isset($l_allocations[$l['location_id']][$id]))
                                        $l_allocations[$l['location_id']][$id] = 0;
                                    $available = $l['available'] - $l_allocations[$l['location_id']][$id];
                                    if($available <= 0)
                                        continue;
                                    if($store_order && $l['preferred'] == 1 && count($locations) > 1)
                                        continue;
                                    if($available < $left)
                                    {
                                        //echo "<p>available < pickcount</p>";
                                        //if($l['preferred'] == 1 && !$store_order)
                                            //$order_error_string .= "<p>$item_name picked from non preferred location</p>";
                                        $f_locations[] = array(
                                            'location_id'               =>  $l['location_id'],
                                            'qty'                       =>  $available,
                                            'client_order_item_id'      => $client_order_item_id,
                                            'shopify_line_item_id'      =>  $shopify_line_item_id,
                                            'ebay_line_item_id'         =>  $ebay_line_item_id,
                                            'marketplacer_line_item_id' => $marketplacer_line_item_id,
                                            'pod_id'                    => $pod_id
                                        );
                                        $l_allocations[$l['location_id']][$id] += $available;
                                        $left -= $available;
                                    }
                                }
                                if($left > 0)
                                {
                                    $item_backorder = true;
                                    //$item_backorder_string .= "<li>There are insufficient quantities of $item_name ($item_sku) to be able to ship this order. $pick_count required, but only $total_available are available. The difference will need to be ordered through Print On Demand</li>";
                                    $item_backorder_string .= "<li>Item $item_name ($item_sku) is awaiting delivery of $pick_count in $pod_id</li>";
                                    $f_locations[] = array(
                                        'location_id'               =>  $this->controller->location->backorders_id,
                                        'qty'                       =>  $left,
                                        'client_order_item_id'      =>  $client_order_item_id,
                                        'shopify_line_item_id'      =>  $shopify_line_item_id,
                                        'ebay_line_item_id'         =>  $ebay_line_item_id,
                                        'marketplacer_line_item_id' => $marketplacer_line_item_id,
                                        'pod_id'                    =>  $pod_id,
                                        'backorder'                 =>  true
                                    );
                                }
                            }
                            else
                            {
                                $item_error = true;
                                $item_error_string .= "<li>There are insufficient quantities of $item_name ($item_sku) to be able to create/update this order. $pick_count required, but only $total_available are available</li>";
                            }

                        }
                        else
                        {
                            $allocations[$id] += $pick_count;
                            //if(isset($this->request->data['pallet_'.$id]))
                            if( $item['palletized'] > 0 )
                            //if(isset($details['whole_pallet']) && $details['whole_pallet'])
                            {
                                //items that use whole bays but have inconsistent numbers
                                $locations = $this->controller->item->getAvailableLocationsForItem($id, true, $order_id);
                                //echo "Pallet Locations for $id<pre>",print_r($locations),"</pre>";//die();
                                foreach($locations as $l)
                                {
                                    //echo "Location<pre>",print_r($l),"</pre>";
                                    if(!isset($l_allocations[$l['location_id']][$id]))
                                        $l_allocations[$l['location_id']][$id] = 0;
                                    $available = $l['available'] - $l_allocations[$l['location_id']][$id];
                                    if($available <= 0)
                                        continue;
                                    if($available == $left)
                                    {
                                        $f_locations[] = array(
                                            'location_id'               =>  $l['location_id'],
                                            'qty'                       =>  $available,
                                            'client_order_item_id'      => $client_order_item_id,
                                            'shopify_line_item_id'      =>  $shopify_line_item_id,
                                            'ebay_line_item_id'         =>  $ebay_line_item_id,
                                            'marketplacer_line_item_id' => $marketplacer_line_item_id,
                                            'pod_id'                    => $pod_id
                                        );
                                        $l_allocations[$l['location_id']][$id] += $available;
                                        $left -= $available;
                                        break;
                                    }
                                }
                            }
                            else
                            {
                                //individual items
                                $locations = $this->controller->item->getAvailableLocationsForItem($id, false, $order_id);
                                //echo "Individual Locations for $item_name<pre>",print_r($locations),"</pre>";//die();
                                //continue;
                                foreach($locations as $l)
                                {
                                    //echo "Location<pre>",print_r($l),"</pre>";
                                    if(!isset($l_allocations[$l['location_id']][$id]))
                                        $l_allocations[$l['location_id']][$id] = 0;
                                    $available = $l['available'] - $l_allocations[$l['location_id']][$id];
                                    //echo "<p>$item_name Available: $available</p><p>Pickcount: $pick_count</p><p>Left: $left</p>";
                                    if($available <= 0)
                                        continue;
                                    if($store_order && $l['preferred'] == 1 && count($locations) > 1)
                                        continue;
                                    if($available < $left)
                                    {
                                        //echo "<p>available < pickcount</p>";
                                        //if($l['preferred'] == 1 && !$store_order)
                                            //$order_error_string .= "<p>$item_name picked from non preferred location</p>";
                                        $f_locations[] = array(
                                            'location_id'               =>  $l['location_id'],
                                            'qty'                       =>  $available,
                                            'client_order_item_id'      => $client_order_item_id,
                                            'shopify_line_item_id'      =>  $shopify_line_item_id,
                                            'ebay_line_item_id'         =>  $ebay_line_item_id,
                                            'marketplacer_line_item_id' => $marketplacer_line_item_id,
                                            'pod_id'                    => $pod_id
                                        );
                                        $l_allocations[$l['location_id']][$id] += $available;
                                        $left -= $available;
                                        //break;
                                        continue;
                                    }
                                    else
                                    {
                                        //echo "<p>available >= pickcount</p>";
                                        $f_locations[] = array(
                                            'location_id'               =>  $l['location_id'],
                                            'qty'                       =>  $left,
                                            'client_order_item_id'      => $client_order_item_id,
                                            'shopify_line_item_id'      =>  $shopify_line_item_id,
                                            'ebay_line_item_id'         =>  $ebay_line_item_id,
                                            'marketplacer_line_item_id' => $marketplacer_line_item_id,
                                            'pod_id'                    => $pod_id
                                        );
                                        $l_allocations[$l['location_id']][$id] += $left;
                                        break;
                                    }
                                }
                                //die();
                            }
                        }
                        if(empty($f_locations))
                        {
                            $import_error = true;
                            $import_error_string .= "<li>Could not find a location for $item_name ($item_sku) for a quantity of $pick_count.</li>";
                        }
                        $varray = array(
                            'item_id'               => $id,
                            'locations'             => $f_locations, 
                            'item_error_string'     => $item_error_string."</ul>",
                            'item_error'            => $item_error,
                            'item_backorder_string' => $item_backorder_string."</ul>",
                            'item_backorder'        => $item_backorder,
                            'order_error_string'    => $order_error_string,
                            'import_error'          => false,
                            'qty'                   => $pick_count
                        );
                        if($add_collection)
                        {
                            $varray['collection_item'] = $collection_item;
                            $add_collection = false;
                        }
                        if($import_error)
                        {
                            $varray['import_error'] = true;
                            $varray['import_error_string'] = $import_error_string."</ul>";
                        }
                        $values[] = $varray;
                    }
                }
            }//endforeach items
            //die();
            $oi_values[$oid] = $values;
        }//endforeach order
        //echo "<pre>OI Values",print_r($oi_values),"</pre>";
        //die();
        //echo "Allocations<pre>",print_r($allocations),"</pre>";
        //echo "l_allocations<pre>",print_r($l_allocations),"</pre>"; die();
        return $oi_values;
    }
}