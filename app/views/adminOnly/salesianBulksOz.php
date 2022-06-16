<?php
//echo "<pre>",print_r($_SESSION),"</pre>";
//echo "<p>DOC_ROOT : ".DOC_ROOT."</p>";

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php"); ?>
        <div class="row">
            <div class="col-md-12">
                <h2>Importing Shipments</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php echo "<pre>",print_r($sments),"</pre>";?>
            </div>
        </div>
    </div>
</div>