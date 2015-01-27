<?php
function strToWiki($oldStr) {
	return str_replace(" ", "_", $oldStr);
}
function wikiToName($oldStr) {
	return urldecode(str_replace("_", " ", $oldStr));
}
function whereField($field) {
	return "(fields='$field' OR fields LIKE '%|".$field."|%' OR fields LIKE '".$field."|%' OR fields LIKE '%|".$field."')";
}
function topMenu() {
?>
	<link rel="stylesheet" type="text/css" href="css/topMenu.css">
	<div id="topMenu">
		<a href="index.php"><img src="images/logo.png" alt="WikiGraph" id="logo" /></a>
		<a href="tree.php"><div class="menuItem">Tree Display</div></a>
		<a href="nbInField.php"><div class="menuItem">Different Counts</div></a>
	</div>
<?php
}
function getKillList() {
	include_once('func.php');
	$file = file_get_contents("killList.txt");
	$killList = explode("[]", $file);
	foreach ($killList as $i => $ele) { $killList[$i] = strToWiki($ele);}
	return $killList;
}
?>