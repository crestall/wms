<?php
//echo "<pre>",print_r($pages),"</pre>";
$this_page = Config::get('curPage');
echo "this page is $this_page";
$bcs = array();
if(count($pages))
{

    foreach($pages as $section => $spages)
    {
        if( (isset($pages[$section]['super_admin_only']) && $pages[$section]['super_admin_only'] == true) )
            continue;
        if(in_array($this_page, $spages))
        {
            $bcs[] = array(
                'icon'      => '<i class="fad fa-home"></i>',
                'p_name'    => '',
                'link'      => '/',
                'active'    => false
            );
            //if($spage == $)
            //echo "<p>Will do breadcrumbs for $section</p>";
            $Section = ucwords(str_replace("-", " ", $section));
            $bcs[] = array(
                'icon'      => '',
                'p_name'    => $Section,
                'link'      => "/$section",
                'active'    => false
            );
            foreach($pages[$section] as $pname => $details)
            {
                if(!is_array($details) || !$details['display'])
                    continue;
                //echo "<pre>$pname",print_r($details),"</pre>";
                $p_name = ucwords(str_replace("-", " ", $pname));
                $bcs[] = array(
                    'icon'      =>  '',
                    'p_name'    =>  $p_name,
                    'link'      =>  "/$section/$pname",
                    'active'    =>  ($pname == $this_page)
                );
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