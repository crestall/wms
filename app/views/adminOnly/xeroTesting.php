<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php"); ?>
        <div class="row">
            <div class='col text-center'>
                <h2>Invoices</h2>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <?php foreach($invoices as $invoice):
                    $contact = $invoice->getContact();
                    $line_items = $invoice->getLineItems();
                    //echo "<pre>",print_r($line_items),"</pre>";
                    ?>
                    <p>CONTACT: <?php echo $contact->getName();?></p>
                    <?php foreach($line_items as $line_item):
                        $description = $line_item->getDescription();
                        $sku = $line_item->getItemCode();
                        $qty = $line_item->getQuantity();
                        ?>
                        <p>Line Item Name: <?php echo $description;?></p>
                        <p>Line Item SKU: <?php echo $sku;?></p>
                        <p>Line Item QTY: <?php echo $qty;?></p>
                    <?php endforeach;?>
                    <hr>
                <?php endforeach;?>
            </div>
        </div>
    </div>
</div>
