<?php
function strToWiki($oldStr) {
	return str_replace(" ", "_", $oldStr);
}
function wikiToName($oldStr) {
	return urldecode(str_replace("_", " ", $oldStr));
}
function getKillList() {
	$file = file_get_contents("txt/killList.txt");
	$killList = explode("[]", $file);
	foreach ($killList as $i => $ele) { $killList[$i] = strToWiki($ele);}
	return $killList;
}
function whereField($field) {
	return "(fields='$field' OR fields LIKE '%|".$field."|%' OR fields LIKE '".$field."|%' OR fields LIKE '%|".$field."')";
}
function cat2OldCat($cat) {
	$cat2OldCat = array(1,        2,          4,            5,          8,               13,            14,         15,             16,      18,              19,             20,           23);
	return $cat2OldCat[$cat];
}
function cleanFieldList() {
	return array('algebra', 'analysis', 'arithmetic', 'calculus', 'discrete_math', 'game_theory', 'geometry', 'graph_theory', 'logic', 'number_theory', 'order_theory', 'prob_stats', 'topology');
}
function cleanFieldListName() {
	return array('Algebra', 'Analysis', 'Arithmetic', 'Calculus', 'Discrete Mathematics', 'Game Theory', 'Geometry', 'Graph Theory', 'Logic', 'Number Theory', 'Order Theory', 'Probability & Statistics', 'Topology');
}
?>