<?php
$sections = $pages[strtolower($page_name)];
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
</div>


<?php echo "<pre>",print_r($sections),"</pre>";?>
