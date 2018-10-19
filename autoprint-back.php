<?php
//we really need to print the FRONT and BACK and not just the back of these things.

//okay we have our arrays lets do the same thing as the previous page, BUT every other page needs to be the backside.


//////////////////////////////////////////////////
/////////////////////DOG TAGS/////////////////////
//////////////////////////////////////////////////

//12 dogtags in a row
//5 dogtags before page break
//find all the dog tags and lay them out


//lets unserialize our cookies.
$dogtagsRef = unserialize($_COOKIE['dogtagsRef']) ;
$dogtagsBack = unserialize($_COOKIE['dogtagsBack']) ;

$front = explode(", ", $dogtagsRef);
$back = explode(", ", $dogtagsBack);


//var_dump($layout);

$end = count($front) ;

$count = 0 ;
$row = 1 ;
$col = 1 ; 
$page = 2 ;
while ($count <= $end){
	
	if ($page == 1){
		$page = 2 ;
	}else{
		$page = 1 ;
	}
	
	if ($page == 1){
		//work with front.
		$img = $front[0] ;
		array_shift($front);
		$qty = $front[0] ;
		array_shift($front);
	}else{
		//work with back
		$img = $back[0] ;
		array_shift($back);
		$qty = $back[0] ;
		array_shift($back);
	}
	
	
	
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
?>