<?php 
session_start();
include('inc.functions.php'); 
company_db_connect(1);
$discount_qty_id = (int) $_GET['Wid']; 
mysql_query("DELETE FROM `products_discount_quantity_template` WHERE `discount_qty_id` = '$discount_qty_id' ") ; 
echo (mysql_affected_rows()) ? "Row deleted.<br /> " : "Nothing deleted.<br /> "; 
header('location:discount_qty.php');
?> 
