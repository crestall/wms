<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php"); ?>
        <div class="row">
            <div class="col">
                <?php echo "<pre>",print_r($products),"</pre>";?>
            </div>
        </div>
    </div>
</div>
