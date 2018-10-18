<?php
$count = 1;
$html = "<div class='body'>";
foreach($locations as $row)
{
    $html .= "<div class='holder";
    if($count % 2 == 0)
    {
        $html .= " left";
    }
    $html .= "'><p><barcode code='{$row['location']}' type='C39+' size='1' height='1' class='barcode' /></p>";

    $html .= "<p class='text'>".$row['location']."</p></div>";
    if($count % 16 == 0 && $count <  count($locations))
    {
        $html .= "</div><pagebreak /><div class='body'>";
    }
    ++$count;

}
$html .= "</div>";

echo $html;

//10.07.E.BFdie();
?>