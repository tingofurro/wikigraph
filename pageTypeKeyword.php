<?php
include('init.php');
$r = mysql_query("SELECT * FROM wg_page WHERE pageType=-1 LIMIT 70");
$scoreWords = array();
$trainingSize = 0;
while($re = mysql_fetch_array($r)) {
	$ret = getWordCounts('data/'.$re['id'].'.txt');
	$words = $ret[0]; $nbWords = $ret[1];
	$keys = array_keys($words);
	for($i = 0; $i < count($keys); $i ++) {
		$myScore = $words[$keys[$i]]/$nbWords;
		if(array_key_exists($keys[$i], $scoreWords)) {
			$scoreWords[$keys[$i]] += $myScore;
		}
		else {
			$scoreWords[$keys[$i]] = $myScore;
		}
	}
	$trainingSize ++;
}
$r = mysql_query("SELECT * FROM wg_page WHERE pageType=1 LIMIT ".(2*$trainingSize)."");
while($re = mysql_fetch_array($r)) {
	$ret = getWordCounts('data/'.$re['id'].'.txt');
	$words = $ret[0]; $nbWords = $ret[1];
	$keys = array_keys($words); $goTo = min(500, count($keys));
	for($i = 0; $i < $goTo; $i ++) {if(array_key_exists($keys[$i], $scoreWords)) {$scoreWords[$keys[$i]] -= 2*$words[$keys[$i]]/$nbWords;}}
}
arsort($scoreWords);
$scoreWords = array_slice($scoreWords, 0, 100);
print_r($scoreWords);

$pages = array();
$s = mysql_query("SELECT * FROM wg_page WHERE pageType=0 ORDER BY RAND() LIMIT 50");
while($se = mysql_fetch_array($s)) {
	$ret = getWordCounts('data/'.$se['id'].'.txt');
	$words = $ret[0]; $nbWords = $ret[1];
	$keys = array_keys($words);
	$myScore = 0;
	for($i = 0; $i < count($keys); $i ++) {
		if(array_key_exists($keys[$i], $scoreWords)) {
			$myScore += $scoreWords[$keys[$i]]*$words[$keys[$i]];
		}
	}
	$pages[$se['name']] = $myScore;
}
arsort($pages);
foreach ($pages as $name => $score) {
	echo "<br /><b>".$name."</b> scored: ".$score."";
}


function getWordCounts($filename) {
	$html = file_get_contents($filename);
	$onlyTxt = strip_tags($html); // strip_tags($html, "<br>") for better aesthetic display
	$onlyTxt = strtolower($onlyTxt);
	$wordCount = str_word_count($onlyTxt, 1, '-'); $nbWords = count($wordCount);
	return array(array_count_values($wordCount), $nbWords);
}
?>