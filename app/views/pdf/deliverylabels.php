<?php
//echo "<pre>",print_r($dl_details),"</pre>";
$address_string = $dl_details['ship_to'];
if(!empty($dl_details['attention'])) $address_string .= "<br>".$dl_details['attention'];
$address_string .= "<br>".$dl_details['address'];
if(!empty($dl_details['address2'])) $address_string .= "<br>".$dl_details['address2'];
$address_string .= "<br>".$dl_details['suburb']." ".$dl_details['state']." ".$dl_details['postcode'];
$bc = (!empty($dl_details['box_count']))? $dl_details['box_count'] : 1;
$job_number = $dl_details['job_number'];
$job_number_label = "Job Number:";
if($sender_details['send_job_no'] != 1)
{
    $job_number = $dl_details['po_number'];
    $job_number_label = "Order Number:";
}
$lb = 0;
if(!empty($dl_details['per_box']))
{
    $lb = $dl_details['quantity'] - ( ($bc - 1) * $dl_details['per_box'] );
}
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
                <td class="w30">Reference:</td>
                <td class="spacer">&nbsp;</td>
                <td><?php echo $dl_details['job_title'];?></td>
            </tr>
            <tr>
                <td class="w30"><?php echo $job_number_label;?></td>
                <td class="spacer">&nbsp;</td>
                <td><?php echo $job_number;?></td>
            </tr>
            <tr>
                <td class="w30"></td>
                <td class="spacer">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
        <table class="box_details">
            <tr>
                <td class="right-align">Box <strong><?php echo $tb;?></strong> of <strong><?php echo $bc;?></strong></td>
            </tr>
            <?php if(!empty($dl_details['per_box'])):?>
                <tr>
                    <td class="right-align"><strong><?php if($tb < $bc) echo $dl_details['per_box']; else echo $lb;?></strong> items in box</td>
                </tr>
            <?php endif;?>
        </table>
    </div>
    <?php if($tb < $bc):?>
        <pagebreak />
    <?php endif; ++$tb;
    endwhile;?>
</body