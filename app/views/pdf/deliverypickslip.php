<?php
$count = 0;
foreach($delivery_ids as $id):
    ++$count;
    $d = $this->controller->delivery->getDeliveryDetails($id);
    //echo "<pre>",print_r($d),"</pre>";//die();
    $address_string = "";
    if(!empty($d['address'])) $address_string .= $d['address'];
    if(!empty($d['address_2'])) $address_string .= "<br/>".$d['address_2'];
    if(!empty($d['suburb'])) $address_string .= "<br/>".$d['suburb'];
    if(!empty($d['state'])) $address_string .= "<br/>".$d['state'];
    if(!empty($d['country'])) $address_string .= "<br/>".$d['country'];
    if(!empty($d['postcode'])) $address_string .= "<br/>".$d['postcode'];
    //$this->controller->delivery->markDeliveryPicked($id)
    ?>
    <div class="pickslip">
        <h2>FSG Delivery Picking Slip</h2>
        <table width="100%">
            <tr>
                <td class="right">Printed</td>
                <td><?php echo date("h:i a d/m/Y");?></td>
            </tr>
            <tr>
                <td class="right">Date/Time Requested</td>
                <td><?php echo date('D d/m/Y - g:i A', $d['date_entered']);?></td>
            </tr>
            <tr>
                <td class="right">Delivery Window</td>
                <td><?php echo ucwords($d['delivery_window']);?></td>
            </tr>
            <tr>
                <td class="right">Deliver To</td>
                <td>
                    <p style="font-weight:bold"><?php echo $d['attention'];?></p>
                    <p><?php echo $address_string;?></p>
                </td>
            </tr>
        </table>
    </div>
    <?php if($count < count($delivery_ids)):?>
        <pagebreak />
    <?php endif;?>
<?php endforeach; ?>