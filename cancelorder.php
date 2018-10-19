<?php

session_start();
require_once('inc.functions.php');


connectToSQL();
secure();
$orderid = mysql_real_escape_string($_GET['orderid']);
$companyid = mysql_real_escape_string($_GET['companyid']);
mysql_close();
company_db_connect($companyid);

$qry = "UPDATE zen_orders SET orders_status = '15' WHERE orders_id = '$orderid'";
mysql_query($qry) or die(mysql_error());

$qry = "INSERT INTO `zen_orders_status_history`(`orders_id`, `orders_status_id`, `date_added`, `customer_notified`, `comments`) "
. "VALUES ('$orderid','15',NOW(),'1','Closed (Cancelled by Customer) by {$_COOKIE["user"]["f_name"]}')";
mysql_query($qry) or die(mysql_error());

header("Location: vieworder.php?orderid=$orderid&companyid=1");
