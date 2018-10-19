<?php
session_start();
?>
<html>
<head>
<script>
var extra = '';
window.onload = function()
                {
                   
						extra = document.getElementById("diffTags").value ;
						//document.cookie="extra="+document.getElementById("diffTags").value ;
						//alert(extra);
					
                };


function addNew(){
	extra = parseInt(extra) + 1 ;
	//document.cookie="extra="+document.cookie("extra"
	document.getElementById("changeThis").innerHTML = document.getElementById("changeThis").innerHTML+"<br />EXTRA TAGS<br />img:<input type=\"file\" id=\"tag-"+extra+"-img\" /><br />qty:<input type=\"number\" id=\"tag-"+extra+"-qty\" value=\"0\" onchange=\"update();\" /><br /><br />";
	//alert(extra);
}

function update(){
	//we are only going to display how many pages we are printing.
	//figure out how many sheets we are currently printing.
	//60 dog tags.
	//alert('updated');
	
	//treat it as dogtags
	
	
}
</script>
</head>
<body>
<a id="information"></a>
<?php
//our objective here is to display an upload form for all of the pictures of the tags
//when the pictures are uploaded we will then remember where they are at
//after they are uploaded and logged in the database we will then 
echo "<form action=\"autoprint-layout.php\" method=\"POST\" enctype=\"multipart/form-data\">";
require('inc.functions.php');
company_db_connect($_GET['companyid']);

//we should really deal with all tags and implement hard code to stop printing of "off" stuff on the next page.


//find all the tags.
$qry = "SELECT * FROM `zen_orders_products` WHERE `orders_id` = '" . mysql_real_escape_string($_GET['orderid']) . "'";
$result = mysql_query($qry)or die(mysql_error());

$count = 0 ;
$dogTags = 0 ;
$otherTags = 0 ;
while ($row = mysql_fetch_assoc($result)){
	$count++;
	echo "<input type=\"checkbox\" />";
	echo $row['products_model'] ;
	echo " (" . $row['products_quantity'] . ")";
	echo "<br />" ;
	$last2 = substr($row['products_model'], -2);
	
	if ($last2 == "01" || "ag"){
		$dogTags = $dogTags + $row['products_quantity'];
		$dogTagsTypes = $dogTagsTypes + 1;
		//
	}elseif($last2 == "02"){
		//rec spirit tags
		$recSpiTags = $recSpiTags + $row['products_quantity'] ;
		$recSpiTagsTypes = $recSpiTagsTypes + 1 ;
		//
	}elseif($last2 == "03"){
		//Oval spirit tags
		$ovaSpiTags = $ovaSpiTags + $row['products_quantity'] ;
		$ovaSpiTagsTypes = $ovaSpiTagsTypes + 1 ;
		//
	}elseif($last2 == "04"){
		//Guitar shape tags
		$guiShaTags = $guiShaTags + $row['products_quantity'] ;
		$guiShaTagsTypes = $guiShaTagsTypes + 1 ;
		//
	}elseif($last2 == "05"){
		//Pencil Shape Tags
		$penShaTags = $penShaTags + $row['products_quantity'] ;
		$penShaTagsTypes = $penShaTagsTypes + 1 ;
		//
	}elseif($last2 == "06"){
		//Clover Shape Tags
		$cloShaTags = $cloShaTags + $row['products_quantity'] ;
		$cloShaTagsTypes = $cloShaTagsTypes + 1 ;
		//
	}elseif($last2 == "07"){
		//Circle Shape Tags
		$cirShaTags = $cirShaTags + $row['products_quantity'] ;
		$cirShaTagsTypes = $cirShaTagsTypes + 1 ;
		//
	}elseif($last2 == "08"){
		//Heart Shape tags
		$heaShaTags = $heaShaTags + $row['products_quantity'] ;
		$heaShaTagsTypes = $heaShaTagsTypes + 1 ;
	}elseif($last2 == "09"){
		//Paw shape tags
		$pawShaTags = $pasShaTags + $row['products_quantity'] ;
		$pawShaTagsTypes = $pasShaTagsTypes + 1 ;
	}elseif($last2 == "10"){
		//Small spirit tags
		$smaSpiTags = $smaSpiTags + $row['products_quantity'] ;
		$smaSpiTagsTypes = $smaSpiTagsTypes + $row['products_quantity'] ;
	}elseif($last2 == "11"){
		//star shape tags
		$staShaTags = $WstaShaTags + $row['products_quantity'] ;
		$staShaTagsTypes = $WstaShaTagsTypes + $row['products_quantity'] ;
	}elseif($last2 == "12"){
		//shoe tags
		$shoeTags = $shoeTags + $row['products_quantity'] ;
		$shoeTagsTypes = $shoeTagsTypes + $row['products_quantity'] ;
	}elseif($last2 == "13"){
		//sticker tags
		$stickTags = $stickTags + $row['products_quantity'] ;
		$stickTagsTypes = $stickTagsTypes + $row['products_quantity'] ;
	}elseif($last2 == "14"){
		//tattoo tags
		$tatTags = $tatTags + $row['products_quantity'] ;
		$tatTagsTypes = $tatTagsTypes + $row['products_quantity'] ;
	}elseif($last2 == "15"){
		//school cards
		$schCards = $schCards + $row['products_quantity'] ;
		$schCardsTypes = $schCardsTypes + $row['products_quantity'] ;
	}elseif($last2 == "16"){
		//promotion
		$promo = $promo + $row['products_quantity'] ;
		$promoTypes = $promoTypes + $row['products_quantity'] ;
	}elseif($last2 == "17"){
		//lanyards
		$lanyards = $lanyards + $row['products_quantity'] ;
		$lanyardsTypes = $lanyardsTypes + $row['products_quantity'] ;
	}elseif($last2 == "18"){
		//even tags
		$eventTags = $eventTags + $row['products_quantity'] ;
		$eventTagsTypes = $eventTagsTypes + $row['products_quantity'] ;
	}elseif($last2 == "19"){
		//bag tags
		$bagTags = $bagTags + $row['products_quantity'] ;
		$bagTagsTypes = $bagTagsTypes + $row['products_quantity'] ;
	}elseif($last2 == "20"){
		//who he hell knows
		$unknown = $unknown + $row['products_quantity'] ;
		$unknownTypes = $unknownTypes + $row['products_quantity'] ;
	}elseif($last2 == "21"){
		//accessories
		//we dont print these.
		$accessories = $accessories + $row['products_quantity'] ;
		$accessoriesTypes = $accessoriesTypes + $row['products_quantity'] ;
	}else{
		$unkownTypes = $unknownTypes + $row['products_quantity'];
		//execute some javascript here to erase everything and die.
		?>
		<script>
		document.body.innerHTML = "" ;
		alert('Something bad happened.  You should tell John what order this was so that he can figure out whatsup in the dilio.');
		</script>
		<?php
		
		die('o.O');
		
	}
	
	
	
}



