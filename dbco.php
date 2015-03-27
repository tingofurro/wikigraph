<?php
$link = mysql_connect("localhost", "root", "wikigraph");
mysql_select_db("wikigraph");
header('Content-Type: text/html; charset=utf-8');
mysql_set_charset('utf8', $link);
?>

