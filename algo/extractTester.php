<?php
include_once('../dbco.php');
include_once('func.php');
include_once('extractor.php');

	$articleId = 1;

	$pageNames = extractLinkArray($articleId);

	$find = mysql_query("SELECT * FROM wg_page WHERE name IN (".'"'.implode('", "', $pageNames).'"'.")");
	while ($found = mysql_fetch_array($find)) {
		if(($key = array_search($found['name'], $pageNames)) !== false) unset($pageNames[$key]);
	}

	$rediName = array(); $toSearch = $pageNames;
	$r = mysql_query("SELECT * FROM wg_redirect WHERE fromName IN (".'"'.implode('", "', $pageNames).'"'.")");
	while($re = mysql_fetch_array($r)) {
		$rediName[$re['fromName']] = $re['toName'];	
		$toSearch = removeByvalue($toSearch, $re['fromName']);
	}


	foreach ($toSearch as $i => $pageName) {
		$r = redirectName($pageName);
		$rediName[$pageName] = $r;
		mysql_query("INSERT INTO `wg_redirect` (`id`, `fromName`, `toName`) VALUES (NULL, '".$pageName."', '".$r."');"); // for now, this should be clustered
	}

	$toSearch = array();
	foreach ($rediName as $page => $rediVal) {
		if($page != $rediVal) array_push($toSearch, $rediVal);
	}

	$find = mysql_query("SELECT * FROM wg_page WHERE name IN (".'"'.implode('", "', $toSearch).'"'.")");
	while($found = mysql_fetch_array($find)) {
		echo 'Extra connection with: '.$found['name'].'<br />';
	}
	// echo implode("<br />", $pageNames);
?>