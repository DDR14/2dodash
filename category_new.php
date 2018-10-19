<?php
session_start();
include('inc.functions.php'); 
company_db_connect(1);
if (isset($_POST['submitted'])) { 

$categories_name=$_POST['categories_name'];
$categories_description=$_POST['categories_description'];

foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 
$sql = "INSERT INTO zen_categories (categories_image, parent_id, sort_order, date_added, last_modified, categories_status) 
		VALUES('$_POST[categories_image]', '$_POST[parent_id]', '$_POST[sort_order]', now(), now(), '$_POST[category_status]')";
mysql_query($sql) or die(mysql_error());  
}
if (mysql_query($sql)) {
	$last_id=mysql_insert_id();	

$sql="INSERT INTO zen_categories_description (categories_id, language_id, categories_name, categories_description)
VALUES ('$last_id', '1', '$_POST[categories_name]', '$_POST[categories_description]')";
mysql_query($sql) or die(mysql_error());
header('location:categories.php');
}


	
?>

