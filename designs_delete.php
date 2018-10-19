<?php

session_start();
require_once('inc.functions.php');
company_db_connect(1);
$products_id = mysql_escape_string($_GET['Pid']);

//MAIN PRODUCTS DELETE
$sql = "DELETE FROM `zen_designs` WHERE `products_id`='$products_id'";
mysql_query($sql) or die(mysql_error());


header("Location: designs.php?cPath={$_GET['cPath']}");
