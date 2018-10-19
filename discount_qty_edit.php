<?php 
session_start();
include('inc.functions.php'); 
company_db_connect(1);
$discount_qty_id=$_GET['Wid'];
if (isset($_POST['submitted'])) { 
foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 
$sql = "UPDATE `products_discount_quantity_template` SET  `template_name` =  '{$_POST['template_name']}' ,  `discount_id` =  '{$_POST['discount_id']}' ,  `discount_qty` =  '{$_POST['discount_qty']}',  `discount_price` =  '{$_POST['discount_price']}'  WHERE `discount_qty_id` = '$discount_qty_id' "; 
mysql_query($sql) or die(mysql_error()); 
echo (mysql_affected_rows()) ? "Edited row.<br />" : "Nothing changed. <br />"; 
header('location:discount_qty.php'); 
} 