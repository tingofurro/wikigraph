<?php
function buildSummaries() {
	include_once('../dbco.php');
	include_once('../mainFunc.php');
	include_once('../algo/extractor.php');
	$nodes = array();
	$n = mysql_query("SELECT * FROM wg_page");
	while($no = mysql_fetch_array($n)) array_push($nodes, $no['id']);
	foreach ($nodes as $i => $node) {
		if(file_exists('../data/'.$node.'.txt')) {
			$html = file_get_contents('../data/'.$node.'.txt');
			$summary = extractSummary('<body>'.$html.'</body>');
			$summary = strip_tags($summary);
			$fh = fopen('txt/'.$node.'.txt', 'w');
			fwrite($fh, $summary);
			fclose($fh);			
		}
	}
}
set_time_limit(3000);
buildSummaries();
?>