<?php
include_once('../dbco.php');
include_once('func.php');
include_once('extractor.php');

$p = mysql_query("SELECT * FROM wg_page WHERE id=1 ORDER BY id");
while($pa = mysql_fetch_array($p)) {
	$pageNames = extractLinkArray($pa['id']);
	$rediName = array(); $outIds = array();
	$foundNames = array();

	$find = mysql_query("SELECT * FROM wg_page WHERE name IN (".'"'.implode('", "', $pageNames).'"'.")");
	while ($found = mysql_fetch_array($find)) {
		if(!in_array($found['id'], $outIds)) array_push($outIds, $found['id']);
		array_push($foundNames, $found['name']);
	}

	// in the leftovers, some actually might be legit, but not found because of redirect
	$pageNames = array_diff($pageNames, $foundNames); $foundNames = array();
	$r = mysql_query("SELECT * FROM wg_redirect WHERE fromName IN (".'"'.implode('", "', $pageNames).'"'.")"); // we cached a bunch of redirects
	while($re = mysql_fetch_array($r)) {
		$rediName[$re['fromName']] = $re['toName'];	
		array_push($foundNames, $re['fromName']);
	}
	$pageNames = array_diff($pageNames, $foundNames); // remove all the ones we've already found
	$rediCache = array();
	foreach ($pageNames as $i => $pageName) { // the rest we haven't found in our DB
		$r = redirectName($pageName); // this will run the http request, get the name of the page
		$rediName[$pageName] = $r;
		echo $pageName." -> ".$r."<br />";
		array_push($rediCache, "(NULL, '".$pageName."', '".$r."')");
	}

	$toSearch2 = array();
	foreach($rediName as $page => $rediVal) {
		if($page != $rediVal) {array_push($toSearch2, $rediVal);}
	}


	$find = mysql_query("SELECT * FROM wg_page WHERE name IN (".'"'.implode('", "', $toSearch2).'"'.")");
	while($found = mysql_fetch_array($find)) {
		if(!in_array($found['id'], $outIds)) array_push($outIds, $found['id']);
	} // done with redirects
	// echo implode(', ', array_values($rediName));


}

?>