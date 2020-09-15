<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php echo "JOB<pre>",print_r($job),"</pre>";?>
        <?php echo "CUSTOMER<pre>",print_r($customer),"</pre>";?>
        <?php echo "SUPPLIER<pre>",print_r($supplier),"</pre>";?>
    </div>
</div>