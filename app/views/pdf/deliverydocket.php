<?php
echo "<pre>",print_r($dd_details),"</pre>";
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
?>
<div id="dd_body">
    <div id="top_half">
        <table id="page_head">
            <tr>
                <td  style="width: 125mm"><img style="height:18mm;width:auto;" src="https://wms.fsg.com.au/images/delivery_docket_logos/<?php echo $sender_details['logo'];?>"></td>
                <td class="right-align sender-address"><?php echo $sender_details['address'];?></td>
            </tr>
        </table>
        <table class="address_details">
            <tr>
                <td style="width: 125mm">
                    <table>
                        <tr>
                            <td>Delivery To:</td>
                            <td style="width:5mm"></td>
                            <td><?php echo $address_string;?></td>
                        </tr>
                        <?php echo $attention;?>
                        <?php echo $job_no;?>
                    </table>
                </td>
                <td class="right-align">Date: <strong><?php echo date("d/m/Y");?></strong></td>
            </tr>
        </table>
    </div>
    <div id="divider">
        <p>-------------------------------------------------------------------------------------------------------------------------------<br>
        <span class="inst">[Detach Here]</span></p>
        <h2>Delivery Docket</h2>
        <h4>Sender's Copy</h4>
    </div>
    <div id="bottom_half">
        <table class="address_details">
            <tr>
                <td style="width: 125mm">
                    <table>
                        <tr>
                            <td>Delivery To:</td>
                            <td style="width:5mm"></td>
                            <td><?php echo $address_string;?></td>
                        </tr>
                        <?php echo $attention;?>
                        <?php echo $job_no;?>
                    </table>
                </td>
                <td class="right-align">Date: <strong><?php echo date("d/m/Y");?></strong></td>
            </tr>
        </table>
    </div>
</div>