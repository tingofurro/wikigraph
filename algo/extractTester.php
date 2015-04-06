<?php
include_once('../dbco.php');
include_once('func.php');
include_once('extractor.php');
set_time_limit(3600);
// $r = mysql_query("SELECT * FROM wg_page WHERE id<1000");
//$r = mysql_query("SELECT * FROM wg_page ORDER BY RAND() LIMIT 10");
$r = mysql_query("SELECT * FROM wg_page WHERE id=23831");
while($re = mysql_fetch_array($r)) {
	$cleanHtml = extractSections($re['name']);
	// echo $re['name']. ": ";
	$cleanHtml = removeLists($cleanHtml);
	// echo "<br />";
	echo $cleanHtml;
}
?>