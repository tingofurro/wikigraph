<?php
include_once('../dbco.php');
include_once('func.php');
set_time_limit(4*3600);

$start = getTime();
$thresh1 = 1; // if in my category, it has to be somewhat relevant
$thresh2 = 30; // if not in my category, it should be highly relevant

$sf = mysql_query("SELECT * FROM wg_subfield ORDER BY id LIMIT 0,10");
while($subf = mysql_fetch_array($sf)) {
	$adja = array(); $fromAdja = array(); $names = array();
	$f = mysql_query("SELECT * FROM wg_field WHERE id=".$subf['field']); $fi = mysql_fetch_array($f);
	$n = mysql_query("SELECT * FROM wg_page WHERE (field=".$subf['field']." AND ".$fi['sname'].">$thresh1) OR ".$fi['sname'].">$thresh2");
	while ($node = mysql_fetch_array($n)) {
		$adja[$node['id']] = array();
		$fromAdja[$node['id']] = array();
		$nodeField[$node['id']] = $node['field'];
		$names[$node['id']] = $node['name'];
	}
	$edg = mysql_query("SELECT * FROM wg_link WHERE `to` IN (".implode(",", array_keys($adja)).") AND `from` IN (".implode(",", array_keys($adja)).") ORDER BY id");
	while($edge = mysql_fetch_array($edg)) {
		array_push($adja[$edge['to']], $edge['from']);
		array_push($fromAdja[$edge['from']], $edge['to']);
	}
	$diffusion = computeDiffusion($adja, $subf['page'], 0.7); // diffusion centered around our new math field

	foreach ($diffusion as $i => $p) {$diffusion[$i] = floor(100*$p)/100;}
	arsort($diffusion);
	$myCluster = array();
	foreach ($diffusion as $node => $diff) {
		if($diff > 1) array_push($myCluster, $node);
		else break;
	}
	// now we have a prototype of a subfield cluster
	foreach ($myCluster as $clusId) echo $names[$clusId]."<br />";
	echo "<br /><Br /><Br />";


}

echo "<u>Time it took to run: ".(floor(100*(getTime()-$start))/100)."s</u><br />";

?>