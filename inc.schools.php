<?php
// DB table to use
$table = 'elementary_schools';
 
// Table's primary key
$primaryKey = 'id';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = [array('db' => 'id', 'dt' => 0),
    array('db' => 'school', 'dt' => 1),
    array('db' => 'phone_number', 'dt' => 2),
    array('db' => 'email_address', 'dt' => 3),
    array('db' => 'invited', 'dt' => 4),
    array('db' => 'date_updated', 'dt' => 5)
];

require( 'inc.ajax_db.php' );
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
 
require( 'ssp.class.php' );
 
echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);