<?php
$this_page = Config::get('curPage');
$role = Session::getUserRole();
$bcs = array();
echo "<p>THIS PAGE: $this_page</p>";
//echo "PAGES<pre>",print_r($pages),"</pre>";
if(isset($pages) && !empty($pages) && count($pages))
{

    foreach($pages as $section => $spages)
    {
        if( (isset($pages[$section]['super_admin_only']) && $pages[$section]['super_admin_only'] == true) && (strtolower($role) != "super admin") )
            continue;
        $SectionName = ucwords(str_replace("-", " ", $section));
        $action = Utility::toCamelCase($SectionName);
        if( array_key_exists($this_page, $spages) )
        {
            $bcs[] = array(
                'icon'      => '<i class="fad fa-home"></i>',
                'p_name'    => '',
                'link'      => '/',
                'active'    => false
            );

            if($pages[$section][$section."-index"])
            {
                $Section = ucwords(str_replace("-", " ", $section));
                $bcs[] = array(
                    'icon'      => '',
                    'p_name'    => $Section,
                    'link'      => "/$section",
                    'active'    => false
                );
                ksort($pages[$section]);
                echo "PAGES<pre>",print_r($pages[$section]),"</pre>";
                foreach($pages[$section] as $pname => $details)
                {
                    if(!is_array($details) || !$details['display'])
                        continue;
                    $p_name = ucwords(str_replace("-", " ", $pname));
                    echo "<p>p_name: $p_name</p>";
                    $action = Utility::toCamelCase($p_name);
                    echo "<p>action: $action</p>";
                    $sectionname = str_replace("-", "", $section);
                    echo "<p>sectionname: $sectionname</p>";
                    if(Permission::check($role, $sectionname, $action))
                    {
                        $bcs[] = array(
                            'icon'      =>  '',
                            'p_name'    =>  $p_name,
                            'link'      =>  "/$section/$pname",
                            'active'    =>  ($pname == $this_page)
                        );
                    }
                    else
                    {
                        echo "<p>No Permission</p>"; 
                    }
                }
            }
            break;
        }
    }
    //echo "<pre>",print_r($bcs),"</pre>";
}
?>
<?php if(count($bcs)):?>
    <div class="mr-auto">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <?php foreach($bcs as $bc):
                    if($bc['active']):?>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo $bc['icon'].$bc['p_name'];?></li>
                    <?php else:?>
                        <li class="breadcrumb-item"><a href="<?php echo $bc['link'];?>"><?php echo $bc['icon'].$bc['p_name'];?></a></li>
                    <?php endif;?>
                <?php endforeach;?>
            </ol>
        </nav>
    </div>
<?php endif;?>