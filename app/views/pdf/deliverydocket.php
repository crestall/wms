<?php
echo "<pre>",print_r($dd_details),"</pre>";
$address_string = $dd_details['ship_to'];
$address_string .= "<br>".$dd_details['address'];
if(!empty($dd_details['address2'])) $address_string .= "<br>".$dd_details['address2'];
$address_string .= "<br>".$dd_details['suburb']." ".$dd_details['state']." ".$dd_details['postcode'];

?>
<div id="dd_body">
    <div id="top_half">
        <table id="page_head">
            <tr>
                <td  style="width: 125mm"><img style="height:18mm;width:auto;" src="https://wms.fsg.com.au/images/delivery_docket_logos/<?php echo $sender_details['logo'];?>"></td>
                <td class="right-align sender-address"><?php echo $sender_details['address'];?></td>
            </tr>
        </table>
        <table id="address_details">
            <tr>
                <td style="width: 125mm">
                    <table>
                        <tr>
                            <td>Delivery To:</td>
                            <td style="width:5mm"></td>
                            <td><?php echo $address_string;?></td>
                        </tr>
                        <tr>
                            <td>Att:</td>
                            <td style="width:5mm"></td>
                            <td><?php echo $dd_details['attention'];?></td>
                        </tr>
                    </table>
                </td>
                <td class="right-align">Date: <strong><?php echo date("d/m/Y");?></strong></td>
            </tr>
        </table>
    </div>
    <div id="divider">
        <p>-------------------------------------------------------------------------------------------------------------------------------<br>
        <span class="inst">[Detach Here]</span</p>
    </div>
    <div id="bottom_half">

    </div>
</div>