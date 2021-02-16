<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php if(empty($customer['id'])):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/no_order_found.php");?>
        <?php else:?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
            <?php echo "<pre>",print_r($customer),"</pre>";?>
        <?php endif;?>
    </div>
</div>