<?php
/*
OBJECTIVE:
From the list of page names in DB, load the pages, extract clean HTML into txt files in wikigraph/data

*/
include_once('../dbco.php');
include_once('func.php');
include_once('extractor.php');
set_time_limit(4*3600);
$r = mysql_query("SELECT * FROM wg_page");
while($re = mysql_fetch_array($r)) {
	if(!file_exists('../data/'.$re['id'].'.txt')) {
		$cleanHtml = extractPage($re['name']);
		$fh = fopen('../data/'.$re['id'].'.txt', 'w'); fwrite($fh, $cleanHtml);
	}
}
?>