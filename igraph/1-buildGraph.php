<?php

function createGraph($limit, $level, $cluster) {
	$nodes = array();
	$where = '';
	if($level > 0) $where = ' WHERE cluster'.$level.'='.$cluster;
	$n = mysql_query("SELECT * FROM wg_page".$where." ORDER BY PR DESC LIMIT ".$limit);
	while($no = mysql_fetch_array($n)) array_push($nodes, $no['id']);
	
	$fullN = mysql_query("SELECT * FROM wg_page".$where."");
	$fullNodes = array();
	while($fullNo = mysql_fetch_array($fullN)) array_push($fullNodes, $fullNo['id']);
	$src = getDocumentRoot()."/igraph/data/fullNodeList.txt";
	$fh = fopen($src, 'w');
	fwrite($fh, implode("\n", $fullNodes));
	fclose($fh);

	$e = mysql_query("SELECT * FROM wg_link WHERE (`to` IN (".implode(", ", $nodes).") AND `from` IN (".implode(", ", $nodes).")) ORDER BY id");
	$edges = array();
	while($ed = mysql_fetch_array($e)) array_push($edges, $ed['from']." ".$ed['to']);
	$src = getDocumentRoot()."/igraph/data/graph.json";
	$fh = fopen($src, 'w');
	fwrite($fh, implode("\n", $edges));
	fclose($fh);
}
function buildSummaries() {
	include_once('../dbco.php');
	include_once('../mainFunc.php');
	include_once('../algo/extractor.php');
	$nodes = array();
	$n = mysql_query("SELECT * FROM wg_page");
	while($no = mysql_fetch_array($n)) array_push($nodes, $no['id']);

	foreach ($nodes as $i => $node) {
		if(file_exists('../data/'.$node.'.txt') AND !file_exists('txt/'.$node.'.txt')) {
			$html = file_get_contents('../data/'.$node.'.txt');
			$summary = extractSummary('<body>'.$html.'</body>');
			$summary = strip_tags($summary);
			$fh = fopen('txt/'.$node.'.txt', 'w');
			fwrite($fh, $summary);
			fclose($fh);			
		}
	}
}
set_time_limit(4*3600);
include_once('../dbco.php');
include_once('../mainFunc.php');
include_once('../algo/func.php');

emptyFolder('txt');
buildSummaries();
?>