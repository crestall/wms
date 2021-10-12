<?php
$pickup_id = $pickup['id'];
$client_id = $pickup['client_id'];
?>
<input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id;?>" />
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    </div>
    <?php echo "<pre>",print_r($pickup),"</pre>";?>
</div>