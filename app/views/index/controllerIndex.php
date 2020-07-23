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
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $details['icon']." ".$section_name;?></h5>
                        </div>
                    </div>
                </div>
            <?php endforeach;?>
        </div>

    </div>
</div>


<?php echo "<pre>",print_r($sections),"</pre>";?>
