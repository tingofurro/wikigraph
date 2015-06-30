<?php
function wordScores($token, $docRoot) {
	$trainPath = $docRoot.'/algo/nlp/'.$token.'Train.txt';
	if(file_exists($trainPath)) {
		$set = array();
		$toks = explode("||", file_get_contents($trainPath));
		foreach ($toks as $i => $val) {
			$toks[$i] = '"'.$val.'"';
		}
		$r = mysql_query("SELECT * FROM wg_page WHERE name IN(".implode(", ", $toks).")");
		while($re = mysql_fetch_array($r)) { $ret = file_get_contents($docRoot.'/data/'.$re['id'].'.txt'); array_push($set, $ret);}
		$commonWords = commonWords();
		$alpha = 0.1;
		$allScores = array(); $allWords = array();
		$eachWordCount = array(); $eachTotWord = array();
		foreach ($set as $id => $doc) {
			$ret = getWordCounts($doc);
			$wordCount = $ret[0]; $nbWords = $ret[1];
			$eachWordCount[$id] = $ret[0]; $eachTotWord[$id] = $ret[1];
			foreach ($wordCount as $word => $freq) {
				if(!in_array($word, $allWords) AND !in_array($word, $commonWords)) {array_push($allWords, $word);}
			}
		}
		foreach ($allWords as $i => $word) {
			$allScores[$word] = 0;
			foreach ($eachWordCount as $doc => $wordCount) {
				$score = 0;
				if(array_key_exists($word, $eachWordCount[$doc])) {
					$eachWordCount[$doc][$word] = ceil($eachWordCount[$doc][$word]/10);
					$score = $eachWordCount[$doc][$word]/$eachTotWord[$doc];
				}
				$allScores[$word] += log($alpha+$score);
			}
		}
		$max = abs(max($allScores));
		foreach ($allScores as $word => $score) {
			$allScores[$word] += ($max+30);
		}
		arsort($allScores);
		$allScores = array_slice($allScores, 0, 200); // keep the 200 most relevant words
		apc_store($token.'TrainSet', $allScores);
	}
}
function getWordCounts($html) {
	$onlyTxt = strip_tags($html); // strip_tags($html, "<br>") for better aesthetic display
	$onlyTxt = strtolower($onlyTxt);
	$wordCount = str_word_count($onlyTxt, 1, '-'); $nbWords = count($wordCount);
	return array(array_count_values($wordCount), $nbWords);
}
function commonWords() {
	return array('the', 'of', 'in', 'and', 'a', 'is', 'at', 'for', 'an', 'by', '-', 'as', 'this', 'that', 'be', 'on', 'edit', 'was');
}
?>