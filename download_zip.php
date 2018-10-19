<?php

ini_set('max_execution_time', 0);
$oid = $_GET['oid'];

function gen_uuid($l = 8) {
    $str = "";
    //orig: 0123456789abcdefghijklmnopqrstuvwxyz
    for ($x = 0; $x < $l; $x++) {
        $str .= substr(str_shuffle("0123456789BCDFGHJKLMNPQRSTVWXYZ"), 0, 1);
    }
    return $str;
}

function insert_codes($opid, $products_model, $manufacturers_id, $looper = 0) {
    $oid = $_GET['oid'];
    $ins = array();
    for ($i = 0; $i < $looper; $i++) {
        //model-order-random
        $code = "'" . mysql_real_escape_string($products_model) . '-' . $oid . '-' . gen_uuid() . "'";
        if ($manufacturers_id == 9) {
            $code = 'UPPER(LEFT(UUID(), 8))';
        }

        $ins[] = "('{$opid}'," . $code . ",'$manufacturers_id', NOW())";
    }
    $qry = "INSERT INTO `gw_codes`(`orders_products_id`, `code`, `manufacturers_id`, `created`) VALUES"
            . implode(",", $ins);
    mysql_query($qry)or die('29' . mysql_error());
}

if (isset($_GET['csv'])) {
    require_once('inc.functions.php');
    company_db_connect(1);

    $qry = "SELECT b.manufacturers_id, a.orders_products_id, a.products_model, a.products_quantity, (
SELECT COUNT( x.id ) 
FROM gw_codes x
WHERE x.orders_products_id = a.orders_products_id
) AS code_ctr
FROM zen_orders_products a
INNER JOIN zen_products b
ON a.products_id = b.products_id
WHERE a.orders_id =  '$oid'
AND b.require_artwork = 1
ORDER BY a.orders_products_id DESC";

    $res = mysql_query($qry)or die('48' . mysql_error());
    $ctr = 0;
    $data = [];
    while ($row = mysql_fetch_assoc($res)) {
        //START
        $opid = $row['orders_products_id'];
        $products_model = $row['products_model'];
        $products_quantity = (int) $row['products_quantity']; //should be a variable
        $code_ctr = (int) $row['code_ctr'];
        $manufacturers_id = (int) $row['manufacturers_id'];

        $qryx = "SELECT a.code, c.name, b.website
FROM gw_codes a
INNER JOIN naz_custom_co b
ON a.orders_products_id = b.orders_products_id
INNER JOIN proofs c
ON b.id = c.naz_custom_id
AND c.status = 1 
WHERE a.orders_products_id = '$opid'";

        if ($code_ctr > 0) {
            if ($code_ctr > $products_quantity) {
                $limit = $code_ctr - $products_quantity;
                $qry = "DELETE FROM gw_codes WHERE orders_products_id = '$opid' LIMIT $limit";
                $result = mysql_query($qry)or die('72' . mysql_error());
            } elseif ($code_ctr < $products_quantity) {
                $limit = $products_quantity - $code_ctr;
                insert_codes($opid, $products_model, $manufacturers_id, $limit);
            }
            $resultx = mysql_query($qryx)or die('77' . mysql_error());
        } else {
            insert_codes($opid, $products_model, $manufacturers_id, $products_quantity);
            $resultx = mysql_query($qryx)or die('83' . mysql_error());
        }

        $data[$ctr] = 'image';
        $data[$ctr + 1] = 'code';
        $data[$ctr + 2] = 'website';
        while ($rowx = mysql_fetch_assoc($resultx)) {
            $data[$ctr] .= ', proofs/' . $rowx['name'];
            $data[$ctr + 1] .= ', ' . $rowx['code'];
            $data[$ctr + 2] .= ', ' . $rowx['website'];
        }

        //END
        $ctr += 3;
    }

    $fp = fopen('php://output', 'w');
    if ($fp) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $oid . '_codes.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');
        foreach ($data as $row) {
            fwrite($fp, $row);
            fwrite($fp, "\n");
        }
        die;
    }
}

