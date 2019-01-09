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
        <table width="100%">
            <tr>
                <td>
                    <p>Order Number: <?php echo $od['order_number'];?></p>
                    <p></p>
                    <p>Item <?php echo $this_item;?> of <?php echo $total_items;?></p>
                </td>
                <td>
                    <h1 style="font-size:40px"><?php echo $od['suburb'];?></h1>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <p><?php echo $ship_to;?></p>
                    <?php echo $delivery_address;?>
                </td>
            </tr>
        </table>
        </div>
        <pagebreak />
        <?php
        ++$this_item;
    endforeach;
    ?>
<?php endforeach; ?>