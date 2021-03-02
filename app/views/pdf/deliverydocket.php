<?php
//echo "<pre>",print_r($dd_details),"</pre>";
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
$job_no = ($sender_details['send_job_no'] == 1)?
    "<tr>
        <td>Job No.</td>
        <td style='width:5mm'></td>
        <td>{$job_details['job_id']}</td>
    </tr>":
    "";
$inst = ((!empty($dd_details['delivery_instructions'])))?
    "<tr>
        <td>Instructions For Driver:</td>
        <td><strong>".$dd_details['delivery_instructions']." </strong></td>
    </tr>":
    "";
$address_details = "
    <table class='address_details'>
            <tr>
                <td style='width: 105mm'>
                    <table>
                        <tr>
                            <td>Delivery To:</td>
                            <td style='width:5mm'></td>
                            <td>".$address_string."</td>
                        </tr>
                        $attention
                        $job_no
                    </table>
                </td>
                <td class='right-align'>
                    <table>
                        <tr>
                            <td>Date:</td>
                            <td><strong>".date("d/m/Y")."</strong></td>
                        </tr>";
$address_details_lower = $address_details.$inst;
$address_details_upper = $address_details;
$more_address_details = "</table>
                </td>
            </tr>
        </table>
";
$address_details_upper .= $more_address_details;
$address_details_lower .= $more_address_details;

$delivery_details = "
    <table class='delivery_details'>
        <tr>";
if($sender_details['send_job_no'] == 1)
{
    $delivery_details .= "
      <td class='job_no'>
        Job Number:<br>
        ".$job_details['job_id']."
      </td>
    ";
}
else
{
    $delivery_details .= "
        <td class='job_no'>
            Order Number:<br>
            ".$dd_details['po_number']."
        </td>
    ";
}
$delivery_details .= "
    <td class='quantity'>
        Quantity:<br>
        <strong>".$dd_details['quantity']."</strong>
";
if(!empty($dd_details['box_count']))
    $delivery_details .= "<br>In <strong>".$dd_details['box_count']."</strong> boxes";
if(!empty($dd_details['packed_as']))
    $delivery_details .= "<br>Packed As <strong>".$dd_details['packed_as']."</strong>";
$delivery_details .= "
    </td>
    <td class='job_title'>
        Job Title:<br>
        <strong>".$dd_details['job_title']."</strong>
    </td>
";
$delivery_details .= "
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
            <?php echo $delivery_details;?>
        </div>
        <div id="divider">
            <span class="inst">[Detach Here]</span>
            <h2>Delivery Docket</h2>
            <h4>Sender's Copy</h4>
        </div>
        <div id="bottom_half">
            <?php echo $address_details_lower;?>
            <?php echo $delivery_details;?>
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
