<?php
function generateGraph($cluster1) {
	$nodes = array();
	$r = mysql_query("SELECT * FROM wg_page WHERE cluster1=".$cluster1);
	while($re = mysql_fetch_array($r)) array_push($nodes, $re['id']);
	include_once('graphFunctions.php');
	nodes2Graph($nodes, getDocumentRoot()."/display/temp.json");
}
?>