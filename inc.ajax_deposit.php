<?php
// DB table to use
$table = 'charges';
 
// Table's primary key
$primaryKey = 'id';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = [array('db' => 'a.id', 'dt' => 'DT_RowId', 'field' => 'id', 'formatter' => function( $d, $row ) {
            // Technically a DOM id cannot start with an integer, so we prefix
            // a string. This can also be useful if you have multiple tables
            // to ensure that the id is unique with a different prefix
            return 'row_'.$d;
        }),
    array('db' => 'a.id', 'dt' => 0, 'field' => 'id'),
    array('db' => 'a.orders_id', 'dt' => 1, 'field' => 'orders_id'),
    array('db' => 'a.amount', 'dt' => 2, 'field' => 'amount',
        'formatter' => function( $d, $row ) {
            return '$' . number_format($d,2);
        }),
    array('db' => 'a.method', 'dt' => 3, 'field' => 'method'),
    array('db' => 'a.insert_date', 'dt' => 4, 'field' => 'insert_date'),
    array('db' => 'a.posted_date', 'dt' => 5, 'field' => 'posted_date'),
    array('db' => 'a.posted', 'dt' => 6, 'field' => 'posted'),
    array('db' => "CONCAT(b.customers_lastname,', ', b.customers_firstname) AS customers_name", 'dt' => 7, 'field' => 'customers_name')
];

require 'inc.ajax_db.php';

$joinQuery = "FROM boostpr1_tododash.charges a INNER JOIN zen_customers b ON a.customers_id = b.customers_id";
$extraWhere = "posted = {$_GET['posted']} AND amount <> 0 " . ($_GET['method'] == ''?'':"AND method = '{$_GET['method']}'");

  
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
 
require( 'ssp.class.php' );
 
echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);