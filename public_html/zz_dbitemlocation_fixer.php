<?php
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
$host = 'localhost';
$db   = 'cobaltma_newclient_portal_dev';
$user = 'cobaltma_cpsite';
$pass = '{,e3^bfcfcMp';
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = new PDO($dsn, $user, $pass, $opt);

$sql = "
SELECT im.*, i.name, i.client_id, cb.*, l.location
FROM `items_movement` im join items i on i.id = im.item_id join clients_bays cb on im.location_id = cb.location_id AND (cb.date_added = im.date OR cb.date_removed = im.date) join locations l on l.id = im.location_id
WHERE i.double_bay = 1
";


$db_item_movements = $pdo->query($sql)->fetchAll();

foreach($db_item_movements as $dbim)
{
    echo "<pre>",print_r($dbim),"</pre>";
    if( preg_match("/\d{1,2}\.\d{1,2}\.\w{1}\.a/i", $dbim['location']) )
    {
        echo "<p>Will update</p>";
    }
    else
    {
        echo "<p>Will Not</p>";
    }
    /*
    $stmt = $pdo->prepare("SELECT location FROM locations WHERE id=?");
    $stmt->execute([$dbim['location_id']]);
    $row = $stmt->fetch();
    $this_location_name = $row['location'];
    echo "<p>$this_location_name</p>";
    */
}
?>