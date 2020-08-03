<?php
$con_index = strtolower(str_replace(" ", "-", $page_name));
$sections = $pages[$con_index];
$page_title = $sections['default-icon']['icon']." ".$page_title;
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="container-fluid">
        <div class="card-deck homepagedeck">
            <?php foreach($sections as $section_name => $details):
                if(!$details['display']) continue;
                $SectionName = ucwords(str_replace("-", " ", $section_name));?>
                <div class="card homepagecard">
                    <div class="card-header">
                        <h4><?php echo $SectionName;?></h4>
                    </div>
                    <div class="card-body text-center">
                    	<a class="btn btn-lg btn-outline-fsg" href="<?php echo "$section_name";?>"><?php echo $details['icon'];?></a>
                    </div>
                </div>
            <?php endforeach;?>
        </div>
    </div>
</div>
