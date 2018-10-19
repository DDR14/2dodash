<?php
session_start();
//this page will build our relational database.
//it will find each keyword in the title and the description of the task.
//The title will be worth 70% weight with the description will count for 25% with following messages filling the void.

//we need to look at the content of the title. removing the's for's and's if's when's but's a's and any other common word we can think of.
//the weight will be base 100 floats.  a weight of 100% will be the only word in my title is "motherboard" 
//common words still count towards your percent total.  Tie's will be broken by newest winning.


require_once('inc.functions.php');


connectToSQL();

$qry = "SELECT * FROM `tickets`";
$result = mysql_query($qry)or die(mysql_error());

while ($row = mysql_fetch_assoc($result)){
	//find out what words are in it and see if those words exist in the keywords database.
	$title_words = explode(" ", $row['title']) ;
	$desc_words = explode(" ", $row['desc']) ;
	
	//we need to find all the messages of this ticket and array_merge all the words in them.
	$qry2 = "SELECT * FROM `ticket_messages` WHERE `ticket_id`='" . $row['id'] . "'";
	$result2 = mysql_query($qry2) ;
	$message_words = array();
	while ($row2 = mysql_fetch_assoc($result2)){
		$message = explode(" ", $row2['message']) ;
		array_merge($message_words, $message);
	}
	
	//we should have all the words for this ticket inside of the database now.
	//iterate through the three arrays doing a couple of things
		//check to see if the keyword exists in the database.
		//give that keyword a score in an array

	//how many DIFFERENT words are in each array?
	$count = 0 ;
	$title_scores = array();
	foreach ($title_words as $p){
		if (empty($title_scores[$p])){
			$title_scores[$p] = 1 ;
			$count++;
		}else{
			$title_scores[$p]++;
		}
	}
	
	//check to see if these keywords exist in the database if they do not then add them
	//immediately after add the score to the rank database
	
	foreach ($title_scores as $key => $value){
		//check to see if this keywords is in the database.  if it isn't add it.
		$qry2 = "SELECT * FROM `keywords` WHERE `word`='" . $key . "'";
		$result2 = mysql_query($qry2)or die(mysql_error());
		
		if (mysql_num_rows($result2) != 1){
			//add it to the database.
			$qry = "INSERT INTO `keywords` (`word`) VALUES ('" . $key . "')";
			mysql_query($qry)or die(mysql_error());
		}
		
		//figure out the percentage of the title it was.
		$thisKeyScore = $value / $count * 75 ;
		
		//figure out the id of our keyword
		$qry2 = "SELECT * FROM `keywords` WHERE `word`='" . $key . "'";
		$result2 = mysql_query($qry2)or die(mysql_error());
		
		while ($row2 = mysql_fetch_assoc($result2)){
			$keywordId = $row2['id'] ;
		}
		
		//check to see if this keyword relation for this ticket already exists.
		//if so update it else add it.
		$qry2 = "SELECT * FROM `rank` WHERE `keyword_id`='" . $keywordId . "' AND `ticket_id`='" . $row['id'] . "'";
		$result2 = mysql_query($qry2)or die(mysql_error());
		
		if (mysql_num_rows($result2) != 1){
			//add it
			$qry2 = "INSERT INTO `rank` (`keyword_id`, `ticket_id`, `rank`) VALUES ('" . $keywordId . "', '" . $row['id'] . "', '" . $thisKeyScore . "')";
			mysql_query($qry2)or die(mysql_error());
		}else{
			//update it
			$qry2 = "UPDATE `rank` SET `rank`='" . $thisKeyScore . "' WHERE `ticket_id`='" . $row['id'] . "' AND `keyword_id`='" . $keywordId . "'";
			mysql_query($qry2)or die(mysql_error());
		}
		
	}
	
	
	
}

mysql_close();
?>