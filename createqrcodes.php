<?php
session_start();
?>
<html>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" ></script>
<script>
$(window).load(function(){
	$("#loading").hide();
})
</script>
</head>
<style type="text/css">
#loading {  
    position:absolute;  
    width:300px;  
    top:0px;  
    left:50%;  
    margin-left:-150px;  
    text-align:center;  
    padding:7px 0 0 0;  
    font:bold 11px Arial, Helvetica, sans-serif;  
} 
</style>
<div id="loading">
<center>
Creating QR codes please wait...
<img src="http://images.wikia.com/epicrapbattlesofhistory/images/archive/4/42/20130821004322!Loading.gif" alt="loading..." />
</center>
</div>

<?php

	set_time_limit(0);
	ini_set('memory_limit','1024M');
	
	require('inc.functions.php');
	//secure();
	//expect that the orderid is givin in the get field
	
	$new_o_id = $_GET['orderid'] ;
	$tag_ids = array();
	$tag_locs = array();
	//we need to create the new qr codes for this order here.
	//we will just save them in qrcodes/$new_o_id
	
	//does that folder exists?
	if (is_dir("qrcodes/" . $new_o_id)){
		//it already exists dont make a directory
	}else{
		//lets create the directory and fill it with qr codes
		mkdir("qrcodes/" . $new_o_id);
		chmod("qrcodes/" . $new_o_id, 0777);
		
	}
	
	//fill our new directory with QR CODES
	//connect to sql
	company_db_connect(6);
	//how many did they order?
	$qry = "SELECT * FROM `orders` WHERE `id`='" . $new_o_id . "'";
	$result = mysql_query($qry)or die(mysql_error());
	
	//$qty = "0";
	while ($row = mysql_fetch_assoc($result)){
		$qty = $row['tags'];
	}

	//$qty is how many codes we need to make.	
	
	$userid = mysql_real_escape_string($_COOKIE['userid']);
	
	//lets get the last serial from the users row in the users table.
	/*
	$qry = "SELECT `id`, `last_serial` FROM `users` WHERE `id`='" . $userid . "'";       //doesn't matter we will always have newer and higher order id's we'll start from 1
	$result = mysql_query($qry)or die(mysql_error());
	
	while ($row = mysql_fetch_assoc($result)){
		$last_serial = $row['last_serial'];
	}
	*/
	
	
	//$qty = 60 ; //simplify it to one page and decrease script run time.
	
	
	//we have the qty we need to make AND the last serial number that user has used.
	
	//lets create qr codes!
	
	//We need to make a total of $qty qr codes in batches of 60
	
	//how many pages do we need to make?
	$pages = $qty / 60 ;
	
	//always need a full page so lets round it up.
	$pages = ceil($pages);
	//echo $pages. "<br />" ;
	
	//awsome we need $pages of qrcodes.
	//lets create the page directories inside of -- we dont need to create directories.  Just 6 different dataset files.
	
	//lets create all of the qrcodes now.
	//screw it lets do $pages * 60 and get an exact number
	
	$qty = $pages * 60 ;
	$count = 0 ;
	while ($count < $qty)
	{	
		$count++;
		//echo "First while: " . $count . "<br />";
		//require QR code lib
		require_once('phpqrcode/qrlib.php');
		//create qr codes and place them in the correct folder.
		//we also need to add that card to the database so scanner things can happen.
		//QRcode::png('http://www.ibologic.com/handler.php?id=' . $userid . '&c=' . $last_serial, 'qrcodes/' . $new_o_id . "/" . $count . '.png', 'L', 4, 2);
		//lets just hash the whole thing
		$hash = md5($new_o_id . $count) ;
		$hash = substr($hash, 0, 2) ;
		QRcode::png('http://www.gowin.us/index.php?t=' . $new_o_id . "-" . $count . "-" . $hash, 'qrcodes/' . $new_o_id . "/" . $count . '.png', 'L', 4, 2);
		
		array_push($tag_ids, $new_o_id . "-" . $count . "-" . $hash);
		array_push($tag_locs, "C:\qrcodes\\" . $new_o_id . "\\" . $count . ".png");
		
		//this qrcode should have been created.
		//lets add a new row into the cards table.
		
		$qry = "INSERT INTO `tags` (`order_id`, `tag_id`) VALUES ('" . $new_o_id . "', '" . $count . "')";
		mysql_query($qry)or die(mysql_error());
		
		
		
	}
	
	//create the dataset text files.
	//we need $pages of them.
	
	//we've got a huge array and we can force array[0] to be the value we want by using array_shift()
	
	$count = 0 ;
	while ($count < $pages){
		$count++;
		//echo "Second while: " . $count . "<br />";
		//this will run for each page.
		
		//while statement for the individual pages.
		$pageCount = 0 ;
		
		$tval = "";
		$qval = "";
		while ($pageCount < 60){
			$pageCount++;
			echo $pageCount . "<br />";
			$tval .= $tag_ids[0] . ", ";
			array_shift($tag_ids);
			$qval .= $tag_locs[0] . ", ";
			array_shift($tag_locs);
			
			
		}
		$content = "tvar1, tvar2, tvar3, tvar4, tvar5, tvar6, tvar7, tvar8, tvar9, tvar10, tvar11, tvar12, tvar13, tvar14, tvar15, tvar16, tvar17, tvar18, tvar19, tvar20, tvar21, tvar22, tvar23, tvar24, tvar25, tvar26, tvar27, tvar28, tvar29, tvar30, tvar31, tvar32, tvar33, tvar34, tvar35, tvar36, tvar37, tvar38, tvar39, tvar40, tvar41, tvar42, tvar43, tvar44, tvar45, tvar46, tvar47, tvar48, tvar49, tvar50, tvar51, tvar52, tvar53, tvar54, tvar55, tvar56, tvar57, tvar58, tvar59, tvar60, ";
		$content .= "qvar1, qvar2, qvar3, qvar4, qvar5, qvar6, qvar7, qvar8, qvar9, qvar10, qvar11, qvar12, qvar13, qvar14, qvar15, qvar16, qvar17, qvar18, qvar19, qvar20, qvar21, qvar22, qvar23, qvar24, qvar25, qvar26, qvar27, qvar28, qvar29, qvar30, qvar31, qvar32, qvar33, qvar34, qvar35, qvar36, qvar37, qvar38, qvar39, qvar40, qvar41, qvar42, qvar43, qvar44, qvar45, qvar46, qvar47, qvar48, qvar49, qvar50, qvar51, qvar52, qvar53, qvar54, qvar55, qvar56, qvar57, qvar58, qvar59, qvar60 ";
		$content .= "\n";
		$content .= $tval ;
		$content .= $qval ;
		
		$content = substr($content, 0, -2);
		
		
		$fp = fopen("qrcodes/" . $new_o_id . "/page" . $count . ".txt","wb");
		fwrite($fp,$content);
		fclose($fp);
	}
	
	
?>

success?
