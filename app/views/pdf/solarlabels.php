<?php
$c = 1;
foreach($orders_ids as $id):
    //$order_ids_string .= $id."-";
    $od = $this->controller->solarorder->getOrderDetail($id);
    //echo "<pre>",print_r($od),"</pre>";die();
    $type = $this->controller->solarordertype->getSolarOrderType($od['type_id']);
    $delivery_address = $this->controller->solarorder->getAddressStringForOrder($id);
    ?>
    <table width="100%" style="font-size:16px; line-height:1.5">
        <tr>
            <td colspan="2" align="center">
                <h1 style="font-size:50px"><?php echo $od['suburb'];?></h1>
            </td>
        </tr>
        <tr>
            <td>Work Order</td>
            <td><?php echo $od['work_order'];?></td>
        </tr>
    </table>
    <?php if ($c < count($orders_ids)):?>
        <pagebreak />
    <?php endif;
    ++$c;?>
<?php endforeach; ?>