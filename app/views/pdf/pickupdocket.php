<?php
//echo "<pre>",print_r($pickup),"</pre>";
$address_string = "";
if(!empty($pickup['address'])) $address_string .= $pickup['address'];
if(!empty($pickup['address_2'])) $address_string .= "<br/>".$pickup['address_2'];
if(!empty($pickup['suburb'])) $address_string .= "<br/>".$pickup['suburb'];
if(!empty($pickup['state'])) $address_string .= "<br/>".$pickup['state'];
if(!empty($pickup['country'])) $address_string .= "<br/>".$pickup['country'];
if(!empty($pickup['postcode'])) $address_string .= "<br/>".$pickup['postcode'];
$items = explode("~",$pickup['items']);
?>
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
<body>
    <div id="pud_body">
        <table id="page_head">
            <tr>
                <td  style="width: 125mm"><img style="height:18mm;width:auto;" src="https://wms.fsg.com.au/images/delivery_docket_logos/fsg_logo.png"></td>
                <td class="right-align sender-address">
                    <strong>FSG Priniting & 3PL Services</strong><br>
                    865 Mountain Hwy, Bayswater VIC 3153<br>
                    <strong>T.</strong>03 9873 5144 - <strong>E.</strong>info@fsg.com.au<br>
                    <strong>www.fsg.com.au</strong>
                </td>
            </tr>
        </table>
        <h2>PICKUP #<?php echo $pickup['pickup_number'];?></h2>
        <h3>DETAILS</h3>
        <table id="pickup_details">
            <tr>
                <td class="w50">
                    <table>
                        <tr>
                            <td class="label">Requested By:</td>
                            <td><?php echo $pickup['requested_by_name'];?></td>
                        </tr>
                        <tr>
                            <td class="label">Vehicle Type:</td>
                            <td><?php echo ucwords($pickup['vehicle_type']);?> </td>
                        </tr>
                        <tr>
                            <td class="label">Pickup Address:</td>
                            <td><?php echo $address_string;?></td>
                        </tr>
                    </table>

                </td>
                <td class="w50 bg-<?php echo $pickup['pickup_window_class'];?> delivery-window">
                    <p>Requested: <strong><?php echo date('D d/m/Y - g:i A', $pickup['date_entered']);?></strong></p>
                    <p>Requested Window: <strong><?php echo ucwords($pickup['pickup_window']);?></strong></p>
                </td>
            </tr>
        </table>
        <h3>ITEMS TO COLLECT</h3>
        <table id="items">
            <?php foreach($items as $i):
                list($item_id, $item_name, $item_sku, $pallet_count) = explode("|",$i);
                $s = ($pallet_count > 1)? "s" : "";?>
                <tr>
                    <td class="w50">
                        <?php echo $item_name." (".$item_sku.")";?>
                    </td>
                    <td>
                        X <strong><?php echo $pallet_count;?></strong> Pallet<?php echo $s;?>
                    </td>
                </tr>
            <?php endforeach;?>
        </table>
    </div>
</body>