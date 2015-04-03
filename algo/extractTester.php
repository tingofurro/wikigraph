<?php
include_once('../dbco.php');
include_once('func.php');
include_once('extractor.php');
set_time_limit(3600);
// $r = mysql_query("SELECT * FROM wg_page WHERE id<1000");
$r = mysql_query("SELECT * FROM wg_page WHERE id=306");
while($re = mysql_fetch_array($r)) {
	$cleanHtml = extractSections($re['name']);
	$return = removeLists($cleanHtml);
}
?>