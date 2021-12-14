<?php
//echo "<pre>",print_r( $invoices ),"</pre>"; die();
//$invoiceId = $invoices->getInvoices()[0]->getInvoiceId();
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
                    echo "<pre>",print_r($contact->getAddresses()),"</pre>";
                    ?>
                    <p>Invoice ID: <?php echo $invoice->getInvoiceId();?></p>
                    <p>CONTACT: <?php echo $contact->getName();?></p>
                    <?php foreach($line_items as $line_item):
                        $description = $line_item->getDescription();
                        $sku = $line_item->getItemCode();
                        $qty = (int)$line_item->getQuantity();
                        if(strpos(strtolower($description), 'freight') !== false)
                            continue;
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
