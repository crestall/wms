<?php
$name = $od['ship_to'];
$company = $od['company_name'];

$address1 =  $od['address'];
$address2 = $od['address_2'];
$suburb = $od['suburb'];
$state =  $od['state'];
$postcode = $od['postcode'];
$country = $od['country'];
$html = "
    <table align='center'>
        <tr>
            <td class='centre middle w70'><h2>PACKING SLIP</h2></td>
            <td class='centre middle w30'><img src='https://wms.3plplus.com.au/images/client_logos/tn_{$client['logo']}' /></td>
        <tr>
        <tr>
            <td class='w70'>
                <span class='address'>
                    $name <br/>";
                    if(!empty($company)) $html .= "$company<br/>";
                    $html .= "$address1<br/>";
                    if(!empty($address2)) $html .= "$address2<br/>";
                    $html .= "$suburb<br/>
                    $state<br/>
                    $postcode<br/>
                    $country
                </span>
            </td>
            <td class='w30'>
                <span class='bb_address'>
                    5 Mosrael Place<br />
                    ROWVILLE VIC 3178<br/>
                    AUSTRALIA<br/>
                    ABN: 11 606 776 587
                </span>
            </td>
        </tr>
        <tr>
            <td class='spacer' colspan='2'>&nbsp;</td>
        </tr>
        <tr>
            <td colspan='2'>Customer Order No: {$od['customer_order_id']}</td>
        </tr>
        <tr>
            <td colspan='2'>Order No: {$od['client_order_id']}</td>
        </tr>
        <tr>
            <td class='head'>Item</td><td class='right head'>Qty</td>
        </tr>";
        foreach($items as $item)
        {
            $html .= "<tr><td class='item'>".$item['name']."</td><td class='item right'>".(int)$item['qty']."</td></tr>";
        }
    $html .= "</table>
";

echo $html;
?>