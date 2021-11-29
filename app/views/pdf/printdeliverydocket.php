<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
<body>  
<?php
$count = 0;
//echo "<pre>",print_r($delivery_ids),"</pre>";die();
//Address Table Constants
$address_table_class = "address_details";
$address_padding_cell_width = "5mm";
$address_cell_width = "105mm";
$date_cell_width = "65mm";
foreach($delivery_ids as $id):
    ++$count;
    $d = $this->controller->delivery->getDeliveryDetails($id);
    //echo "<pre>",print_r($d),"</pre>";die();
    $address_string = $d['client_name'];
    if(!empty($d['address'])) $address_string .= "<br/>".$d['address'];
    if(!empty($d['address_2'])) $address_string .= "<br/>".$d['address_2'];
    if(!empty($d['suburb'])) $address_string .= "<br/>".$d['suburb'];
    if(!empty($d['state'])) $address_string .= "<br/>".$d['state'];
    if(!empty($d['country'])) $address_string .= "<br/>".$d['country'];
    if(!empty($d['postcode'])) $address_string .= "<br/>".$d['postcode'];
    $this->controller->delivery->markDeliveryVehicleAssigned($id);
    $items = explode("~",$d['items']);
    $total_pallets = count($items);
    $items_array = array();
    foreach($items as $i):
        list($item_id, $item_name, $item_sku, $item_qty, $location_id) = explode("|",$i);
        //$total_items += $item_qty;
        //$skus[] = $item_sku;
        if(isset($items_array[$item_sku]))
            $items_array[$item_sku]['qty'] += $item_qty;
        else
            $items_array[$item_sku] = array(
                'name'  => $item_name,
                'qty'   => $item_qty
            );
    endforeach;
    //echo "<pre>",print_r($items_array),"</pre>";
    $item_string = "";
    foreach($items_array as $sku => $item):
        $item_string .= "<br><strong>".$item['qty']."</strong> of <strong>".$item['name']." (".$sku.")</strong";
    endforeach;
    //echo "<p>$item_string</p>";
    //Receivers Address Table
    $address_details_upper = "
        <table class='".$address_table_class."'>
            <tr>
                <td style='width: ".$address_cell_width."'>
                    <table>
                        <tr>
                            <td>Attention</td>
                            <td style='width:".$address_padding_cell_width."'></td>
                            <td>{$d['attention']}</td>
                        </tr>
                        <tr>
                            <td>Address:</td>
                            <td style='width:".$address_padding_cell_width."'></td>
                            <td>".$address_string."</td>
                        </tr>
                    </table>
                </td>
                <td style='width:".$date_cell_width."' class='right-align'>
                    <table>
                        <tr>
                            <td>Date:</td>
                            <td><strong>".date("d/m/Y")."</strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    ";
    //Delivery Table Constants
    $delivery_table_class = "delivery_details";

    //Receiver Delivery Details
    $delivery_details_upper = "
        <table class='".$delivery_table_class."'>
            <tr>
                <td class='job_no'>
                        Delivery Reference:<br>
                        ".$d['client_reference']."
                </td>
                <td class='quantity'>
                    Pallets:<br>
                    <strong>".$total_pallets."</strong>
                </td>
                <td>
                    Items:".$item_string."
                </td>
            </tr>
        </table>
    ";
    //echo $delivery_details_upper;
    ?>
    <div class="dd_body">
        <div class="top_half">
            <table>
                <tr>
                    <td  style="width: 125mm"><img style="height:18mm;width:auto;" src="https://wms.fsg.com.au/images/delivery_docket_logos/fsg_logo.png"></td>
                    <td class="right-align sender-address"><strong>FSG Priniting & 3PL Services</strong><br>865 Mountain Hwy, Bayswater VIC 3153<br><strong>T.</strong>03 9873 5144 - <strong>E.</strong>info@fsg.com.au<br><strong>www.fsg.com.au</strong></td>
                </tr>
            </table>
            <?php echo $address_details_upper;?>
            <?php //echo $delivery_details_upper;?>
            <table class="<?php echo $delivery_table_class;?>">
                <tr>
                    <td class='job_no'>
                            Delivery Reference:<br>
                            <?php echo $d['client_reference'];?>
                    </td>
                    <td class='quantity'>
                        Pallets:<br>
                        <strong><?php echo $total_pallets;?></strong>
                    </td>
                    <td>
                        Items:
                        <?php foreach($items_array as $sku => $details):?>
                            <br><strong><?php echo $details['qty'];?></strong> of <?php echo $details['name']."(<strong>".$sku."</strong>)";?>
                        <?php endforeach;?>
                    </td>
                </tr>
            </table>
        </div>
        <div id="divider">
            <span class="inst">[Detach Here]</span>
            <h2>Delivery Docket</h2>
            <h4>Sender's Copy</h4>
        </div>
        <div class="bottom_half">
            <table class="<?php echo $address_table_class;?>">
                <tr>
                    <td style="width: <?php echo $address_cell_width;?>">
                        <table>
                            <tr>
                                <td>Attention</td>
                                <td style='width:<?php $address_padding_cell_width;?>'></td>
                                <td><?php echo $d['attention'];?></td>
                            </tr>
                            <tr>
                                <td>Delivery To:</td>
                                <td style="width:<?php $address_padding_cell_width;?>"></td>
                                <td><?php echo $address_string;?></td>
                            </tr>
                        </table>
                    </td>
                    <td style='width:".$date_cell_width."' class='right-align'>
                        <table>
                            <tr>
                                <td>Date:</td>
                                <td><strong><?php echo date("d/m/Y");?></strong></td>
                            </tr>
                            <tr>
                                <td>FSG Delivery Number:</td>
                                <td><strong><?php echo $d['delivery_number'];?></strong></td>
                            </tr>
                            <?php if(!empty($d['client_reference'])):?>
                                <tr>
                                    <td>Client Delivery Reference</td>
                                    <td><strong><?php echo $d['client_reference'];?></strong></td>
                                </tr>
                            <?php endif;?>
                            <?php if(!empty($d['notes'])):?>
                                <tr>
                                    <td>Delivery Instructions</td>
                                    <td><strong><?php echo $d['notes'];?></strong></td>
                                </tr>
                            <?php endif;?>
                        </table>
                    </td>
                </tr>
            </table>
            <table id="signatures">
                <tr>
                    <td class="w50"></td>
                    <td class="right-align w50"><strong>Received in good order and conditions</strong></td>
                </tr>
                <tr>
                    <td>Delivered by:__________________</td>
                    <td class="right-align">Print name:____________________</td>
                </tr>
                <tr>
                    <td>Date: <?php echo date("d/m/Y/");?>&nbsp;&nbsp;Time:____________</td>
                    <td class="right-align">Signature:_____________________</td>
                </tr>
            </table>
        </div>
    </div>
    <?php if($count < count($delivery_ids)):?>
        <pagebreak />
    <?php endif;?>
<?php endforeach;?>
</body>

