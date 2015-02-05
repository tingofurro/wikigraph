<?php
set_time_limit(4*3600);
include('init.php');
include_once('nlp/nlp.php');
$goodWords = apc_fetch('mathematicianTrainSet');
$badWords = apc_fetch('normalTrainSet');
$s = mysql_query("SELECT * FROM wg_page WHERE list=0");
$myScores = array();
while($se = mysql_fetch_array($s)) {
	$html = file_get_contents('data/'.$se['id'].'.txt');
	$ret = getWordCounts($html);
	$words = $ret[0]; $nbWords = $ret[1];
	$dom = new DOMDocument;
	@$dom->loadHTML($html); $pageNb = 0;
	foreach ($dom->getElementsByTagName('a') as $link) {
		$href = $link->getAttribute('href');
		if(strpos($href, "/wiki/") !== false) {$pageNb ++;}
	}
	$score = $pageNb*$pageNb/$nbWords;
	mysql_query("UPDATE wg_page SET list=".$score." WHERE id=".$se['id']);
}
?>