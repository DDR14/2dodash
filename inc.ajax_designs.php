<?php

// DB table to use
$table = 'zen_designs';

// Table's primary key
$primaryKey = 'a.products_id';
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes


 
if ($_GET['display'] == 'all' || $_GET['display']) {
    $joinQuery = "FROM zen_designs a";
    $columns = [array('db' => 'a.products_id', 'dt' => 0, 'field' => 'products_id'),
    array('db' => 'a.design_name', 'dt' => 1, 'field' => 'design_name'),
    array('db' => 'a.products_model', 'dt' => 2, 'field' => 'products_model'),
    array('db' => 'a.products_image', 'dt' => 3, 'field' => 'products_image'),
    array('db' => 'a.products_id', 'dt' => 4, 'field' => 'products_id')
];
} else{
    $columns = [array('db' => 'a.products_id', 'dt' => 0, 'field' => 'products_id'),
    array('db' => 'a.design_name', 'dt' => 1, 'field' => 'design_name'),
    array('db' => 'a.products_model', 'dt' => 2, 'field' => 'products_model'),
    array('db' => 'a.products_image', 'dt' => 3, 'field' => 'products_image'),
    array('db' => 'a.products_id', 'dt' => 4, 'field' => 'products_id'),
    array('db' => 'b.dd_new_products_price', 'dt' => 5, 'field' => 'dd_new_products_price'),
    array('db' => 'b.specials_date_available', 'dt' => 6, 'field' => 'specials_date_available'),
    array('db' => 'b.expires_date', 'dt' => 7, 'field' => 'expires_date'),
    array('db' => 'b.dd_uses', 'dt' => 8, 'field' => 'dd_uses')
];
}

require 'inc.ajax_db.php';


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
$joinQuery = "FROM zen_designs a INNER JOIN zen_discounted_designs b ON a.products_id = b.design_id"; 
// extra where
$extraWhere = '';


if ($_GET['display'] == 'all') {
    $joinQuery = "FROM zen_designs a";
} elseif($_GET['display']) {
    $joinQuery = "FROM zen_designs a";
    $extraWhere = 'a.master_categories_id = ' . (int)$_GET['display'];
}

require( 'ssp.class.php' );

echo json_encode(
        SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);

