<?php
function strToWiki($oldStr) {
	return str_replace(" ", "_", $oldStr);
}
function wikiToName($oldStr) {
	return urldecode(str_replace("_", " ", $oldStr));
}
function getKillList() {
	$file = file_get_contents("txt/killList.txt");
	$killList = explode("[]", $file);
	foreach ($killList as $i => $ele) { $killList[$i] = strToWiki($ele);}
	return $killList;
}
function getTime() {
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	return ($mtime[1] + $mtime[0]);
}
function totalSum($PR) {
	$totScore = 0;
	foreach ($PR as $i => $score) $totScore += $score;
	return $totScore;
}
function totalChange($PR, $NPR) {
	$totalChange = 0;
	foreach ($PR as $i => $score) $totalChange += abs($score-$NPR[$i]);
	return $totalChange;
}
function computePR($adja) {
	// the keys are the nodes
	// each value is an array of incoming nodes to this key value
	$d = 0.85; $totalPts = 1000;

	// it is important to know which nodes have no outgoing edges
	$nodeNb = count(array_keys($adja));
	$outCount = array(); $PR = array();
	foreach ($adja as $node => $vv) {
		$outCount[$node] = 0;
		$PR[$node] = $totalPts/$nodeNb; // initialize PR in equilibrium
	}

	foreach ($adja as $to => $incoming) {
		foreach ($incoming as $inc) $outCount[$inc] ++;
	}
	$totalChange = $totalPts; $round = 0;
	while($totalChange > 0.00001*$nodeNb) {
		$NPR = array();
		// Going to calculate how much is going to be lost
		$loss = 0;
		foreach ($outCount as $node => $count) {
			if($count == 0) $loss += $PR[$node];
		}

		foreach ($adja as $node => $incoming) {
			$NPR[$node] = ($totalPts*(1-$d) + $d*$loss)/$nodeNb;
			foreach ($incoming as $inc) {
				$NPR[$node] += $d*$PR[$inc]/$outCount[$inc];
			}
		}
		$totalChange = totalChange($PR, $NPR); // see how much we moved
		$PR = $NPR; // set this to the new PR, for next iteration
		$round ++;
	}
	echo "Computed pagerank in: ".$round." rounds.<br />";
	return $PR;
}
function computeDiffusion($adja, $source, $d) { // d is the damping comefficient, between 0 and 1
	$nodeNb = count(array_keys($adja));
	$diff = array(); $outCount = array();
	
	foreach ($adja as $node => $adj) {
		$diff[$node] = 0;	
		$outCount[$node] = 0;	
	}

	foreach ($adja as $to => $incoming) {
		foreach ($incoming as $inc) $outCount[$inc] ++;
	}

	$noOutCount = array();
	foreach ($outCount as $node => $count) {if($count == 0) array_push($noOutCount, $node);}

	// initial conditions
	$diff[$source] = $nodeNb; $totalChange = $nodeNb;

	while($totalChange > 1) {
		$Ndiff = array();
		foreach ($adja as $node => $incoming) {
			$Ndiff[$node] = (($node==$source)?((1-$d)*$nodeNb):0); // put the ashes in the center
			foreach ($incoming as $inc) {
				$Ndiff[$node] += $d*$diff[$inc]/$outCount[$inc]; // dissipation process, with damping
			}
		}
		$losses = 0;
		foreach ($noOutCount as $node) {$losses += $d*$diff[$node];} // these guys didn't dissipate their weight, it would be lost
		$Ndiff[$source] += $losses; // put these losses in center too
		$totalChange = totalChange($diff, $Ndiff);
		$diff = $Ndiff;
	}
	return $diff;
}
?>