if (isset($_GET['unplugged'])) {
    require_once('inc.functions.php');
    company_db_connect(1);

    $qry = "SELECT b.manufacturers_id, a.orders_products_id, b.products_image, a.products_model, a.products_quantity, (
SELECT COUNT( x.id ) 
FROM gw_codes x
WHERE x.orders_products_id = a.orders_products_id
) AS code_ctr
FROM zen_orders_products a
INNER JOIN zen_products b
ON a.products_id = b.products_id
WHERE a.orders_id =  '$oid'
AND b.require_artwork = 0
ORDER BY a.orders_products_id DESC";

    $res = mysql_query($qry)or die('48' . mysql_error());
    $ctr = 1;
    $i = 0;
    $data = [];
    $data[0] = 'TextVariable1,TextVariable2,TextVariable3,TextVariable4,TextVariable5,TextVariable6,TextVariable7,TextVariable8,TextVariable9,TextVariable10,TextVariable11,TextVariable12,TextVariable13,TextVariable14,TextVariable15,TextVariable16,TextVariable17,TextVariable18,TextVariable19,TextVariable20,TextVariable21,TextVariable22,TextVariable23,TextVariable24,TextVariable25,TextVariable26,TextVariable27,TextVariable28,TextVariable29,TextVariable30,TextVariable31,TextVariable32,TextVariable33,TextVariable34,TextVariable35,TextVariable36,TextVariable37,TextVariable38,TextVariable39,TextVariable40,TextVariable41,TextVariable42,TextVariable43,TextVariable44,TextVariable45,TextVariable46,TextVariable47,TextVariable48,TextVariable49,TextVariable50,TextVariable51,TextVariable52,TextVariable53,TextVariable54,TextVariable55,TextVariable56,TextVariable57,TextVariable58,TextVariable59,TextVariable60,TextVariable61,TextVariable62,TextVariable63,TextVariable64,TextVariable65';
    $data[1] = '';
    while ($row = mysql_fetch_assoc($res)) {
        //START
        $opid = $row['orders_products_id'];
        $products_model = $row['products_model'];
        $products_quantity = (int) $row['products_quantity']; //should be a variable
        $code_ctr = (int) $row['code_ctr'];
        $manufacturers_id = (int) $row['manufacturers_id'];
        $products_image = $row['products_image']; //cause this is not proof based

        $qryx = "SELECT a.code FROM gw_codes a WHERE a.orders_products_id = '$opid'";

        if ($code_ctr > 0) {
            if ($code_ctr > $products_quantity) {
                $limit = $code_ctr - $products_quantity;
                $qry = "DELETE FROM gw_codes WHERE orders_products_id = '$opid' LIMIT $limit";
                $result = mysql_query($qry)or die('72' . mysql_error());
            } elseif ($code_ctr < $products_quantity) {
                $limit = $products_quantity - $code_ctr;
                insert_codes($opid, $products_model, $manufacturers_id, $limit);
            }
            $resultx = mysql_query($qryx)or die('77' . mysql_error());
        } else {
            insert_codes($opid, $products_model, $manufacturers_id, $products_quantity);
            $resultx = mysql_query($qryx)or die('83' . mysql_error());
        }

        while ($rowx = mysql_fetch_assoc($resultx)) {            
            if ($i == 65) {
                $ctr ++;
                $i = 0;
            }
            if ($data[$ctr]):
                $data[$ctr] .= ', ' . $rowx['code'];
            else:
                $data[$ctr] = $rowx['code'];
            endif;   
            $i++;
        }
        $ctr += 1;
    }

    $fp = fopen('php://output', 'w');
    if ($fp) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $oid . '_unplugged_codes.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');
        foreach ($data as $row) {
            fwrite($fp, $row);
            fwrite($fp, "\n");
        }
        die;
    }
}

//Download all proofs
if (isset($_GET['zip'])) {
    $files = unserialize($_GET['proofs']);
    $zipname = $oid . '_proofs.zip';
    $zip = new \ZipArchive;
    $zip->open($zipname, ZipArchive::OVERWRITE | ZipArchive::CREATE | ZipArchive::EXCL);
    foreach ($files as $file) {
        $zip->addFile($file);
    }
    $zip->close();

    header('Content-Type: application/zip');
    header('Content-disposition: attachment; filename=' . $zipname);
    header('Content-Length: ' . filesize($zipname));
    readfile($zipname);
    unlink($zipname);
}


if (isset($_GET['activate'])) {
    include "include/db.php";
    $db = new db('boostpr1_boostpromotions');

    $result = $db->raw(<<<SQL
    UPDATE gw_codes b INNER JOIN zen_orders_products a 
        ON a.orders_products_id = b.orders_products_id
    SET b.status = :status 
    WHERE a.orders_id = :oid AND b.status = 0
SQL
            , ['status' => 1, 'oid' => $oid]);

    echo $result->rowCount() . ' codes activated successfuly!';
}