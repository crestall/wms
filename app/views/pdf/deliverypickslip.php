<?php

foreach($delivery_ids as $id):
    $dd = $this->controller->delivery->getDeliveryDetails($id);
    echo "<pre>",print_r($dd),"</pre>";die();
    ?>
    <div class='pickslip'>
        <h2>FSG Delivery Picking Slip</h2>
        <table width='100%'>
            <tr>
                <td>Printed : <?php echo date("h:i a d/m/Y");?></td>
                <td>Date/Time Requested: <?php echo date("d/m/Y", $od['date_ordered']);?></td>
            </tr>
        </table>
        <table width='100%'>
            <tr><td><h4>Client</h4></td><td><h4><?php echo $client_name;?></h4></td></tr>
            <tr><td><h4>Order Number:</h4></td><td><h4><?php echo $od['order_number'];?></h4></td></tr>
            <tr><td><h4>Client Invoice Number:</h4></td><td><h4><?php echo $od['client_order_id'];?></h4></td></tr>
            <tr><td>Deliver To</td><td><?php echo $ship_to;?></td></tr>
            <tr><td>Delivery address</td><td><?php echo $delivery_address;?></td></tr>
        </table>
        <table class='pickslip' width='100%'>
        <tr>
            <th>Item</th>
            <th>SKU</th>
            <th>Barcode</th>
            <th>Location</th>
            <th>Quantity</th>
            <th>Image</th>
            <th>Picked</th>
            <th>Checked</th>
        </tr>
        <?php foreach($items as $i):
            $location = $this->controller->location->getLocationName($i['location_id']);
            $express = ($od['eparcel_express'] > 0)? "Express Post": "";
            if( isset($summary_pick[$i['location_id']][$i['id']]) )
            {
                $summary_pick[$i['location_id']][$i['id']]['qty'] += $i['qty'];
                $summary_pick[$i['location_id']][$i['id']]['item_name'] = $i['name']." (".$i['sku'].")";

            }
            else
            {
                $summary_pick[$i['location_id']][$i['id']]['qty'] = $i['qty'];
                $summary_pick[$i['location_id']][$i['id']]['item_name'] = $i['name']." (".$i['sku'].")";
                $summary_pick[$i['location_id']]['location'] = $location;
            }
            /**/
            if(isset($summary_scan[$i['location_id']]))
            {
                $summary_scan[$i['location_id']]['items'][] = array(
                    'id'    => $i['id'],
                    'name'  => $i['name'],
                    'qty'   => $i['qty']
                );
            }
            else
            {
                $summary_scan[$i['location_id']] = array(
                    'location_name' => $location,
                    'items'         => array(
                        array(
                            'id'    => $i['id'],
                            'name'  => $i['name'],
                            'qty'   => $i['qty']
                        )
                    )
                );
            }
            $image = "";
            if(preg_match('/https?/i', $i['image']))
            {
                $image = "<img src='".$i['image']."' class='img-thumbnail img-fluid'>";
            }
            elseif(!empty($i['image']))
            {
                $image = "<img src='/images/products/tn_".$i['image']."' class='img-fluid img-thumbnail'>";
            }
            ?>
            <tr>
               	<td><?php echo $i['name'];?></td>
                <td><?php echo $i['sku'];?></td>
                <td><?php echo $i['barcode'];?></td>
                <td><?php echo $location;?></td>
                <td class='number bold'><?php echo $i['qty'];?></td>
                <td><?php echo $image;?></td>
                <td class='centre'><span class='check_box'></span></td>
                <td class='centre'><span class='check_box'></span></td>
            </tr>
        <?php endforeach;?>
        <tr><td align='right' colspan='5'><?php echo $courier;?></td><td align='right' colspan='3'><?php echo $express;?></td></tr>
        <tr><td align='center' colspan='8'><?php echo $od['3pl_comments'];?></td></tr>
        <tr><td align='center' colspan='8'><?php echo $od['pick_notices'];?></td></tr>
        </table>
    </div>
    <pagebreak />
<?php endforeach; ?>