<?php
	include_once('../dbco.php');
	include_once('func.php');
	set_time_limit(4*3600);

	$start = getTime();
	$source = 5325; // Partial diff eq
	$source = 7494; // Group Theory
	$source = 19332; // Computational_complexity_theory
	$field = 5;
	$thresh1 = 1; // if in my category, it has to be somewhat relevant
	$thresh2 = 30; // if not in my category, it should be highly relevant
	$cleanField = cleanFieldList();
	$myName = $cleanField[($field-1)];

	$d = 0.8;

	$PR = array(); $adja = array(); $names = array();
	$outCount = array();
	$c = mysql_query("SELECT COUNT(*) AS count FROM wg_page WHERE (cleanField=$field AND ".$myName.">$thresh1) OR ".$myName.">$thresh2");
	$co = mysql_fetch_array($c); $totalNodes = $co['count'];
	$n = mysql_query("SELECT id, pagerank, name, cleanField FROM wg_page WHERE (cleanField=$field AND ".$myName.">$thresh1) OR ".$myName.">$thresh2");
	while ($from = mysql_fetch_array($n)) {
		$PR[$from['id']] = 0;
		$adja[$from['id']] = array();
		$names[$from['id']] = $from['name'];
		$outCount[$from['id']] = 0;
	}

	$edg = mysql_query("SELECT * FROM wg_link WHERE `to` IN (".implode(",", array_keys($adja)).") AND `from` IN (".implode(",", array_keys($adja)).") ORDER BY id");
	while($edge = mysql_fetch_array($edg)) {
		if(!isset($adja[$edge['to']])) {$adja[$edge['to']] = array();}
		array_push($adja[$edge['to']], $edge['from']);
		$outCount[$edge['from']] ++;
	}
	$noOutCount = array();
	foreach ($outCount as $node => $nb) {
		if($nb == 0) array_push($noOutCount, $node);
	}
	$PR[$source] = $totalNodes; $totalChange = $totalNodes;
	while($totalChange > 1) {
		$NPR = array();
		foreach ($adja as $node => $incoming) {
			$NPR[$node] = (($node==$source)?((1-$d)*$totalNodes):0);
			foreach ($incoming as $inc) {
				$NPR[$node] += $d*$PR[$inc]/$outCount[$inc];
			}
		}
		$losses = 0;
		foreach ($noOutCount as $node) {$losses += $d*$PR[$node];}
		$NPR[$source] += $losses;
		$totalChange = totalChange($PR, $NPR);
		echo "Total score: ".totalSum($NPR).". Total change: ".$totalChange."<br />";
		$PR = $NPR;
	}
	foreach ($PR as $i => $p) {$PR[$i] = floor(100*$p)/100;}
	
	// $ids = implode(',', array_keys($PR));
	// $sql = "UPDATE wg_page SET ".$fieldName." = CASE id ";
	// foreach ($PR as $id => $pr) {$sql .= "WHEN ".$id." THEN ".$pr." ";}
	// $sql .= "END WHERE id IN ($ids)";
	// mysql_query($sql);

	echo "<u>Time it took to run: ".(floor(100*(getTime()-$start))/100)."s:</u><br />";

	arsort($PR);
	// echo 'pr = ['.implode(",", array_values($PR)).'];<br /><br />';
	// foreach ($PR as $node => $myPR) {
	// 	echo "<u>".$names[$node].":</u> ".$myPR."<br />";
	// }
	echo "<u>List of ids relevant to <b>".$names[$source]."</b>:</u><br /><br />";
	$finalNodes = array(); $thresh3 = 2;
	foreach ($PR as $node => $thisPR) {
		if($thisPR > $thresh3) {
			array_push($finalNodes, $node);
		}
	}
	sort($finalNodes);
	echo implode(", ", $finalNodes);
function totalSum($PR) {
	$totScore = 0;
	foreach ($PR as $i => $score) {
		$totScore += $score;
	}
	return $totScore;
}
function totalChange($PR, $NPR) {
	$totalChange = 0;
	foreach ($PR as $i => $score) {
		$totalChange += abs($score-$NPR[$i]);
	}
	return $totalChange;
}
function getTime() {
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	return $mtime;
}
?>