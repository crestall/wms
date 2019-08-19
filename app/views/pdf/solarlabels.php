<?php
$c = 1;
foreach($orders_ids as $id):
    //$order_ids_string .= $id."-";
    $od = $this->controller->solarorder->getOrderDetail($id);
    //echo "<pre>",print_r($od),"</pre>";die();
    $type = $this->controller->solarordertype->getSolarOrderType($od['type_id']);
    $delivery_address = $this->controller->solarorder->getAddressStringForOrder($id);
    ?>
    <table width="100%" style="font-size:24px; line-height:1.5">
        <tr>
            <td colspan="2" align="center">
                <h1 style="font-size:100px"><?php echo $type;?></h1>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <h1 style="font-size:60px"><?php echo $od['suburb'];?></h1>
            </td>
        </tr>
        <tr>
            <td>Work Order</td>
            <td><strong><?php echo $od['work_order'];?></strong></td>
        </tr>
        <tr>
            <td>Team</td>
            <td><strong><?php echo $this->controller->solarteam->getTeamName($od['team_id']);?></strong></td>
        </tr>
        <tr>
            <td>Install Date</td>
            <td><strong><?php echo date("d/m/Y",$od['install_date']);?></strong></td>
        </tr>
        <tr>
            <td>Address</td>
            <td><?php echo $delivery_address;?></td>
        </tr>
    </table>
    <?php if ($c < count($orders_ids)):?>
        <pagebreak />
    <?php endif;
    ++$c;?>
<?php endforeach; ?>