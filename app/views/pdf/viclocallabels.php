<?php
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
    $items = $this->controller->order->getItemsForOrder($id);
    $total_items = count($items);
    $this_item = 1;
    //echo "<pre>",print_r($items),"</pre>";
    //continue;
    foreach($items as $item):
    ?>
    <div class='pickslip'>
        <h2><?php echo $ship_to;?></h2>
        <?php echo $delivery_address;?>
        <table width='100%'>
            <tr>
                <td><barcode type='ean13' code='<?php echo $od['order_number'];?>' /></td>
                <td><h3>Item <?php echo $this_item;?> of <?php echo $total_items;?></h3></td>
            </tr>
        </table>
    </div>
    <pagebreak />
    <?php
    ++$this_item;
    endforeach;
    ?>
<?php endforeach; ?>