<?php
session_start();
include('inc.functions.php'); 
company_db_connect(1);
if (isset($_POST['submitted'])) { 
foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 
$sql = "INSERT INTO `products_discount_quantity_template` ( `template_name` ,  `discount_id` ,  `discount_qty` ,  `discount_price`  ) VALUES(  '{$_POST['template_name']}' ,  '{$_POST['discount_id']}' , '{$_POST['discount_qty']}',  '{$_POST['discount_price']}')"; 
mysql_query($sql) or die(mysql_error()); 
echo "Added row.<br />"; 
header('location:discount_qty.php'); 
} 