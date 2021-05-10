<?php
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
                    <li>The item is not associated with that POD invoice</li>
                    <li>The item is associated with that POD invoice, but not the order</li>
                    <li>The item is not classified as a Print On Demand item</li>
                </ul>
                <p>Please check the order id, POD invoice, and barcode and try again</p>
            </div>
        </div>
    </div>
<?php endif;?>