<?php
//echo "dl_details<pre>",print_r($dl_details),"</pre>";
//echo "sender_details<pre>",print_r($sender_details),"</pre>";
$address_string = $dl_details['ship_to'];
if(!empty($dl_details['attention'])) $address_string .= "<br>".$dl_details['attention'];
$address_string .= "<br>".$dl_details['address'];
if(!empty($dl_details['address2'])) $address_string .= "<br>".$dl_details['address2'];
$address_string .= "<br>".$dl_details['suburb']." ".$dl_details['state']." ".$dl_details['postcode'];
$bc = (!empty($dl_details['box_count']))? $dl_details['box_count'] : 1;
$pc = (!empty($dl_details['pallet_count']))? $dl_details['pallet_count'] : 0;
$box_label = "Box";
if(isset($dl_details['order_number']))
{
    $bc = (!empty($dl_details['box_count']))? $dl_details['box_count'] : 0;
    $pc = (!empty($dl_details['pallet_count']))? $dl_details['pallet_count'] : 0;
    $box_label = "Box/Pallet";
}
$bc += $pc;
$tb = 1;
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;700&display=swap" rel="stylesheet">
<body>
    <?php while ($tb <= $bc):?>
        <div class="dl_body">
            <div class="cp_label">
                <div class="header">
                    <?php echo $dl_details['job_title'];?>
                </div>
            </div>
        </div>
        <?php if($tb < $bc):?>
            <pagebreak />
        <?php endif; ++$tb;
    endwhile;?>
</body>