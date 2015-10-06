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
	case 'tree':
		include_once('display/tree.php');
	break;
	case 'explore':
		include_once('display/explore.php');
	break;
	case 'folders':
		include_once('display/folders.php');
	break;
	case 'pie':
		include_once('display/pie.php');
	break;
	case 'graph':
		include_once('display/graph.php');
	break;
	case 'whatisthis':
		include_once('display/whatisthis.php');
	break;
	case 'regenPies':
		include_once('display/createCsvPies.php');
	break;
	case 'subcolor':
		include_once('display/subcolor.php');
	break;
	case 'new':
		include_once('display/new.php');
	break;
	case '':
		include_once('display/graph.php');
	break;
}

$exp = explode("/", $myUrl);
if(count($exp) == 1 and is_numeric($exp[0])) {
	$_GET['cluster'] = mysql_real_escape_string($exp[0]);
	include_once('display/graph.php');
}
if(count($exp) >= 2 && $exp[0] == 'category') {
	if(is_numeric($exp[1])) {$_GET['source'] = mysql_real_escape_string($exp[1]);}
	else {$_GET['sourceName'] = mysql_real_escape_string($exp[1]);}
	include_once('display/tree.php');
}
if(count($exp) >= 2 && $exp[0] == 'graph') {
	if(is_numeric($exp[1])) {$_GET['cluster'] = mysql_real_escape_string($exp[1]);}
	include_once('display/graph.php');
}
if(count($exp) >= 2 && $exp[0] == 'explore') {
	if(is_numeric($exp[1])) {$_GET['id'] = mysql_real_escape_string($exp[1]);}
	else {
		$r = mysql_query("SELECT * FROM wg_page WHERE name='".mysql_real_escape_string($exp[1])."'");
		if($re = mysql_fetch_array($r)) {$_GET['id'] = $re['id'];}
	}
	include_once('display/explore.php');
}
if(count($exp) >= 2 && $exp[0] == 'wiki') {
	$_GET['name'] = mysql_real_escape_string($exp[1]);
	include_once('display/explore.php');
}
if(count($exp) >= 2 && $exp[0] == 'loadGraph') {
	$_GET['topic'] = mysql_real_escape_string($exp[1]);
	include_once('display/loadGraph.php');
}
?>