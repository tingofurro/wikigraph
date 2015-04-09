<?php
$link = mysql_connect("localhost", "root", "wikigraph");
mysql_select_db("wikigraph");
header('Content-Type: text/html; charset=utf-8');
mysql_set_charset('utf8', $link);
// error_reporting(E_ERROR);
error_reporting(E_ALL);
ini_set('display_errors', 1); 
?>
