<?php
// DB table to use
$table = 'zen_customers';
 
// Table's primary key
$primaryKey = 'a.customers_id';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = [array('db' => 'a.customers_id', 'dt' => 0, 'field' => 'customers_id'),
    array('db' => 'b.entry_company', 'dt' => 1, 'field' => 'entry_company'),
    array('db' => 'b.entry_lastname', 'dt' => 2, 'field' => 'entry_lastname'),
    array('db' => 'b.entry_firstname', 'dt' => 3, 'field' => 'entry_firstname'),    
    array('db' => 'b.entry_city', 'dt' => 4, 'field' => 'entry_city'),
    array('db' => 'IFNULL(d.zone_name,b.entry_state) AS state', 'dt' => 5, 'field' => 'state'),
    array('db' => 'c.countries_name', 'dt' => 6, 'field' => 'countries_name'),
    array('db' => 'a.customers_email_address', 'dt' => 7, 'field' => 'customers_email_address'),
    array('db' => 'a.customers_telephone', 'dt' => 8, 'field' => 'customers_telephone')
];

require 'inc.ajax_db.php';
 
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
$joinQuery = "FROM zen_customers AS a 
LEFT JOIN zen_address_book b 
ON b.address_book_id = a.customers_default_address_id 
LEFT JOIN zen_countries c 
ON b.entry_country_id = c.countries_id 
LEFT JOIN zen_zones d 
ON d.zone_id = b.entry_zone_id ";
$extraWhere = ""; // extra where

if(isset($_GET["display"])){
    switch ($_GET["display"]) {
        case "ACTIVE":
            $extraWhere = "a.customers_id IN (SELECT customers_id FROM zen_orders)";
            break;
        case "CCREDITS":
            $extraWhere = "a.customers_id IN (SELECT b.customers_id FROM zen_transactions b 
LEFT JOIN boostpr1_tododash.charges c
ON c.txn_id = b.txn_id                
WHERE txn_type != 'Refund'
AND c.txn_id IS NULL
AND a.customers_id = b.customers_id
AND b.ref_no NOT LIKE '%REF%') 
OR a.customers_id IN (SELECT a.customers_id 
FROM zen_orders a 
INNER JOIN boostpr1_tododash.charges b 
ON a.orders_id = b.orders_id 
AND a.order_total < b.amount)";            
            break;
        case "CREFCREDITS":
            $extraWhere = "a.customers_id IN (SELECT b.customers_id FROM zen_transactions b 
	WHERE txn_type = 'Credit'
	AND a.customers_id = b.customers_id
        AND b.ref_no LIKE '%REF%')";
            break;
    }
}

require( 'ssp.class.php' );
 
echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);