<?php
$db = Database::openConnection();
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php echo "<pre>",print_r($runsheets),"/<pre>";?>
    </div>
</div>