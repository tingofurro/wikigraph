<?php
function generateGraph($level, $clus, $limit = 1000) {
	$nodes = array();
	$where = '';
	if($level > 0) $where = 'WHERE cluster'.$level."=".$clus;
	$r = mysql_query("SELECT * FROM page ".$where." ORDER BY PR DESC LIMIT ".$limit);
	while($re = mysql_fetch_array($r)) array_push($nodes, $re['id']);
	sort($nodes);
	include_once('graphFunctions.php');
	nodes2Graph($nodes, getDocumentRoot()."/display/temp.json", ($level+1));
}
?>