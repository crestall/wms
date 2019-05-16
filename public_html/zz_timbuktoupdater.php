<?php
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
$host = 'localhost';
$db   = 'cobaltma_newclientportal';
$user = 'cobaltma_cpsite';
$pass = '{,e3^bfcfcMp';
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = new PDO($dsn, $user, $pass, $opt);


$rfile = fopen($root.'/data/timbukto.csv', 'r') or die('could not open');

$line = 1;
//$skip_first = isset($_POST['header_row']);
$skip_first = true;
while (($row = fgetcsv($rfile)) !== FALSE)
{
    if($skip_first)
    {
        $skip_first = false;
        continue;
    }
    echo "<pre>",print_r($row),"</pre>";
    $sql = "SELECT id FROM items WHERE sku = :sku LIMIT 1";
    $old_sku = str_replace(' ', '', $row[1]);
    $ar = array('sku' => $old_sku);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($ar);
    $line =  $stmt->fetch();

    $sql = "UPDATE items SET sku = :sku WHERE id = :id";
    $pdo->prepare($sql)->execute(array(
        'sku'   => $row[2],
        'id'    => $line['id']
    ));
    echo "<p>{$line['id']} updated to {$line[2]}</p>";
    //echo "<pre>",print_r($ar),"</pre>";
    //echo "<p>{$line['id']}</p>";
}
?>