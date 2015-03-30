<?php
	include_once('../dbco.php');
	include_once('func.php');
	$field = 1;

	$fieldNames = array();
	$f = mysql_query("SELECT * FROM wg_field ORDER BY id");
	while($fi = mysql_fetch_array($f)) array_push($fieldNames, $fi['sname']);

	$f = mysql_query("SELECT * FROM wg_field WHERE id=$field"); $fi = mysql_fetch_array($f);

	$thresh1 = 0.3; // if in my category, it has to be somewhat relevant
	$thresh2 = 30; // if not in my category, it should be highly relevant
	$fromAdja = array(); $outCount = array();

	$n = mysql_query("SELECT * FROM wg_page WHERE (field=".$field." AND ".$fi['sname'].">$thresh1) OR ".$fi['sname'].">$thresh2");
	while ($node = mysql_fetch_array($n)) {
		$fromAdja[$node['id']] = array();
		$outCount[$node['id']] = 0;
	}
	$edg = mysql_query("SELECT * FROM wg_link WHERE `to` IN (".implode(",", array_keys($fromAdja)).") AND `from` IN (".implode(",", array_keys($fromAdja)).") ORDER BY id");
	$totalEdges = 0;
	while($edge = mysql_fetch_array($edg)) {
		array_push($fromAdja[$edge['from']], $edge['to']);
		$outCount[$edge['from']] ++;
		$totalEdges ++;
	}

	$avgDegree = $totalEdges/count(array_keys($fromAdja));

	$p = mysql_query("SELECT * FROM wg_page WHERE field=$field AND id!=".$fi['page']." ORDER BY SPR DESC LIMIT 50");

	while($pa = mysql_fetch_array($p)) {
		$specifiArr = array();
		foreach ($fieldNames as $sname) if($sname != $fi['sname']) array_push($specifiArr, $pa[$sname]);
		arsort($specifiArr);
		$avg = ($specifiArr[0]+$specifiArr[1]+$specifiArr[2])/3;
		$specificity = (min(7, ($pa[$fi['sname']]/$avg))-1);
		$degreeAvg = ($outCount[$pa['id']])/$avgDegree;
		$SPR = min(6, $pa['SPR']);
		$score = pow($pa[$fi['sname']], 0.2)*$SPR*$specificity*$degreeAvg;
		$str = $pa['name']." {DiffScore: ".tDec(pow($pa[$fi['sname']], 0.2)).", SPR:".tDec($SPR).", specificity: ".tDec($specificity).", degreeAvg: ".tDec($degreeAvg).", score: ".tDec($score)."}<br />";
		$sortingArr[$str] = $score;
	}
	arsort($sortingArr);
	foreach ($sortingArr as $str => $val) echo $str;

	function tDec($nb) {return floor(100*$nb)/100;}
?>