<?php
function strToWiki($oldStr) {
	return str_replace(" ", "_", $oldStr);
}
function wikiToName($oldStr) {
	return urldecode(str_replace("_", " ", $oldStr));
}
function wherePython() {
	$link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	if(strpos($link, "/Wikigraph")) {return "C:/Python27/python.exe";} // kinda hack, this is on Windows Machine
	else {return '/usr/bin/python2.7';} // this is on server
}

function getRoot() {
	$link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	if(strpos($link, "/Wikigraph")) {$pos = strpos($link, "/Wikigraph"); $root = substr($link, 0, ($pos+10))."/";}
	if(strpos($link, "/wikigraph")) {$pos = strpos($link, "/wikigraph"); $root = substr($link, 0, ($pos+10))."/";}
	if(strpos($link, "http://wikigraph.gatech.edu/")!==false) return "http://wikigraph.gatech.edu/";
	return $root;
}
function getRealRoot() {
	return getRoot().'display/';
}
function getDocumentRoot() {
	$link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	if(strpos($link, "/Wikigraph")) {$root = $_SERVER['DOCUMENT_ROOT']."/Wikigraph";}
	if(strpos($link, "/wikigraph")) {$root = $_SERVER['DOCUMENT_ROOT']."/wikigraph";}
	if(strpos($link, "gatech.edu") !== false) {$root = $_SERVER['DOCUMENT_ROOT'];}
	return $root;
}

?>