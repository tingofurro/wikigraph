<?php
	set_time_limit(4*3600);
	include_once('../dbco.php');
	include_once('func.php');
	$start = getTime();

	$adja = array();

	$f = mysql_query("SELECT * FROM wg_page ORDER BY id");
	while ($from = mysql_fetch_array($f)) $adja[$from['id']] = array();

	$edg = mysql_query("SELECT * FROM wg_link ORDER BY id");
	while($edge = mysql_fetch_array($edg)) array_push($adja[$edge['to']], $edge['from']);

	$f = mysql_query("SELECT * FROM wg_field ORDER BY id");
	while($fi = mysql_fetch_array($f)) {
		$hasVisited = false; $col = mysql_query("SHOW COLUMNS FROM wg_page;");
		while($colu = mysql_fetch_array($col)) {if($colu[0] == $fi['sname']) {$hasVisited = true;}}
		if (!$hasVisited) mysql_query("ALTER TABLE `wg_page` ADD `".$fi['sname']."` DOUBLE( 20, 5 ) NOT NULL DEFAULT '0'");

		$diffusion = computeDiffusion($adja, $fi['page'], 0.85); // diffusion centered around our new math field

		foreach ($diffusion as $i => $p) {$diffusion[$i] = floor(100*$p)/100;}
		
		$ids = implode(',', array_keys($diffusion));
		$sql = "UPDATE wg_page SET ".$fi['sname']." = CASE id ";
		foreach ($diffusion as $id => $di) {$sql .= "WHEN ".$id." THEN ".$di." ";}
		$sql .= "END WHERE id IN ($ids)";
		mysql_query($sql);
	}

	echo "<u>Time it took to run: ".(floor(100*(getTime()-$start))/100)."s:</u>";
?>