//We've got the quantity plus how many different types of each one.
//we also haven't displayed any html yet except for opening tags so lets display a header.

echo "<form action=\"autoprint-layout.php\" method=\"POST\" enctype=\"multipart/form-data\" id=\"form1\" >";
echo "<br /><br />";
echo "<input type=\"submit\" name=\"submit\" value=\"Layout\" />";



//we should worry about extra's here.

//dogtags are 60 per page
//we need to find out if we are a multiple of 60


$multiple = 60 ;
$go = true ;
while ($go === true){
	if ($dogTags > $multiple){
		$multiple = $multiple + 60 ;
	}else{
		$go = false ;
		$extra = $multiple - $dogTags ;
	}
}

if ($dogTags > 0){
	//display the extra form
	//extra needed 
	echo "<br />Extra dogtags needed [" . $extra . "]";
}




if ($dogTagsTypes == 0){

}else{

	echo "<h1>";
	echo "Dog Tags";
	echo "</h1>";
	//$dogTags = $dogTags + $row['products_quantity'];
	//$dogTagsTypes = $dogTagsTypes + 1;
	$count = 0 ;
	while ($count < $dogTagsTypes){
		$count++;
		echo "<br /><input type=\"file\" name=\"dogTag-" . $count . "-img\" />";
		echo "<br /><input type=\"number\" name=\"dogTag-" . $count . "-qty\" />";
		//we need to know if this tag has a backside.
		//ibo logic backside is for business cards only
		
		//unplugged backside is for dogtags but hold off on programming this.
		
		
		//javascript dynamic function to change the form for this tag
		?>
		<script>
		function updateDogTag<?php echo $count ; ?>(){
			document.getElementById("dogTag<?php echo $count ; ?>").innerHTML = "";
			document.getElementById("HAdogTag<?php echo $count ; ?>").stle = "block" ;
			document.getElementById("dogTag<?php echo $count ; ?>").style.display = 'block' ;
		}
		</script>
		<?
		
		//image backside
		echo "<br /><a id=\"dogTag" . $count . "\"><input type=\"checkbox\" name=\"dogTag-" . $count . "-imgbs\" value=\"dogTag" . $count . "\" onchange=\"updateDogTag" . $count . "();\" />Check here if this tag needs a backside image.</a><hr>";
		echo "<a id=\"HAdogTag<?php echo $count ; ?>\" style=\"visibility:hidden;\">Backside of this tag </a><input type=\"file\" name=\"dogTag-<?php echo $count ; ?>-back\" style=\"display:none;\" id=\"dogTag<?php echo $count ; ?>\" />";
		
	}
	//ku channa, ku channa
	
}
if ($recSpiTagsTypes == 0){

}else{
	echo "<h1>";
	echo "Rectangle Spirit Tags";
	echo "</h1>";
	//$recSpiTags = $recSpiTags + $row['products_quantity'] ;
	//$recSpiTagsTypes = $recSpiTagsTypes + 1 ;
	$count = 0 ;
	while ($count < $recSpiTagsTypes){
		$count++;
		echo "<br /><input type=\"file\" name=\"recSpi-" . $count . "-img\" />";
		echo "<br /><input type=\"number\" name=\"recSpi-" . $count . "-qty\" />";
	}
}

