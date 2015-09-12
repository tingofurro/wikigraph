<?php
include_once('createJsonGraph.php');
include_once('graphFunctions.php');

if(isset($_GET['topic'])) {
	$topic = mysql_real_escape_string($_GET['topic']);
	$cluster = 0; $level = 0; $limit = 700;
	$c = mysql_query("SELECT * FROM cluster WHERE id=".$topic);
	if($cl = mysql_fetch_array($c)) {$cluster = $cl['id']; $level = $cl['level'];}
	
	$fileUrl = "display/cache/".$topic.".json";
	$fileExists = file_exists(getDocumentRoot().'/'.$fileUrl);
	$toFile = '';
	if(!$fileExists) {
		$toFile = getDocumentRoot()."/display/cache/".$topic.".json";
		generateGraph($level, $cluster, $limit);
		$fileUrl = "display/temp.json";
	}
	$jsonGraph =  json_decode(fread(fopen(getDocumentRoot().'/'.$fileUrl, "r"), filesize(getDocumentRoot().'/'.$fileUrl)));
	$finalObject = array();
	$finalObject['preloaded'] = ($fileExists)?1:0;
	$finalObject['toFile'] = $toFile;
	$finalObject['graph'] = $jsonGraph;
	echo json_encode($finalObject);
}
?>