<?php
$root = realpath($_SERVER["DOCUMENT_ROOT"]);

include_once($root."../required.php"); 

$stmt = $pdo->prepare('SELECT * FROM users WHERE role = ?');
$stmt->execute(['client']);
//$user = $stmt->fetch();
while ($user = $stmt->fetch())
{
    echo "<pre>",print_r($user),"</pre>";
	if (preg_match('/.*\@user\.com/i',$user['email']))
	{
		$new_hash = password_hash('3PLPlus', PASSWORD_DEFAULT, array('cost' => 10));
		$sql = "UPDATE users SET hashed_password = ? WHERE id = ?";
		$pdo->prepare($sql)->execute([$new_hash, $user['id']]);
		echo "<h1>updated</h1>";
	}

}
?>