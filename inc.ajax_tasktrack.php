<?php

// DB table to use
$table = 'zen_customers';

// Table's primary key
$primaryKey = 'a.id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = [array('db' => 'a.id', 'dt' => 0, 'field' => 'id'),
    array('db' => 'b.f_name', 'dt' => 1, 'field' => 'f_name'),
    array('db' => 'a.start', 'dt' => 2, 'field' => 'start'),
    array('db' => 'TIMEDIFF(a.end, a.start) AS duration', 'dt' => 3, 'field' => 'duration'),
    array('db' => 'a.orders_id', 'dt' => 4, 'field' => 'orders_id'),
    array('db' => 'a.working_on', 'dt' => 5, 'field' => 'working_on'),
    array('db' => "CONCAT(a.comment, ': ', a.comment_data) AS data", 'dt' => 6, 'field' => 'data')
];

require( 'inc.ajax_db.php' );


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
$joinQuery = "FROM boostpr1_tododash.user_tasktrack a INNER JOIN boostpr1_tododash.users b ON a.user_id = b.id ";
$extraWhere = ""; // extra where

if (isset($_GET["display"])) {
    switch ($_GET["display"]) {
        
    }
}

require( 'ssp.class.php' );

echo json_encode(
        SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
