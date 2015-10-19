<?php
function generateGraph($level, $clus, $limit = 1000) {
	$nodes = array();
	$where = '';
	if($level > 0) $where = 'AND cluster'.$level."=".$clus;
	$r = mysql_query("SELECT * FROM page WHERE badPage=0 ".$where." ORDER BY PR DESC LIMIT ".$limit);
	while($re = mysql_fetch_array($r)) array_push($nodes, $re['id']);
	sort($nodes);
	include_once('graphFunctions.php');
	nodes2Graph($nodes, getDocumentRoot()."/display/temp.json", ($level+1));
}
function generateClusterGraph($file, $depth=2) {
	$nodes = array(); $edges = array();
	array_push($nodes, "{'index': 0, 'id': 0, 'name': 'Mathematics'}");
	$r = mysql_query("SELECT * FROM cluster WHERE level<=".$depth);
	while($re = mysql_fetch_array($r)) {
		array_push($nodes, "{'index': ".$re['id'].", 'id': ".$re['id'].", 'group': ".$re['id'].", 'name': \"".$re['name']."\"}");
		array_push($edges, '{"source":'.$re['parent'].',"target":'.$re['id'].',"value":1}');
	}
	$txt = "{\n";
	$txt .= $sp."\"nodes\": [\n";
		$txt .= implode(", \n", $nodes);
	$txt .= "\n], \n";
	$txt .= "\"links\": [\n";
		$txt .= implode(", \n", $edges);
	$txt .= "\n]\n";
	$txt .= "}";
	$fh = fopen($file, 'w'); fwrite($fh, $txt);

}
?>