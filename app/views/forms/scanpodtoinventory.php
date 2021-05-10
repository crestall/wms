<?php
//echo "<p>POD INVOICE: $pod_invoice</p>";
//echo "<p>ORDER ID: $order_id</p>";
?>
<?php if(!empty($items)): ?>
    <div class="row">
        <div class="col-12">
            <?php //echo "<pre>",print_r($items),"</pre>";?>
            <table class="table-striped table-hover" id="receive_pod_items">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Client Order ID</th>
                        <th>WMS Order No</th>
                        <th>Number Required</th>
                        <th>Number Received</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($items as $i):
                        $image = "";
                        if(preg_match('/https?/i', $i['image']))
                        {
                                $image = "<br><img src='".$i['image']."' class='img-thumbnail img-fluid'>";
                        }
                        elseif(!empty($i['image']))
                        {
                                $image = "<br><img src='/images/products/tn_".$i['image']."' class='img-fluid img-thumbnail'>";
                        }
                        ?>
                        <tr id="<?php echo $i['order_item_id'];?>">
                            <td style="max-width: 175px;"><?php echo $i['name'].$image;?></td>
                            <td class="number"><?php echo $i['client_order_id'];?></td>
                            <td class="number"><?php echo $i['order_number'];?></td>
                            <td class="number required_<?php echo $i['order_item_id'];?>">
                                <input type="text" class="form-control number" name="required_<?php echo $i['order_item_id'];?>" id="required_<?php echo $i['order_item_id'];?>" value="<?php echo $i['qty'];?>" readonly>
                            </td>
                            <td class="number">
                                <input type="text" class="form-control number" name="received_<?php echo $i['order_item_id'];?>" id="received_<?php echo $i['order_item_id'];?>">
                            </td>
                            <td><button class="btn btn-sm btn-outline-fsg receive_pod_item" data-itemid="<?php echo $i['item_id'];?>" data-orderitemid="<?php echo $i['order_item_id'];?>" data-orderid="<?php echo $i['order_id'];?>">Record</button></td>
                        </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
<?php else:?>
    <div class="row">
        <div class="col-lg-12">
            <div class='errorbox'><h2><i class="far fa-times-circle"></i> Item Not Found</h2>
                <p>Possible reasons are</p>
                <ul>
                    <li>The item is not associated with POD invoice: <?php echo $pod_invoice;?></li>
                    <?php if($order_number):?>
                        <li>The item is associated with that POD invoice, but not the WMS order <?php echo $order_number;?></li>
                    <?php endif;?>
                    <li>The item is not classified as a Print On Demand item</li>
                </ul>
                <p>Please check the order id, POD invoice, and barcode and try again</p>
            </div>
        </div>
    </div>
<?php endif;?>