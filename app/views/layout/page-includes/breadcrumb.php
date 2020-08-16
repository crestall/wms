<?php
echo "<pre>",print_r($pages),"</pre>";
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
                'icon'  => $spages['default-icon']['icon'],
                'p_name'    =>  $section,
                'link'      =>
            );
            break;
        }
    }
}
?>
<div class="mr-auto">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a class="black-text" href="/">Home</a></li>
            <li class="breadcrumb-item"><a class="black-text" href="/orders">Orders</a></li>
            <li class="breadcrumb-item"><a class="black-text" href="#">No arrows for these</a></li>
            <li class="breadcrumb-item"><a class="black-text" href="#">No arrows for these</a></li>
            <li class="breadcrumb-item"><a class="black-text" href="#">No arrows for these</a></li>
            <li class="breadcrumb-item"><a class="black-text" href="#">No arrows for these</a></li>
            <li class="breadcrumb-item">and so on...</li>
        </ol>
    </nav>
</div>