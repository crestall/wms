<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php"); ?>
        <div class="row">
            <div class="col-md-12">
                <h2>Current Orders</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php echo "<pre>",print_r($orders),"</pre>";?>
            </div>
        </div>
    </div>
</div>
