<?php
	set_time_limit(4*3600);
	$start = getTime();
	include_once('init.php');

	// $fieldName = 'algebra'; $source = 2; 
	// $fieldName = 'analysis'; $source = 121; 
	// $fieldName = 'arithmetic'; $source = 272; 
	// $fieldName = 'calculus'; $source = 310; 
	// $fieldName = 'discrete_math'; $source = 541; 
	// $fieldName = 'game_theory'; $source = 610; 
	// $fieldName = 'geometry'; $source = 808; 
	// $fieldName = 'graph_theory'; $source = 916; 
	// $fieldName = 'logic'; $source = 999; 
	// $fieldName = 'number_theory'; $source = 1172; 
	// $fieldName = 'order_theory'; $source = 1351; 
	// $fieldName = 'prob_stats'; $source = 1497; 
	// $fieldName = 'topology'; $source = 1718;

	$d = 0.85;
	$PR = array(); $adja = array(); $names = array();
	$outCount = array();
	$c = mysql_query("SELECT COUNT(*) AS count FROM wg_page"); $co = mysql_fetch_array($c); $nb = $co['count'];
	$f = mysql_query("SELECT * FROM wg_page ORDER BY id");
	while ($from = mysql_fetch_array($f)) {
		$PR[$from['id']] = 0;
		$adja[$from['id']] = array();
		$names[$from['id']] = $from['name'];
		$outCount[$from['id']] = 0;
	}

	$edg = mysql_query("SELECT * FROM wg_links ORDER BY id");
	while($edge = mysql_fetch_array($edg)) {
		if(!isset($adja[$edge['to']])) {$adja[$edge['to']] = array();}
		array_push($adja[$edge['to']], $edge['from']);
		$outCount[$edge['from']] ++;
	}
	$noOutCount = array();
	foreach ($outCount as $node => $nb) {
		if($nb == 0) array_push($noOutCount, $node);
	}
	$totalNodes = 31900;
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
	
	$ids = implode(',', array_keys($PR));
	$sql = "UPDATE wg_page SET ".$fieldName." = CASE id ";
	foreach ($PR as $id => $pr) {$sql .= "WHEN ".$id." THEN ".$pr." ";}
	$sql .= "END WHERE id IN ($ids)";
	mysql_query($sql);

	echo "<u>Time it took to run: ".(floor(100*(getTime()-$start))/100)."s:</u>";
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