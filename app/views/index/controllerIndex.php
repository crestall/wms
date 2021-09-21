<?php
$con_index = strtolower(str_replace(" ", "-", $page_name));
$sections = $pages[$con_index];
ksort($sections);
$page_title = $sections['default-icon']['icon']." ".$page_title;
$role = Session::getUserRole();
$resource = strtolower(str_replace(" ", "", $page_name));
echo "<p>Current Resource: $resource</p>";
echo "<pre>",print_r(Permission::$perms),"</pre>";
?>
<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div id="page_container" class="container-xl"> 
        <div class="card-deck homepagedeck">
            <?php foreach($sections as $section_name => $details):
                if(!$details['display']) continue;
                $SectionName = ucwords(str_replace("-", " ", $section_name));
                $action = Utility::toCamelCase($SectionName);
                if(Permission::check($role, $resource, $action)):?>
                    <div class="card homepagecard">
                        <div class="card-header">
                            <h4><?php echo $SectionName;?></h4>
                        </div>
                        <div class="card-body text-center">
                        	<a class="btn btn-lg btn-outline-fsg" href="/<?php echo $con_index."/".$section_name;?>"><?php echo $details['icon'];?></a>
                        </div>
                    </div>
                <?php endif;?>
            <?php endforeach;?>
        </div>
    </div>
</div>
