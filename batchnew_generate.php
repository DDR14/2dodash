<?php

session_start();
require_once('inc.functions.php');
company_db_connect(1);
$limit = mysql_escape_string((int)$_POST['limit']);

//DELETE THEN INSERT
$sql = "DELETE FROM zen_products_to_categories WHERE categories_id = 131";
mysql_query($sql) or die(mysql_error());

$sql = "INSERT INTO zen_products_to_categories (products_id,categories_id)
SELECT products_id, 131 FROM `zen_products` WHERE manufacturers_id = 1 AND products_status = 1
ORDER BY products_date_added DESC LIMIT $limit";
mysql_query($sql) or die(mysql_error());

mysql_close();
header("Location: catalog.php?cPath=131");
