<?php
include_once('../dbco.php');
/*
OBJECTIVE:
This script gets a list of pages from the category list built previously

CURRENT STATUS:
31971 Pages total
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
	while($re = mysql_fetch_array($r)) extractPages($re);
}
function extractPages($parent) {
	$dom = new DOMDocument;
	$html = file_get_contents('http://en.wikipedia.org/wiki/Category:'.$parent['name']);
	@$dom->loadHTML($html);
	$dom = $dom->getElementById('mw-pages');
	if(!is_null($dom)) {
		$articleNames = array();
		foreach ($dom->getElementsByTagName('a') as $link) {
			$href = $link->getAttribute('href');
			if(strpos($href, "/wiki/") !== false) { // this is an interesting link
				$h = str_replace("/wiki/", "", $href);
				$pieces = explode("#", urldecode(utf8_encode(($h))));
				array_push($articleNames, "'".mysql_real_escape_string($pieces[0])."'"); // get rid of anchor if there is one
			}
		}
		$sqlValues = array();
		$p = mysql_query("SELECT * FROM wg_page WHERE name IN (".implode(", ", $articleNames).")");
		while($pa = mysql_fetch_array($p)) {array_splice($articleNames, array_search("'".$pa['name']."'", $articleNames), 1);} // remove articles that are already in DB
		foreach ($articleNames as $cleanName) array_push($sqlValues, "(NULL, ".$cleanName.", '".$parent['id']."')");
		if(count($sqlValues) > 0) mysql_query("INSERT INTO `wg_page` (`id`, `name`, `category`) VALUES ".implode(",", $sqlValues).";");
	}
}
?>