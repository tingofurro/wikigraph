<?php
include('init.php');
$r = mysql_query("SELECT * FROM `wg_page` WHERE `name` LIKE '%â€“%' LIMIT 0 , 30");
while($re = mysql_fetch_array($r)) {
	mysql_query("UPDATE wg_page SET name='".str_replace("â€“", "-", $re['name'])."' WHERE id='".$re['id']."'");
}
?>