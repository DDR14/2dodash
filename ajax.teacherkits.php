<?php

$accepted_origins = array("http://localhost", "https://boostpromotions.com/", "http://2dodash.com");

if (isset($_SERVER['HTTP_ORIGIN'])) {
    // same-origin requests won't set an origin. If the origin is set, it must be valid.
    if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    } else {
        header("HTTP/1.0 403 Origin Denied");
        return;
    }
}

require_once('inc.functions.php');
company_db_connect(1);
$term = mysql_real_escape_string($_REQUEST['q']);
$offest = isset($_REQUEST['page'])?$_REQUEST['page']:0;

$qry = "SELECT a.products_image, a.products_model as id, b.products_name as description, 
    b.products_description
FROM zen_products a INNER JOIN zen_products_description b
ON a.products_id = b.products_id
WHERE a.require_artwork = 1 
AND a.master_categories_id NOT IN(101,94)
AND a.manufacturers_id = 1
AND a.products_status = 1
AND (a.products_model LIKE '%{$term}%' OR
    b.products_name LIKE '%{$term}%')
LIMIT $offest,15";
$result = mysql_query($qry)or die(mysql_error());

$data = array();
while($row =  mysql_fetch_object($result)){
    $data[] = $row;
}
header('Content-type:application/json;charset=utf-8');
echo json_encode(["total_count"=> 10, "items" =>$data]);