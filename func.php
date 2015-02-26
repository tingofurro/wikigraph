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
function topMenu($root) {
?>
	<link rel="stylesheet" type="text/css" href="<?php echo $root;?>/css/topMenu.css">
	<div id="topMenu">
		<a href="<?php echo $root;?>"><img src="<?php echo $root;?>/images/logo.png" alt="WikiGraph" id="logo" /></a>
		<a href="<?php echo $root."category";?>"><div class="menuItem firstItem">Category Tree</div></a>
		<a href="<?php echo $root."fields";?>"><div class="menuItem">Math fields</div></a>
		<a href="<?php echo $root."explore";?>"><div class="menuItem">Explore the Set</div></a>
		<a href="<?php echo $root."clustering";?>"><div class="menuItem">Clustering Work</div></a>
	</div>
<?php
}
function getKillList() {
	include_once('func.php');
	$file = file_get_contents("txt/killList.txt");
	$killList = explode("[]", $file);
	foreach ($killList as $i => $ele) { $killList[$i] = strToWiki($ele);}
	return $killList;
}
function getRoot() {
	$link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	if(strpos($link, "/Wikigraph")) {$pos = strpos($link, "/Wikigraph"); $root = substr($link, 0, ($pos+10))."/";}
	if(strpos($link, "/wikigraph")) {$pos = strpos($link, "/wikigraph"); $root = substr($link, 0, ($pos+10))."/";}
	return $root;
}
function cat2OldCat($cat) {
	$cat2OldCat = array(1,        2,          4,            5,          8,               13,            14,         15,             16,      18,              19,             20,           24);
	return $cat2OldCat[$cat];
}
function cleanFieldList() {
	return array('algebra', 'analysis', 'arithmetic', 'calculus', 'discrete_math', 'game_theory', 'geometry', 'graph_theory', 'logic', 'number_theory', 'order_theory', 'prob_stats', 'topology');
}
function cleanFieldListName() {
	return array('Algebra', 'Analysis', 'Arithmetic', 'Calculus', 'Discrete Mathematics', 'Game Theory', 'Geometry', 'Graph Theory', 'Logic', 'Number THeory', 'Order Theory', 'Probability & Statistics', 'Topology');
}
?>