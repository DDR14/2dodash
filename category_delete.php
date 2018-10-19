<?php 
session_start();
include('inc.functions.php'); 
company_db_connect(1);
$categories_id = (int) $_GET['categories_id']; 
mysql_query("DELETE FROM `zen_categories` WHERE `categories_id` = '$categories_id' ") ; 
echo (mysql_affected_rows()) ? "Row deleted.<br /> " : "Nothing deleted.<br /> "; 
header('location:categories.php');
?> 
