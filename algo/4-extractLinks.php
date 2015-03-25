<?php
/*
OBJECTIVE:
From the HTML pages in the data folder, extract <a> links, and write working edges to the database

CURRENT STATUS:
630.827 links

*/
include('../dbco.php');
include('func.php');
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
$addVisited = array();
$values = array();
$p = mysql_query("SELECT * FROM wg_page WHERE visited=0 ORDER BY id");
while($pa = mysql_fetch_array($p)) {
	$pageNames = extractLinkArray($pa['id']);
	$rediName = array();
	$outIds = array();

	$find = mysql_query("SELECT * FROM wg_page WHERE name IN (".'"'.implode('", "', $pageNames).'"'.")");
	while ($found = mysql_fetch_array($find)) {
		if(!in_array($found['id'], $outIds)) array_push($values, "(NULL, '".$pa['id']."', '".$found['id']."')"); // avoid duplicates
		if(($key = array_search($found['name'], $pageNames)) !== false) unset($pageNames[$key]);
	}

	// in the leftovers, some actually might be legit, but not found because of redirect
	$toSearch = $pageNames;
	$r = mysql_query("SELECT * FROM wg_redirect WHERE fromName IN (".'"'.implode('", "', $pageNames).'"'.")"); // we cached a bunch of redirects
	while($re = mysql_fetch_array($r)) {
		$rediName[$re['fromName']] = $re['toName'];	
		$toSearch = removeByvalue($toSearch, $re['fromName']);
	}
	$rediCache = array();
	foreach ($toSearch as $i => $pageName) { // the rest we haven't found in our DB
		$r = redirectName($pageName);
		$rediName[$pageName] = $r;
		array_push($rediCache, "(NULL, '".$pageName."', '".$r."')");
	}
	mysql_query("INSERT INTO `wg_redirect` (`id`, `fromName`, `toName`) VALUES ".implode(", ", $rediCache).";"); // for now, this should be clustered

	$toSearch = array();
	foreach ($rediName as $page => $rediVal) {
		if($page != $rediVal) array_push($toSearch, $rediVal);
	}

	$find = mysql_query("SELECT * FROM wg_page WHERE name IN (".'"'.implode('", "', $toSearch).'"'.")");
	while($found = mysql_fetch_array($find)) {
		if(!in_array($found['id'], $outIds)) array_push($values, "(NULL, '".$pa['id']."', '".$found['id']."')");
	}
	// done with redirects

	if(count($values) > 200) {
		mysql_query("INSERT INTO `wg_link` (`id`, `from`, `to`) VALUES ".implode(",", $values).";");
		$values = array();
	}

	array_push($addVisited, $pa['id']);

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
mysql_query("ALTER TABLE wg_page DROP COLUMN visited");
?>