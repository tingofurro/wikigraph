<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
<?php
include_once('../dbco.php');
include_once('../mainFunc.php');
include_once('../algo/func.php');

set_time_limit(4*3600);
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
	echo "Level: ".$level.". Cluster: ".$cluster.".<br />"; fl();
	
	include_once('1-buildGraph.php');
	if($level == 0) {
		emptyFolder('txt');
		buildSummaries($limit);
		echo "Built page summaries<br />"; fl();
	}

	createGraph($limit, $level, $cluster);
	echo 'Built graph for community detection<br />'; fl();

	$pyscript = '"'.getDocumentRoot().'/igraph/2-community.py"';
	$param1 = '"'.getDocumentRoot().'"';
	exec($python.' '.$pyscript." ".$param1, $output);
	echo 'Ran community detection<br />'; fl();

	$pyscript = '"'.getDocumentRoot().'/igraph/3-closeness.py"';
	exec($python.' '.$pyscript." ".$param1, $output);
	echo 'Scored communities with NLP; Generated community names<br />'; fl();

	$pyscript = '"'.getDocumentRoot().'/igraph/4-extrapolate.py"';
	exec($python.' '.$pyscript." ".$param1, $output);
	echo 'Extrapolated other nodes<br />'; fl();

	include_once('5-saveResults.php');
	saveResults($level, $cluster);
	echo "Saved results to database."; fl();
	echo 'All done.<br />';
	// echo '<script>window.location.reload();</script>';
}
function fl() {ob_flush(); flush();}
?>
</body>
</html>