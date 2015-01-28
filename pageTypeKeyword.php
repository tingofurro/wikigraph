<?php
include('init.php');
$r = mysql_query("SELECT * FROM wg_page WHERE pageType=-1 LIMIT 70");
$scoreWords = array();
$trainingSize = 0;
while($re = mysql_fetch_array($r)) {
	$ret = getWordCounts('data/'.$re['id'].'.txt');
	$words = $ret[0]; $nbWords = $ret[1];
	$keys = array_keys($words); $goTo = min(150, count($keys));
	for($i = 0; $i < $goTo; $i ++) {
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
print_r($scoreWords);
function getWordCounts($filename) {
	$html = file_get_contents($filename);
	$onlyTxt = strip_tags($html); // strip_tags($html, "<br>") for better aesthetic display
	$onlyTxt = strtolower($onlyTxt);
	$wordCount = str_word_count($onlyTxt, 1); $nbWords = count($wordCount);
	return array(array_count_values($wordCount), $nbWords);
}
?>