<?php
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
include_once($root."/../required.php");
$client_id = 51;

$stmt = $pdo->prepare('SELECT * FROM items WHERE client_id = ? AND barcode IS NULL'); 
$stmt->execute([$client_id]);

$stmt2 = $pdo->prepare('SELECT id FROM items WHERE barcode = ?');
while ($item = $stmt->fetch())
{
	//echo "<pre>",print_r($item),"</pre>";
	$order_number = ean13_check_digit(randomNumber(12));
	
	while($stmt2->execute([$order_number])->num_rows > 0)
	{
		$order_number = ean13_check_digit(randomNumber(12));	
	}
	$sql = "UPDATE items SET barcode = ? WHERE id = ?";
	$pdo->prepare($sql)->execute([$order_number, $item['id']]);
	echo "<p>$order_number</p>";
}
















function ean13_check_digit($digits)
{
	//first change digits to a string so that we can access individual numbers
	$digits =(string)$digits;
	// 1. Add the values of the digits in the even-numbered positions: 2, 4, 6, etc.
	$even_sum = $digits{1} + $digits{3} + $digits{5} + $digits{7} + $digits{9} + $digits{11};
	// 2. Multiply this result by 3.
	$even_sum_three = $even_sum * 3;
	// 3. Add the values of the digits in the odd-numbered positions: 1, 3, 5, etc.
	$odd_sum = $digits{0} + $digits{2} + $digits{4} + $digits{6} + $digits{8} + $digits{10};
	// 4. Sum the results of steps 2 and 3.
	$total_sum = $even_sum_three + $odd_sum;
	// 5. The check character is the smallest number which, when added to the result in step 4,  produces a multiple of 10.
	$next_ten = (ceil($total_sum/10))*10;
	$check_digit = $next_ten - $total_sum;
	return $digits . $check_digit;
}

function randomNumber($length = 6)
{
	$result = mt_rand(1, 9);
	for($i = 1; $i < $length; $i++) {
		$result .= mt_rand(0, 9);
	}

	return $result;
}

?>