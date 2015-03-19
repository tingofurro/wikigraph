<?php
include_once('../dbco.php');
include_once('func.php');

$thresh1 = 1; // if in my category, it has to be somewhat relevant
$thresh2 = 30; // if not in my category, it should be highly relevant
$subf = array(); 
// $subf['field'] = 1; $subf['page'] = 7510; // Group Theory 
// $subf['field'] = 2; $subf['page'] = 5330; // PDE
$subf['field'] = 13; $subf['page'] = 766; // Nash Equilibrium

$f = mysql_query("SELECT * FROM wg_field WHERE id=".$subf['field']); $fi = mysql_fetch_array($f);


$adja = array(); $fromAdja = array();
$f = mysql_query("SELECT * FROM wg_field WHERE id=".$subf['field']); $fi = mysql_fetch_array($f);
$n = mysql_query("SELECT * FROM wg_page WHERE (field=".$subf['field']." AND ".$fi['sname'].">$thresh1) OR ".$fi['sname'].">$thresh2");
while ($node = mysql_fetch_array($n)) {
	$adja[$node['id']] = array();
	$fromAdja[$node['id']] = array();
	$nodeField[$node['id']] = $node['field'];
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
	if($diff > 1.5) array_push($myCluster, $node);
	else break;
}
echo implode(",", $myCluster);
?>