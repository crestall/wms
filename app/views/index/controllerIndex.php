<?php
$sections = $pages[strtolower($page_name)];
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="container">
        <div class="row">
            <?php foreach($sections as $section_name => $details):
                if(!$details['display']) continue; ?>
                <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
                    <p><?php echo $details['icon']." ".$section_name;?></p>    
                </div>
            <?php endforeach;?>
        </div>

    </div>
</div>


<?php echo "<pre>",print_r($sections),"</pre>";?>
