<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if($runsheet_id == 0):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/no_runsheet_id.php");?>
        <?php elseif(empty($runsheet)):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/no_runsheet_found.php");?>
        <?php else:?>
            <p><button class="btn runsheet">Print</button></p>
            <?php echo "<pre>",print_r($runsheet),"</pre>";?>
        <?php endif;?>
    </div>
</div>