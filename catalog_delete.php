<?php

session_start();
require_once('inc.functions.php');
company_db_connect(1);
$products_id = mysql_escape_string($_GET['Pid']);


//a customer has ordered this tag. it cannot be deleted.
$qry = "SELECT COUNT(*) AS ctr, MIN(orders_id) AS oid FROM `zen_orders_products` WHERE `products_id`='$products_id' GROUP BY products_id";
$result = mysql_query($qry)or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
    $ordered = $row['ctr'];
    $ordered_id = $row['oid'];
}

if ($ordered != "0") {
    die("a customer has ordered this tag. it cannot be deleted. Order #" . $ordered_id);
}

//Start deleting some things
if (file_exists("../images/" . $_GET['image'])) {
    echo "../images/" . $_GET['image'] . " the file exists. deleting file... ";
    unlink("../images/" . $_GET['image']);
    $hasPSD = substr($_GET['image'], 0, -4) . ".psd";
    if (file_exists("../images/" . $hasPSD)) {
        echo " the PSD file exists. deleting file... ";
        unlink($hasPSD);
    }
} else {
    echo "File not found";
}

//MAIN PRODUCTS DELETE
$sql = "DELETE FROM `zen_products` WHERE `products_id`='$products_id'";
mysql_query($sql) or die(mysql_error());

//DELETE zen_products_discount_quantity
$sql = "DELETE FROM `zen_products_discount_quantity` WHERE `products_id`='$products_id'";
mysql_query($sql) or die(mysql_error());

///DELETE zen_products_to_categories
$sql = "DELETE FROM `zen_products_to_categories` WHERE `products_id`='$products_id'";
mysql_query($sql) or die(mysql_error());

///DELETE zen_specials
$sql = "DELETE FROM `zen_specials` WHERE `products_id`='$products_id'";
mysql_query($sql) or die(mysql_error());

///DELETE zen_meta_tags_products_description
$sql = "DELETE FROM `zen_meta_tags_products_description` WHERE `products_id`='$products_id'";
mysql_query($sql) or die(mysql_error());

///DELETE zen_products_attributes
$sql = "DELETE FROM `zen_products_attributes` WHERE `products_id`='$products_id'";
mysql_query($sql) or die(mysql_error());

///DELETE zen_customers_basket
$sql = "DELETE FROM `zen_customers_basket` WHERE `products_id`='$products_id'";
mysql_query($sql) or die(mysql_error());

///DELETE zen_customers_basket_attributes
$sql = "DELETE FROM `zen_customers_basket_attributes` WHERE `products_id`='$products_id'";
mysql_query($sql) or die(mysql_error());

//DELETE zen_products_description
$sql = "DELETE FROM `zen_products_description`WHERE `products_id`='$products_id'";
mysql_query($sql) or die(mysql_error());

//REVIEWS NOT NECESSARY FOR DELETION

mysql_close();
if ($_GET['cPath'] == 'pubs') {
    header("Location: publisheddesign.php");
} else {
    header("Location: catalog.php?cPath={$_GET['cPath']}");
}