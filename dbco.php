<?php
$link = mysql_connect("localhost", "root", "wikigraph");
// $link = mysql_connect("wikigraph", "wikiuser", "wikigraph@123");
mysql_select_db("wikigraph");
header('Content-Type: text/html; charset=utf-8');
mysql_set_charset('utf8', $link);
// error_reporting(E_ERROR);
error_reporting(E_ALL);
ini_set('display_errors', 1); 
?>
