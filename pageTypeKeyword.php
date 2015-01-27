<?php
include('init.php');
$r = mysql_query("SELECT * FROM wg_page WHERE pageType=-1 LIMIT 1");
while($re = mysql_fetch_array($r)) {
	$html = file_get_contents('data/'.$re['id'].'.txt');
	$onlyTxt = strip_tags($html, "<br>");
	echo $onlyTxt;
}
?>