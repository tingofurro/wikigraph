<?php
include_once('../dbco.php');
include_once('extractor.php');

	$articleNames = extractPagesFromCat('Four-dimensional_geometry');
	$sqlValues = array();
	echo implode(", ", $articleNames)."<br /><br />";
	$p = mysql_query(utf8_encode("SELECT * FROM wg_page WHERE name IN (".'"'.implode('", "', $articleNames).'"'.")"))or die(mysql_error());
	while($pa = mysql_fetch_array($p)) {
		if(($key = array_search($pa['name'], $articleNames)) !== false) unset($articleNames[$key]);
	} // remove articles that are already in DB

	echo implode(", ", $articleNames);

	// foreach ($articleNames as $cleanName) array_push($sqlValues, "(NULL, ".$cleanName.", '".$re['id']."')");
	// if(count($sqlValues) > 0) mysql_query("INSERT INTO `wg_page` (`id`, `name`, `category`) VALUES ".implode(",", $sqlValues).";");

?>