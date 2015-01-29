<?php
function wordScores($set) {
	$alpha = 0.1;
	$allScores = array(); $allWords = array();
	$eachWordCount = array(); $eachTotWord = array();
	foreach ($set as $id => $doc) {
		$ret = getWordCounts($doc);
		$wordCount = $ret[0]; $nbWords = $ret[1];
		$eachWordCount[$id] = $ret[0]; $eachTotWord[$id] = $ret[1];
		foreach ($wordCount as $word => $freq) {
			if(!in_array($word, $allWords)) {array_push($allWords, $word);}
		}
	}
	foreach ($allWords as $i => $word) {
		$allScores[$word] = 0;
		foreach ($eachWordCount as $doc => $wordCount) {
			$score = 0;
			if(array_key_exists($word, $eachWordCount[$doc])) {
				$eachWordCount[$doc][$word] = ceil($eachWordCount[$doc][$word]/5);
				$score = $eachWordCount[$doc][$word]/$eachTotWord[$doc];
			}
			$allScores[$word] += log($alpha+$score);
		}
	}
	foreach ($allScores as $word => $score) {
		$allScores[$word] = 172+$score;
	}
	arsort($allScores);
	$allScores = array_slice($allScores, 0, 200);
	return $allScores;
}
function getWordCounts($html) {
	$onlyTxt = strip_tags($html); // strip_tags($html, "<br>") for better aesthetic display
	$onlyTxt = strtolower($onlyTxt);
	$wordCount = str_word_count($onlyTxt, 1, '-'); $nbWords = count($wordCount);
	return array(array_count_values($wordCount), $nbWords);
}
?>