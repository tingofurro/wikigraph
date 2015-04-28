<?php

function createGraph($limit) {
	$nodes = array();
	$n = mysql_query("SELECT * FROM wg_page ORDER BY PR DESC LIMIT ".$limit);
	while($no = mysql_fetch_array($n)) array_push($nodes, $no['id']);

	$e = mysql_query("SELECT * FROM wg_link WHERE (`to` IN (".implode(", ", $nodes).") AND `from` IN (".implode(", ", $nodes).")) ORDER BY id");
	$edges = array();
	while($ed = mysql_fetch_array($e)) {
		array_push($edges, $ed['from']." ".$ed['to']);
	}
	$src = getDocumentRoot()."/igraph/data/graph.json";
	$fh = fopen($src, 'w');
	fwrite($fh, implode("\n", $edges));
	fclose($fh);
}
function buildSummaries($limit) {
	include_once('../dbco.php');
	include_once('../mainFunc.php');
	include_once('../algo/extractor.php');
	$nodes = array();
	$n = mysql_query("SELECT * FROM wg_page ORDER BY PR DESC LIMIT ".$limit);
	while($no = mysql_fetch_array($n)) array_push($nodes, $no['id']);

	emptyFolder('txt');
	foreach ($nodes as $i => $node) {
		$html = file_get_contents('../data/'.$node.'.txt');
		$summary = extractSummary('<body>'.$html.'</body>');
		$summary = strip_tags($summary);
		$fh = fopen('txt/'.$node.'.txt', 'w');
		fwrite($fh, $summary);
		fclose($fh);
	}
}
?>