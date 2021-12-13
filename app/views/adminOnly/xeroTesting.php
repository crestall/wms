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
                    var_dump($line_items);
                ?>
                    <p>CONTACT: <?php echo $contact->getName();?></p>
                <?php endforeach;?>
            </div>
        </div>
    </div>
</div>
