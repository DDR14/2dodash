<?php

// DB table to use
$table = 'zen_orders';

// Table's primary key
$primaryKey = 'orders_id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = [array('db' => 'orders_id', 'dt' => 0, 'field' => 'orders_id'),
    array('db' => 'ship_by', 'dt' => 1, 'field' => 'ship_by'),
    array('db' => 'order_total', 'dt' => 2,
        'formatter' => function( $d, $row ) {
            return '$' . number_format($d, 2);
        }, 'field' => 'order_total'
    ),
    array('db' => 'customers_company', 'dt' => 3, 'field' => 'customers_company'),
    array('db' => 'customers_email_address', 'dt' => 4, 'field' => 'customers_email_address'),
    array('db' => 'customers_name', 'dt' => 5, 'field' => 'customers_name'),
    array('db' => 'customers_telephone', 'dt' => 6, 'field' => 'customers_telephone'),
    array('db' => 'date_purchased', 'dt' => 7, 'field' => 'date_purchased')
];

require( 'inc.ajax_db.php' );


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
$joinQuery = "";
$extraWhere = '1';


switch ($_GET["display"]) {
    case 'ALLSHIPPED':
        $extraWhere = "shipped <> '0'";
        $columns[1] = array('db' => 'shipped', 'dt' => 1, 'field' => 'shipped');
        break;
    case 'CANCELLED':
        $extraWhere = "orders_status = 15";
        break;
    case 'ALL_BOOST':
        $extraWhere = "orders_id IN(SELECT DISTINCT order_id FROM naz_custom_co WHERE website = 'https://boostpromotions.com')";
        break;
    case "ALL_CTR":
        $extraWhere = "orders_id IN(SELECT DISTINCT order_id FROM naz_custom_co WHERE website = 'http://ctrtags.com')";
        break;
    case "ALL_ISMILE":
        $extraWhere = "orders_id IN(SELECT DISTINCT order_id FROM naz_custom_co WHERE website = 'http://ismiletags.com')";
        break;
    case "ALL_YBA":
        $extraWhere = "orders_id IN(SELECT DISTINCT order_id FROM naz_custom_co WHERE website = 'http://youthbowlingawards.com')";
        break;
    case false:
        $table = "zen_orders_status_history";
        $extraWhere = "orders_status_id = 2 AND comments <> '' GROUP BY orders_id";
        $columns = [
            array('db' => 'orders_id', 'dt' => 0, 'field' => 'orders_id'),
            array('db' => 'comments', 'dt' => 1, 'field' => 'comments')];
        break;
}

if (isset($_GET['startdate'])) {
    $startdate = (int) str_replace('-', '', $_GET['startdate']);
    $enddate = (int) str_replace('-', '', $_GET['enddate']);
    if ($startdate != 0 && $enddate != 0) {
        $extraWhere .= " AND (shipped BETWEEN '{$startdate}' AND '{$enddate}' )";
    } else if ($_GET['startdate'] != '' || $_GET['enddate'] != '') {
        $extraWhere .= " AND (shipped = '" . ($startdate + $enddate) . "')";
    }
}

require( 'ssp.class.php' );

echo json_encode(
        SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
