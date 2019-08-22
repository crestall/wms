<?php
class Itemmovement extends Model{
    public $table = "items_movement";

    public function recordData($data)
    {
        $db = Database::openConnection();
        $db->insertQuery($this->table, $data);
        return true;
    }

    public function getItemMovements($client_id, $from, $to, $exc = array())
    {
        $db = Database::openConnection();
        $exids = (count($exc))? "AND reason_id NOT IN (".implode(",", $exc).")" : "";
        $query = "
            SELECT
                i.name, i.sku, im.*, l.location
            FROM
                items i JOIN items_movement im ON i.id = im.item_id JOIN locations l ON im.location_id = l.id
            WHERE
                i.client_id = :client_id $exids AND date >= :from AND date <= :to
            ORDER BY
                im.date DESC
        ";
        $array = array(
            'client_id' => 	$client_id,
            'to'        =>  $to,
            'from'      =>  $from
        );
        return $db->queryData($query, $array);
    }

    public function getItemMovementsSummary($client_id, $from, $to, $exc = array())
    {
        $db = Database::openConnection();
        $exids = (count($exc))? "AND reason_id NOT IN (".implode(",", $exc).")" : "";
        $query = "
            SELECT
                i.id, i.name, i.sku, SUM(im.qty_in) AS total_in, SUM(im.qty_out) AS total_out
            FROM
                items i JOIN items_movement im ON i.id = im.item_id
            WHERE
                i.client_id = :client_id $exids AND date >= :from AND date <= :to
            GROUP BY
                i.id
        ";
        $array = array(
            'client_id' => 	$client_id,
            'to'        =>  $to,
            'from'      =>  $from
        );
        return $db->queryData($query, $array);
    }

    public function getItemMovementsSummaryArray($client_id, $from, $to, $exc = array())
    {
        $db = Database::openConnection();
        $summary = $this->getItemMovementsSummary($client_id, $from, $to, $exc);
        $return = array();
        foreach($summary as $sm)
        {
            $row = array(
                'name'  => $sm['name'],
                'sku'   => $sm['sku'],
                'total_in'  => $sm['total_in'],
                'total_out' => $sm['total_out']
            );
            $ohq = $db->queryRow("SELECT SUM(qty) AS on_hand FROM items_locations WHERE item_id = {$sm['id']} GROUP BY item_id");
            $row['on_hand'] = $ohq['on_hand'];
            $return[] = $row;
        }
        return $return;
    }

    public function getItemMovementsArray($client_id, $from, $to, $exc = array())
    {
        $db = Database::openConnection();
        $movements = $this->getItemMovements($client_id, $from, $to, $exc);
        $return = array();
        foreach($movements as $sm)
        {
            $eb = $db->queryValue('users', array('id' => $sm['entered_by']), 'name');
            $row = array(
                'date'          => date('d-m-Y', $sm['date']),
                'reference'     => $sm['reference'],
                'location'      => $sm['location'],
                'entered_by'    => $eb
            );
            if(is_null($sm['order_id']))
            {
                $row['order_number'] = $sm['reference'];
                $row['client_order_id'] = "";
                $row['address'] = "";
            }
            else
            {
                if($client_id == 67)
                {
                   $od = $db->queryByID('solar_orders', $sm['order_id']);
                   $on = $od['work_order'];
                   $row['client_order_id'] = "";
                   $row['address'] = $od['customer_name'];
                }
                else
                {
                    $od = $db->queryByID('orders', $sm['order_id']);
                    $on = $od['order_number'];
                    $row['client_order_id'] = $od['client_order_id'];
                    $row['address'] = $od['ship_to'];
                }
                $row['order_number'] = $on;
                $ad = array(
                    'address'   =>  $od['address'],
                    'address_2' =>  $od['address_2'],
                    'suburb'    =>  $od['suburb'],
                    'state'     =>  $od['state'],
                    'postcode'  =>  $od['postcode'],
                    'country'   =>  $od['country']
                );

                if(!empty($od['company_name']))
                    $row['address'] .= "<br/>".$od['company_name'];
                $row['address'] .= "<br/>".Utility::formatAddressWeb($ad);
                $row['ref'] = "<a href='/client-order-detail/{$sm['order_id']}'>Order No: ".str_pad($on,8,'0',STR_PAD_LEFT)."</a>";
            }
            $row['reason'] = $db->queryValue('stock_movement_labels', array('id'=>$sm['reason_id']), 'name');
            $row['sku'] = $sm['sku'];
            $row['name'] = $sm['name'];
            $row['qty_in']  = $sm['qty_in'];
            $row['qty_out'] = $sm['qty_out'];
            $return[] = $row;
        }
        return $return;
    }

    public function getStockAtDateArray($client_id, $date = 0)
    {
        $date = ($date == 0)? time(): $date;
        //echo date("l jS \of F Y h:i:s A", $date);
        $date += 24 * 60 *60; //move to end of day
        $db = Database::openConnection();
        $return = array();
        $items = $db->queryData("SELECT id, name, sku, image FROM items WHERE client_id = $client_id AND active = 1 AND collection = 0 AND pack_item = 0 ORDER BY name");
        foreach($items as $i)
        {
            $ohq = $db->queryRow("SELECT SUM(qty) AS on_hand, SUM(qc_count) AS qc_count FROM items_locations WHERE item_id = {$i['id']} GROUP BY item_id");
            $on_hand = $ohq['on_hand'];
            $qc = $ohq['qc_count'];
            $io_array = $db->queryRow("SELECT sum(qty_out) AS qty_out, sum(qty_in) AS qty_in FROM items_movement WHERE item_id = {$i['id']} AND date >= $date");
            $soh = $on_hand + $io_array['qty_out'] - $io_array['qty_in'];
            $row = array(
                'name'      =>  $i['name'],
                'sku'       =>  $i['sku'],
                'on_hand'   =>  $soh
            );
            $return[] = $row;
        }
        return $return;
    }
}
?>