if ($ovaSpiTagsTypes == 0){

}else{
	echo "<h1>";
	echo "Oval Spirit Tags";
	echo "</h1>";
	//$ovaSpiTags = $ovaSpiTags + $row['products_quantity'] ;
	//$ovaSpiTagsTypes = $ovaSpiTagsTypes + 1 ;
	$count = 0 ;
	while ($count < $ovaSpiTagsTypes){
		$count++;
		echo "<br /><input type=\"file\" name=\"ovaSpi-" . $count . "-img\" />";
		echo "<br /><input type=\"number\" name=\"ovaSpi-" . $count . "-qty\" />";
	}
}

if ($guiShaTagsTypes == 0){

}else{
	echo "<h1>";
	echo "Guitar Shape Tags";
	echo "</h1>";
	//$guiShaTags = $guiShaTags + $row['products_quantity'] ;
	//$guiShaTagsTypes = $guiShaTagsTypes + 1 ;
	$count = 0 ;
	while ($count < $guiShaTagsTypes){
		$count++;
		echo "<br /><input type=\"file\" name=\"guiSha-" . $count . "-img\" />";
		echo "<br /><input type=\"number\" name=\"guiSha-" . $count . "-qty\" />";
	}
}

if ($penShaTagsTypes == 0){

}else{
	echo "<h1>";
	echo "Pencil Shape Tags";
	echo "</h1>";
	//$penShaTags = $penShaTags + $row['products_quantity'] ;
	//$penShaTagsTypes = $penShaTagsTypes + 1 ;
	$count = 0 ;
	while ($count < $penShaTagsTypes){
		$count++;
		echo "<br /><input type=\"file\" name=\"penSha-" . $count . "-img\" />";
		echo "<br /><input type=\"number\" name=\"penSha-" . $count . "-qty\" />";
	}
}

if ($cloShaTagsTypes == 0){

}else{
	echo "<h1>";
	echo "Clover Shape Tags";
	echo "</h1>";
	//$cloShaTags = $cloShaTags + $row['products_quantity'] ;
	//$cloShaTagsTypes = $cloShaTagsTypes + 1 ;
	$count = 0 ;
	while ($count < $cloShaTagsTypes){
		$count++;
		echo "<br /><input type=\"file\" name=\"cloSha-" . $count . "-img\" />";
		echo "<br /><input type=\"number\" name=\"cloSha-" . $count . "-qty\" />";
	}
}

if ($cirShaTagsTypes == 0){

}else{
	echo "<h1>";
	echo "Circle Shape Tags";
	echo "</h1>";
	//$cirShaTags = $cirShaTags + $row['products_quantity'] ;
	//$cirShaTagsTypes = $cirShaTagsTypes + 1 ;
	$count = 0 ;
	while ($count < $cirShaTagsTypes){
		$count++;
		echo "<br /><input type=\"file\" name=\"cirSha-" . $count . "-img\" />";
		echo "<br /><input type=\"number\" name=\"cirSha-" . $count . "-qty\" />";
	}
}

