<?php
/*
OBJECTIVE:
From the HTML pages in the data folder, extract <a> links, and write working edges to the database

CURRENT STATUS:
652.047 links
*/
include('../dbco.php');
include('extractor.php');
set_time_limit(4*3600);
$hasVisited = false;
$col = mysql_query("SHOW COLUMNS FROM wg_page;");
while($colu = mysql_fetch_array($col)) {
	if($colu[0] == 'visited') {$hasVisited = true;}
}
if(isset($_GET['fullReset']) OR !$hasVisited) {
	mysql_query("TRUNCATE wg_link");
	if(!$hasVisited) {mysql_query("ALTER TABLE wg_page ADD visited INT DEFAULT 0");}
	else {mysql_query("UPDATE wg_page SET visited=0");}
}
$r = mysql_query("SELECT * FROM wg_page WHERE visited=0 ORDER BY id");
$addVisited = array();
$values = array();
while($re = mysql_fetch_array($r)) {
	$pageNames = extractLinkArray($re['id']);
	// echo count($pageNames).'<br /><br />';

	$find = mysql_query("SELECT * FROM wg_page WHERE name IN (".'"'.implode('", "', $pageNames).'"'.")");
	echo "SELECT * FROM wg_page WHERE name IN (".'"'.implode('", "', $pageNames).'"'.")";
	while ($found = mysql_fetch_array($find)) {
		array_push($values, "(NULL, '".$re['id']."', '".$found['id']."')");
		array_splice($pageNames, array_search($re['name'], $pageNames), 1);
	}
	echo implode("<Br />", $pageNames);

	if(count($values) > 200) {
		mysql_query("INSERT INTO `wg_link` (`id`, `from`, `to`) VALUES ".implode(",", $values).";");
		$values = array();
	}
	array_push($addVisited, $re['id']);
	if(count($addVisited) > 200) {
		mysql_query("UPDATE wg_page SET visited=1 WHERE id IN (".implode(", ", $addVisited).")");
		$addVisited = array();
	}
}
if(count($values) > 0) { // the leftovers...
	mysql_query("INSERT INTO `wg_link` (`id`, `from`, `to`) VALUES ".implode(",", $values).";");
	$values = array();
}
// if we get there, the program is done running
// mysql_query("ALTER TABLE wg_page DROP COLUMN visited");
?>