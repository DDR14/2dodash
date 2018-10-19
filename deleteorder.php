<?php

session_start();
require_once('inc.functions.php');


connectToSQL();
secure();
$orderid = mysql_real_escape_string($_GET['orderid']);
$companyid = mysql_real_escape_string($_GET['companyid']);
mysql_close();
company_db_connect($companyid);

$qry = "SELECT `id` FROM boostpr1_tododash.charges  WHERE orders_id = '$orderid'";
$result = mysql_query($qry) or die(mysql_error());
if (mysql_num_rows($result) > 0) {
    die("THIS ORDER HAS PAYMENT, DONT DELETE YET. <br/> I think its best to transfer the payment to another order before deleting this.");
}
// UNPLUGGED CODES
$qry = <<<SQL
        SELECT (SELECT COUNT( x.id ) FROM gw_codes x
            WHERE x.orders_products_id = a.orders_products_id
            AND x.status <> 0
            ) AS code_ctr
        FROM zen_orders_products a 
        WHERE orders_id = '$orderid' LIMIT 1
SQL;
$result = mysql_query($qry)or die(mysql_error());
$row = mysql_fetch_assoc($result);
$code_ctr = $row['code_ctr'];
if ($code_ctr > 0) {
    die('The Back of these Tags are activated. cannot delete. press back to continue');
}

$qry = "DELETE FROM `gw_codes` WHERE orders_products_id IN (SELECT orders_products_id FROM zen_orders_products  WHERE orders_id = '$orderid')";
mysql_query($qry)or die(mysql_error());

$qry = "DELETE FROM zen_orders  WHERE orders_id = '$orderid'";
mysql_query($qry) or die(mysql_error());

$qry = "DELETE FROM naz_custom_co  WHERE order_id = '$orderid'";
mysql_query($qry) or die(mysql_error());

$qry = "DELETE FROM zen_orders_products  WHERE orders_id = '$orderid'";
mysql_query($qry) or die(mysql_error());

$qry = "DELETE FROM zen_orders_products_attributes
                  WHERE orders_id = '$orderid'";
mysql_query($qry) or die(mysql_error());

$qry = "DELETE FROM zen_orders_products_download
                  WHERE orders_id = '$orderid'";
mysql_query($qry) or die(mysql_error());

$qry = "DELETE FROM zen_orders_status_history WHERE orders_id = '$orderid'";
mysql_query($qry) or die(mysql_error());

$qry = "DELETE FROM zen_orders_total WHERE orders_id = '$orderid'";
mysql_query($qry) or die(mysql_error());

$qry = "DELETE FROM zen_coupon_gv_queue WHERE order_id = '$orderid' and release_flag = 'N'";
mysql_query($qry) or die(mysql_error());

$qry = "DELETE FROM zen_coupon_redeem_track WHERE order_id = '$orderid'";
mysql_query($qry) or die(mysql_error());

header('Location: dashboard.php?display=EVERYTHING');
