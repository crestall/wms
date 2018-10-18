<?php
$_SESSION['value_array'] = $_POST;
echo "<pre>",print_r($_POST),"</pre>";

echo "<p>field value: ".value('product')."</p>";

function value($field)
{
  if(array_key_exists($field,$_SESSION['value_array']))
  {
  	if(is_array($_SESSION['value_array'][$field]))
    {
        return $_SESSION['value_array'][$field];
    }
     return htmlspecialchars(stripslashes($_SESSION['value_array'][$field]));
  }
  else
  {
     return "";
  }
}
?>
<form method="post">
    <p>Name: <input name="product[0][name]" type="text" value="product 1" />
    Qty: <input name="product[0][qty]" type="text" value="1" /> </p>
    <p>Name: <input name="product[1][name]" type="text" value="product 2" />
    Qty: <input name="product[1][qty]" type="text" value="2" /> </p>
    <p>Name: <input name="product[2][name]" type="text" value="product 3" />
    Qty: <input name="product[2][qty]" type="text" value="3" /> </p>
    <p><input type="submit" /></p>
</form>