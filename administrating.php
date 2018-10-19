<?php
session_start();

require_once('inc.functions.php');
secure();
adminSecure();

if (isset($_GET['addcompany']) && isset($_GET['userid'])){
	//we are going to add a company to this userid.
	
	connectToSQL();
	$company_id = mysql_real_escape_string($_POST['company']);
	$userid = mysql_real_escape_string($_GET['userid']);
	$qry = "INSERT INTO `company_cross` (`c_id`, `u_id`) VALUES ('" . $company_id . "', '" . $userid . "')";
	mysql_query($qry)or die(mysql_error());
	
	
	
	mysql_close();
	
	header('Location: admin.php');
	exit();
}elseif ($_GET['approve'] == 1){
	//change the approval rating of the userid in the field.
	connectToSQL();
	$userid = mysql_real_escape_string($_GET['userid']);
	$qry = "SELECT `approved` FROM `users` WHERE `id`='" . $userid ."'";
	$result = mysql_query($qry)or die(mysql_error());
	
	while ($row = mysql_fetch_assoc($result)){
		$approved = $row['approved'];
	}
	
	if ($approved == 0){
		$approved = 1 ;
	}elseif ($approved == 1){
		$approved = 3 ;
	}elseif ($approved == 3){
		$approved = 1 ;
	}else{
		$approved = 1 ;
	}
	
	$qry = "UPDATE `users` SET `approved`='" . $approved . "' WHERE `id`='" . $userid . "'";
	mysql_query($qry);
	
	mysql_close();
	header('Location: admin.php');
	exit();
}elseif ($_GET['levelup'] == 1){
	//change the approval rating of the userid in the field.
	connectToSQL();
	$userid = mysql_real_escape_string($_GET['userid']);
	$qry = "SELECT `level` FROM `users` WHERE `id`='" . $userid ."'";
	$result = mysql_query($qry)or die(mysql_error());
	
	while ($row = mysql_fetch_assoc($result)){
		$approved = $row['level'];
	}
	
	if ($approved == 0){
		$approved = 2 ;
	}elseif ($approved == 2){
		$approved = 1 ;
	}elseif ($approved == 1){
		$approved = 0 ;
	}else{
		$approved = 0 ;
	}
	
	$qry = "UPDATE `users` SET `level`='" . $approved . "' WHERE `id`='" . $userid . "'";
	mysql_query($qry);
	
	//echo $approved ;
	
	mysql_close();
	header('Location: admin.php');
	exit();
}
?>