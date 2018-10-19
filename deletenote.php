<?php
session_start();
require('inc.functions.php');

secure();

connectToSQL();
company_db_connect(1);

$qry = "DELETE FROM `zen_orders_notes` WHERE id={$_GET["noteid"]}";
mysql_query($qry)or die(mysql_error());

mysql_close();

header('Location: vieworder.php?orderid=' . $_GET['orderid'] . '&companyid=1');
