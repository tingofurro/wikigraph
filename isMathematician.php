<?php
set_time_limit(4*3600);
include('init.php');
include_once('nlp/nlp.php');
$goodWords = apc_fetch('mathematicianTrainSet', $loadSuccess);
if(isset($_GET['retrain']) OR !$loadSuccess) {
	wordScores('mathematician');
	wordScores('normal');
}

$goodWords = apc_fetch('mathematicianTrainSet');
$badWords = apc_fetch('normalTrainSet');
$s = mysql_query("SELECT * FROM wg_page WHERE mathematician=0");
$myScores = array();
while($se = mysql_fetch_array($s)) {
	$ret = getWordCounts(file_get_contents('data/'.$se['id'].'.txt'));
	$words = $ret[0]; $nbWords = $ret[1];
	$keys = array_keys($words);
	$goodScore = 0; $badScore = 0;
	foreach ($words as $word => $freq) {
		if(array_key_exists($word, $goodWords)) {$goodScore += $goodWords[$word];}
		if(array_key_exists($word, $badWords)) {$badScore += $badWords[$word];}
	}
	$myScores[$se['name']] = $goodScore-$badScore;
	mysql_query("UPDATE wg_page SET mathematician=".floor($myScores[$se['name']])." WHERE id=".$se['id']);
}

// arsort($myScores);
// foreach ($myScores as $name => $score) {
// 	echo '<p style="color: '.(($score>0)?'green':'red').'">'.$name.': '.$score.'</p>';
// }
?>