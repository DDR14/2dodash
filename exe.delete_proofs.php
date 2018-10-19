<?php

/* WARNING deletes ALL TEACHER KITS */

//require_once('inc.functions.php');
//company_db_connect(1);
//
//$qry = "SELECT name, location FROM proofs";
//$res = mysql_query($qry) or die(mysql_error());
//$psds = array();
//$pngs = array();
//while ($row = mysql_fetch_assoc($res)) { 
//    $pngs[] = 'proofs/' . $row['name'];
//    $psds[] = $row['location'];
//}
//
//$database = array_merge($pngs,$psds);
//$drive = array_diff(scandir("proofs/"), array('.', '..'));
//
//$existing = array();
//foreach ($drive as $row) {
//    $existing[] = 'proofs/'.$row;
//}
//
//$trash =array_diff($existing, $database);
//foreach ($trash as $junk) {
//    echo $junk . ' ' . date ("F d Y H:i:s.", filemtime($junk)) . '<br/>';
//}

$folderName = 'proofs/';
$total = 0;
if (file_exists($folderName)) {
    $ctr = 0;
    foreach (new DirectoryIterator($folderName) as $fileInfo) {
        if ($fileInfo->isDot()) {
            continue;
        }
        //if ($fileInfo->isFile() && time() - $fileInfo->getCTime() >= $age) {
        if ($fileInfo->isFile() && date('Y', $fileInfo->getATime()) == '2017') {
            $size = $fileInfo->getSize()/1000000;
            $total += $size;
            //echo $fileInfo->getRealPath() . ' ' . date('Y-m-d', $fileInfo->getATime()) .  ' '. $size . ' MB <br/>';

            //unlink($fileInfo->getRealPath());

            $ctr++;
        }
    }
    echo $total;
}