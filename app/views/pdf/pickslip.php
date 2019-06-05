<?php
$summary_pick = array();
$summary_scan = array();
$express = "";
foreach($orders_ids as $id):
    //$order_ids_string .= $id."-";
    $od = $this->controller->order->getOrderDetail($id);
    //echo "<pre>",print_r($od),"</pre>";die();
    $courier = $this->controller->courier->getCourierName($od['courier_id']);
    $client_name = $this->controller->client->getClientName($od['client_id']);
    $delivery_address = $this->controller->address->getAddressStringForOrder($id);
    if(empty($od['ship_to']))
    {
        $ship_to = $this->controller->customer->getCustomerName($od['customer_id']) ;
    }
    else
    {
        $ship_to =  $od['ship_to'];
    }
    if(!empty($od['contact_phone']))
    {
        $ship_to .= "<br/>".$od['contact_phone'];
    }
    $items = $this->controller->order->getItemsForOrder($id);
    $this->controller->order->setSlipPrinted($id);
    $picked_id = $this->controller->order->picked_id;
    $ordered_id = $this->controller->order->ordered_id;
    $satchels = 0;
    if($od['status_id'] == $ordered_id)
        $this->controller->order->updateStatus($picked_id, $id);
    ?>
    <div class='pickslip'>
        <table width='100%'>
            <tr>
                <td><h3>3PL Packing Slip</h3></td>
                <td><barcode type='ean13' code='<?php echo $od['order_number'];?>' /></td>
            </tr>
            <tr>
                <td>Date : <?php echo date("d/m/Y");?></td>
                <td>Date Ordered: <?php echo date("d/m/Y", $od['date_ordered']);?></td>
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
            <th>Location</th>
            <th>Quantity</th>
            <th></th>
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

            ?>
            <tr>
               	<td><?php echo $i['name'];?></td>
                <td><?php echo $i['sku'];?></td>
                <td><?php echo $location;?></td>
                <td class='number bold'><?php echo $i['qty'];?></td><td>
                </td><td class='centre'><span class='check_box'></span></td>
                <td class='centre'><span class='check_box'></span></td>
            </tr>
        <?php endforeach;?>
        <tr><td align='right' colspan='4'><?php echo $courier;?></td><td align='right' colspan='3'><?php echo $express;?></td></tr>
        <tr><td align='center' colspan='7'><?php echo $od['3pl_comments'];?></td></tr>
        <tr><td align='center' colspan='7'><?php echo $od['pick_notices'];?></td></tr>
        </table>
    </div>
    <pagebreak />
<?php endforeach;
//echo "<pre>",print_r($summary_pick),"</pre>";
//echo "<pre>",print_r($summary_scan),"</pre>";die();
$ids_barcode = $this->controller->pickorder->savePickSummary($summary_scan);

$barcode_html = "<p style='text-align: center'><barcode code='$ids_barcode' type='EAN13' class='barcode' height='0.5' /></p>";

$html = "
<div class='pickslip'>
<h1>3PL Picking Slip Summary</h1>
<p>Date : ".date("d/m/Y")."</p>
$barcode_html
<table class='pickslip'>
    <tr>
        <th>Location</th>
        <th>Items</th>
        <th>Quantity</th>
    </tr>
";
$ls = 0;
foreach($summary_pick as $l_id => $details)
{
    $c = 0;

    $rows = count($details) - 1;
    $class = ($ls % 2 == 0)? "even":"odd";
    $html .= "
    <tr class='$class'>
    <td rowspan='$rows' valign='top'>{$details['location']}</td>";
    foreach($details as $i_d => $d)
    {
        if(!is_array($d)) continue;
        if($c > 0) $html .= "<tr class='$class'>";
        $html .= "<td>{$d['item_name']}</td><td>{$d['qty']}</td>";
        $html .= "</tr>";
        ++$c;
    }
    ++$ls;
}
$html .= "</table></div>";
echo $html;
?>