<?php
include_once('dbco.php');
$myUrl = str_replace('/Wikigraph/', '', $_SERVER['REQUEST_URI']);
$myUrl = str_replace('/wikigraph/', '', $myUrl);
if($myUrl[0] == '/') {$myUrl = substr($myUrl, 1);}
if($myUrl[(strlen($myUrl)-1)] == '/') {$myUrl = substr($myUrl, 0, (strlen($myUrl)-1));}
switch ($myUrl) {
	case 'category':
		include_once('displayTree.php');
	break;
	case 'explore':
		include_once('displayExplore.php');
	break;
	case 'clustering':
		include_once('displayClustering.php');
	break;
	case 'fields':
		include_once('displayField.php');
	break;
	case '':
		include_once('index.php');
	break;
}

$exp = explode("/", $myUrl);
if(count($exp) >= 2 && $exp[0] == 'category') {
	if(is_numeric($exp[1])) {$_GET['source'] = mysql_real_escape_string($exp[1]);}
	else {$_GET['sourceName'] = mysql_real_escape_string($exp[1]);}
	include_once('displayTree.php');
}
if(count($exp) >= 2 && $exp[0] == 'explore') {
	if(is_numeric($exp[1])) {$_GET['id'] = mysql_real_escape_string($exp[1]);}
	include_once('displayExplore.php');
}
if(count($exp) >= 2 && $exp[0] == 'clustering') {
	if($exp[1] == 'keywords') {
		$_GET['keywords'] = 1;
		if(count($exp) >= 3) {$_GET['keywords'] = $exp[2];}
	}
	elseif($exp[1] == 'pages') {
		$_GET['pages'] = 1;
		if(count($exp) >= 3) {$_GET['pages'] = $exp[2];}
		if(count($exp) >= 4) {$_GET['page'] = $exp[3];}
	}
	include_once('displayClustering.php');
}
if(count($exp) >= 2 && $exp[0] == 'wiki') {
	$_GET['name'] = mysql_real_escape_string($exp[1]);
	include_once('displayExplore.php');
}
?>