<?php
	include_once('../dbco.php');
	include_once('func.php');
	/*
	OBJECTIVE:
	Compute SPR: Specific PageRank
	It is PageRank calculated on subgraph of only specific field
	*/


	set_time_limit(4*3600);
	$start = getTime();

	$thresh1 = 1; // if in my category, it has to be somewhat relevant
	$thresh2 = 30; // if not in my category, it should be highly relevant
	$d = 0.85;

	$hasSPR = false;
	$col = mysql_query("SHOW COLUMNS FROM wg_page;");
	while($colu = mysql_fetch_array($col)) if($colu[0] == 'SPR') $hasSPR = true;
	if(!$hasSPR) mysql_query("ALTER TABLE `wg_page` ADD `SPR` DOUBLE( 25, 5 ) NOT NULL DEFAULT '0' AFTER `PR`");

	$f = mysql_query("SELECT * FROM wg_field ORDER BY id");
	while($fi = mysql_fetch_array($f)) {
		$PR = array(); $adja = array(); $noField = array();
		$outCount = array();
		$c = mysql_query("SELECT COUNT(*) AS count FROM wg_page WHERE (field=".$fi['id']." AND ".$fi['sname'].">$thresh1) OR ".$fi['sname'].">$thresh2");
		$co = mysql_fetch_array($c); $nb = $co['count'];
		$n = mysql_query("SELECT id, PR, name, field FROM wg_page WHERE (field=".$fi['id']." AND ".$fi['sname'].">$thresh1) OR ".$fi['sname'].">$thresh2");
		while($no = mysql_fetch_array($n)) {
			$PR[$no['id']] = 1000/$nb;
			$adja[$no['id']] = array();
			$noField[$no['id']] = $no['field'];
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
			if($count == 0) $noOutCount ++;
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
		$sql = "UPDATE wg_page SET SPR = CASE id ";
		$updateList = array();
		foreach ($PR as $id => $pr) {
			if($noField[$id] == $fi['id']) {$sql .= "WHEN ".$id." THEN ".(floor(100000*$pr)/100000)." "; array_push($updateList, $id);} // only update SPR once
		}
		$sql .= "END WHERE id IN (".implode(',', $updateList).")";
		mysql_query($sql);
	}
	echo "<u>Time it took to run: ".(floor(100*(getTime()-$start))/100)."s:</u><br /><br />";
?>