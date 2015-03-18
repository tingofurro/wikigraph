<?php
	/*
	OBJECTIVE:
	Measure centrality of each page using the pagerank algorithm, and save it into the database
	*/

	set_time_limit(4*3600);
	$start = getTime();
	include_once('../dbco.php');
	
	$hasPR = false;
	$col = mysql_query("SHOW COLUMNS FROM wg_page;");
	while($colu = mysql_fetch_array($col)) {
		if($colu[0] == 'PR') $hasPR = true;
	}
	if(!$hasPR) mysql_query("ALTER TABLE `wg_page` ADD `PR` DOUBLE( 25, 5 ) NOT NULL DEFAULT '0'");

	$d = 0.85;
	$PR = array(); $adja = array(); $names = array();
	$outCount = array();
	$c = mysql_query("SELECT COUNT(*) AS count FROM wg_page"); $co = mysql_fetch_array($c); $nb = $co['count'];
	$f = mysql_query("SELECT * FROM wg_page ORDER BY id");
	while ($from = mysql_fetch_array($f)) {
		$PR[$from['id']] = 1000/$nb;
		$adja[$from['id']] = array();
		$names[$from['id']] = $from['name'];
		$outCount[$from['id']] = 0;
	}
	$edg = mysql_query("SELECT * FROM wg_link ORDER BY id");
	while($edge = mysql_fetch_array($edg)) {
		if(!isset($adja[$edge['to']])) {$adja[$edge['to']] = array();}
		array_push($adja[$edge['to']], $edge['from']);
		$outCount[$edge['from']] ++;
	}
	$noOutCount = 0;
	foreach ($outCount as $node => $count) {
		if($count == 0) {$noOutCount ++;}
	}
	for($round = 1; $round < 20; $round ++) {
		$NPR = array();
		foreach ($adja as $node => $incoming) {
			$NPR[$node] = (1-$d+0.12*$noOutCount)/$nb;
			foreach ($incoming as $inc) {
				$NPR[$node] += $d*$PR[$inc]/$outCount[$inc];
			}
		}
		$PR = $NPR;
	}
	$i = 0;
	$ids = implode(',', array_keys($PR));
	$sql = "UPDATE wg_page SET PR = CASE id ";
	foreach ($PR as $id => $pr) {$sql .= "WHEN ".$id." THEN ".(floor(100000*$pr)/100000)." ";}
	$sql .= "END WHERE id IN ($ids)";
	mysql_query($sql);
	echo "<u>Time it took to run: ".(floor(100*(getTime()-$start))/100)."s:</u><br /><br />";
function totalSum($PR) {
	$totScore = 0;
	foreach ($PR as $i => $score) {
		$totScore += $score;
	}
	return $totScore;
}
function getTime() {
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	return $mtime;
}
?>