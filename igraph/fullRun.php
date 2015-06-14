<?php
include_once('../dbco.php');
include_once('../mainFunc.php');
include_once('../algo/func.php');

$python = wherePython();
$limit = 1500;
if(isset($_GET['reset'])) {
	mysql_query("TRUNCATE TABLE `wg_cluster`");
	mysql_query("UPDATE wg_page SET cluster1=0, cluster2=0, cluster3=0, cluster4=0");
}

$clus = mysql_query("SELECT * FROM wg_cluster WHERE complete=0 ORDER BY id");
if($clust = mysql_fetch_array($clus)) {
	$level = $clust['level']; $cluster = $clust['id'];
}
else  {
	$any = mysql_query("SELECT * FROM wg_cluster");
	if(!$any = mysql_fetch_array($any)) {$level = 0; $cluster = 0;}
	else {echo "Program is done running.<br />";}
}

if(isset($level)) {
	emptyFolder('data');
	echo "Running level ".$level.", cluster: ".$cluster."<br />";
	
	include_once('1-buildGraph.php');
	if($level == 0) {
		emptyFolder('txt');
		buildSummaries($limit);
		echo "Done building summaries<br />";
	}

	createGraph($limit, $level, $cluster);
	echo 'Done with building the graph.json<br />';

	$pyscript = '"'.getDocumentRoot().'/igraph/2-community.py"';
	$param1 = '"'.getDocumentRoot().'"';
	exec($python.' '.$pyscript." ".$param1, $output);
	echo 'Done building clean graph communities<br />';

	$pyscript = '"'.getDocumentRoot().'/igraph/3-closeness.py"';
	exec($python.' '.$pyscript." ".$param1, $output);
	echo 'Done getting NLP scoring and terms<br />';
	
	include_once('4-saveResults.php');
	saveResults($level, $cluster);
	echo "Done saving results to database.";
	echo '<script>window.location.reload();</script>';
}



?>