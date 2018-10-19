<?php
// DB table to use
$table = 'zen_coupons';
 
// Table's primary key
$primaryKey = 'a.coupon_id';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = [array('db' => 'a.coupon_id', 'dt' => 0, 'field' => 'coupon_id'),
    array('db' => 'b.coupon_name', 'dt' => 1, 'field' => 'coupon_name'),
    array('db' => "IF(a.coupon_type='F',CONCAT('$',a.coupon_amount),CONCAT(a.coupon_amount, '%')) AS coupon_amount", 'dt' => 2, 'field' => 'coupon_amount'),
    array('db' => 'a.coupon_code', 'dt' => 3, 'field' => 'coupon_code'),    
    array('db' => 'a.coupon_active', 'dt' => 4, 'field' => 'coupon_active'),
    array('db' => 'a.coupon_start_date', 'dt' => 5, 'field' => 'coupon_start_date'),
    array('db' => 'a.coupon_expire_date', 'dt' => 6, 'field' => 'coupon_expire_date')
];

require 'inc.ajax_db.php';
 
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
$joinQuery = "FROM zen_coupons a LEFT JOIN zen_coupons_description b ON a.coupon_id = b.coupon_id";
$extraWhere = "a.coupon_code NOT LIKE '%REF'"; // extra where

if(isset($_GET["display"])){
    switch ($_GET["display"]) {
        case "REF":
            $extraWhere = "a.coupon_code LIKE '%REF'";
            break;   
        case "FIRST":
            $extraWhere = "a.coupon_code NOT LIKE '%REF' AND loyal_customer = 0";
            break;  
        case "OLD":
            $extraWhere = "a.coupon_code NOT LIKE '%REF' AND loyal_customer = 1";
            break;   
        
    }
}

require( 'ssp.class.php' );
 
echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);