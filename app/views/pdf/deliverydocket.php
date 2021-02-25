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

$address_details = "
    <table class='address_details'>
            <tr>
                <td style='width: 125mm'>
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
                <td class='right-align'>Date: <strong>".date("d/m/Y")."</strong></td>
            </tr>
        </table>
";

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
        Quantity<br>
        <strong>".$dd_details['quantity']."</strong>
";
if(!empty($dd_details['box_count']))
    $delivery_details .= "<br>In <strong>".$dd_details['box_count']."</strong> boxes";
if(!empty($dd_details['packed_as']))
    $delivery_details .= "<br>Packed As <strong>".$dd_details['packed_as']."</strong>";
$delivery_details .= "
    </td>
    <td class='job_title'>
        Job Title<br>
        <strong>".$dd_details['job_title']."</strong>
    </td>
";
$delivery_details .= "
        </tr>
    </table>
";
?>
<div id="dd_body">
    <div id="top_half">
        <table id="page_head">
            <tr>
                <td  style="width: 125mm"><img style="height:18mm;width:auto;" src="https://wms.fsg.com.au/images/delivery_docket_logos/<?php echo $sender_details['logo'];?>"></td>
                <td class="right-align sender-address"><?php echo $sender_details['address'];?></td>
            </tr>
        </table>
        <?php echo $address_details;?>
        <?php echo $delivery_details;?>
    </div>
    <div id="divider">
        <p>-------------------------------------------------------------------------------------------------------------------------------<br>
        <span class="inst">[Detach Here]</span></p>
        <h2>Delivery Docket</h2>
        <h4>Sender's Copy</h4>
    </div>
    <div id="bottom_half">
        <?php echo $address_details;?>
        <?php echo $delivery_details;?>
        <table id="signatures">
            <tr>
                <td sclass="w50"></td>
                <td class="right-align w50"><strong>Received in good order and conditions</strong></td>
            </tr>
        </table>
    </div>
</div>