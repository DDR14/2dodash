<?php
//this page actually lay's out the tags to be printed.
//we use css printer page breaks to force pages to print in the correct spot.


//var_dump($_POST);
//die();


//refer to test.php for successful dogtag implementation.
//settings must be correct in firefox for this print job to work correctly.
mysql_connect("tododash.db.2010831.hostedresource.com", "tododash", "LedG1090#");
mysql_select_db("tododash");
//here we are uploading stuff from the previous page
require('inc.functions.php');  //closes mysql?

//function to rename it
function newName($input){
	$rand = rand(1, 999999999) ;
	$comb = $rand . $input ;
	$newName = substr(md5($comb), 0 , 10);
	$newName = $newName . "." . $input ;
	return $newName ;
}


//////////////////////////////////////////////////
/////////////////////DOG TAGS/////////////////////
//////////////////////////////////////////////////
$needBack = false ;

//array for our cookie later
$dogtags = array();
$dogtagsRef = array();
$dogtagsBack = array();
//check to see how many qty's were submit.
//we should have a lovely POST with all the information we need.
$count = 0 ;
$go = true ;
//var_dump($_POST);
//echo "<br />";
while ($go === true){
	$count++;
	if (isset($_POST['dogTag-' . $count . '-qty'])){
		//move this image
		$newName = newName($_FILES["dogTag-" . $count . "-img"]["name"]);
		move_uploaded_file($_FILES["dogTag-" . $count . "-img"]["tmp_name"], "print/" . $newName);
		
		//set a cookie with the image location
		//since these are guaranteed dogtags we'll just set a csv cookie that says image then qty then image then qty
		
		
		array_push($dogtags, "print/" . $newName);
		array_push($dogtags, $_POST["dogTag-" . $count . "-qty"]);
		
		
		//check to see if this has anything on the backside
		//if it does put it in the backside reference if not put null so it can easily fill those spaces.
		if (isset($_POST['dogTag-' . $count . '-back'])){
			?>
			<script>
			alert('we are setting arrays for the back side');
			</script>
			<?php
			$needBack = true ;
			//do it
			array_push($dogtagsBack, $_POST["dogTag-" . $count . "-back"]);
			array_push($dogtags, $_POST["dogTag-" . $count . "-qty"]);
			
			//array_push the reference array incase there is anything that needs to go on the backside so that we cant print on it.
			array_push($dogtagsRef, "print/" . $newName);
			array_push($dogtagsRef, $_POST["dogTag-" . $count . "-qty"]);
		
		}else{
			array_push($dogtagsBack, 0);
			array_push($dogtags, $_POST["dogTag-" . $count . "-qty"]);
		}
		
		//echo "<br />" . $_POST['dogTag-' . $count . '-qty'] ;
		//set a cookie with the quantity.
	}else{
		$go = false ;
		//we're done!
	}
}

foreach ($dogtags as $p){
	if (isset($_COOKIE['dogtags'])){
		$_COOKIE['dogtags'] = $_COOKIE['dogtags'] . ", " . $p ;
	}else{
		$_COOKIE['dogtags'] = $p ;
	}
}

///////////////////////////////////////////////////////////
/////////////////////DODG TAGS END/////////////////////////
///////////////////////////////////////////////////////////


//////////////////////////////////////////////////
/////////////////////BAG TAGS/////////////////////
//////////////////////////////////////////////////


//array for our cookie later
$bagtags = array();
//check to see how many qty's were submit.
//we should have a lovely POST with all the information we need.
$count = 0 ;
$go = true ;
//var_dump($_POST);
//echo "<br />";
while ($go === true){
	$count++;
	if (isset($_POST['bagTag-' . $count . '-qty'])){
		//move this image
		$newName = newName($_FILES["bagTag-" . $count . "-img"]["name"]);
		move_uploaded_file($_FILES["bagTag-" . $count . "-img"]["tmp_name"], "print/" . $newName);
		
		//set a cookie with the image location
		//since these are guaranteed dogtags we'll just set a csv cookie that says image then qty then image then qty
		array_push($bagtags, "print/" . $newName);
		array_push($bagtags, $_POST["bagTag-" . $count . "-qty"]);
		//echo "<br />" . $_POST['dogTag-' . $count . '-qty'] ;
		//set a cookie with the quantity.
	}else{
		$go = false ;
		//we're done!
	}
}

foreach ($bagtags as $p){
	if (isset($_COOKIE['bagtags'])){
		$_COOKIE['bagtags'] = $_COOKIE['bagtags'] . ", " . $p ;
	}else{
		$_COOKIE['bagtags'] = $p ;
	}
}

///////////////////////////////////////////////////////////
/////////////////////BAG TAGS END//////////////////////////
///////////////////////////////////////////////////////////


