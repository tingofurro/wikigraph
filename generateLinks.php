<?php
include('init.php');
set_time_limit(4*3600);
if(isset($_GET['fullReset'])) {
	mysql_query("TRUNCATE wg_links");
	mysql_query("UPDATE wg_page SET visited=0");
}
$r = mysql_query("SELECT * FROM wg_page WHERE visited=0 ORDER BY id");
while($re = mysql_fetch_array($r)) {
	$html = file_get_contents('data/'.$re['id'].'.txt');
	$dom = new DOMDocument;
	@$dom->loadHTML($html); $pageNames = array();
	foreach ($dom->getElementsByTagName('a') as $link) {
		$href = $link->getAttribute('href');
		if(strpos($href, "/wiki/") !== false) { // this is an interesting link
			$h = str_replace("/wiki/", "", $href); $cleanName = urldecode(utf8_encode(($h)));
			$toks = explode("#", $cleanName); $cleanName = $toks[0];
			$try = explode(":", $cleanName);
			if(in_array($try[0], array("Help", "Wikipedia", "Category", "Special", "Template", "Portal", "File", "Template_talk"))) {}
			else if(!in_array($cleanName, $pageNames)) {array_push($pageNames, "\"".$cleanName."\"");}
		}
	}
	$find = mysql_query("SELECT * FROM wg_page WHERE name IN (".implode(", ", $pageNames).")");
	while ($found = mysql_fetch_array($find)) {
		mysql_query("INSERT INTO `wg_links` (`id`, `from`, `to`, `type`) VALUES (NULL, '".$re['id']."', '".$found['id']."', '0');");
	}
	echo 'Done with '.$re['id']."<br />";
	mysql_query("UPDATE wg_page SET visited=1 WHERE id=".$re['id']);
}
?>