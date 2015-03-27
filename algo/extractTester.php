<?php
include_once('../dbco.php');
include_once('func.php');
include_once('extractor.php');

	$category = 309;
	$cleanName = array();
	$r = mysql_query("SELECT * FROM wg_category WHERE id=$category");
	if($re = mysql_fetch_array($r)) {
		$articleNames = extractPagesFromCat($re['name']);
		echo count($articleNames);
		$sqlValues = array();
		$p = mysql_query("SELECT * FROM wg_page WHERE name IN (".'"'.implode('", "', $articleNames).'"'.")")or die(mysql_error());
		// echo "Checking: "."SELECT * FROM wg_page WHERE name IN (".'"'.implode('", "', $articleNames).'"'.")";
		$extra = array();
		while($pa = mysql_fetch_array($p)) if(!in_array($pa['name'], $extra)) array_push($extra, $pa['name']);
		$articleNames = array_diff($articleNames, $extra);
		echo "<br />".count($articleNames);

		// echo implode("<br />", $articleNames);

	}
?>