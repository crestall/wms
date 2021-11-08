<?php
$pickups = Form::value('pickups');
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php echo $form;?>
        <?php if(count($pickups)):?>
            <?php echo "<pre>",print_r($pickups),"</pre>";?>
        <?php else:?>
            <div class="errorbox">
                <h2>No Pickups Found</h2>
                <?php if(!empty($term)):?>
                    <p>No Pickups were found when searching against "<strong><?php echo $term;?></strong>"</p>
                <?php endif;?>
                <p>Maybe remove some filters?</p>
            </div>
        <?php endif;?>
    </div>
</div>