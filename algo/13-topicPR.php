<?php
	include_once('../dbco.php');
	include_once('func.php');
	set_time_limit(4*3600);
	$start = getTime();
	
	$hasPR = false;
	$col = mysql_query("SHOW COLUMNS FROM wg_topic;");
	while($colu = mysql_fetch_array($col)) {
		if($colu[0] == 'PR') $hasPR = true;
	}
	if(!$hasPR) mysql_query("ALTER TABLE `wg_topic` ADD `PR` DOUBLE( 25, 5 ) NOT NULL DEFAULT '0'");


	$f = mysql_query("SELECT * FROM wg_field ORDER BY id");
	while($fi = mysql_fetch_array($f)) {
		$t = mysql_query("SELECT DISTINCT topic FROM wg_topic WHERE field=".$fi['id']);
		while($to = mysql_fetch_array($t)) {
			$adja = array();
			$pageT = mysql_query("SELECT * FROM wg_topic WHERE field=".$fi['id']." AND topic=".$to['topic']);
			while($pageTo = mysql_fetch_array($pageT)) $adja[$pageTo['page']] = array();

			$edg = mysql_query("SELECT * FROM wg_link WHERE (`from` IN (".implode(", ", array_keys($adja)).") AND `to` IN (".implode(", ", array_keys($adja)).")) ORDER BY id"); // initialize all the edges
			while($edge = mysql_fetch_array($edg)) array_push($adja[$edge['to']], $edge['from']);

			$PR = computePR($adja); // compute the pagerank of the given graph

			$ids = implode(',', array_keys($PR));
			$sql = "UPDATE wg_topic SET PR = CASE id ";
			foreach ($PR as $id => $pr) {$sql .= "WHEN ".$id." THEN ".(floor(100000*$pr)/100000)." ";}
			$sql .= "END WHERE id IN ($ids) AND field=".$fi['id'];
			mysql_query($sql);
			echo "<u>Time it took to run: ".(floor(100*(getTime()-$start))/100)."s</u><br /><br />";		
		}
		
	}
?>