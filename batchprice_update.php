<?php

session_start();
require_once('inc.functions.php');
company_db_connect(1);
$products_id = mysql_escape_string($_GET['Pid']);
$products_price = $_POST['products_price'];
$products_min_quantity = $_POST['products_min_quantity'];
$discount_qty = $_POST['discount_qty'];
$cPath = $_GET['cPath'];

//UPDATE PRICE
$sql = "UPDATE zen_products SET products_price = '$products_price', "
        . "products_quantity_order_min = '$products_min_quantity', "
        . "products_discount_type='2' "
        . "WHERE master_categories_id = '$cPath'";
mysql_query($sql) or die(mysql_error());

//DELETE THEN INSERT
$sql = "DELETE x.* FROM zen_products_discount_quantity x "
        . "INNER JOIN zen_products y "
        . "ON x.products_id = y.products_id "
        . "WHERE y.master_categories_id = '$cPath'";
mysql_query($sql) or die(mysql_error());

$sql = "INSERT INTO `zen_products_discount_quantity` "
        . "(`discount_id` ,`products_id`, `discount_qty`, `discount_price`) "
        . "SELECT a.discount_id, b.products_id, a.discount_qty, a.discount_price "
        . "FROM products_discount_quantity_template a "
        . "INNER JOIN zen_products b "
        . "ON 1=1 "
        . "WHERE a.template_name = '$discount_qty' "
        . "AND b.master_categories_id = '$cPath'";
mysql_query($sql) or die(mysql_error());

mysql_close();
header("Location: catalog.php?cPath={$cPath}");
