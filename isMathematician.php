<?php
include('init.php');
include_once('nlp/nlp.php');
$r = mysql_query("SELECT * FROM wg_page WHERE pageType=-1 LIMIT 70");
$txtSet = array();
while($re = mysql_fetch_array($r)) {
	$ret = file_get_contents('data/'.$re['id'].'.txt');
	array_push($txtSet, $ret);
}
$goodWords = wordScores($txtSet);
$r = mysql_query("SELECT * FROM wg_page WHERE pageType=1 LIMIT 70");
$txtSet = array();
while($re = mysql_fetch_array($r)) {
	$ret = file_get_contents('data/'.$re['id'].'.txt'); array_push($txtSet, $ret);
}
$badWords = wordScores($txtSet);
$s = mysql_query("SELECT * FROM wg_page WHERE pageType=0 ORDER BY RAND() LIMIT 200");

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
}
arsort($myScores);
foreach ($myScores as $name => $score) {
	echo '<p style="color: '.(($score>0)?'green':'red').'">'.$name.': '.$score.'</p>';
}
?>