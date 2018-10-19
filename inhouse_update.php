<?php
session_start();
 require_once('inc.functions.php');
company_db_connect(1);
//update proofs on db
$inhouse = '0';
if (isset($_POST['inhouse'])){
    $inhouse = '1';
}
$qry = "UPDATE `zen_orders` SET inhouse='$inhouse' WHERE orders_id = '" . $_GET['oid'] . "'";
mysql_query($qry)or die(mysql_error());

mysql_close();
header("Location: vieworder.php?orderid=". $_GET['oid']."&companyid=1");
