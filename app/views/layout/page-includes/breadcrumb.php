<?php
//echo "<pre>",print_r($pages),"</pre>";
$this_page = Config::get('curPage');
echo "this page is $this_page";
if(count($pages))
{
    $bcs = array(
        array(
            'icon'      => '<i class="fad fa-home"></i>',
            'p_name'    => '',
            'link'      => '/'
        )
    );
    foreach($pages as $section => $spages)
    {
        if( (isset($pages[$section]['super_admin_only']) && $pages[$section]['super_admin_only'] == true) )
            continue;
        if(in_array($this_page, $spages))
        {
            //if($spage == $)
            echo "<p>Will do breadcrumbs for $section</p>";
            $Section = ucwords(str_replace("-", " ", $section));
            $bcs[] = array(
                'icon'      => '',
                'p_name'    => $Section,
                'link'      => "/$section"
            );
            foreach($pages[$section] as $pname => $details)
            {
                if(!is_array($details) || !$details['display'])
                    continue;
                $p_name = ucwords(str_replace("-", " ", $pname));
                $bcs[] = array(
                    'icon'      =>  '',
                    'p_name'    =>  $p_name,
                    'link'      =>  "/$section/$pname"
                );
            }
            break;
        }
    }
    echo "<pre>",print_r($bcs),"</pre>";
}
?>
<div class="mr-auto">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <?php foreach($bcs as $bc):?>
                <li class="breadcrumb-item"><a href="<?php echo $bc['link'];?>"><?php echo $bc['icon'].$bc['p_name'];?></a></li></li>
            <?php endforeach;?>
        </ol>
    </nav>
</div>