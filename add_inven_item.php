<?php
session_start();
//we are adding an item to the inventory.
require_once('inc.functions.php');
secure();

connectToSQL();
$company = mysql_real_escape_string($_POST['company']);
$name = mysql_real_escape_string($_POST['name']);
$amount = mysql_real_escape_string($_POST['amount']);
$alarm = mysql_real_escape_string($_POST['alarm']);

//echo $company . "<br />";
//echo $name . "<br />";
//echo $amount . "<br />";
//echo $alarm . "<br />"; 

//lets put this shit in the database.

$qry = "INSERT INTO `inventory_items` (`name`, `amount`, `company`) VALUES ('" . $name . "', '" . $amount . "', '" . $company . "')";
mysql_query($qry)or die(mysql_error());

//get the id of the newest row.

$qry = "SELECT * FROM `inventory_items` WHERE `id`>'0' ORDER BY `id` DESC LIMIT 1";
$result = mysql_query($qry)or die(mysql_error());

while ($row = mysql_fetch_assoc($result)){
	$id = $row['id'];
}

//we should have it now.

//lets add an alarm now.
$qry = "INSERT INTO `inventory_alarm` (`c_id`, `i_id`, `alarm`) VALUES ('" . $company . "', '" . $id . "', '" . $alarm . "')";
mysql_query($qry)or die(mysql_error());

//everything should be inserted correctly now!
//lets take them back to the inventory page. 

mysql_close();
header('Location: inventory.php');
exit();


?>