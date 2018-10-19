<?php
/*WARNING deletes ALL TEACHER KITS*/

require_once('inc.functions.php');
company_db_connect(1);

$qry = "SELECT name, location FROM proofs";
$res = mysql_query($qry) or die(mysql_error());
$psds = array();
$pngs = array();
while ($row = mysql_fetch_assoc($res)) { 
    $pngs[] = 'proofs/' . $row['name'];
    $psds[] = $row['location'];
}

$database = array_merge($pngs,$psds);
$drive = array_diff(scandir("proofs/"), array('.', '..'));
var_dump(count($drive));

$existing = array();
foreach ($drive as $row) {
    $existing[] = 'proofs/'.$row;
}

$trash =array_diff($existing, $database);

$ctr = 0;
$checks2 = array();
$checks = array();
foreach ($trash as $pic) {
    $checks[] = "'" . mysql_real_escape_string($pic) . "'";    
    $checks2[] = "'" . mysql_real_escape_string(substr($pic,7)) . "'";
    if (file_exists($pic)) {
        echo "yes";
        //unlink($pic);
    } else {
        echo "no";
    }

    $ctr++;
}

//$check = "SELECT * FROM proofs WHERE  location IN (". implode(',', $checks) .") OR name IN (". implode(',', $checks2) .")";
//$res = mysql_query($check) or die(mysql_error());
//while ($row1 = mysql_fetch_array($res)) {
//    var_dump($row1);
//}
//var_dump(mysql_num_rows($res));
var_dump(count($checks2));