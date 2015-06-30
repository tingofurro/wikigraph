<?php
	/*
	OBJECTIVE:
	Measure centrality of each page using the pagerank algorithm, and save it into the database
	*/

	include_once('../dbco.php');
	include_once('func.php');
	set_time_limit(4*3600);
	$start = getTime();
	
	$hasPR = false;
	$col = mysql_query("SHOW COLUMNS FROM wg_page;");
	while($colu = mysql_fetch_array($col)) {
		if($colu[0] == 'PR') $hasPR = true;
	}
	if(!$hasPR) mysql_query("ALTER TABLE `wg_page` ADD `PR` DOUBLE( 25, 5 ) NOT NULL DEFAULT '0'");

	$adja = array();
	$n = mysql_query("SELECT * FROM wg_page ORDER BY id"); // initialize all the nodes
	while ($no = mysql_fetch_array($n)) $adja[$no['id']] = array();

	$edg = mysql_query("SELECT * FROM wg_link ORDER BY id"); // initialize all the edges
	while($edge = mysql_fetch_array($edg)) array_push($adja[$edge['to']], $edge['from']);

	$PR = computePR($adja); // compute the pagerank of the given graph

	$ids = implode(',', array_keys($PR));
	$sql = "UPDATE wg_page SET PR = CASE id ";
	foreach ($PR as $id => $pr) {$sql .= "WHEN ".$id." THEN ".(floor(100000*$pr)/100000)." ";}
	$sql .= "END WHERE id IN ($ids)";
	mysql_query($sql);
	echo "<u>Time it took to run: ".(floor(100*(getTime()-$start))/100)."s</u><br /><br />";
?>