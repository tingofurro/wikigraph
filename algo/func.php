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
function getTime() {
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	return ($mtime[1] + $mtime[0]);
}
function totalSum($PR) {
	$totScore = 0;
	foreach ($PR as $i => $score) $totScore += $score;
	return $totScore;
}
?>