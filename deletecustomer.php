<?php

session_start();
require_once('inc.functions.php');

secure();
company_db_connect(1);
$custid = mysql_real_escape_string($_GET['customers_id']);

if (isset($_POST['submitted'])) {
    $qry = "DELETE FROM zen_address_book WHERE customers_id = '$custid'";
    mysql_query($qry) or die(mysql_error());
    
    $qry = "UPDATE bt_leads SET customer_id = 0 WHERE customer_id = $custid";
    mysql_query($qry) or die(mysql_error());

    $qry = "DELETE FROM zen_customers WHERE customers_id = '$custid'";
    mysql_query($qry) or die(mysql_error());
    
    $qry = "DELETE FROM zen_customers_info WHERE customers_info_id = '$custid'";
    mysql_query($qry) or die(mysql_error());

    $qry = "DELETE FROM zen_customers_basket WHERE customers_id = '$custid'";
    mysql_query($qry) or die(mysql_error());

    header('Location: customers.php');
}