<?php

$charge_id = $_GET['chargeid'];
session_start();
require_once('inc.functions.php');

company_db_connect(1);

// update transaction the charge belongs to
$qry = "SELECT b.id, b.txn_id FROM boostpr1_tododash.charges b 
WHERE b.txn_id = (SELECT a.txn_id FROM boostpr1_tododash.charges a WHERE a.id = {$charge_id})  ";
$result = mysql_query($qry)or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
    //Delete the charge and all relation
    paymentOrderAlign('DELETE', $row['id']);
    $txn_id = $row['txn_id'];
}

//Delete only if the resulting amount is 0
$qry = "DELETE FROM zen_transactions WHERE txn_id = {$txn_id}";
mysql_query($qry)or die(mysql_error());

header("Location: vieworder.php?orderid=" . $_GET['orderid'] . "&companyid=1");
