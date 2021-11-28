<?php

?>
<?php include(Config::get('VIEWS_PATH')."layout/page-includes/breadcrumb.php");?>
<?php if(isset($page_title)):?>
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header" id="page_header"><?php echo $page_title;?></h2>
        </div>
    </div>
<?php endif;?>