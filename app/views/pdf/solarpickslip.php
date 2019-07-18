<?php
$c = 1;
foreach($orders_ids as $id):
    //$order_ids_string .= $id."-";
    $od = $this->controller->solarorder->getOrderDetail($id);
    //echo "<pre>",print_r($od),"</pre>";die();
    $type = $this->controller->solarordertype->getSolarOrderType($od['type_id']);
    $delivery_address = $this->controller->solarorder->getAddressStringForOrder($id);
    //$items = array();
    $items = $this->controller->solarorder->getItemsForOrder($id);
    $this->controller->solarorder->setSlipPrinted($id);
    $picked_id = $this->controller->solarorder->picked_id;
    $ordered_id = $this->controller->solarorder->ordered_id;
    $team = $this->controller->solarteam->getTeamName($od['team_id']);
    if($od['status_id'] == $ordered_id)
        $this->controller->solarorder->updateStatus($picked_id, $id);
    ?>
    <div class='pickslip'>
        <table width='100%'>
            <tr>
                <td><h2>3PL Solar Packing Slip</h2></td>
            </tr>
            <tr>
                <td><h4>Install Date : <?php echo date("d/m/Y", $od['install_date']);?></h4></td>
            </tr>
        </table>
        <table width='100%'>
            <tr><td><h4>Order Type</h4></td><td><h4><?php echo $type;?></h4></td></tr>
            <tr><td><h4>Work Order:</h4></td><td><h4><?php echo $od['work_order'];?></h4></td></tr>
            <tr><td><h4>Team:</h4></td><td><h4><?php echo $team;?></h4></td></tr>
            <tr><td>Address</td><td><?php echo $delivery_address;?></td></tr>
        </table>
        <table class='pickslip'>
        <tr>
            <th>Item</th>
            <th>SKU</th>
            <th>Location</th>
            <th>Qty</th>
            <th>Picked</th>
            <th>Checked</th>
            <th colspan="2" style="width:60px">Used</th>
        </tr>
            <?php foreach($items as $i):
                $location = $this->controller->location->getLocationName($i['location_id']);
                ?>
                <tr>
                   	<td><?php echo $i['name'];?></td>
                    <td><?php echo $i['sku'];?></td>
                    <td><?php echo $location;?></td>
                    <td class='number bold'><?php echo $i['qty'];?></td>
                    <td class='centre'></td>
                    <td class='centre'></td>
                    <td class='centre' colspan="2"></td>
                </tr>
            <?php endforeach;?>
            <tr>
                <td colspan="2" style="text-align:right; font-weight:bold;height:50px">Time In</td>
                <td class='centre' colspan="2"></td>
                <td colspan="2" style="text-align:right; font-weight:bold;height:50px">Time Out</td>
                <td class='centre' colspan="2"></td>
            </tr>
            <tr>
                <td colspan="6" style="text-align:right; font-weight:bold;height:50px">Signed</td>
                <td class='centre' colspan="2"></td>
            </tr>
        </table>
    </div>
    <?php if ($c < count($orders_ids)):?>
        <pagebreak />
    <?php endif;
    ++$c;?>
<?php endforeach; ?>