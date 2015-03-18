<?php
	include_once('../dbco.php');
	include_once('func.php');
	set_time_limit(4*3600);

	$field = 2;
	$thresh1 = 1; // if in my category, it has to be somewhat relevant
	$thresh2 = 30; // if not in my category, it should be highly relevant
	$cleanField = cleanFieldList();
	$myName = $cleanField[($field-1)];

	$start = getTime();
	$d = 0.85;
	$PR = array(); $adja = array(); $names = array();
	$outCount = array();
	$c = mysql_query("SELECT COUNT(*) AS count FROM wg_page WHERE (cleanField=$field AND ".$myName.">$thresh1) OR ".$myName.">$thresh2");
	$co = mysql_fetch_array($c); $nb = $co['count'];
	$n = mysql_query("SELECT id, pagerank, name, cleanField FROM wg_page WHERE (cleanField=$field AND ".$myName.">$thresh1) OR ".$myName.">$thresh2");
	while($no = mysql_fetch_array($n)) {
		$PR[$no['id']] = 1000/$nb;
		$adja[$no['id']] = array();
		$names[$no['id']] = $no['name'];
		$outCount[$no['id']] = 0;		
	}
	$edg = mysql_query("SELECT * FROM wg_link WHERE `to` IN (".implode(",", array_keys($adja)).") AND `from` IN (".implode(",", array_keys($adja)).") ORDER BY id");
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
	// $sql = "UPDATE wg_page SET pagerank = CASE id ";
	// foreach ($PR as $id => $pr) {$sql .= "WHEN ".$id." THEN ".(floor(100000*$pr)/100000)." ";}
	// $sql .= "END WHERE id IN ($ids)";
	// mysql_query($sql);
	// echo "<u>Time it took to run: ".(floor(100*(getTime()-$start))/100)."s:</u><br /><br />";
	arsort($PR);
	foreach ($PR as $node => $myPR) {
		echo "<u>".$names[$node].":</u> ".$myPR."<br />";
	}
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