<?php
function saveResults($level, $cluster) { // level and cluster we are going to take care of
	include_once('../dbco.php');
	include_once('../mainFunc.php');

	$where = '';
	if($level > 0) $where = ' WHERE cluster'.$level.'='.$cluster;

	mysql_query("DELETE FROM wg_cluster WHERE parent=$cluster"); // full reset
	mysql_query("UPDATE wg_cluster SET cluster".($level+1)."=0".$where); // full reset

	$clusterLines = preg_split('/\r\n|\n|\r/', file_get_contents('data/clusters.txt'));
	$clusterMapping = array();
	foreach ($clusterLines as $toks) {
		$toks = explode("[]", $toks);
		if(count($toks) > 2) {
			$thisCluster = $toks[0];
			$thisScore = $toks[1];
			$thisName = $toks[2];
			$isComplete = ($thisScore>1.5 and $thisCluster != 0 and $level<4)?"0":"1";

			mysql_query("INSERT INTO `wg_cluster` (`id`, `parent`, `name`, `level`, `score`, `complete`) VALUES (NULL, '".$cluster."', '".$thisName."', '".($level+1)."', '".$thisScore."', '".$isComplete."');");
			$r = mysql_query("SELECT * FROM wg_cluster ORDER BY id DESC LIMIT 1"); $re = mysql_fetch_array($r);
			$clusterMapping[$thisCluster] = $re['id'];
		}
		else {echo implode("[]", $toks);}
	}

	$pages = preg_split('/\r\n|\n|\r/', file_get_contents('data/community.txt'));

	$sql = "UPDATE wg_page SET cluster".($level+1)." = CASE id ";
	$pageIds = array();
	foreach ($pages as $page) {
		$page = explode(" ", $page);
		if(count($page) > 1) {
			$sql .= "WHEN ".$page[0]." THEN ".$clusterMapping[$page[1]]." ";
			array_push($pageIds, $page[0]);
		}
	}
	$sql .= "END WHERE id IN (".implode(",", $pageIds).")";
	mysql_query($sql);
	mysql_query("UPDATE wg_cluster SET complete=1 WHERE level=$level AND cluster=$cluster");

}
?>