if ($heaShaTagsTypes == 0){

}else{
	echo "<h1>";
	echo "Heart Shape Tags";
	echo "</h1>";
	//$heaShaTags = $heaShaTags + $row['products_quantity'] ;
	//$heaShaTagsTypes = $heaShaTagsTypes + 1 ;
	$count = 0 ;
	while ($count < $heaShaTagsTypes){
		$count++;
		echo "<br /><input type=\"file\" name=\"heaSha-" . $count . "-img\" />";
		echo "<br /><input type=\"number\" name=\"heaSha-" . $count . "-qty\" />";
	}
}

if ($pawShaTagsTypes == 0){

}else{
	echo "<h1>";
	echo "Paw Shape Tags";
	echo "</h1>";
	//$pawShaTags = $pasShaTags + $row['products_quantity'] ;
	//$pawShaTagsTypes = $pasShaTagsTypes + 1 ;
	$count = 0 ;
	while ($count < $pawShaTagsTypes){
		$count++;
		echo "<br /><input type=\"file\" name=\"pawSha-" . $count . "-img\" />";
		echo "<br /><input type=\"number\" name=\"pawSha-" . $count . "-qty\" />";
	}
}

if ($smaSpiTagsTypes == 0){

}else{
	echo "<h1>";
	echo "Small Spirit Tags";
	echo "</h1>";
	//$smaSpiTags = $smaSpiTags + $row['products_quantity'] ;
	//$smaSpiTagsTypes = $smaSpiTagsTypes + $row['products_quantity'] ;
	$count = 0 ;
	while ($count < $smaSpiTagsTypes){
		$count++;
		echo "<br /><input type=\"file\" name=\"smaSpi-" . $count . "-img\" />";
		echo "<br /><input type=\"number\" name=\"smaSpi-" . $count . "-qty\" />";
	}
}

if ($staShaTagsTypes == 0){

}else{
	echo "<h1>";
	echo "Star Shape Tags";
	echo "</h1>";
	//$staShaTags = $WstaShaTags + $row['products_quantity'] ;
	//$staShaTagsTypes = $WstaShaTagsTypes + $row['products_quantity'] ;
	$count = 0 ;
	while ($count < $staShaTagsTypes){
		$count++;
		echo "<br /><input type=\"file\" name=\"staSha-" . $count . "-img\" />";
		echo "<br /><input type=\"number\" name=\"staSha-" . $count . "-qty\" />";
	}
}

if ($shoeTagsTypes == 0){

}else{
	echo "<h1>";
	echo "Shoe Tags";
	echo "</h1>";
	//$shoeTags = $shoeTags + $row['products_quantity'] ;
	//$shoeTagsTypes = $shoeTagsTypes + $row['products_quantity'] ;
	$count = 0 ;
	while ($count < $shoeTagsTypes){
		$count++;
		echo "<br /><input type=\"file\" name=\"shoeTag-" . $count . "-img\" />";
		echo "<br /><input type=\"number\" name=\"shoeTag-" . $count . "-qty\" />";
	}
}


if ($stickTagsTypes == 0){

}else{
	echo "<h1>";
	echo "Sticker Tags";
	echo "</h1>";
	//$stickTags = $stickTags + $row['products_quantity'] ;
	//$stickTagsTypes = $stickTagsTypes + $row['products_quantity'] ;
	$count = 0 ;
	while ($count < $stickTagsTypes){
		$count++;
		echo "<br /><input type=\"file\" name=\"stickTag-" . $count . "-img\" />";
		echo "<br /><input type=\"number\" name=\"stickTag-" . $count . "-qty\" />";
	}
}

if ($tatTagsTypes == 0){

}else{
	echo "<h1>";
	echo "Tattoo Tags";
	echo "</h1>";
	//$tatTags = $tatTags + $row['products_quantity'] ;
	//$tatTagsTypes = $tatTagsTypes + $row['products_quantity'] ;
	$count = 0 ;
	while ($count < $tatTagsTypes){
		$count++;
		echo "<br /><input type=\"file\" name=\"tatTag-" . $count . "-img\" />";
		echo "<br /><input type=\"number\" name=\"tatTag-" . $count . "-qty\" />";
	}
}

if ($schCardsTypes == 0){

}else{
	echo "<h1>";
	echo "School Cards";
	echo "</h1>";
	//$schCards = $schCards + $row['products_quantity'] ;
	//$schCardsTypes = $schCardsTypes + $row['products_quantity'] ;
	$count = 0 ;
	while ($count < $schCardsTypes){
		$count++;
		echo "<br /><input type=\"file\" name=\"schCard-" . $count . "-img\" />";
		echo "<br /><input type=\"number\" name=\"schCard-" . $count . "-qty\" />";
	}
}

