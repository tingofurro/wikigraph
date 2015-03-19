<?php
/*
OBJECTIVE:
From the HTML pages in the data folder, extract <a> links, and write working edges to the database

CURRENT STATUS:
652.047 links
*/
include('../dbco.php');
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
	$html = file_get_contents('../data/'.$re['id'].'.txt');
	$dom = new DOMDocument;
	@$dom->loadHTML($html); $pageNames = array();
	foreach ($dom->getElementsByTagName('a') as $link) {
		$href = $link->getAttribute('href');
		if(strpos($href, "/wiki/") !== false) { // this is an interesting link
			$h = str_replace("/wiki/", "", $href); $cleanName = urldecode(utf8_encode(($h)));
			$toks = explode("#", $cleanName); $cleanName = mysql_real_escape_string($toks[0]);
			$try = explode(":", $cleanName);
			if(in_array($try[0], array("Help", "Wikipedia", "Category", "Special", "Template", "Portal", "File", "Template_talk"))) {}
			else if(!in_array($cleanName, $pageNames)) {array_push($pageNames, "\"".$cleanName."\"");}
		}
	}
	$find = mysql_query("SELECT * FROM wg_page WHERE name IN (".implode(", ", $pageNames).")");
	while ($found = mysql_fetch_array($find)) {array_push($values, "(NULL, '".$re['id']."', '".$found['id']."', '0')");}

	if(count($values) > 200) {
		mysql_query("INSERT INTO `wg_link` (`id`, `from`, `to`, `type`) VALUES ".implode(",", $values).";");
		$values = array();
	}
	array_push($addVisited, $re['id']);
	if(count($addVisited) > 200) {
		mysql_query("UPDATE wg_page SET visited=1 WHERE id IN (".implode(", ", $addVisited).")");
		$addVisited = array();
	}
}
// if we get there, the program is done running
mysql_query("ALTER TABLE wg_page DROP COLUMN visited");
?>