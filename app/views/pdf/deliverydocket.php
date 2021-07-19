<?php
echo "<pre>",print_r($dd_details),"</pre>";

//The setup

//Send to address string
$address_string = $dd_details['ship_to'];
$address_string .= "<br>".$dd_details['address'];
if(!empty($dd_details['address2'])) $address_string .= "<br>".$dd_details['address2'];
$address_string .= "<br>".$dd_details['suburb']." ".$dd_details['state']." ".$dd_details['postcode'];
$attention = (!empty($dd_details['attention']))?
    "<tr>
        <td>Attention</td>
        <td style='width:5mm'></td>
        <td>{$dd_details['attention']}</td>
    </tr>":
    "";

//Instructions for Driver
$inst = ((!empty($dd_details['delivery_instructions'])))?
    "<tr>
        <td>Delivery Instructions:</td>
        <td><strong>".$dd_details['delivery_instructions']." </strong></td>
    </tr>":
    "";

//The Job Number
$job_no = (!isset($dd_details['order_number']))?
    "<tr>
        <td>Job No.</td>
        <td style='width:5mm'></td>
        <td>{$dd_details['job_number']}</td>
    </tr>":
    "<tr>
        <td>WMS Order No.</td>
        <td style='width:5mm'></td>
        <td>{$dd_details['order_number']}</td>
    </tr>";

//The Purchase Order Number
$po_no =
    "<tr>
        <td>Order No.</td>
        <td style='width:5mm'></td>
        <td>{$dd_details['po_number']}</td>
    </tr>";

//Address Table Constants
$address_table_class = "address_details";
$address_padding_cell_width = "5mm";
$address_cell_width = "105mm";
$date_cell_width = "65mm";

//Receivers Address Table
$address_details_upper = "
    <table class='".$address_table_class."'>
        <tr>
            <td style='width: ".$address_cell_width."'>
                <table>
                    <tr>
                        <td>Delivery To:</td>
                        <td style='width:".$address_padding_cell_width."'></td>
                        <td>".$address_string."</td>
                    </tr>
                    ".$attention;
if($sender_details['send_job_no'] == 1)
{
    $address_details_upper .= $job_no;
    if(!empty($dd_details['po_number']))
        $address_details_upper .= $po_no;
}
$address_details_upper .= "
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
//Drivers Address Table
$address_details_lower = "
    <table class='".$address_table_class."'>
        <tr>
            <td style='width: ".$address_cell_width."'>
                <table>
                    <tr>
                        <td>Delivery To:</td>
                        <td style='width:".$address_padding_cell_width."'></td>
                        <td>".$address_string."</td>
                    </tr>
                    $attention
                    $job_no
";
if(!empty($dd_details['po_number']))
        $address_details_lower .= $po_no;
$address_details_lower .= "
                </table>
            </td>
            <td style='width:".$date_cell_width."' class='right-align'>
                <table>
                    <tr>
                        <td>Date:</td>
                        <td><strong>".date("d/m/Y")."</strong></td>
                    </tr>
                    $inst
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
        <tr>";
if($sender_details['send_job_no'] == 1)
{
    if(isset($dd_details['order_number']))
    {
        $delivery_details_upper .= "
            <td class='job_no'>
                WMS Order Number:<br>
                ".$dd_details['order_number']."</td>"
        ;
    }
    else
    {
        $delivery_details_upper .= "
          <td class='job_no'>
            Job Number:<br>
            ".$dd_details['job_number']."</td>"
        ;
    }
}
else
{
    $delivery_details_upper .= "
        <td class='job_no'>
            Order Number:<br>
            ".$dd_details['po_number']."
        </td>
    ";
}
$delivery_details_upper .= "
    <td class='quantity'>
        Quantity:<br>
        <strong>".$dd_details['quantity']."</strong><br>
";
if(!empty($dd_details['box_count']))
    $delivery_details_upper .= " In <strong>".$dd_details['box_count']."</strong> boxes ";
if(!empty($dd_details['box_count']) && !empty($dd_details['pallet_count']))
    $delivery_details_upper .= "and";
if(!empty($dd_details['pallet_count']))
    $delivery_details_upper .= " In <strong>".$dd_details['pallet_count']."</strong> pallets ";
if(!empty($dd_details['packed_as']))
    $delivery_details_upper .= "<br>Packed As <strong>".$dd_details['packed_as']."</strong>";
$delivery_details_upper .= "
    </td>
    <td class='job_title'>
        Job Title:<br>
        <strong>".$dd_details['job_title']."</strong>
    </td>
";
$delivery_details_upper .= "
        </tr>
    </table>
";
//Drivers Delivery Details
$delivery_details_lower = "
        <table class='".$delivery_table_class."'>
                <tr>";
                if(isset($dd_details['order_number'])):
                    $delivery_details_lower .= "<td class='job_no'>
                        WMS Order Number:<br>
                        ".$dd_details['order_number']."
                    </td>";
                else:
                    $delivery_details_lower .= "<td class='job_no'>
                        Job Number:<br>
                        ".$dd_details['job_number']."
                    </td>";
                endif;
                    $delivery_details_lower .= "<td class='quantity'>
                        Quantity:<br>
                        <strong>".$dd_details['quantity']."</strong><br>In
";
if(!empty($dd_details['box_count']))
    $delivery_details_lower .= " In <strong>".$dd_details['box_count']."</strong> boxes ";
if(!empty($dd_details['box_count']) && !empty($dd_details['pallet_count']))
    $delivery_details_lower .= "and";
if(!empty($dd_details['pallet_count']))
    $delivery_details_lower .= " In <strong>".$dd_details['pallet_count']."</strong> pallets ";
if(!empty($dd_details['packed_as']))
        $delivery_details_lower .= "<br>Packed As <strong>".$dd_details['packed_as']."</strong>";
$delivery_details_lower .= "
        </td>
        <td class='job_title'>
                Job Title:<br>
                <strong>".$dd_details['job_title']."</strong>
        </td>
    </tr>
</table>
";
?>
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
<body>
    <div id="dd_body">
        <div id="top_half">
            <table id="page_head">
                <tr>
                    <td  style="width: 125mm"><img style="height:18mm;width:auto;" src="https://wms.fsg.com.au/images/delivery_docket_logos/<?php echo $sender_details['logo'];?>"></td>
                    <td class="right-align sender-address"><?php echo $sender_details['address'];?></td>
                </tr>
            </table>
            <?php echo $address_details_upper;?>
            <?php echo $delivery_details_upper;?>
        </div>
        <div id="divider">
            <span class="inst">[Detach Here]</span>
            <h2>Delivery Docket</h2>
            <h4>Sender's Copy</h4>
        </div>
        <div id="bottom_half">
            <?php echo $address_details_lower;?>
            <?php echo $delivery_details_lower;?>
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
</body>
