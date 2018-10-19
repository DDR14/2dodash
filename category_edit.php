<?php
session_start();
include('inc.functions.php'); 
company_db_connect(1);
$categories_id=$_GET['Cid'];
if (isset($_POST['submitted'])) { 
foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 

$extra_qry ="";
if(!file_exists($_FILES['categories_image']['tmp_name']) || !is_uploaded_file($_FILES['categories_image']['tmp_name'])) {
    //echo 'No upload';
}else{
    if (file_exists("../images/" . $_FILES["categories_image"]["name"])) {
        echo( $_FILES["categories_image"]["name"] . " already exists. ");
        die();
    }
    $target = "../images/" . basename($_FILES['categories_image']['name']);
    if (move_uploaded_file($_FILES['categories_image']['tmp_name'], $target)) {
        $extra_qry = ", categories_image = '" . basename($_FILES['categories_image']['name']) ."'";
    }
}

$sql = "UPDATE zen_categories 
                SET size='$_POST[size]', categories_status =  '$_POST[categories_status]' {$extra_qry}, parent_id = '$_POST[parent_id]', sort_order = '$_POST[sort_order]', last_modified = NOW() 
		WHERE categories_id = '$categories_id'"; 
mysql_query($sql) or die(mysql_error()); 
} 
$sql = "UPDATE zen_categories_description 
		SET language_id = '1', categories_name = '$_POST[categories_name]', categories_description = '$_POST[categories_description]'
		WHERE categories_id = '$categories_id'"; 
mysql_query($sql) or die(mysql_error()); 
header('location:categories.php?cPath=' . $categories_id)

?>

