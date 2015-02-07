<?php
	$start = getTime();
	include_once('init.php');
	$d = 0.85;
	$PR = array(); $adja = array(); $names = array();
	$outCount = array();
	$c = mysql_query("SELECT COUNT(*) AS count FROM wg_page"); $co = mysql_fetch_array($c); $nb = $co['count'];
	$f = mysql_query("SELECT * FROM wg_page ORDER BY id");
	while ($from = mysql_fetch_array($f)) {
		$PR[$from['id']] = 1000/$nb;
		$adja[$from['id']] = array();
		$names[$from['id']] = $from['name'];
	}
	$edg = mysql_query("SELECT * FROM wg_links ORDER BY id");
	while($edge = mysql_fetch_array($edg)) {
		if(!isset($adja[$edge['to']])) {$adja[$edge['to']] = array();}
		array_push($adja[$edge['to']], $edge['from']);
		if(!isset($outCount[$edge['from']])) {$outCount[$edge['from']] = 1;}
		else {$outCount[$edge['from']] ++;}
	}

	echo "Time it took to run: ".(floor(100*(getTime()-$start))/100)."s<br />";


	$NPR = array();
	foreach ($adja as $node => $incoming) {
		$NPR[$node] = (1-$d)/$nb;
		foreach ($incoming as $inc) {
			$NPR[$node] += $d*$PR[$inc]/$outCount[$inc];
		}
	}
	$PR = $NPR;

	arsort($PR);
	$i = 0;
	foreach ($PR as $node => $score) {
		echo $names[$node]." score: ".$score."<br />";
		$i ++; if($i > 1000) {break;}
	}
function getTime() {
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	return $mtime;
}
?>