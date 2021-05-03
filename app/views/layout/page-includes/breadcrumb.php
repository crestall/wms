<?php
$this_page = Config::get('curPage');
$role = Session::getUserRole();
$bcs = array();
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
                foreach($pages[$section] as $pname => $details)
                {
                    if(!is_array($details) || !$details['display'])
                        continue;
                    $p_name = ucwords(str_replace("-", " ", $pname));
                    $action = Utility::toCamelCase($p_name);
                    $sectionname = str_replace("-", "", $section);
                    if(Permission::check($role, $sectionname, $action))
                    {
                        $bcs[] = array(
                            'icon'      =>  '',
                            'p_name'    =>  $p_name,
                            'link'      =>  "/$section/$pname",
                            'active'    =>  ($pname == $this_page)
                        );
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