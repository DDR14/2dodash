<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
require_once('inc.functions.php');
connectToSQL();
company_db_connect(1);

$orderid = mysql_real_escape_string($_GET['oid']);
$proofid = mysql_real_escape_string($_GET['proofid']);

$qry = "UPDATE `proofs` SET `status`='1' WHERE `id`='" . $proofid . "'";
mysql_query($qry)or die(mysql_error());

//check IF there are still pending proofs before updating artwork_approved
$qry = "SELECT COUNT(*) AS RESULT  FROM (
SELECT a.id FROM naz_custom_co a 
INNER JOIN zen_orders_products d
ON d.orders_products_id = a.orders_products_id
INNER JOIN zen_products b
ON d.products_id = b.products_id
LEFT JOIN proofs c
ON c.naz_custom_id = a.id 
WHERE a.order_id = '$orderid'
AND b.require_artwork = '1'
GROUP BY a.orders_products_id, c.design_model HAVING MIN(status) IS NULL OR MIN(status) <> 1 )as a";
$result = mysql_query($qry)or die(mysql_error());
if (mysql_fetch_array($result)['RESULT'] == 0) {
     readyShipArtwork($orderid);    
}

header('Location: vieworder.php?orderid=' . $orderid . '&companyid=1');