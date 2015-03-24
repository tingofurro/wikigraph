<?php
include_once('../dbco.php');
include_once('extractor.php');

	$articleId = 4;

	$pageNames = extractLinkArray($articleId);

	$find = mysql_query("SELECT * FROM wg_page WHERE name IN (".'"'.implode('", "', $pageNames).'"'.")");
	while ($found = mysql_fetch_array($find)) {
		if(($key = array_search($found['name'], $pageNames)) !== false) unset($pageNames[$key]);
	}

	$rediName = array();
	foreach ($pageNames as $i => $pageName) {
		$rediName[$i] = redirectName($pageName);
		echo $pageName." => ".$rediName[$i]."<br />";
	}
	// echo implode("<br />", $pageNames);

?>