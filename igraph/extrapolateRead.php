<?php
include('../dbco.php');
$clusNames = array();
$clus = mysql_query("SELECT * FROM wg_cluster ORDER BY id");
while($clust = mysql_fetch_array($clus)) $clusNames[$clust['id']] = $clust['name'];

$clusterLines = preg_split('/\r\n|\n|\r/', file_get_contents('data/extrapolate.txt'));
$nodeClus = array();
foreach ($clusterLines as $line) {
	$line = explode(" ", $line);
	if(count($line) >= 2) $nodeClus[$line[0]] = $line[1];
}
$r = mysql_query("SELECT * FROM wg_page WHERE id IN(".implode(",", array_keys($nodeClus)).")");
while($re = mysql_fetch_array($r)) {

	echo $re['name']."-> ".$clusNames[$nodeClus[$re['id']]+1]."<br />";
}
?>