<?php
$con_index = strtolower(str_replace(" ", "-", $page_name));
$sections = $pages[$con_index];
$page_title = $sections['default-icon']['icon']." ".$page_title;
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="container">
        <div class="row">
            <?php foreach($sections as $section_name => $details):
                if(!$details['display']) continue;
                $SectionName = ucwords(str_replace("-", " ", $section_name));?>
                <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
                    <a class="index-link" href="<?php echo "$section_name";?>">
                        <div class="card text-white h-100 index-card">
                            <div class="card-body">
                                <?php echo $details['icon']." ".$SectionName;?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach;?>
        </div>

    </div>
</div>
