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
            <div class="cpl">
                <?php echo "<pre>",print_r($invoices),"</pre>";?>
            </div>
        </div>
    </div>
</div>
