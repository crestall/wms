<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-l">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php"); ?>
        <?php echo "FINISHERS<pre>",print_r($production_finishers)."</pre>";?>
        <?php echo "CUSTOMERS<pre>",print_r($production_customers)."</pre>";?>
    </div>
</div>
