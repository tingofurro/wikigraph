<?php
include_once('../dbco.php');
include_once('extractor.php');
/*
OBJECTIVE:
This script gets a list of pages from the category list built previously

CURRENT STATUS: 35017 pages


*/
getPages((isset($_GET['fullReset'])?1:0));
function getPages($fullReset) {
	set_time_limit(3600);
	$startAt = 1;
	if($fullReset == 1) {
		mysql_query("TRUNCATE wg_page"); mysql_query("TRUNCATE wg_link");
	}
	else {
		$r = mysql_query("SELECT * FROM wg_page ORDER BY category DESC LIMIT 1");
		if($re = mysql_fetch_array($r)) $startAt = $re['category']; // restart where we left off
	}
	$r = mysql_query("SELECT * FROM wg_category WHERE distance>=1 AND killBranch=0 AND id>=$startAt ORDER BY id");
	while($re = mysql_fetch_array($r)) {
		$articleNames = extractPagesFromCat($re['name']);
		$sqlValues = array();
		$p = mysql_query(utf8_encode("SELECT * FROM wg_page WHERE name IN (".'"'.implode('", "', $articleNames).'"'.")"))or die(mysql_error());
		while($pa = mysql_fetch_array($p)) {
			if(($key = array_search($pa['name'], $articleNames)) !== false) unset($articleNames[$key]);
		} // remove articles that are already in DB

		foreach ($articleNames as $cleanName) array_push($sqlValues, "(NULL, \"".$cleanName."\", '".$re['id']."')");
		if(count($sqlValues) > 0) mysql_query("INSERT INTO `wg_page` (`id`, `name`, `category`) VALUES ".implode(",", $sqlValues).";");
	}
}
?>