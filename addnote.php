<?php
session_start();
require('inc.functions.php');

secure();

company_db_connect(1);

$qry = "INSERT INTO `zen_orders_notes` (`note`, `orders_id`, `user_id`) VALUES ('" . mysql_real_escape_string($_POST['note']) . "', '" . mysql_real_escape_string($_GET['orderid']) . "', '" . $_COOKIE['userid'] . "')";
mysql_query($qry)or die(mysql_error());

mysql_close();

header('Location: vieworder.php?orderid=' . $_GET['orderid'] . '&companyid=1');
?>