//mysql_close();



/***********************************************************************************************
LAYOUT STARTS HERE!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
************************************************************************************************/




//////////////////////////////////////////////////
/////////////////////DOG TAGS/////////////////////
//////////////////////////////////////////////////

//12 dogtags in a row
//5 dogtags before page break
//find all the dog tags and lay them out


$layout = $_COOKIE['dogtags'] ;
$layout = explode(", ", $layout);

//var_dump($layout);

$end = count($layout) / 2 ;

$count = 0 ;
$row = 1 ;
$col = 1 ; 
while ($count <= $end){
	$img = $layout[0] ;
	array_shift($layout);
	$qty = $layout[0] ;
	array_shift($layout);
	
	$counter = 0 ;
	while ($counter < $qty){
		$counter++;
		//printing each image while keeping track of col and row
		
		
		if ($col === 5 && $row === 1){
			//remove print/ from $img
			$img2 = substr($img, 6) ;
			echo "<img src=\"watermark.php?top=1&filename=" . $img2 . "\" height=\"210px\" width=\"125px\" />";
		}elseif($col === 11 && $row === 1){
			//remove print/ from $img
			$img2 = substr($img, 6) ;
			echo "<img src=\"watermark.php?top=1&filename=" . $img2 . "\" height=\"210px\" width=\"125px\" />";
		}elseif ($col === 2 && $row === 5){
			//remove print/ from $img
			$img2 = substr($img, 6) ;
			echo "<img src=\"watermark.php?top=0&filename=" . $img2 . "\" height=\"210px\" width=\"125px\" />";
		}elseif($col === 8 && $row === 5){
			//remove print/ from $img
			$img2 = substr($img, 6) ;
			echo "<img src=\"watermark.php?top=0&filename=" . $img2 . "\" height=\"210px\" width=\"125px\" />";
		}else{
			echo "<img src=\"" . $img . "\" height=\"210px\" width=\"125px\" />";
		}
		
		
		$col++;
		if ($col == 13){
			$col = 1 ;
			
			if ($row == 5){
				echo "<DIV style=\"page-break-after:always\"></DIV>";
				$row = 0 ;
			}
			echo "<br />";
			$row++;
		}
		
		
	}
	
	$count++;
}
///////////////////////////////////////////////////////////
/////////////////////DODG TAGS END/////////////////////////
///////////////////////////////////////////////////////////



//////////////////////////////////////////////////
/////////////////////BAG TAGS/////////////////////
//////////////////////////////////////////////////

//12 dogtags in a row
//5 dogtags before page break
//find all the dog tags and lay them out


$layout = $_COOKIE['bagtags'] ;
$layout = explode(", ", $layout);

//var_dump($layout);

$end = count($layout) / 2 ;

$count = 0 ;
$row = 1 ;
$col = 1 ; 
while ($count <= $end){
	$img = $layout[0] ;
	array_shift($layout);
	$qty = $layout[0] ;
	array_shift($layout);
	
	$counter = 0 ;
	$row = 1 ;
	while ($counter < $qty){
		$counter++;
		//printing each image while keeping track of col and row
		//echo "<img src=\"" . $img . "\" height=\"210px\" width=\"125px\" />"; //dog tag sizing
		
		if ($row == 3){
			echo "<img src=\"" . $img . "\" height=\"400px\" width=\"309px\" />";
		}elseif ($row == 5){
			echo "<img src=\"" . $img . "\" height=\"400px\" width=\"309px\" />";
		}else{
			echo "<img src=\"" . $img . "\" height=\"400px\" width=\"310px\" />";
		}
		
		
		if ($row == 5){
			$row = 0 ;
			echo "<br />";
		}
		$row++;
		
		//need to find a different way to do this.  because row three needs to be completely different
		//as well as basically start from the beginning on the next page.
		//echo "<DIV style=\"page-break-after:always\"></DIV>";
		
	}
	
	$count++;
}
///////////////////////////////////////////////////////////
/////////////////////BAG TAGS END//////////////////////////
///////////////////////////////////////////////////////////


//check to see if we have a dogtagRef array - if we do serialize it cookie it and send it to the next page.
if ($needBack == true){
	?>
	<script>
	alert('needBack is running');
	</script>
	<?php
	//do stuff
	$dogtagsRef = serialize($dogtagsRef) ;
	$dogtagsBack = serialize($dogtagsBack) ;
	$_COOKIE['dogtagsRef'] = $dogtagsRef ;
	$_COOKIE['dogtagsBack'] = $dogtagsBack ;
	
	
	//redirect to print back page.
	?>
	<script type="text/javascript">
	<!--
	window.location = "autoprint-back.php"
	//-->
	</script>
	<?php
}


?>