if ($promoTypes == 0){

}else{
	echo "<h1>";
	echo "Promotion";
	echo "</h1>";
	//$promo = $promo + $row['products_quantity'] ;
	//$promoTypes = $promoTypes + $row['products_quantity'] ;
	$count = 0 ;
	while ($count < $promoTypes){
		$count++;
		echo "<br /><input type=\"file\" name=\"promo-" . $count . "-img\" />";
		echo "<br /><input type=\"number\" name=\"promo-" . $count . "-qty\" />";
	}
}

if ($lanyardsTypes == 0){

}else{
	echo "<h1>";
	echo "Lanyards";
	echo "</h1>";
	//$lanyards = $lanyards + $row['products_quantity'] ;
	//$lanyardsTypes = $lanyardsTypes + $row['products_quantity'] ;
	$count = 0 ;
	while ($count < $lanyardsTypes){
		$count++;
		echo "<br /><input type=\"file\" name=\"lanyard-" . $count . "-img\" />";
		echo "<br /><input type=\"number\" name=\"lanyard-" . $count . "-qty\" />";
	}
}

if ($eventTagsTypes == 0){

}else{
	echo "<h1>";
	echo "Event Tags";
	echo "</h1>";
	//$eventTags = $eventTags + $row['products_quantity'] ;
	//$eventTagsTypes = $eventTagsTypes + $row['products_quantity'] ;
	$count = 0 ;
	while ($count < $eventTagsTypes){
		$count++;
		echo "<br /><input type=\"file\" name=\"eventTag-" . $count . "-img\" />";
		echo "<br /><input type=\"number\" name=\"eventTag-" . $count . "-qty\" />";
	}
}



$multiple = 13 ;
$go = true ;
while ($go === true){
	if ($bagTags > $multiple){
		$multiple = $multiple + 13 ;
	}else{
		$go = false ;
		$extra = $multiple - $bagTags ;
	}
}

if ($bagTags > 0){
	//display the extra form
	//extra needed 
	echo "<br />Extra bagtags needed [" . $extra . "]";
}




if ($bagTagsTypes == 0){

}else{

	echo "<h1>";
	echo "Bag Tags";
	echo "</h1>";
	//$dogTags = $dogTags + $row['products_quantity'];
	//$dogTagsTypes = $dogTagsTypes + 1;
	$count = 0 ;
	while ($count < $bagTagsTypes){
		$count++;
		echo "<br /><input type=\"file\" name=\"bagTag-" . $count . "-img\" />";
		echo "<br /><input type=\"number\" name=\"bagTag-" . $count . "-qty\" />";
		//we need to know if this tag has a backside.
		//ibo logic backside is for business cards only
		//unplugged backside is for dogtags but hold off on programming this.
		//javascript dynamic function to change the form for this tag
		?>
		<br /><input type="radio" name="type" value="regular" />Regular
		<br /><input type="radio" name="type" value="megaphone" onclick="this.checked=false;alert('Sorry, this option is not available!')" />Megaphone
		<br /><input type="radio" name="type" value="acorn" onclick="this.checked=false;alert('Sorry, this option is not available!')" />Acorn
		<script>
		function updateBagTag<?php echo $count ; ?>(){
			document.getElementById("bagTag<?php echo $count ; ?>").innerHTML = "<br /><table border=\"1\"><tr><td>Backside of this tag</td><td><input type=\"file\" name=\"bagTag-<?php echo $count ; ?>-back\" /></td></tr></table>";
		}
		</script>
		<?
		
		//image backside
		//echo "<br /><a id=\"dogTag" . $count . "\"><input type=\"checkbox\" name=\"dogTag-" . $count . "-imgbs\" value=\"dogTag" . $count . "\" onchange=\"updateDogTag" . $count . "();\" />Check here if this tag needs a backside image.</a><hr>";
		////disabled for now - until backside is worked out good enough.
	}
	//ku channa, ku channa
	
}



if ($accessoriesTypes == 0){

}else{
	echo "<h1>";
	echo "Accessories";
	echo "</h1>";
	//$accessories = $accessories + $row['products_quantity'] ;
	//$accessoriesTypes = $accessoriesTypes + $row['products_quantity'] ;
	$count = 0 ;
	while ($count < $accessoriesTypes){
		$count++;
		echo "<br />Accessory" . $count ;
	}
}
?>
</form>

</body>
</html>