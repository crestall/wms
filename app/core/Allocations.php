<?php

/**
 * Allocations class.
 *
 * Manages allocation of items and locations to orders

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class Allocations{

    public $controller;

    public function __construct(Controller $controller){
        $this->controller = $controller;
    }

    public function createOrderItemsArray(array $items, $order_id = 0, $store_order = false)
    {
        $oi_values = array();
        $allocations = array();
        $oi_index = 0;
        //echo "<pre>",print_r($items),"</pre>"; die();
        $import_error = false;
        foreach($items as $oid => $order_items)
        {
            $values = array();
            $import_error_string = "";
            $item_error = false;
            $item_error_string = "<ul>";
            $order_error_string = "";
            foreach($order_items as $details)
            {
                $i_id = $details['id'];
                //$pick_count = $left = (int)$details['qty'];
                $item = $this->controller->item->getItemById($i_id);
                //$item_name = $item['name'];
                if(filter_var($details['qty'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1))) === false)
                {
                    $import_error = true;
                    $import_error_string .= "Only whole positive numbers can be selected for pick quantities. ";
                }
                else
                {
                    if($item['collection'] > 0)
                    {
                        $collection_items = $this->controller->item->getCollectionDetails($i_id);
                    }
                    else
                    {
                        $item['number'] = 1;
                        $collection_items = array(
                            $item
                        );

                    }

                    foreach($collection_items as $ci)
                    {
                        //echo "<pre>",print_r($collection_items),"</pre>";continue;
                        $pick_count = $left = $ci['number'] * $details['qty'];
                        $item_name = $ci['name'];
                        $id = $ci['id'];

                        $f_locations = array();

                        if(!isset($allocations[$id])) $allocations[$id] = 0;
                        $total_available = $this->controller->item->getAvailableStock($id, $this->controller->order->fulfilled_id) - $allocations[$id];
                        if($order_id > 0)
                        {
                            $total_available += $this->controller->order->countItemForOrder($id, $order_id);
                        }
                        if( $total_available < $pick_count)
                        {
                            $item_error = true;
                            $item_error_string .= "<li>There are insufficient quantities of $item_name to be able to create/update this order</li>";
                        }
                        else
                        {
                            $allocations[$id] += $pick_count;
                            //if(isset($this->request->data['pallet_'.$id]))
                            //if( $item['palletized'] > 0 )
                            if(isset($details['whole_pallet']) && $details['whole_pallet'])
                            {
                                //items that use whole bays but have inconsistent numbers
                                $locations = $this->controller->item->getAvailableLocationsForItem($id, false, $order_id);
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
                                            'location_id'   =>  $l['location_id'],
                                            'qty'           =>  $available
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
                                //echo "Individual Locations for $id<pre>",print_r($locations),"</pre>";//die();
                                //continue;
                                foreach($locations as $l)
                                {
                                    //echo "Location<pre>",print_r($l),"</pre>";
                                    if(!isset($l_allocations[$l['location_id']][$id]))
                                        $l_allocations[$l['location_id']][$id] = 0;
                                    $available = $l['available'] - $l_allocations[$l['location_id']][$id];
                                    if($available <= 0)
                                        continue;
                                    if($store_order && $l['preferred'] == 1 && count($locations) > 1)
                                        continue;
                                    if($available < $pick_count)
                                    {
                                        if($l['preferred'] == 1 && !$store_order)
                                            $order_error_string .= "<p>$item_name picked from non preferred location</p>";
                                    }
                                    else
                                    {
                                        $f_locations[] = array(
                                            'location_id'   =>  $l['location_id'],
                                            'qty'           =>  $pick_count
                                        );
                                        $l_allocations[$l['location_id']][$id] += $pick_count;
                                        break;
                                    }
                                }
                                //die();
                            }
                        }
                        if(empty($f_locations))
                        {
                            $import_error = true;
                            $import_error_string .= "Could not find a location for $item_name for a quantity of $pick_count. ";
                        }
                        $varray = array(
                            'item_id'               => $id,
                            'locations'             => $f_locations,
                            'item_error_string'     => $item_error_string."</ul>",
                            'item_error'            => $item_error,
                            'order_error_string'    => $order_error_string,
                            'import_error'          => false,
                            'qty'                   => $pick_count
                        );
                        if($import_error)
                        {
                            $varray['import_error'] = true;
                            $varray['import_error_string'] = $import_error_string;
                        }
                        $values[] = $varray;
                    }

                }
            }//endforeach items
            //die();
            $oi_values[$oid] = $values;
        }//endforeach order
        //echo "<pre>",print_r($oi_values),"</pre>";  die();
        //echo "<pre>",print_r($allocations),"</pre>";
        //echo "<pre>",print_r($l_allocations),"</pre>";
        return $oi_values;
    }

    public function createSolarOrderItemsArray(array $items, $order_id = 0, $store_order = false)
    {
        $oi_values = array();
        $allocations = array();
        $oi_index = 0;
        //$an_item = new Item();
        echo "<pre>",print_r($items),"</pre>"; //die();
        $import_error = false;
        foreach($items as $oid => $order_items)
        {
            $values = array();
            $import_error_string = "";
            $item_error = false;
            $item_error_string = "<ul>";
            $order_error_string = "";
            foreach($order_items as $details)
            {
                $i_id = $details['id'];
                //$pick_count = $left = (int)$details['qty'];
                $item = $this->controller->item->getItemById($i_id);
                //$item_name = $item['name'];
                if(filter_var($details['qty'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1))) === false)
                {
                    $import_error = true;
                    $import_error_string .= "Only whole positive numbers can be selected for pick quantities. ";
                }
                else
                {
                    if($item['collection'] > 0)
                    {
                        $collection_items = $this->controller->item->getCollectionDetails($i_id);
                    }
                    else
                    {
                        $item['number'] = 1;
                        $collection_items = array(
                            $item
                        );

                    }

                    foreach($collection_items as $ci)
                    {
                        echo "<pre>",print_r($collection_items),"</pre>"; //continue;
                        $pick_count = $left = $ci['number'] * $details['qty'];
                        $item_name = $ci['name'];
                        $id = $ci['id'];

                        $f_locations = array();

                        if(!isset($allocations[$id])) $allocations[$id] = 0;
                        $total_available = $this->controller->item->getAvailableStock($id, $this->controller->order->fulfilled_id, 'solar_orders_items') - $allocations[$id];
                        echo "<p>Allocation: {$allocations[$id]}</p>";
                        if($order_id > 0)
                        {
                            //$total_available += $this->controller->order->countItemForOrder($id, $order_id);
                            //Add what is in this order
                            if(in_array($ci['client_id'], $this-controller-item->solar_client_ids))
                            {
                                echo "<p>Solar item</p>";
                                $total_available += $this->controller->solarorder->countItemForOrder($id, $order_id);
                                $total_available += $this->controller->solarservicejob->countItemForJob($id, $order_id);
                            }
                            else
                            {
                                $total_available += $this->controller->order->countItemForOrder($id, $order_id);
                            }
                        }
                        echo "<p>Total Available: $total_available</p>"; continue;
                        if( $total_available < $pick_count)
                        {
                            $item_error = true;
                            $item_error_string .= "<li>There are insufficient quantities of $item_name to be able to create/update this order</li>";
                        }
                        else
                        {
                            $allocations[$id] += $pick_count;
                            //if(isset($this->request->data['pallet_'.$id]))
                            //if( $item['palletized'] > 0 )
                            if(isset($details['whole_pallet']) && $details['whole_pallet'])
                            {
                                //items that use whole bays but have inconsistent numbers
                                $locations = $this->controller->item->getAvailableLocationsForItem($id, false, $order_id, 'solar_orders_items');
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
                                            'location_id'   =>  $l['location_id'],
                                            'qty'           =>  $available
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
                                //echo "Individual Locations for $id : $item_name<pre>",print_r($locations),"</pre>";//die();
                                //continue;
                                foreach($locations as $l)
                                {
                                    //echo "Location<pre>",print_r($l),"</pre>";
                                    if(!isset($l_allocations[$l['location_id']][$id]))
                                        $l_allocations[$l['location_id']][$id] = 0;
                                    $available = $l['available'] - $l_allocations[$l['location_id']][$id];
									//echo "Allocation<pre>",print_r($l_allocations),"</pre>";
									//echo "<p>Available: $available</p>";
									//echo "<p>Left: $left</p>";
                                    if($available <= 0)
                                        continue;
                                    if($available <= $left)
                                    {
                                        $f_locations[] = array(
                                            'location_id'   =>  $l['location_id'],
                                            'qty'           =>  $available
                                        );
                                        $l_allocations[$l['location_id']][$id] += $available;
										$left -= $available;
                                    }
									else
									{
										$f_locations[] = array(
                                            'location_id'   =>  $l['location_id'],
                                            'qty'           =>  $left
                                        );
                                        $l_allocations[$l['location_id']][$id] += $left;
										break;
									}
                                }
                                //die();
                            }
							//die();
                        }
						//die();
                        if(empty($f_locations))
                        {
                            $import_error = true;
                            $import_error_string .= "Could not find a location for $item_name for a quantity of $pick_count. ";
                        }
                        $varray = array(
                            'item_id'               => $id,
                            'locations'             => $f_locations,
                            'item_error_string'     => $item_error_string."</ul>",
                            'item_error'            => $item_error,
                            'order_error_string'    => $order_error_string,
                            'import_error'          => false,
                            'qty'                   => $pick_count
                        );
                        if($import_error)
                        {
                            $varray['import_error'] = true;
                            $varray['import_error_string'] = $import_error_string;
                        }
                        $values[] = $varray;
                    }

                }
            }//endforeach items
            die();
            $oi_values[$oid] = $values;
        }//endforeach order
        //echo "<pre>",print_r($oi_values),"</pre>";  die();
        //echo "<pre>",print_r($allocations),"</pre>";
        //echo "<pre>",print_r($l_allocations),"</pre>";
        return $oi_values;
    }
}