<?php
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
include("$root/php-includes/session.php");
include_once("$root/php-includes/my-mpdf.php");
$count = 1;
//$html = "<table border='1' cellspacing='10'><tr>";
//$locations = $db->queryData("SELECT * FROM locations WHERE SUBSTRING_INDEX(location, '.', 1) = '9'");
//$locations = $db->queryData("SELECT * FROM locations WHERE location = 'receiving'");
$locations = $db->queryData("SELECT * FROM locations");
$html = "<div class='body'>";
foreach($locations as $row)
{
    $html .= "<div class='holder";
	if($count % 2 == 0)
	{
		$html .= " left";
	}
	$html .= "'><p><barcode code='{$row['location']}' type='C39+' size='1.5' height='1.1' class='barcode' /></p>";
    $html .= "<p class='text'>".$row['location']."</p></div>";
	/*$html .= "<td><p><barcode code='{$row['location']}' type='C39+' size='5' height='2' class='barcode' /></p>";
    $html .= "<p class='text'>".$row['location']."</p></td>";*/
	if($count % 16 == 0 && $count <  count($locations))
	{
		$html .= "</div><pagebreak /><div class='body'>";
	}
	++$count;
	
}
//4955699260050
//echo $html; die();

//$html .= "</tr></table>";
$html .= "</div>";
$pdf = new myMPDF('', 'A4', '', 5, 5, 12, 12);
$stylesheet = file_get_contents($root."/styles/barcodes.css");
$pdf->WriteHTML($stylesheet,1);
$pdf->WriteHTML($html);
$pdf->Output();

?>