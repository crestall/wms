<?php
$address_string = $dd_details['ship_to'];
if(!empty($dd_details['attention'])) $address_string .= "<br>".$dd_details['attention'];
$address_string .= "<br>".$dd_details['address'];
if(!empty($dd_details['address2'])) $address_string .= "<br>".$dd_details['address2'];
$address_string .= "<br>".$dd_details['suburb']." ".$dd_details['state']." ".$dd_details['postcode'];
$bc = (!empty($dl_details['box_count']))? $dl_details['box_count'] : 1;
$tb = 1;
?>
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
<body>
    <?php while ($tb <= $bc):?>
    <div class="dl_body">
        <table class="address_details">
            <tr>
                <td><strong>T0:</strong></td>
                <td class="spacer">&nbsp;</td>
                <td><?php echo $address_string;?></td>
            </tr>
        </table>
        <table class="label_details">
            <tr>
                Box <?php echo $tb;?> of <?php echo $bc;?>
            </tr>
        </table>
    </div>
    <pagebreak />
    <?php ++$tb;
    endwhile;?>
</body