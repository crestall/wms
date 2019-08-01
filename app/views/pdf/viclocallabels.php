<?php
$this_order = 1;
foreach($orders_ids as $id):
    //$order_ids_string .= $id."-";
    $od = $this->controller->order->getOrderDetail($id);
    //echo "<pre>",print_r($od),"</pre>";//die();
    $delivery_address = $this->controller->address->getAddressStringForOrder($id);
    if(empty($od['ship_to']))
    {
        $ship_to = $this->controller->customer->getCustomerName($od['customer_id']) ;
    }
    else
    {
        $ship_to =  $od['ship_to'];
    }
    if(!empty($od['contact_phone']))
    {
        $ship_to .= "<br/>".$od['contact_phone'];
    }
    $items = $this->controller->order->getItemsForOrderNoLocations($id);
    //echo "<pre>",print_r($items),"</pre>";
    //continue;
    $total_items = 0;
    foreach($items as $i)
    {
        $total_items += $i['qty'];
    }
    $c = 1;
    while($c <= $total_items):
    ?>
        <table width="100%" style="font-size:16px; line-height:1.5">
            <tr>
                <td colspan="2" align="center">
                    <h1 style="font-size:50px"><?php echo $od['suburb'];?></h1>
                </td>
            </tr>
            <tr>
                <td>Ship To</td>
                <td><?php echo $ship_to;?></td>
            </tr>
            <tr>
                <td>Order Number</td>
                <td>
                    <?php echo $od['order_number'];?><br/>
                    <strong>Item <?php echo $c;?> of <?php echo $total_items;?></strong>
                </td>
            </tr>
            <tr>
                <td>Address</td>
                <td><?php echo $delivery_address;?></td>
            </tr>
        </table>
        <?php if($this_order <= count($orders_ids)):?>
            <pagebreak />
        <?php endif;?>
        <?php
        ++$c;
        ++$this_order;
    endwhile;
    ?>
<?php endforeach; ?>