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
        $next_location = substr($dbim['location'], 0, -1)."b";
        $stmt = $pdo->prepare("SELECT id FROM locations WHERE location=?");
        $stmt->execute([$next_location]);
        $row = $stmt->fetch();
        $next_location_id =  $row['id'];
        if($dbim['date'] === $dbim['date_added'])
        {
            echo "<p>Will add for $next_location : $next_location_id</p>";
            $sql = "
                INSERT INTO clients_locations (location_id, client_id, notes, date_added)
                VALUES (?,?,?,?)
            ";

            $pdo->prepare($sql)->execute([$next_location_id, $dbim['client_id'], 'Double Bay Item', $dbim['date_added']]);
        }
        elseif($dbim['date'] === $dbim['date_removed'])
        {
            echo "<p>Will remove for $next_location : $next_location_id</p>";
            $sql = "
                SELECT id FROM clients_locations WHERE location_id = ? AND client_id = ? AND date_removed = 0
            ";
            $row = $pdo->prepare($sql)->execute([$next_location_id, $dbim['client_id'], 0])->fetch();
            $row_id = $row['id'];
            $sql = "
                UPDATE clients_locations SET date_removed = ? WHERE id = ?
            ";
            $array = [$dbim['date_removed'], $row_id];
            $pdo->prepare($sql)->execute($array);
            echo "<p>SELECT id FROM clients_locations WHERE location_id = ? AND client_id = ? AND date_removed = ?</p>";
            echo "<pre>",print_r($array),"</pre>";
        }
        else
        {
            echo "<p>Date mismatch</p>";
        }
    }
}
?>