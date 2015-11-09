<?php
include_once('dbco.php');
include_once('display/func.php');
include_once('mainFunc.php');
$root = getRoot();
$realRoot = getRealRoot();
$myUrl = str_replace('/Wikigraph/', '', $_SERVER['REQUEST_URI']);
$myUrl = str_replace('/wikigraph/', '', $myUrl);
$myUrl = str_replace('display/', '', $myUrl);
if($myUrl != '' AND $myUrl[0] == '/') {$myUrl = substr($myUrl, 1);}
if($myUrl != '' AND $myUrl[(strlen($myUrl)-1)] == '/') {$myUrl = substr($myUrl, 0, (strlen($myUrl)-1));}
switch ($myUrl) {
	case 'graph':
		include_once('display/graph.php');
	break;
	case 'whatisthis':
		include_once('display/whatisthis.php');
	break;
	case '':
		include_once('display/index.php');
	break;
}

$exp = explode("/", $myUrl);
if(count($exp) == 1 && strlen($exp[0]) <= 3) {
	$_GET['dbPrefix'] = mysql_real_escape_string($exp[0]).'_';
	include_once('display/index.php');
}
if(count($exp) >= 2 && $exp[0] == 'graph') {
	if(is_numeric($exp[1])) {$_GET['cluster'] = mysql_real_escape_string($exp[1]);}
	include_once('display/graph.php');
}
if(count($exp) >= 2 && $exp[0] == 'loadGraph') {
	$_GET['topic'] = mysql_real_escape_string($exp[1]);
	include_once('display/loadGraph.php');
}
if(count($exp) == 2 and is_numeric($exp[1])) {
	$_GET['dbPrefix'] = mysql_real_escape_string($exp[0])."_";	
	$_GET['cluster'] = mysql_real_escape_string($exp[1]);
	include_once('display/graph.php');
}
?>