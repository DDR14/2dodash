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
    array('db' => 'x.last_cart_update', 'dt' => 7, 'field' => 'last_cart_update'),
    array('db' => 'x.cart_item_count', 'dt' => 8, 'field' => 'cart_item_count')
];

require 'inc.ajax_db.php';


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
$joinQuery = "FROM zen_customers AS a 
INNER JOIN zen_address_book b 
ON b.address_book_id = a.customers_default_address_id 
INNER JOIN zen_countries c 
ON b.entry_country_id = c.countries_id 
INNER JOIN (SELECT e.customers_id, 
        COUNT(e.customers_basket_id) AS cart_item_count, 
        DATE_FORMAT(MAX(e.customers_basket_date_added), '%Y-%m-%d') AS last_cart_update
    FROM zen_customers_basket e GROUP BY e.customers_id) AS x
ON a.customers_id = x.customers_id
LEFT JOIN zen_zones d 
ON d.zone_id = b.entry_zone_id ";

$extraWhere = "a.customers_id <> 1738";
/*
 * Derived table is the way to go to work with datatables
 * -elvis
 */

require( 'ssp.class.php' );

echo json_encode(
        SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
