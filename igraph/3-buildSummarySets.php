<?php
include_once('../dbco.php');
include_once('../mainFunc.php');
include_once('../algo/extractor.php');
$src = getDocumentRoot()."/igraph/data/spinglass.txt";
$groups = file_get_contents($src); $groups = preg_split('/\r\n|\n|\r/', trim($groups));
$nodes = array();
foreach ($groups as $toks) {
	$tok = explode(" ", $toks);
	array_push($nodes, $tok[0]);
}
$i = 0;
foreach ($nodes as $node) {
	$html = file_get_contents('../data/'.$node.'.txt');
	if($i == 10) {echo extractSummary('<body>'.$html.'</body>'); break;}
	$i ++;
}
?>