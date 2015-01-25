<?php
include('init.php');
set_time_limit(7200);
$r = mysql_query("SELECT * FROM wg_page WHERE visited=0 ORDER BY id");
while($re = mysql_fetch_array($r)) {
	$html = file_get_contents('http://en.wikipedia.org/wiki/'.$re['name']);
	mysql_query("UPDATE wg_page SET html='".mysql_real_escape_string($html)."', visited='1' WHERE id='".$re['id']."'");
}

?>