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


$rfile = fopen($root.'/data/woolworths.csv', 'r') or die('could not open');

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
    $sql = "INSERT INTO stores (active, store_number, phone, name, address, suburb, state, postcode)
            VALUES (?,?,?,?,?,?,?,?)";

    $pdo->prepare($sql)->execute([$row[0],$row[1],$row[3],$row[4],$row[5],$row[6],$row[7],$row[9]]);
    echo "<h1>{$row[4]} inserted</h1>";
}
?>