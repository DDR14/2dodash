<?php
//backend file to add an item to an order.

session_start();
require_once('inc.functions.php');
company_db_connect(1);

$products_model = strtoupper(trim($_POST['modelNumber']));
$products_price = mysql_real_escape_string($_POST['price']);
$products_quantity = mysql_real_escape_string($_POST['qty']);
$orderid = mysql_real_escape_string($_GET['orderid']);
$companyid = $_GET['companyid'];
$customs = mysql_real_escape_string($_POST['customs']);
$title = mysql_real_escape_string($_POST['title']);
$footer = mysql_real_escape_string($_POST['footer']);
$background = mysql_real_escape_string($_POST['background']);
$website = mysql_real_escape_string($_POST['website']);

//inserting customizations
//need userid of customer

$qry = "SELECT orders_id, customers_id FROM zen_orders WHERE orders_id = '" . $orderid . "'";
$result = mysql_query($qry)or die(mysql_error());
$num_rows = mysql_num_rows($result);
while ($row = mysql_fetch_assoc($result)) {
    $naz_user_id = $row['customers_id'];
}

if ($num_rows <= 0) {
    die("something went wrong, customer user id is unidentified: $naz_user_id");
}

$qry = "SELECT a.products_id, a.require_artwork, a.products_model, b.products_name 
FROM zen_products a 
INNER JOIN zen_products_description b 
ON a.products_id = b.products_id 
WHERE products_model='" . $products_model . "'"
        . "";
$result = mysql_query($qry)or die(mysql_error());

$num_rows = mysql_num_rows($result);

if ($num_rows <= 0) {
    die('That model number could not be found.');
}

while ($row = mysql_fetch_assoc($result)) {
    $products_id = $row['products_id'];
    $require_artwork = $row['require_artwork'];
    $products_name = mysql_real_escape_string($row["products_name"]);
}

$qry = "INSERT INTO zen_orders_products (products_name, orders_id, products_model, products_price, final_price, products_quantity, products_tax, products_id, products_prid) ";
$qry .= "VALUES ('$products_name','" . $orderid . "', '" . $products_model . "', '" . $products_price . "', '" . $products_price . "', '" . $products_quantity . "', '6.85', '" . $products_id . "', '" . $products_id . "')";
mysql_query($qry)or die(mysql_error());

//GET new row
$orders_products_id = mysql_insert_id();

//insert customs
$qry = "INSERT INTO naz_custom_co (user_id, order_id, date, model,customs, orders_products_id, title, footer, background, website) "
        . "VALUES ('" . $naz_user_id . "', '" . $orderid . "', NOW(), '" . $products_model . "', '" . $customs . "', '$orders_products_id', '$title', '$footer', '$background', '$website')";
mysql_query($qry)or die(mysql_error());

//REMOVE artwork approved status
if ($require_artwork == '1') {
    $qry = "UPDATE zen_orders SET artwork_approved = '0', cut = 0, laminated = 0, printed = 0, orders_status='2' WHERE orders_id = '$orderid'";
    mysql_query($qry)or die(mysql_error());
}

$qry = "INSERT INTO zen_orders_status_history(orders_id, orders_status_id, date_added, customer_notified, comments) 
VALUES ('$orderid', 2, NOW(), 1, '$products_model added by {$_COOKIE["user"]["f_name"]}')";
mysql_query($qry)or die(mysql_error());

header('Location: editorder.php?opt=skynet&orderid=' . $orderid . '&companyid=1');
