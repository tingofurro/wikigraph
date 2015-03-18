<?php
	set_time_limit(4*3600);
	$start = getTime();
	include_once('../dbco.php');

	$adja = array(); $names = array();
	$PR = array();
	$outCount = array();
	$c = mysql_query("SELECT COUNT(*) AS count FROM wg_page"); $co = mysql_fetch_array($c); $totalNodes = $co['count'];
	$f = mysql_query("SELECT * FROM wg_page ORDER BY id");
	while ($from = mysql_fetch_array($f)) {
		$PR[$from['id']] = 0;
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
	$noOutCount = array();
	foreach ($outCount as $node => $nb) {
		if($nb == 0) array_push($noOutCount, $node);
	}

	$d = 0.85; // damping factor


	$f = mysql_query("SELECT * FROM wg_field ORDER BY id");
	while($fi = mysql_fetch_array($f)) {
		$hasVisited = false; $col = mysql_query("SHOW COLUMNS FROM wg_page;");
		while($colu = mysql_fetch_array($col)) {if($colu[0] == $fi['sname']) {$hasVisited = true;}}
		if (!$hasVisited) mysql_query("ALTER TABLE `wg_page` ADD `".$fi['sname']."` DOUBLE( 20, 5 ) NOT NULL DEFAULT '0'");

		$source = $fi['page']; // diffusion centered around our new math field
		foreach ($PR as $node => $uu) {$PR[$node] = 0;} // Reset PR grid
		$PR[$source] = $totalNodes; $totalChange = $totalNodes;

		while($totalChange > 1) {
			$NPR = array();
			foreach ($adja as $node => $incoming) {
				$NPR[$node] = (($node==$source)?((1-$d)*$totalNodes):0); // put the ashes in the center
				foreach ($incoming as $inc) {
					$NPR[$node] += $d*$PR[$inc]/$outCount[$inc]; // dissipation process, with damping
				}
			}
			$losses = 0;
			foreach ($noOutCount as $node) {$losses += $d*$PR[$node];} // these guys didn't dissipate their weight, it would be lost
			$NPR[$source] += $losses; // put these losses in center too
			$totalChange = totalChange($PR, $NPR);
			$PR = $NPR;
		}
		foreach ($PR as $i => $p) {$PR[$i] = floor(100*$p)/100;}
		
		$ids = implode(',', array_keys($PR));
		$sql = "UPDATE wg_page SET ".$fi['sname']." = CASE id ";
		foreach ($PR as $id => $pr) {$sql .= "WHEN ".$id." THEN ".$pr." ";}
		$sql .= "END WHERE id IN ($ids)";
		mysql_query($sql);
	}

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