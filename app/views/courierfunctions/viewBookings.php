<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <?php echo "<pre>",print_r($bookings),"</pre>";?>
        </div>
    </div>
</div>