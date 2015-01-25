<?php
include_once('init.php');
// originally 33.561 pages :)
getPages((isset($_GET['fullReset'])?1:0));
function getPages($fullReset) {
	set_time_limit(7200);
	$startAt = 1;
	if($fullReset == 1) {
		mysql_query("TRUNCATE wg_page");
		mysql_query("TRUNCATE wg_links");
	}
	else {
		$r = mysql_query("SELECT * FROM wg_page ORDER BY category DESC LIMIT 1");
		if($re = mysql_fetch_array($r)) {
			$startAt = $re['category']; // restart where we left off
		}
	}
	$r = mysql_query("SELECT * FROM wg_category WHERE distance>=1 AND killBranch=0 AND id>=$startAt ORDER BY id");
	while($re = mysql_fetch_array($r)) {
		extractPages($re);
		echo $re['name']." is done<br />";
	}
}
function extractPages($parent) {
	$dom = new DOMDocument;
	$html = file_get_contents('http://en.wikipedia.org/wiki/Category:'.$parent['name']);
	@$dom->loadHTML($html);
	$dom = $dom->getElementById('mw-pages');
	if(!is_null($dom)) {
		foreach ($dom->getElementsByTagName('a') as $link) {
			$href = $link->getAttribute('href');
			if(strpos($href, "/wiki/") !== false) { // this is an interesting link
				$h = str_replace("/wiki/", "", $href); $cleanName = wikiToName($h);
				$p = mysql_query("SELECT * FROM wg_page WHERE name=\"$cleanName\"");
				if($pa = mysql_fetch_array($p)) {
					// for now do nothing...
				}
				else {
					mysql_query("INSERT INTO `wg_page` (`id`, `name`, `category`, `fields`) VALUES (NULL, '".$cleanName."', '".$parent['id']."', '".$parent['fields']."');");
				}
			}
		}
	}
}

?>