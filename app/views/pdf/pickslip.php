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
    if(!empty($od['company_name']))
    {
        $ship_to = $od['company_name']."<br>Attn:".$od['ship_to'];
    }
    else
    {
        $ship_to = $od['ship_to'];
    }
    if(!empty($od['contact_phone']))
    {
        $ship_to .= "<br/>".$od['contact_phone'];
    }
    $items = $this->controller->order->getItemsForOrder($id);
    $item_count = $this->controller->order->getItemCountForOrder($id);
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
                <td><h3>FSG Packing Slip</h3></td>
                <td><barcode type='ean13' code='<?php echo $od['order_number'];?>' /></td>
            </tr>
            <tr>
                <td>Printed : <?php echo date("h:i a d/m/Y");?></td>
                <td>Date Ordered: <?php echo date("d/m/Y", $od['date_ordered']);?></td>
            </tr>
        </table>
        <table width='100%'>
            <tr><td>
                <h4>Client</h4></td><td><h4><?php echo $client_name;?></h4>
                <?php if($od['is_voicecaddy'] == 1):?>
                    <p>Voice Caddy Order</p>
                <?php elseif($od['is_homecoursegolf'] == 1):?>
                    <p>Home Course Golf Order</p>
                <?php elseif($od['is_superspeedgolf'] == 1):?>
                    <p>Superspeed Golf Order</p>
                <?php elseif($od['is_rukket'] == 1):?>
                    <p>Rucket Order</p>
                <?php endif;?>
            </td></tr>
            <tr><td><h4>Order Number:</h4></td><td><h4><?php echo $od['order_number'];?></h4></td></tr>
            <tr>
                <td><h4>Client Invoice Number:</h4></td>
                <td>
                    <h4><?php echo $od['client_order_id'];?></h4>
                    <?php if($od['is_woocommerce'] == 1):?>
                        <p>WooCommerce Order</p>
                    <?php elseif($od['is_shopify'] == 1):?>
                        <p>Shopify Order</p>
                    <?php elseif($od['is_ebay'] == 1):?>
                        <p>eBay Order</p>
                    <?php elseif($od['is_marketplacer'] == 1):?>
                        <p>Marketplace Order</p>
                    <?php endif;?>
                </td>
            </tr>
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
            if( preg_match('/https?/i', $i['image'])  )
            {
                try{
                    $file_headers = @get_headers($i['image']);
                    //if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found' || $file_headers[0] == 'HTTP/1.1 403 Forbidden')
                    if(!$file_headers || $file_headers[0] != 'HTTP/1.1 200 OK')
                    {

                    }
                    else
                    {
                        $image = "<img src='".$i['image']."' class='img-thumbnail img-fluid'>";
                    }
                }catch(Exception $e)
                {

                }
            }
            elseif(!empty($i['image']))
            {
                $image = "<img src='".PUBLIC_ROOT."/images/products/tn_".$i['image']."' class='img-fluid img-thumbnail'>";
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
        <?php if($item_count == 1 && $items[0]['boxed_item'] == 1):?>
            <tr><td align='center' colspan='8'>Auto Packaging Is Available</td></tr>  
        <?php endif;?>
        <?php if(!empty($od['3pl_comments'])):?>
            <tr><td align='center' colspan='8'><?php echo $od['3pl_comments'];?></td></tr>
        <?php endif;?>
        <?php if(!empty($od['pick_notices'])):?>
            <tr><td align='center' colspan='8'><?php echo $od['pick_notices'];?></td></tr>
        <?php endif;?>

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
<h1>FSG Picking Slip Summary</h1>
<p>Printed : ".date("h:i a d/m/Y")."</p>
$barcode_html
<table class='pickslip' width='100%'>
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