<?php
echo "<p>POD INVOICE: $pod_invoice</p>";
echo "<p>ORDER ID: $order_id</p>";
?>
<?php if(!empty($items)): ?>
    <div class="row">
        <div class="col-12">
            <?php echo "<pre>",print_r($items),"</pre>";?>
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